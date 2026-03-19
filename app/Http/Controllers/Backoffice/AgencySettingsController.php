<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Agency\UpdateAgencySettingsRequest;
use App\Models\Agency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencySettingsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Check if user has permission to manage agency settings
     */
    private function checkAgencyPermission(Agency $agency): void
    {
        $user = Auth::guard('backoffice')->user();
        
        // Super admin can access any agency
        if ($user->hasRole('super-admin')) {
            return;
        }
        
        // Check if user belongs to this agency
        if ($user->agency_id !== $agency->id) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder aux paramètres de cette agence.');
        }
        
        // Check if user has edit permission
        if (!$user->can('agency-settings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de l\'agence.');
        }
    }

    /**
     * Display the settings form for the given agency.
     */
    public function edit(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);

        return view('backoffice.profile.profile-setting', compact('agency'));
    }

    /**
     * Display the notifications settings form.
     */
    public function notifications(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);

        return view('backoffice.profile.notifications-setting', compact('agency'));
    }

    /**
     * Display the invoice template settings form.
     */
    public function invoiceTemplate(Agency $agency): View
    {
        //$this->checkAgencyPermission($agency);

        return view('backoffice.profile.invoice-template', compact('agency'));
    }

    /**
     * Display the company (system) settings form.
     */
    public function company(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);

        return view('backoffice.profile.company-setting', compact('agency'));
    }

    /**
     * Display the signatures (branding) settings form.
     */
    public function signatures(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);

        return view('backoffice.profile.signatures-setting', compact('agency'));
    }

    /**
     * Show website settings page.
     */
    public function website(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);
        
        return view('backoffice.profile.website-setting', compact('agency'));
    }

    /**
     * Update website settings.
     */
public function updateWebsite(Request $request, Agency $agency): RedirectResponse
{
    $this->checkAgencyPermission($agency);
    
    $validated = $request->validate([
        // Localization fields
        'timezone' => 'nullable|string|max:50',
        'week_start' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        'date_format' => 'nullable|string|max:20',
        'time_format' => 'nullable|string|max:20',
        
        // Currency fields
        'currency' => 'nullable|string|max:10',
        'currency_symbol' => 'nullable|string|max:10',
        'currency_position' => 'nullable|string|in:left,right,left_space,right_space',
        'decimal_separator' => 'nullable|string|max:1|in:.,,',
        'thousand_separator' => 'nullable|string|max:1|in:,. ,',
    ]);

    $settings = $agency->settings ?? [];
    $settings['website'] = array_merge($settings['website'] ?? [], $validated);
    $agency->settings = $settings;
    $agency->save();

    $this->createNotification('update', 'website_settings', $agency);

    return redirect()
        ->route('backoffice.agencies.settings.website', $agency)
        ->with('toast', [
            'title'   => 'Mis à jour',
            'message' => "Les paramètres du site web ont été mis à jour avec succès.",
            'dot'     => '#0d6efd',
            'delay'   => 3500,
            'time'    => 'now',
        ]);
}

    /**
     * Show invoice settings page.
     */
    public function invoiceSettings(Agency $agency): View
    {
        $this->checkAgencyPermission($agency);
        
        return view('backoffice.profile.invoice-settings', compact('agency'));
    }

    /**
     * Update invoice settings.
     */
public function updateInvoiceSettings(Request $request, Agency $agency): RedirectResponse
{
    $this->checkAgencyPermission($agency);
    
    $validated = $request->validate([
        // Logo
        'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120|dimensions:max_width=180,max_height=180',
        
        // Invoice Format
        'invoice_prefix' => 'nullable|string|max:20',
        'invoice_start' => 'nullable|integer|min:1',
        'invoice_due_days' => 'nullable|integer|min:0|max:90',
        'invoice_round_off' => 'nullable|string|max:10',
        
        // Company Details
        'show_company_details' => 'nullable|boolean',
        'company_name' => 'nullable|string|max:255',
        'company_reg_number' => 'nullable|string|max:50',
        'company_tax_number' => 'nullable|string|max:50',
        'company_phone' => 'nullable|string|max:20',
        'company_email' => 'nullable|email|max:255',
        'company_address' => 'nullable|string|max:500',
        
        // Terms & Footer
        'invoice_terms' => 'nullable|string',
        'invoice_footer' => 'nullable|string',
        
        // Payment
        'payment_instructions' => 'nullable|string',
    ]);

    // Handle logo upload
    if ($request->hasFile('invoice_logo')) {
        $agency->clearMediaCollection('logo');
        $agency->addMediaFromRequest('invoice_logo')
            ->toMediaCollection('logo');
    }

    $settings = $agency->settings ?? [];
    $settings['invoice'] = array_merge($settings['invoice'] ?? [], $validated);
    $agency->settings = $settings;
    $agency->save();

    $this->createNotification('update', 'invoice_settings', $agency);

    return redirect()
        ->route('backoffice.agencies.settings.invoice-settings', $agency)
        ->with('toast', [
            'title'   => 'Mis à jour',
            'message' => "Les paramètres de facturation ont été mis à jour avec succès.",
            'dot'     => '#0d6efd',
            'delay'   => 3500,
            'time'    => 'now',
        ]);
}

    /**
     * Update company settings via POST
     */
    public function updateCompany(Request $request, Agency $agency): RedirectResponse
    {
        $this->checkAgencyPermission($agency);

        // Manually validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'default_currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'tp_number' => 'nullable|string|max:50',
            'rc_number' => 'nullable|string|max:50',
            'if_number' => 'nullable|string|max:50',
            'ice_number' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'creation_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Extract media files from validated data
        $logo = $request->file('logo');
        $signature = $request->file('signature');

        // Remove media from validated array to avoid storing in settings JSON
        unset($validated['logo'], $validated['signature']);

        // Merge settings (deep merge to avoid overwriting all settings)
        $currentSettings = $agency->settings ?? [];
        $newSettings = array_replace_recursive($currentSettings, $validated);

        // Update settings
        $agency->update(['settings' => $newSettings]);

        // Handle media uploads
        if ($logo) {
            $agency->clearMediaCollection('logo');
            $agency->addMedia($logo)->toMediaCollection('logo');
        }

        if ($signature) {
            $agency->clearMediaCollection('signature');
            $agency->addMedia($signature)->toMediaCollection('signature');
        }

        // Create notification for update
        $this->createNotification('update', 'agency_settings', $agency);

        return redirect()
            ->route('backoffice.agencies.settings.company', $agency)
            ->with('toast', [
                'title'   => 'Updated',
                'message' => "Les paramètres de l'agence « {$agency->name} » ont été mis à jour avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Update the agency settings.
     */
    public function update(UpdateAgencySettingsRequest $request, Agency $agency): RedirectResponse
    {
        $this->checkAgencyPermission($agency);

        $validated = $request->validated();

        // Extract media files from validated data
        $logo = $validated['logo'] ?? null;
        $signature = $validated['signature'] ?? null;
        $stamp = $validated['stamp'] ?? null;

        // Remove media from validated array to avoid storing in settings JSON
        unset($validated['logo'], $validated['signature'], $validated['stamp']);

        // Merge settings (deep merge to avoid overwriting all settings)
        $currentSettings = $agency->settings ?? [];
        $newSettings = array_replace_recursive($currentSettings, $validated);

        // Update settings
        $agency->update(['settings' => $newSettings]);

        // Handle media uploads
        if ($logo) {
            $agency->clearMediaCollection('logo');
            $agency->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        if ($signature) {
            $agency->clearMediaCollection('signature');
            $agency->addMediaFromRequest('signature')
                ->toMediaCollection('signature');
        }

        if ($stamp) {
            $agency->clearMediaCollection('stamp');
            $agency->addMediaFromRequest('stamp')
                ->toMediaCollection('stamp');
        }

        // Create notification for update
        $this->createNotification('update', 'agency_settings', $agency);

        return redirect()
            ->route('backoffice.agencies.settings.edit', $agency)
            ->with('toast', [
                'title'   => 'Updated',
                'message' => "Les paramètres de l'agence « {$agency->name} » ont été mis à jour avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Create a notification for agency settings actions
     */
    protected function createNotification($action, $module, $item = null, $customData = []): void
    {
        try {
            // Check if the NotificationController exists and method is available
            if (class_exists('App\Http\Controllers\Backoffice\NotificationController')) {
                $notificationController = app('App\Http\Controllers\Backoffice\NotificationController');
                
                if (method_exists($notificationController, 'createFromAction')) {
                    // Pass the agency model as the item
                    $notificationController->createFromAction($action, $module, $item, $customData);
                }
            }
        } catch (\Exception $e) {
            // Silently fail - notification is not critical
            \Log::warning('Could not create notification: ' . $e->getMessage());
        }
    }  


    /**
     * Delete logo
     */
    public function deleteLogo(Request $request)
    {
        $user = Auth::guard('backoffice')->user();
        $agency = $user->agency;
        
        if (!$agency) {
            return response()->json(['success' => false, 'message' => 'Agence non trouvée.'], 404);
        }
        
        if ($agency->hasMedia('logo')) {
            $agency->clearMediaCollection('logo');
            return response()->json(['success' => true, 'message' => 'Logo supprimé avec succès.']);
        }
        
        return response()->json(['success' => false, 'message' => 'Aucun logo à supprimer.'], 404);
    }


    

    

    /**
     * Delete signature
     */
    public function deleteSignature(Request $request)
    {
        $user = Auth::guard('backoffice')->user();
        $agency = $user->agency;
        
        if (!$agency) {
            return response()->json(['success' => false, 'message' => 'Agence non trouvée.'], 404);
        }
        
        if ($agency->hasMedia('signature')) {
            $agency->clearMediaCollection('signature');
            return response()->json(['success' => true, 'message' => 'Signature supprimée avec succès.']);
        }
        
        return response()->json(['success' => false, 'message' => 'Aucune signature à supprimer.'], 404);
    }

    
}