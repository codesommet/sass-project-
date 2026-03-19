<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Agency;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class InvoicePDFController extends Controller
{
    /**
     * Export single invoice as PDF with logo and signature
     */
    public function exportSingle($id)
    {
        $invoice = Invoice::with([
            'client', 
            'rentalContract', 
            'agency',
            'items'
        ])->findOrFail($id);
        
        // Get agency with media
        $agency = $invoice->agency;
        
        // Get logo and signature URLs
        $logoUrl = $agency?->getFirstMediaUrl('logo');
        $signatureUrl = $agency?->getFirstMediaUrl('signature');
        
        // Convert to base64 for PDF embedding - WITH ERROR HANDLING
        $logoBase64 = null;
        $signatureBase64 = null;
        
        // Process logo with timeout protection
        if ($logoUrl) {
            try {
                // Set a timeout for file operations
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                
                // Try different paths
                if (file_exists(public_path($logoUrl))) {
                    $logoPath = public_path($logoUrl);
                    $logoData = file_get_contents($logoPath, false, $ctx);
                    if ($logoData !== false) {
                        $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
                    }
                } elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $logoUrl)))) {
                    $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $logoUrl));
                    $logoData = file_get_contents($logoPath, false, $ctx);
                    if ($logoData !== false) {
                        $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue without logo
                \Log::warning('Could not load logo for PDF: ' . $e->getMessage());
            }
        }
        
        // Process signature with timeout protection
        if ($signatureUrl) {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                
                if (file_exists(public_path($signatureUrl))) {
                    $signaturePath = public_path($signatureUrl);
                    $signatureData = file_get_contents($signaturePath, false, $ctx);
                    if ($signatureData !== false) {
                        $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                        $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
                    }
                } elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl)))) {
                    $signaturePath = storage_path('app/public/' . str_replace('/storage/', '', $signatureUrl));
                    $signatureData = file_get_contents($signaturePath, false, $ctx);
                    if ($signatureData !== false) {
                        $extension = pathinfo($signaturePath, PATHINFO_EXTENSION);
                        $signatureBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($signatureData);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load signature for PDF: ' . $e->getMessage());
            }
        }
        
        $data = [
            'invoice' => $invoice,
            'agency' => $agency,
            'client' => $invoice->client,
            'contract' => $invoice->rentalContract,
            'items' => $invoice->items,
            'logo' => $logoBase64,
            'signature' => $signatureBase64,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System'
        ];

        // Use the selected template from agency settings
        $template = $agency?->settings['app']['invoice_template'] ?? 'template1';
        $viewName = in_array($template, ['template1', 'template2']) ? $template : 'template1';

        $pdf = Pdf::loadView('backoffice.invoices.pdf.' . $viewName, $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'facture_' . $invoice->invoice_number . '_' . date('Y-m-d');

        return $pdf->download($filename . '.pdf');
    }


    public function view($id)
    {
        $invoice = Invoice::with(['client', 'rentalContract', 'items', 'agency'])->findOrFail($id);

        $agency = $invoice->agency;

        // Use the selected template from agency settings
        $template = $agency?->settings['app']['invoice_template'] ?? 'template1';
        $viewName = in_array($template, ['template1', 'template2']) ? $template : 'template1';

        $pdf = PDF::loadView('backoffice.invoices.pdf.' . $viewName, [
            'invoice' => $invoice,
            'agency' => $agency,
            'logo' => null,
            'signature' => null,
            'generated_at' => now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System',
        ]);

        return $pdf->stream('facture-' . $invoice->invoice_number . '.pdf');
    }
    
    /**
     * Export multiple invoices as PDF with logo
     */
    public function exportMultiple(Request $request)
    {
        $ids = $request->ids;
        
        if (!$ids) {
            return redirect()->back()->with('error', 'Aucune facture sélectionnée');
        }
        
        $invoices = Invoice::with([
            'client', 
            'agency',
            'items',
            'rentalContract'
        ])->whereIn('id', $ids)->get();
        
        // Get agency from first invoice (assuming same agency)
        $agency = $invoices->first()?->agency;
        
        // Get logo URL with timeout protection
        $logoBase64 = null;
        $logoUrl = $agency?->getFirstMediaUrl('logo');
        
        if ($logoUrl) {
            try {
                $ctx = stream_context_create(['http' => ['timeout' => 2]]);
                
                if (file_exists(public_path($logoUrl))) {
                    $logoPath = public_path($logoUrl);
                    $logoData = file_get_contents($logoPath, false, $ctx);
                    if ($logoData !== false) {
                        $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
                    }
                } elseif (file_exists(storage_path('app/public/' . str_replace('/storage/', '', $logoUrl)))) {
                    $logoPath = storage_path('app/public/' . str_replace('/storage/', '', $logoUrl));
                    $logoData = file_get_contents($logoPath, false, $ctx);
                    if ($logoData !== false) {
                        $extension = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $extension . ';base64,' . base64_encode($logoData);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load logo for multiple PDF: ' . $e->getMessage());
            }
        }
        
        $data = [
            'invoices' => $invoices,
            'agency' => $agency,
            'logo' => $logoBase64,
            'generated_at' => Carbon::now()->format('d/m/Y H:i'),
            'generated_by' => auth()->user()->name ?? 'System',
            'total_count' => $invoices->count(),
            'total_amount' => $invoices->sum('total_ttc')
        ];
        
        $pdf = Pdf::loadView('backoffice.invoices.pdf.multiple', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'factures_' . date('Y-m-d_His');
        
        return $pdf->download($filename . '.pdf');
    }
}