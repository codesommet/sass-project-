<!-- Add Brand -->
<div class="modal fade" id="add_brand" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <form class="needs-validation" novalidate action="{{ route('backoffice.vehicle-brands.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="_modal" value="add_brand">

                <div class="modal-header">
                    <h5 class="mb-0">Créer une marque</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>

                <div class="modal-body pb-1">

                    {{-- BRAND IMAGE --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Image de la marque <span class="text-danger">*</span>
                        </label>

                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-2">
                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames overflow-hidden"
                                style="border-radius: 10px !important;">

                                {{-- Placeholder icon --}}
                                <i class="ti ti-photo-up text-gray-4 fs-24" id="addBrandIcon"></i>

                                {{-- Preview image --}}
                                <img id="addBrandPreview" src="" alt="preview" class="img-fluid"
                                    style="display:none; width:100%; height:100%; object-fit:cover; border-radius: 10px !important;">
                            </div>

                            <div class="profile-upload">
                                <div class="profile-uploader d-flex align-items-center">
                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                        <i class="ti ti-photo-up fs-14"></i>
                                        Télécharger
                                        <input type="file" name="logo" id="addBrandLogoInput"
                                            class="form-control image-sign @error('logo') is-invalid @enderror"
                                            accept="image/*" required>
                                    </div>
                                </div>

                                {{-- CLIENT validation message --}}
                                <div class="invalid-feedback d-block" id="addBrandLogoClientError"
                                    style="display:none;">
                                    Veuillez télécharger une image de marque.
                                </div>

                                {{-- SERVER validation message --}}
                                @error('logo')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="mt-2">
                                    <p class="fs-14 mb-0">
                                        Taille de l'image 180x180, max 5 Mo
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BRAND NAME --}}
                    <div class="mb-3">
                        <label class="form-label">
                            Nom de la marque <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>

                        <div class="invalid-feedback">
                            Veuillez saisir un nom de marque.
                        </div>

                        @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Créer
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- /Add Brand -->
