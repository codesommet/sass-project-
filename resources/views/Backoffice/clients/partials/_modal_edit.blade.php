<?php $page = 'clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Wizard Navigation */
    .wizard-nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .wizard-nav .nav-item {
        flex: 1;
        min-width: 150px;
    }
    .wizard-nav .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
    .wizard-nav .nav-link i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .wizard-nav .nav-link.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }

    /* Fieldset */
    .fieldset {
        display: none;
    }
    .fieldset.active {
        display: block;
    }

    /* Document Upload */
    .document-preview {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    .document-placeholder {
        width: 100%;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .document-placeholder:hover {
        border-color: #0d6efd;
        color: #0d6efd;
    }
    .document-placeholder i {
        font-size: 2rem;
        margin-right: 8px;
    }
    .document-placeholder img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .document-placeholder.has-image {
        border: 2px solid #198754;
    }
    .document-placeholder.has-image i,
    .document-placeholder.has-image span {
        opacity: 0;
    }
    .document-preview-small {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 1px solid #dee2e6;
    }
    .existing-document {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .remove-document {
        cursor: pointer;
        color: #dc3545;
        margin-left: auto;
    }
    .remove-document:hover {
        color: #bb2d3b;
    }

    /* Optional Section */
    .optional-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #0d6efd;
    }
    .optional-section .form-check {
        margin-bottom: 1rem;
    }
    .optional-fields {
        display: none;
        margin-top: 1rem;
    }
    .optional-fields.show {
        display: block;
    }

    /* Photo Upload */
    #avatarFrame {
        width: 120px;
        height: 120px;
        border-radius: 16px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    #avatarFrame.has-image {
        border: 2px solid #0d6efd;
    }
    #avatarFrame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3">
                    <a href="{{ route('backoffice.clients.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-3">
                        <i class="ti ti-alert-circle me-1"></i>
                        <strong>Erreur de validation</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Modifier le client
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-user"></i>
                                    Informations personnelles
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-map-pin"></i>
                                    Adresse & Contact
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-id"></i>
                                    Documents d'identité
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.clients.update', $client) }}" method="POST"
                            enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="remove_avatar" id="removeAvatar" value="0">
                            <input type="hidden" name="remove_cin_front" id="removeCinFront" value="0">
                            <input type="hidden" name="remove_cin_back" id="removeCinBack" value="0">
                            <input type="hidden" name="remove_passport" id="removePassport" value="0">
                            <input type="hidden" name="remove_license" id="removeLicense" value="0">

                            <!-- ==================== TAB 1: INFORMATIONS PERSONNELLES ==================== -->
                            <fieldset class="fieldset active" id="tab1">
                                <!-- Photo Upload -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium">
                                        Photo du client
                                        <span class="text-muted fs-12">(2MB max - JPG, PNG, GIF)</span>
                                    </label>

                                    <div class="d-flex align-items-center flex-wrap row-gap-3">
                                        <!-- Photo Preview Frame -->
                                        <div id="avatarFrame">
                                            @if ($client->getFirstMediaUrl('avatar'))
                                                <img id="avatarImg" src="{{ $client->getFirstMediaUrl('avatar') }}" alt="Photo du client"
                                                    style="width:100%;height:100%;object-fit:cover;">
                                                <a href="javascript:void(0);" id="avatarTrash"
                                                    class="position-absolute top-0 end-0 bg-light text-danger rounded-circle m-1"
                                                    style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                <i id="avatarIcon" class="ti ti-photo-up text-gray-4 fs-32" style="display:none;"></i>
                                            @else
                                                <i id="avatarIcon" class="ti ti-photo-up text-gray-4 fs-32"></i>
                                                <img id="avatarImg" src="" alt="Photo du client"
                                                    style="display:none;width:100%;height:100%;object-fit:cover;">
                                                <a href="javascript:void(0);" id="avatarTrash"
                                                    class="position-absolute top-0 end-0 bg-light text-danger rounded-circle m-1"
                                                    style="display:none; width: 30px; height: 30px; align-items: center; justify-content: center; text-decoration: none;">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="profile-upload flex-grow-1 ms-3">
                                            <div class="profile-uploader d-flex align-items-center flex-wrap gap-2">
                                                <div class="drag-upload-btn btn btn-md btn-dark position-relative">
                                                    <i class="ti ti-upload me-1"></i>
                                                    Changer la photo
                                                    <input type="file" name="avatar" id="avatarInput"
                                                        class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                                        accept="image/jpeg,image/png,image/jpg,image/gif"
                                                        style="cursor: pointer;">
                                                </div>
                                                @if ($client->getFirstMediaUrl('avatar'))
                                                    <button type="button" class="btn btn-md btn-light" id="avatarClearBtn">
                                                        <i class="ti ti-trash me-1"></i>
                                                        Supprimer
                                                    </button>
                                                @endif
                                            </div>

                                            <div id="photoValidationMessage" class="mt-2">
                                                @error('avatar')
                                                    <div class="text-danger small">
                                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="mt-1">
                                                <p class="fs-12 text-muted mb-0">
                                                    <i class="ti ti-info-circle me-1"></i>
                                                    Formats: JPG, PNG, GIF • Max: 2MB • Max: 2000x2000px
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agence -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Agence <span class="text-danger">*</span>
                                    </label>
                                    <select name="agency_id" class="form-select @error('agency_id') is-invalid @enderror" required>
                                        <option value="">Sélectionner une agence</option>
                                        @foreach ($agencies as $agency)
                                            <option value="{{ $agency->id }}"
                                                {{ old('agency_id', $client->agency_id) == $agency->id ? 'selected' : '' }}>
                                                {{ $agency->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agency_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <!-- Prénom -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Prénom <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="first_name"
                                                value="{{ old('first_name', $client->first_name) }}"
                                                class="form-control @error('first_name') is-invalid @enderror" required maxlength="100"
                                                placeholder="Jean">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nom -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nom <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="last_name"
                                                value="{{ old('last_name', $client->last_name) }}"
                                                class="form-control @error('last_name') is-invalid @enderror" required maxlength="100"
                                                placeholder="Dupont">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">E-mail</label>
                                            <input type="email" name="email"
                                                value="{{ old('email', $client->email) }}"
                                                class="form-control @error('email') is-invalid @enderror" maxlength="150"
                                                placeholder="client@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Téléphone -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Téléphone <span class="text-danger">*</span>
                                            </label>
                                            <input type="tel" name="phone"
                                                value="{{ old('phone', $client->phone) }}"
                                                class="form-control @error('phone') is-invalid @enderror" required maxlength="50"
                                                placeholder="+33 1 23 45 67 89">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Date de naissance -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Date de naissance</label>
                                            <input type="date" name="birth_date"
                                                value="{{ old('birth_date', $client->birth_date ? $client->birth_date->format('Y-m-d') : '') }}"
                                                class="form-control @error('birth_date') is-invalid @enderror">
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Nationalité -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nationalité</label>
                                            <input type="text" name="nationality"
                                                value="{{ old('nationality', $client->nationality) }}"
                                                class="form-control @error('nationality') is-invalid @enderror"
                                                placeholder="Française">
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-tab" data-next="2">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- ==================== TAB 2: ADRESSE & CONTACT ==================== -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Adresse -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Adresse</label>
                                            <input type="text" name="address"
                                                value="{{ old('address', $client->address) }}"
                                                class="form-control @error('address') is-invalid @enderror"
                                                placeholder="123 Rue de Paris">
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Ville -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ville</label>
                                            <input type="text" name="city"
                                                value="{{ old('city', $client->city) }}"
                                                class="form-control @error('city') is-invalid @enderror" placeholder="Paris">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Pays -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Pays</label>
                                            <input type="text" name="country"
                                                value="{{ old('country', $client->country) }}"
                                                class="form-control @error('country') is-invalid @enderror" placeholder="France">
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Statut -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Statut</label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                                <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>
                                                    Actif</option>
                                                <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>
                                                    Inactif</option>
                                                <option value="blacklisted" {{ old('status', $client->status) == 'blacklisted' ? 'selected' : '' }}>
                                                    Blacklisté</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="1">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary next-tab" data-next="3">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- ==================== TAB 3: DOCUMENTS D'IDENTITÉ ==================== -->
                            <fieldset class="fieldset" id="tab3">
                                <!-- CIN Section -->
                                <div class="optional-section">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_cin" name="has_cin" value="1"
                                            {{ $client->cin_number || $client->getFirstMedia('cin_front') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="has_cin">
                                            Carte d'Identité Nationale (CIN)
                                        </label>
                                    </div>
                                    <div class="optional-fields" id="cin_fields" style="{{ $client->cin_number || $client->getFirstMedia('cin_front') ? 'display: block;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Numéro CIN</label>
                                                    <input type="text" name="cin_number"
                                                        value="{{ old('cin_number', $client->cin_number) }}"
                                                        class="form-control @error('cin_number') is-invalid @enderror"
                                                        placeholder="AB123456">
                                                    @error('cin_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Validité CIN</label>
                                                    <input type="date" name="cin_valid_until"
                                                        value="{{ old('cin_valid_until', $client->cin_valid_until ? $client->cin_valid_until->format('Y-m-d') : '') }}"
                                                        class="form-control @error('cin_valid_until') is-invalid @enderror">
                                                    @error('cin_valid_until')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Recto CIN</label>
                                                    @if($client->getFirstMedia('cin_front'))
                                                        <div class="existing-document">
                                                            <img src="{{ $client->getFirstMediaUrl('cin_front') }}" 
                                                                 class="document-preview-small me-2" 
                                                                 alt="Recto CIN"
                                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_front') }}', 'Recto CIN')">
                                                            <span>Recto CIN existant</span>
                                                            <span class="remove-document" onclick="removeDocument('cin_front')">
                                                                <i class="ti ti-trash"></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="document-placeholder" data-input="cin_front" id="cin_front_placeholder"
                                                        style="{{ $client->getFirstMedia('cin_front') ? 'display: none;' : '' }}">
                                                        <i class="ti ti-upload"></i>
                                                        <span>Télécharger recto</span>
                                                        <img id="preview_cin_front" src="" style="display: none;">
                                                    </div>
                                                    <input type="file" name="cin_front" id="cin_front" class="d-none" accept="image/*,application/pdf">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Verso CIN</label>
                                                    @if($client->getFirstMedia('cin_back'))
                                                        <div class="existing-document">
                                                            <img src="{{ $client->getFirstMediaUrl('cin_back') }}" 
                                                                 class="document-preview-small me-2" 
                                                                 alt="Verso CIN"
                                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_back') }}', 'Verso CIN')">
                                                            <span>Verso CIN existant</span>
                                                            <span class="remove-document" onclick="removeDocument('cin_back')">
                                                                <i class="ti ti-trash"></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="document-placeholder" data-input="cin_back" id="cin_back_placeholder"
                                                        style="{{ $client->getFirstMedia('cin_back') ? 'display: none;' : '' }}">
                                                        <i class="ti ti-upload"></i>
                                                        <span>Télécharger verso</span>
                                                        <img id="preview_cin_back" src="" style="display: none;">
                                                    </div>
                                                    <input type="file" name="cin_back" id="cin_back" class="d-none" accept="image/*,application/pdf">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Passport Section -->
                                <div class="optional-section">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_passport" name="has_passport" value="1"
                                            {{ $client->passport_number || $client->getFirstMedia('passport') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="has_passport">
                                            Passeport
                                        </label>
                                    </div>
                                    <div class="optional-fields" id="passport_fields" style="{{ $client->passport_number || $client->getFirstMedia('passport') ? 'display: block;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Numéro passeport</label>
                                                    <input type="text" name="passport_number"
                                                        value="{{ old('passport_number', $client->passport_number) }}"
                                                        class="form-control @error('passport_number') is-invalid @enderror"
                                                        placeholder="AB123456">
                                                    @error('passport_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date délivrance</label>
                                                    <input type="date" name="passport_issue_date"
                                                        value="{{ old('passport_issue_date', $client->passport_issue_date ? $client->passport_issue_date->format('Y-m-d') : '') }}"
                                                        class="form-control @error('passport_issue_date') is-invalid @enderror">
                                                    @error('passport_issue_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Copie du passeport</label>
                                                    @if($client->getFirstMedia('passport'))
                                                        <div class="existing-document">
                                                            <img src="{{ $client->getFirstMediaUrl('passport') }}" 
                                                                 class="document-preview-small me-2" 
                                                                 alt="Passeport"
                                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('passport') }}', 'Passeport')">
                                                            <span>Passeport existant</span>
                                                            <span class="remove-document" onclick="removeDocument('passport')">
                                                                <i class="ti ti-trash"></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="document-placeholder" data-input="passport_file" id="passport_placeholder"
                                                        style="{{ $client->getFirstMedia('passport') ? 'display: none;' : '' }}">
                                                        <i class="ti ti-upload"></i>
                                                        <span>Télécharger le document</span>
                                                        <img id="preview_passport" src="" style="display: none;">
                                                    </div>
                                                    <input type="file" name="passport_file" id="passport_file" class="d-none" accept="image/*,application/pdf">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permis de Conduire Section -->
                                <div class="optional-section">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_license" name="has_license" value="1"
                                            {{ $client->driving_license_number || $client->getFirstMedia('license') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="has_license">
                                            Permis de Conduire
                                        </label>
                                    </div>
                                    <div class="optional-fields" id="license_fields" style="{{ $client->driving_license_number || $client->getFirstMedia('license') ? 'display: block;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Numéro permis</label>
                                                    <input type="text" name="driving_license_number"
                                                        value="{{ old('driving_license_number', $client->driving_license_number) }}"
                                                        class="form-control @error('driving_license_number') is-invalid @enderror"
                                                        placeholder="AB123456">
                                                    @error('driving_license_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Date délivrance</label>
                                                    <input type="date" name="driving_license_issue_date"
                                                        value="{{ old('driving_license_issue_date', $client->driving_license_issue_date ? $client->driving_license_issue_date->format('Y-m-d') : '') }}"
                                                        class="form-control @error('driving_license_issue_date') is-invalid @enderror">
                                                    @error('driving_license_issue_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Copie du permis</label>
                                                    @if($client->getFirstMedia('license'))
                                                        <div class="existing-document">
                                                            <img src="{{ $client->getFirstMediaUrl('license') }}" 
                                                                 class="document-preview-small me-2" 
                                                                 alt="Permis"
                                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('license') }}', 'Permis de Conduire')">
                                                            <span>Permis existant</span>
                                                            <span class="remove-document" onclick="removeDocument('license')">
                                                                <i class="ti ti-trash"></i>
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="document-placeholder" data-input="license_file" id="license_placeholder"
                                                        style="{{ $client->getFirstMedia('license') ? 'display: none;' : '' }}">
                                                        <i class="ti ti-upload"></i>
                                                        <span>Télécharger le document</span>
                                                        <img id="preview_license" src="" style="display: none;">
                                                    </div>
                                                    <input type="file" name="license_file" id="license_file" class="d-none" accept="image/*,application/pdf">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="Informations complémentaires...">{{ old('notes', $client->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i>
                                        Mettre à jour
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    function openImageModal(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    function removeDocument(type) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            const hiddenInput = document.getElementById(`remove${type.charAt(0).toUpperCase() + type.slice(1)}`);
            if (hiddenInput) hiddenInput.value = '1';
            
            // Hide existing document and show upload placeholder
            const existingDoc = document.querySelector(`[onclick="removeDocument('${type}')"]`).closest('.existing-document');
            if (existingDoc) existingDoc.style.display = 'none';
            
            const placeholder = document.getElementById(`${type}_placeholder`);
            if (placeholder) placeholder.style.display = 'flex';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ==================== TAB NAVIGATION ====================
        const tabs = document.querySelectorAll('.nav-link[data-tab]');
        const fieldsets = document.querySelectorAll('.fieldset');
        
        function showTab(tabNumber) {
            // Hide all fieldsets
            fieldsets.forEach(f => f.classList.remove('active'));
            
            // Show selected tab
            const selectedTab = document.getElementById(`tab${tabNumber}`);
            if (selectedTab) selectedTab.classList.add('active');

            // Update nav links
            tabs.forEach(t => t.classList.remove('active'));
            const activeTab = document.querySelector(`.nav-link[data-tab="${tabNumber}"]`);
            if (activeTab) activeTab.classList.add('active');
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                showTab(this.getAttribute('data-tab'));
            });
        });

        document.querySelectorAll('.next-tab').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                showTab(this.getAttribute('data-next'));
            });
        });

        document.querySelectorAll('.prev-tab').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                showTab(this.getAttribute('data-prev'));
            });
        });

        // ==================== PHOTO UPLOAD ====================
        const avatarInput = document.getElementById('avatarInput');
        const avatarImg = document.getElementById('avatarImg');
        const avatarIcon = document.getElementById('avatarIcon');
        const avatarTrash = document.getElementById('avatarTrash');
        const avatarClear = document.getElementById('avatarClearBtn');
        const removeAvatar = document.getElementById('removeAvatar');
        const avatarFrame = document.getElementById('avatarFrame');

        if (avatarInput) {
            avatarInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    alert('La photo ne doit pas dépasser 2MB');
                    this.value = '';
                    return;
                }

                if (!file.type.match('image.*')) {
                    alert('Veuillez sélectionner une image valide');
                    this.value = '';
                    return;
                }

                removeAvatar.value = '0';
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarImg.src = e.target.result;
                    avatarImg.style.display = 'block';
                    avatarIcon.style.display = 'none';
                    if (avatarTrash) avatarTrash.style.display = 'flex';
                    avatarFrame.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            });
        }

        if (avatarTrash) {
            avatarTrash.addEventListener('click', function(e) {
                e.preventDefault();
                removeAvatar.value = '1';
                if (avatarInput) avatarInput.value = '';
                avatarImg.src = '';
                avatarImg.style.display = 'none';
                avatarIcon.style.display = 'flex';
                avatarTrash.style.display = 'none';
                avatarFrame.classList.remove('has-image');
            });
        }

        if (avatarClear) {
            avatarClear.addEventListener('click', function() {
                removeAvatar.value = '1';
                if (avatarInput) avatarInput.value = '';
                avatarImg.src = '';
                avatarImg.style.display = 'none';
                avatarIcon.style.display = 'flex';
                if (avatarTrash) avatarTrash.style.display = 'none';
                avatarFrame.classList.remove('has-image');
            });
        }

        // ==================== OPTIONAL SECTIONS TOGGLE ====================
        const optionalSections = [
            { checkbox: 'has_cin', fields: 'cin_fields' },
            { checkbox: 'has_passport', fields: 'passport_fields' },
            { checkbox: 'has_license', fields: 'license_fields' }
        ];

        optionalSections.forEach(section => {
            const checkbox = document.getElementById(section.checkbox);
            const fields = document.getElementById(section.fields);

            if (checkbox && fields) {
                checkbox.addEventListener('change', function() {
                    fields.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        // Clear input values when unchecked
                        const inputs = fields.querySelectorAll('input[type="text"], input[type="date"]');
                        inputs.forEach(input => {
                            if (!input.name.includes('_file')) {
                                input.value = '';
                            }
                        });
                    }
                });
            }
        });

        // ==================== DOCUMENT PREVIEW ====================
        function setupDocumentPreview(inputId, previewId, placeholderSelector) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.querySelector(placeholderSelector);

            if (input && preview && placeholder) {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                placeholder.classList.add('has-image');
                                // Hide text and icon
                                const icon = placeholder.querySelector('i');
                                const span = placeholder.querySelector('span');
                                if (icon) icon.style.opacity = '0';
                                if (span) span.style.opacity = '0';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // For PDF files, show PDF icon
                            preview.style.display = 'none';
                            placeholder.classList.add('has-image');
                            const icon = placeholder.querySelector('i');
                            const span = placeholder.querySelector('span');
                            if (icon) {
                                icon.className = 'ti ti-file-text';
                                icon.style.opacity = '1';
                            }
                            if (span) {
                                span.textContent = file.name;
                                span.style.opacity = '1';
                            }
                        }
                    }
                });
            }
        }

        // Setup previews for all documents
        setupDocumentPreview('cin_front', 'preview_cin_front', '#cin_front_placeholder');
        setupDocumentPreview('cin_back', 'preview_cin_back', '#cin_back_placeholder');
        setupDocumentPreview('passport_file', 'preview_passport', '#passport_placeholder');
        setupDocumentPreview('license_file', 'preview_license', '#license_placeholder');

        // Make placeholders clickable
        document.querySelectorAll('.document-placeholder').forEach(placeholder => {
            placeholder.addEventListener('click', function() {
                const inputId = this.getAttribute('data-input');
                if (inputId) {
                    document.getElementById(inputId)?.click();
                }
            });
        });

        // ==================== BOOTSTRAP VALIDATION ====================
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection