<?php

namespace App\Http\Requests\Backoffice\Agency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('backoffice')->user();
        if (!$user) return false;

        // Super-admin can update any agency
        if ($user->hasRole('super-admin', 'backoffice')) return true;

        // Agency users with settings permission can update their own agency
        $agency = $this->route('agency');
        return $agency && $user->agency_id === $agency->id && $user->can('agency-settings.general.edit');
    }

    public function rules(): array
    {
        return [
            // Account Settings
            'account.language' => ['sometimes', 'string', 'in:fr,en,es'],
            'account.timezone' => ['sometimes', 'string', 'timezone'],

            // Notifications
            'notifications.notify_me_about' => ['sometimes', 'string', 'in:all_messages,mentions_only,nothing'],
            'notifications.desktop_enabled' => ['sometimes', 'boolean'],
            'notifications.unread_badge_enabled' => ['sometimes', 'boolean'],

            // Notification Types
            'notifications.types.booking_rental_updates' => ['sometimes', 'boolean'],
            'notifications.types.payment_invoice_notifications' => ['sometimes', 'boolean'],
            'notifications.types.user_tenant_notifications' => ['sometimes', 'boolean'],
            'notifications.types.vehicle_management' => ['sometimes', 'boolean'],

            // App Settings
            'app.invoice_template' => ['sometimes', 'string', 'in:template1,template2,template3'],
            'app.contract_template' => ['sometimes', 'string', 'in:template1,template2,template3'],

            // System Settings
            'app.system.default_currency' => ['sometimes', 'string', 'size:3'],
            'app.system.vat_enabled' => ['sometimes', 'boolean'],
            'app.system.vat_percentage' => [
                'sometimes',
                'nullable',
                'integer',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($this->input('app.system.vat_enabled') && $value === null) {
                        $fail('Le pourcentage TVA est obligatoire si la TVA est activée.');
                    }
                },
            ],

            // Media Files
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'stamp' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'account.language' => 'langue',
            'account.timezone' => 'fuseau horaire',
            'notifications.notify_me_about' => 'notifications',
            'notifications.desktop_enabled' => 'notifications de bureau',
            'notifications.unread_badge_enabled' => 'badge non lus',
            'notifications.types.booking_rental_updates' => 'mises à jour de réservation',
            'notifications.types.payment_invoice_notifications' => 'notifications de paiement',
            'notifications.types.user_tenant_notifications' => 'notifications utilisateur',
            'notifications.types.vehicle_management' => 'gestion des véhicules',
            'app.invoice_template' => 'modèle de facture',
            'app.contract_template' => 'modèle de contrat',
            'app.system.default_currency' => 'devise par défaut',
            'app.system.vat_enabled' => 'TVA activée',
            'app.system.vat_percentage' => 'pourcentage TVA',
            'logo' => 'logo',
            'signature' => 'signature',
            'stamp' => 'tampon',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => 'Le logo doit être une image valide.',
            'logo.mimes' => 'Le logo doit être au format PNG, JPG, JPEG ou WebP.',
            'logo.max' => 'Le logo ne doit pas dépasser 2 MB.',
            'signature.image' => 'La signature doit être une image valide.',
            'signature.mimes' => 'La signature doit être au format PNG, JPG, JPEG ou WebP.',
            'signature.max' => 'La signature ne doit pas dépasser 2 MB.',
            'stamp.image' => 'Le tampon doit être une image valide.',
            'stamp.mimes' => 'Le tampon doit être au format PNG, JPG, JPEG ou WebP.',
            'stamp.max' => 'Le tampon ne doit pas dépasser 2 MB.',
            'app.system.default_currency.size' => 'La devise doit faire exactement 3 caractères (ex: MAD, USD).',
            'app.system.vat_percentage.between' => 'Le pourcentage TVA doit être entre 0 et 100.',
        ];
    }
}
