<?php

namespace App\Traits;

/**
 * Shared helpers for CRUD controllers.
 * Standardizes toast notifications, permission checks, and error handling.
 */
trait HasCrudHelpers
{
    /**
     * Build a standard toast redirect response.
     */
    protected function toastRedirect(string $route, string $title, string $message, string $color = '#198754')
    {
        return redirect()
            ->route($route)
            ->with('toast', [
                'title'   => $title,
                'message' => $message,
                'dot'     => $color,
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Build a standard toast success response for create actions.
     */
    protected function toastCreated(string $route, string $label)
    {
        return $this->toastRedirect($route, 'Créé', "$label créé(e) avec succès.", '#198754');
    }

    /**
     * Build a standard toast success response for update actions.
     */
    protected function toastUpdated(string $route, string $label)
    {
        return $this->toastRedirect($route, 'Mis à jour', "$label mis(e) à jour avec succès.", '#0d6efd');
    }

    /**
     * Build a standard toast success response for delete actions.
     */
    protected function toastDeleted(string $route, string $label)
    {
        return $this->toastRedirect($route, 'Supprimé', "$label supprimé(e) avec succès.", '#dc3545');
    }

    /**
     * Build a standard error redirect.
     */
    protected function toastError(string $action, \Exception $e)
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('toast', [
                'title'   => 'Erreur',
                'message' => "Erreur lors de l'opération : " . $e->getMessage(),
                'dot'     => '#dc3545',
                'delay'   => 5000,
                'time'    => 'now',
            ]);
    }

    /**
     * Check permission and abort 403 if denied.
     */
    protected function checkPermission(string $permission, string $message = null)
    {
        if (!auth()->user()->can($permission)) {
            abort(403, $message ?? 'Vous n\'avez pas la permission d\'effectuer cette action.');
        }
    }

    /**
     * Build standard permissions array for views.
     */
    protected function viewPermissions(string $module): array
    {
        return [
            'can_view'   => auth()->user()->can("$module.general.view"),
            'can_create' => auth()->user()->can("$module.general.create"),
            'can_edit'   => auth()->user()->can("$module.general.edit"),
            'can_delete' => auth()->user()->can("$module.general.delete"),
        ];
    }
}
