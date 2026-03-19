<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\RentalContract;
use App\Models\Agency;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ContractPDFController extends Controller
{
    /**
     * Export single contract as PDF with logo and signature
     */
    public function exportSingle($id)
    {
        $contract = RentalContract::with([
            'vehicle', 
            'vehicle.model',
            'vehicle.model.brand',
            'primaryClient', 
            'secondaryClient', 
            'agency',
            'createdBy'
        ])->findOrFail($id);
        
        $agency = $contract->agency;
        
        // Get logo and signature URLs from media library
        $logoUrl = $agency?->getFirstMediaUrl('logo');
        $signatureUrl = $agency?->getFirstMediaUrl('signature');
        
        // Convert to base64 for PDF embedding
        $logoBase64 = null;
        $signatureBase64 = null;
        
        // Process logo
        if ($logoUrl) {
            // Check if it's a local file
            if (file_exists(public_path($logoUrl))) {
                $logoPath = public_path($logoUrl);
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            } 
            // Check if it's a storage path
            elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $logoUrl)))) {
                $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $logoUrl));
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            }
            // If it's a URL, try to fetch it
            elseif (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                try {
                    $logoData = file_get_contents($logoUrl);
                    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
                } catch (\Exception $e) {
                    \Log::error('Failed to fetch logo: ' . $e->getMessage());
                }
            }
        }
        
        // Process signature
        if ($signatureUrl) {
            if (file_exists(public_path($signatureUrl))) {
                $signaturePath = public_path($signatureUrl);
                $signatureData = file_get_contents($signaturePath);
                $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
            }
            elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl)))) {
                $signaturePath = storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl));
                $signatureData = file_get_contents($signaturePath);
                $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
            }
            elseif (filter_var($signatureUrl, FILTER_VALIDATE_URL)) {
                try {
                    $signatureData = file_get_contents($signatureUrl);
                    $signatureBase64 = 'data:image/jpeg;base64,' . base64_encode($signatureData);
                } catch (\Exception $e) {
                    \Log::error('Failed to fetch signature: ' . $e->getMessage());
                }
            }
        }
        
        $data = [
            'contract' => $contract,
            'agency' => $agency,
            'client' => $contract->primaryClient,
            'secondaryClient' => $contract->secondaryClient,
            'vehicle' => $contract->vehicle,
            'logo' => $logoBase64,
            'signature' => $signatureBase64,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System'
        ];
        
        // Use the selected template from agency settings
        $template = $agency?->settings['app']['contract_template'] ?? 'template1';
        $viewName = in_array($template, ['template1', 'template2', 'template3']) ? $template : 'template1';

        $pdf = Pdf::loadView('backoffice.rental-contracts.pdf.' . $viewName, $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'contrat_' . $contract->contract_number . '_' . date('Y-m-d');

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Export multiple contracts as PDF with logo
     */
    public function exportMultiple(Request $request)
    {
        $ids = $request->ids;
        
        if (!$ids) {
            return redirect()->back()->with('error', 'Aucun contrat sélectionné');
        }
        
        $contracts = RentalContract::with([
            'vehicle', 
            'primaryClient', 
            'agency'
        ])->whereIn('id', $ids)->get();
        
        // Get agency from first contract (assuming same agency)
        $agency = $contracts->first()?->agency;
        
        // Get logo URL and convert to base64
        $logoUrl = $agency?->getFirstMediaUrl('logo');
        $logoBase64 = null;
        
        if ($logoUrl) {
            if (file_exists(public_path($logoUrl))) {
                $logoPath = public_path($logoUrl);
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            }
            elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $logoUrl)))) {
                $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $logoUrl));
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            }
        }
        
        $data = [
            'contracts' => $contracts,
            'agency' => $agency,
            'logo' => $logoBase64,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System',
            'total_count' => $contracts->count()
        ];
        
        $pdf = Pdf::loadView('backoffice.rental-contracts.pdf.multiple', $data);
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'contrats_' . date('Y-m-d_His');
        
        return $pdf->download($filename . '.pdf');
    }
    
    /**
     * Stream contract in browser
     */
    public function view($id)
    {
        $contract = RentalContract::with([
            'vehicle', 
            'vehicle.model',
            'vehicle.model.brand',
            'primaryClient', 
            'secondaryClient', 
            'agency'
        ])->findOrFail($id);
        
        $agency = $contract->agency;
        
        // Get logo and signature URLs
        $logoUrl = $agency?->getFirstMediaUrl('logo');
        $signatureUrl = $agency?->getFirstMediaUrl('signature');
        
        // Convert to base64
        $logoBase64 = null;
        $signatureBase64 = null;
        
        if ($logoUrl) {
            if (file_exists(public_path($logoUrl))) {
                $logoPath = public_path($logoUrl);
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            }
            elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $logoUrl)))) {
                $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $logoUrl));
                $logoData = file_get_contents($logoPath);
                $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
            }
        }
        
        if ($signatureUrl) {
            if (file_exists(public_path($signatureUrl))) {
                $signaturePath = public_path($signatureUrl);
                $signatureData = file_get_contents($signaturePath);
                $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
            }
            elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl)))) {
                $signaturePath = storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl));
                $signatureData = file_get_contents($signaturePath);
                $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
            }
        }
        
        $data = [
            'contract' => $contract,
            'agency' => $agency,
            'client' => $contract->primaryClient,
            'secondaryClient' => $contract->secondaryClient,
            'vehicle' => $contract->vehicle,
            'logo' => $logoBase64,
            'signature' => $signatureBase64,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System'
        ];
        
        // Use the selected template from agency settings
        $template = $agency?->settings['app']['contract_template'] ?? 'template1';
        $viewName = in_array($template, ['template1', 'template2', 'template3']) ? $template : 'template1';

        $pdf = Pdf::loadView('backoffice.rental-contracts.pdf.' . $viewName, $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('contrat_' . $contract->contract_number . '.pdf');
    }
}