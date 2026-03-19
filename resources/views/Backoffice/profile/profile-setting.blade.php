<?php $page = 'profile-setting'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4 pb-0">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Paramètres Agence</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agences</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        @if(session('toast'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Succès!</strong> {{ session('toast')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">

            <!-- Sidebar -->
            <div class="col-lg-3">
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'profile',
                ])
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="card profile-setting-section">
                    <div class="card-header">
                        <h5 class="fw-bold">Paramètres du compte</h5>
                    </div>

                    <!-- CHANGED: Form action to agency profile update route -->
                    <form action="{{ route('backoffice.agencies.settings.update.profile', $agency) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body pb-1">

                            <h6 class="fw-bold mb-3">Informations de base</h6>

                            <div class="border-bottom mb-3">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Photo de profil</label>
                                            <div class="d-flex align-items-start flex-wrap row-gap-3 mb-3">
                                                <div class="me-4 position-relative">
                                                    <!-- Delete button positioned above the photo -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-light rounded-circle p-0 position-absolute" 
                                                            id="deletePhotoBtn"
                                                            style="width: 24px; height: 24px; top: -8px; right: -8px; z-index: 10; display: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                                        <i class="ti ti-x fs-14"></i>
                                                    </button>
                                                    
                                                    <!-- Photo container - CHANGED to agency logo -->
                                                    <div class="avatar avatar-xxl" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 2px solid #e0e0e0;">
                                                        <img src="{{ $agency->getFirstMediaUrl('logo') ?: URL::asset('admin_assets/img/customer/customer-01.jpg') }}"
                                                             class="img-fluid w-100 h-100"
                                                             alt="agency logo"
                                                             id="photoPreview"
                                                             style="object-fit: cover;">
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="position-relative">
                                                        <button type="button" class="btn btn-dark" id="uploadBtn">
                                                            <i class="ti ti-photo-up fs-14 me-1"></i>
                                                            Changer
                                                        </button>
                                                        <input type="file" 
                                                               id="photoInput"
                                                               name="profile_photo"
                                                               accept="image/*"
                                                               style="position: absolute; opacity: 0; width: 0; height: 0;">
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14 text-muted mb-0">Taille recommandée: 500px x 500px</p>
                                                    </div>
                                                    @error('profile_photo')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CHANGED: These fields should now be for agency, not user -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nom de l'agence<span class="text-danger ms-1">*</span></label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name', $agency->name ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Adresse e-mail</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', $agency->email ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Numéro de téléphone</label>
                                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $agency->phone ?? '') }}">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">Informations d'adresse</h6>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Adresse</label>
                                        <input type="text" class="form-control" name="address" value="{{ old('address', $agency->address ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pays</label>
                                        <select class="form-select" name="country">
                                            <option value="">Sélectionner</option>
                                            <option value="USA" {{ old('country', $agency->country ?? '') == 'USA' ? 'selected' : '' }}>États-Unis</option>
                                            <option value="Canada" {{ old('country', $agency->country ?? '') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                            <option value="UK" {{ old('country', $agency->country ?? '') == 'UK' ? 'selected' : '' }}>Royaume-Uni</option>
                                            <option value="France" {{ old('country', $agency->country ?? '') == 'France' ? 'selected' : '' }}>France</option>
                                            <option value="Morocco" {{ old('country', $agency->country ?? '') == 'Morocco' ? 'selected' : '' }}>Maroc</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ville</label>
                                        <input type="text" class="form-control" name="city" value="{{ old('city', $agency->city ?? '') }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('backoffice.agencies.settings.profile', $agency) }}" class="btn btn-light me-3">Annuler</a>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d’utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
            <a href="javascript:void(0);" class="text-secondary">Dreams</a>
        </p>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const uploadBtn = document.getElementById('uploadBtn');
    const deleteBtn = document.getElementById('deletePhotoBtn');
    const defaultImage = "{{ URL::asset('admin_assets/img/customer/customer-01.jpg') }}";
    
    // Check if agency already has a logo - CHANGED to agency.logo
    @if($agency->getFirstMediaUrl('logo'))
        deleteBtn.style.display = 'flex';
    @endif
    
    // Upload button click triggers file input
    uploadBtn.addEventListener('click', function() {
        photoInput.click();
    });
    
    // Preview image when file is selected
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                // Show delete button when custom photo is loaded
                deleteBtn.style.display = 'flex';
            }
            
            reader.readAsDataURL(file);
        }
    });
    
    // Delete button functionality with AJAX - CHANGED to agency logo delete route
    deleteBtn.addEventListener('click', function() {
        if (confirm('Voulez-vous vraiment supprimer le logo de l\'agence?')) {
            // Show loading state
            const originalHtml = deleteBtn.innerHTML;
            deleteBtn.innerHTML = '<i class="ti ti-loader fs-12"></i>';
            deleteBtn.disabled = true;
            
            fetch('{{ route("backoffice.agencies.settings.delete-logo", $agency) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset to default image
                    photoPreview.src = defaultImage;
                    
                    // Clear file input
                    photoInput.value = '';
                    
                    // Hide delete button
                    deleteBtn.style.display = 'none';
                    
                    // Show success message
                    alert('Logo de l\'agence supprimé avec succès');
                } else {
                    alert(data.message || 'Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la suppression');
            })
            .finally(() => {
                // Restore button
                deleteBtn.innerHTML = originalHtml;
                deleteBtn.disabled = false;
            });
        }
    });
});
</script>

<style>
.avatar-xxl {
    width: 100px;
    height: 100px;
}
.avatar-xxl img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Delete button styling */
#deletePhotoBtn {
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid #ddd;
    transition: all 0.2s;
}

#deletePhotoBtn:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white !important;
}

#deletePhotoBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.drag-upload-btn {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.drag-upload-btn input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
</style>
@endsection