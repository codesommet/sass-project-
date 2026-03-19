<?php $page = 'website-settings'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .settings-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .settings-section h6 {
        color: #0d6efd;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .required:after {
        content: " *";
        color: #dc3545;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Paramètres de l'agence</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agences</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Paramètres du site</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'website',
                ])
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold">Paramètres du site</h5>
                    </div>
                    <form action="{{ route('backoffice.agencies.settings.website.update', $agency) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            
                            <!-- Localization Section -->
                            <div class="settings-section">
                                <h6>🌍 Localisation</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Fuseau horaire</label>
                                        <select name="timezone" class="form-select">
                                            <option value="">Sélectionner le fuseau horaire</option>
                                            <option value="UTC" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'UTC') ? 'selected' : '' }}>UTC</option>
                                            <option value="Africa/Casablanca" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Africa/Casablanca') ? 'selected' : '' }}>Africa/Casablanca</option>
                                            <option value="Europe/London" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Europe/London') ? 'selected' : '' }}>Europe/London</option>
                                            <option value="Europe/Paris" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Europe/Paris') ? 'selected' : '' }}>Europe/Paris</option>
                                            <option value="America/New_York" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'America/New_York') ? 'selected' : '' }}>America/New York</option>
                                            <option value="Asia/Dubai" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Asia/Dubai') ? 'selected' : '' }}>Asia/Dubai</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Début de la semaine</label>
                                        <select name="week_start" class="form-select">
                                            <option value="">Sélectionner</option>
                                            <option value="monday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'monday') ? 'selected' : '' }}>Lundi</option>
                                            <option value="tuesday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'tuesday') ? 'selected' : '' }}>Mardi</option>
                                            <option value="wednesday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'wednesday') ? 'selected' : '' }}>Mercredi</option>
                                            <option value="thursday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'thursday') ? 'selected' : '' }}>Jeudi</option>
                                            <option value="friday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'friday') ? 'selected' : '' }}>Vendredi</option>
                                            <option value="saturday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'saturday') ? 'selected' : '' }}>Samedi</option>
                                            <option value="sunday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'sunday') ? 'selected' : '' }}>Dimanche</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Format de date</label>
                                        <select name="date_format" class="form-select">
                                            <option value="">Sélectionner le format</option>
                                            <option value="d/m/Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'd/m/Y') ? 'selected' : '' }}>DD/MM/YYYY (31/12/2024)</option>
                                            <option value="m/d/Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'm/d/Y') ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                                            <option value="Y-m-d" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'Y-m-d') ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
                                            <option value="d M Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'd M Y') ? 'selected' : '' }}>DD MMM YYYY (31 Dec 2024)</option>
                                            <option value="M d, Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'M d, Y') ? 'selected' : '' }}>MMM DD, YYYY (Dec 31, 2024)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Format de l'heure</label>
                                        <select name="time_format" class="form-select">
                                            <option value="">Sélectionner le format</option>
                                            <option value="H:i" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'H:i') ? 'selected' : '' }}>24 heures (14:30)</option>
                                            <option value="h:i A" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'h:i A') ? 'selected' : '' }}>12 heures (02:30 PM)</option>
                                            <option value="h:i a" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'h:i a') ? 'selected' : '' }}>12 heures (02:30 pm)</option>
                                            <option value="H:i:s" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'H:i:s') ? 'selected' : '' }}>24 heures avec secondes (14:30:45)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Currency Information Section -->
                            <div class="settings-section">
                                <h6>💰 Informations de devise</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Devise</label>
                                        <select name="currency" class="form-select">
                                            <option value="">Sélectionner la devise</option>
                                            <option value="MAD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'MAD') ? 'selected' : '' }}>MAD - Dirham marocain</option>
                                            <option value="USD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'USD') ? 'selected' : '' }}>USD - Dollar américain</option>
                                            <option value="EUR" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'EUR') ? 'selected' : '' }}>EUR - Euro</option>
                                            <option value="GBP" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'GBP') ? 'selected' : '' }}>GBP - Livre sterling</option>
                                            <option value="CAD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'CAD') ? 'selected' : '' }}>CAD - Dollar canadien</option>
                                            <option value="AED" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'AED') ? 'selected' : '' }}>AED - Dirham émirati</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Symbole de devise</label>
                                        <input type="text" name="currency_symbol" class="form-control"
                                               value="{{ old('currency_symbol', $agency->settings['website']['currency_symbol'] ?? '') }}"
                                               placeholder="ex. : $, €, £, DH">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Position de la devise</label>
                                        <select name="currency_position" class="form-select">
                                            <option value="">Sélectionner la position</option>
                                            <option value="left" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'left') ? 'selected' : '' }}>Gauche ($100)</option>
                                            <option value="right" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'right') ? 'selected' : '' }}>Droite (100$)</option>
                                            <option value="left_space" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'left_space') ? 'selected' : '' }}>Gauche avec espace ($ 100)</option>
                                            <option value="right_space" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'right_space') ? 'selected' : '' }}>Droite avec espace (100 $)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Séparateur décimal</label>
                                        <select name="decimal_separator" class="form-select">
                                            <option value="">Sélectionner le séparateur</option>
                                            <option value="." {{ (old('decimal_separator', $agency->settings['website']['decimal_separator'] ?? '') == '.') ? 'selected' : '' }}>Point (.) - 100.50</option>
                                            <option value="," {{ (old('decimal_separator', $agency->settings['website']['decimal_separator'] ?? '') == ',') ? 'selected' : '' }}>Virgule (,) - 100,50</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Séparateur de milliers</label>
                                        <select name="thousand_separator" class="form-select">
                                            <option value="">Sélectionner le séparateur</option>
                                            <option value="," {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == ',') ? 'selected' : '' }}>Virgule (,) - 1,000</option>
                                            <option value="." {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == '.') ? 'selected' : '' }}>Point (.) - 1.000</option>
                                            <option value=" " {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == ' ') ? 'selected' : '' }}>Espace ( ) - 1 000</option>
                                            <option value="" {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == '') ? 'selected' : '' }}>Aucun - 1000</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection