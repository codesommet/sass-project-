<div class="settings-sidebar slimscroll">
    <div class="sidebar-menu">
        <ul>
            <li class="menu-title"><span>PARAMÈTRES</span></li>
            <li>
                <ul class="sidebar-links pb-3 mb-3 border-bottom">

                    <li @class(['active' => $active === 'profile'])>
                        <a href="{{ route('backoffice.agencies.settings.profile', $agency) }}">
                            <i class="ti ti-user-edit me-2"></i>
                            <span>Profil</span>
                        </a>
                    </li>

                    <!-- EDIT PASSWORD -->
                    <li @class(['active' => $active === 'change-password'])>
                        <a href="{{ route('backoffice.profile.change-password') }}">
                            <i class="ti ti-lock me-2"></i>
                            <span>Modifier le mot de passe</span>
                        </a>
                    </li>

                    <li @class(['active' => $active === 'notifications'])>
                        <a href="{{ route('backoffice.agencies.settings.notifications', $agency) }}">
                            <i class="ti ti-bell me-2"></i>
                            <span>Notifications</span>
                        </a>
                    </li>

                    <!-- NEW: WEBSITE SETTINGS TAB -->
                    <li @class(['active' => $active === 'website'])>
                        <a href="{{ route('backoffice.agencies.settings.website', $agency) }}">
                            <i class="ti ti-world me-2"></i>
                            <span>Site Web</span>
                        </a>
                    </li>

                    <li @class(['active' => $active === 'invoice-template'])>
                        <a href="{{ route('backoffice.agencies.settings.invoice-template', $agency) }}">
                            <i class="ti ti-template me-2"></i>
                            <span>Modèle de facture</span>
                        </a>
                    </li>

                    <!-- NEW: INVOICES SETTINGS TAB -->
                    <li @class(['active' => $active === 'invoice-settings'])>
                        <a href="{{ route('backoffice.agencies.settings.invoice-settings', $agency) }}">
                            <i class="ti ti-file-invoice me-2"></i>
                            <span>Paramètres de facturation</span>
                        </a>
                    </li>

                    <li @class(['active' => $active === 'company'])>
                        <a href="{{ route('backoffice.agencies.settings.company', $agency) }}">
                            <i class="ti ti-building me-2"></i>
                            <span>Paramètres système</span>
                        </a>
                    </li>

                    <!-- SUBSCRIPTION TAB -->
                    <li @class(['active' => $active === 'subscription'])>
                        <a href="{{ route('backoffice.my-subscription') }}">
                            <i class="ti ti-crown me-2"></i>
                            <span>Abonnement</span>
                        </a>
                    </li>

                    <!-- WhatsApp (COMING SOON) -->
                    <li class="position-relative" style="opacity: 0.5; pointer-events: none;">
                        <a href="javascript:void(0);" style="color: #9c9c9c; text-decoration: line-through;">
                            <i class="ti ti-brand-whatsapp me-2"></i>
                            <span>Connexion WhatsApp</span>
                        </a>
                        <small class="badge bg-warning text-dark position-absolute"
                            style="top: 5px; right: 10px; font-size: 0.65rem;">
                            Bientôt
                        </small>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>