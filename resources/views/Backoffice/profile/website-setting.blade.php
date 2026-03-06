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
                <h2 class="mb-1">Agency Settings</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agencies</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Website Settings</li>
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
                        <h5 class="fw-bold">Website Settings</h5>
                    </div>
                    <form action="{{ route('backoffice.agencies.settings.website.update', $agency) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            
                            <!-- Localization Section -->
                            <div class="settings-section">
                                <h6>🌍 Localization</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Time Zone</label>
                                        <select name="timezone" class="form-select">
                                            <option value="">Select Time Zone</option>
                                            <option value="UTC" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'UTC') ? 'selected' : '' }}>UTC</option>
                                            <option value="Africa/Casablanca" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Africa/Casablanca') ? 'selected' : '' }}>Africa/Casablanca</option>
                                            <option value="Europe/London" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Europe/London') ? 'selected' : '' }}>Europe/London</option>
                                            <option value="Europe/Paris" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Europe/Paris') ? 'selected' : '' }}>Europe/Paris</option>
                                            <option value="America/New_York" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'America/New_York') ? 'selected' : '' }}>America/New York</option>
                                            <option value="Asia/Dubai" {{ (old('timezone', $agency->settings['website']['timezone'] ?? '') == 'Asia/Dubai') ? 'selected' : '' }}>Asia/Dubai</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Start Week On</label>
                                        <select name="week_start" class="form-select">
                                            <option value="">Select</option>
                                            <option value="monday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'monday') ? 'selected' : '' }}>Monday</option>
                                            <option value="tuesday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'tuesday') ? 'selected' : '' }}>Tuesday</option>
                                            <option value="wednesday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'wednesday') ? 'selected' : '' }}>Wednesday</option>
                                            <option value="thursday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'thursday') ? 'selected' : '' }}>Thursday</option>
                                            <option value="friday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'friday') ? 'selected' : '' }}>Friday</option>
                                            <option value="saturday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'saturday') ? 'selected' : '' }}>Saturday</option>
                                            <option value="sunday" {{ (old('week_start', $agency->settings['website']['week_start'] ?? '') == 'sunday') ? 'selected' : '' }}>Sunday</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Date Format</label>
                                        <select name="date_format" class="form-select">
                                            <option value="">Select Format</option>
                                            <option value="d/m/Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'd/m/Y') ? 'selected' : '' }}>DD/MM/YYYY (31/12/2024)</option>
                                            <option value="m/d/Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'm/d/Y') ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                                            <option value="Y-m-d" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'Y-m-d') ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
                                            <option value="d M Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'd M Y') ? 'selected' : '' }}>DD MMM YYYY (31 Dec 2024)</option>
                                            <option value="M d, Y" {{ (old('date_format', $agency->settings['website']['date_format'] ?? '') == 'M d, Y') ? 'selected' : '' }}>MMM DD, YYYY (Dec 31, 2024)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Time Format</label>
                                        <select name="time_format" class="form-select">
                                            <option value="">Select Format</option>
                                            <option value="H:i" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'H:i') ? 'selected' : '' }}>24 Hours (14:30)</option>
                                            <option value="h:i A" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'h:i A') ? 'selected' : '' }}>12 Hours (02:30 PM)</option>
                                            <option value="h:i a" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'h:i a') ? 'selected' : '' }}>12 Hours (02:30 pm)</option>
                                            <option value="H:i:s" {{ (old('time_format', $agency->settings['website']['time_format'] ?? '') == 'H:i:s') ? 'selected' : '' }}>24 Hours with seconds (14:30:45)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Currency Information Section -->
                            <div class="settings-section">
                                <h6>💰 Currency Information</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Currency</label>
                                        <select name="currency" class="form-select">
                                            <option value="">Select Currency</option>
                                            <option value="MAD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'MAD') ? 'selected' : '' }}>MAD - Moroccan Dirham</option>
                                            <option value="USD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'USD') ? 'selected' : '' }}>USD - US Dollar</option>
                                            <option value="EUR" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'EUR') ? 'selected' : '' }}>EUR - Euro</option>
                                            <option value="GBP" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'GBP') ? 'selected' : '' }}>GBP - British Pound</option>
                                            <option value="CAD" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'CAD') ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                            <option value="AED" {{ (old('currency', $agency->settings['website']['currency'] ?? '') == 'AED') ? 'selected' : '' }}>AED - UAE Dirham</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Currency Symbol</label>
                                        <input type="text" name="currency_symbol" class="form-control" 
                                               value="{{ old('currency_symbol', $agency->settings['website']['currency_symbol'] ?? '') }}" 
                                               placeholder="e.g., $, €, £, DH">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Currency Position</label>
                                        <select name="currency_position" class="form-select">
                                            <option value="">Select Position</option>
                                            <option value="left" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'left') ? 'selected' : '' }}>Left ($100)</option>
                                            <option value="right" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'right') ? 'selected' : '' }}>Right (100$)</option>
                                            <option value="left_space" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'left_space') ? 'selected' : '' }}>Left with space ($ 100)</option>
                                            <option value="right_space" {{ (old('currency_position', $agency->settings['website']['currency_position'] ?? '') == 'right_space') ? 'selected' : '' }}>Right with space (100 $)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Decimal Separator</label>
                                        <select name="decimal_separator" class="form-select">
                                            <option value="">Select Separator</option>
                                            <option value="." {{ (old('decimal_separator', $agency->settings['website']['decimal_separator'] ?? '') == '.') ? 'selected' : '' }}>Period (.) - 100.50</option>
                                            <option value="," {{ (old('decimal_separator', $agency->settings['website']['decimal_separator'] ?? '') == ',') ? 'selected' : '' }}>Comma (,) - 100,50</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Thousand Separator</label>
                                        <select name="thousand_separator" class="form-select">
                                            <option value="">Select Separator</option>
                                            <option value="," {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == ',') ? 'selected' : '' }}>Comma (,) - 1,000</option>
                                            <option value="." {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == '.') ? 'selected' : '' }}>Period (.) - 1.000</option>
                                            <option value=" " {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == ' ') ? 'selected' : '' }}>Space ( ) - 1 000</option>
                                            <option value="" {{ (old('thousand_separator', $agency->settings['website']['thousand_separator'] ?? '') == '') ? 'selected' : '' }}>None - 1000</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection