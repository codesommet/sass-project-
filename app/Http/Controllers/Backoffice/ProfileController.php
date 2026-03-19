<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Show the profile settings page
     */
    public function edit()
    {
        $user = auth('backoffice')->user();
        $agency = $user->agency;

        return view('backoffice.profile.profile-setting', [
            'agency' => $agency,
            'user' => $user,
        ]);
    }

    /**
     * Update profile information
     */
public function update(UpdateProfileRequest $request)
{
    // Simple debug to a different file
    file_put_contents(storage_path('logs/debug.txt'), 'Update method called at ' . now() . PHP_EOL, FILE_APPEND);
    file_put_contents(storage_path('logs/debug.txt'), 'Has file: ' . ($request->hasFile('profile_photo') ? 'YES' : 'NO') . PHP_EOL, FILE_APPEND);
    
    if ($request->hasFile('profile_photo')) {
        file_put_contents(storage_path('logs/debug.txt'), 'File name: ' . $request->file('profile_photo')->getClientOriginalName() . PHP_EOL, FILE_APPEND);
    }

    $user = $request->user();

    $user->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'name' => $request->first_name . ' ' . $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'country' => $request->country,
        'postal_code' => $request->postal_code,
    ]);

    // Handle profile photo upload
    if ($request->hasFile('profile_photo')) {
        try {
            $user->clearMediaCollection('avatar');
            $media = $user->addMediaFromRequest('profile_photo')
                ->toMediaCollection('avatar');
            file_put_contents(storage_path('logs/debug.txt'), 'Photo saved with ID: ' . $media->id . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/debug.txt'), 'Erreur : ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }

    return redirect()
        ->route('backoffice.profile.setting')
        ->with('toast', [
            'title' => 'Succès',
            'message' => 'Profil mis à jour avec succès.',
            'dot' => '#28a745',
            'delay' => 3500,
            'time' => 'now',
        ]);
}

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
     * Delete profile photo
     */
    public function deletePhoto(Request $request)
    {
        $user = auth('backoffice')->user();
        
        Log::info('=== DELETE PHOTO STARTED ===');
        Log::info('User ID: ' . $user->id);
        
        if ($user->hasMedia('avatar')) {
            try {
                $media = $user->getFirstMedia('avatar');
                Log::info('Found media ID: ' . $media->id);
                Log::info('Media path: ' . $media->getPath());
                
                $user->clearMediaCollection('avatar');
                Log::info('Media collection cleared');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Photo de profil supprimée avec succès.'
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur suppression photo : ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ], 500);
            }
        }
        
        Log::info('No media found to delete');
        return response()->json([
            'success' => false,
            'message' => 'Aucune photo à supprimer.'
        ], 404);
    }

    /**
     * Show the change password form
     */
    public function showChangePassword()
    {
        $user = auth('backoffice')->user();
        $agency = $user->agency;

        return view('backoffice.profile.change-password', [
            'agency' => $agency,
            'user' => $user,
        ]);
    }

    /**
     * Update the user's password with timestamp tracking
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password:backoffice'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = auth('backoffice')->user();

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        Log::info('Password updated for user: ' . $user->id);

        return redirect()
            ->route('backoffice.profile.change-password')
            ->with('toast', [
                'title' => 'Succès',
                'message' => 'Mot de passe mis à jour avec succès.',
                'dot' => '#28a745',
                'delay' => 3500,
                'time' => 'now',
            ]);
    }
}