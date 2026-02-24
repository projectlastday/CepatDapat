@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Setting Website</h5>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="settingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4 py-2" id="logo-tab" data-bs-toggle="tab"
                                data-bs-target="#logo-param" type="button" role="tab" aria-controls="logo-param"
                                aria-selected="true">
                                <i class="ti ti-photo me-2"></i>Logo Website
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4 py-2" id="backup-tab" data-bs-toggle="tab"
                                data-bs-target="#backup-param" type="button" role="tab" aria-controls="backup-param"
                                aria-selected="false">
                                <i class="ti ti-database me-2"></i>Backup Database
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4 py-2" id="access-tab" data-bs-toggle="tab"
                                data-bs-target="#access-param" type="button" role="tab" aria-controls="access-param"
                                aria-selected="false">
                                <i class="ti ti-lock-access me-2"></i>Hak Akses
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="settingTabsContent">

                        {{-- TAB 1: LOGO --}}
                        <div class="tab-pane fade show active" id="logo-param" role="tabpanel" aria-labelledby="logo-tab">
                            <div class="row align-items-center">
                                {{-- Current Logo Preview --}}
                                <div class="col-md-5 text-center mb-4 mb-md-0">
                                    <p class="text-muted fw-bold mb-3">Logo Saat Ini</p>
                                    <div class="p-4 bg-light rounded-4 d-inline-block" style="min-width: 200px;">
                                        <img src="{{ asset($logo ?? 'assets/images/CepatDapat.png') }}" alt="Current Logo"
                                            class="img-fluid" style="max-height: 120px;">
                                    </div>
                                </div>

                                {{-- Upload Form --}}
                                <div class="col-md-7">
                                    <form action="{{ route('setting.update_logo') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Upload Logo Baru</label>
                                            <input type="file" name="logo" class="form-control"
                                                accept="image/png, image/jpeg" required onchange="previewLogo(event)">
                                            <small class="text-muted">Format: PNG, JPG, JPEG. Maks: 2MB.</small>
                                            @error('logo')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Preview Container (Hidden by default) --}}
                                        <div id="preview-container" class="mb-3 d-none text-center">
                                            <p class="text-muted mb-2">Preview Upload:</p>
                                            <img id="logo-preview" src="#" alt="Logo Preview"
                                                class="img-fluid border rounded p-2"
                                                style="max-height: 100px; max-width: 100%;">
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ti ti-device-floppy me-2"></i>Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: BACKUP --}}
                        <div class="tab-pane fade" id="backup-param" role="tabpanel" aria-labelledby="backup-tab">
                            <div class="row align-items-center justify-content-center py-4">
                                <div class="col-md-8 text-center">
                                    <div class="mb-4">
                                        <i class="ti ti-database-export text-primary" style="font-size: 4rem;"></i>
                                    </div>
                                    <h4 class="fw-bold">Backup Database</h4>
                                    <p class="text-muted mb-4">
                                        Unduh salinan lengkap database database dalam format SQL.
                                        Pastikan untuk menyimpan file backup di tempat yang aman.
                                    </p>

                                    <form action="{{ route('setting.backup') }}" method="POST" id="backupForm">
                                        @csrf
                                        <button type="button" class="btn btn-success btn-lg px-5 shadow-sm" id="btnBackup"
                                            onclick="confirmBackup()">
                                            <i class="ti ti-download me-2"></i>Download Backup (.sql)
                                        </button>
                                    </form>
                                    <small class="text-muted d-block mt-3">
                                        *Proses download mungkin memakan waktu beberapa saat tergantung ukuran data.
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: HAK AKSES --}}
                        <div class="tab-pane fade" id="access-param" role="tabpanel" aria-labelledby="access-tab">
                            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                <i class="ti ti-info-circle fs-4 me-2"></i>
                                <div>
                                    <strong>Info:</strong> Super Admin (7) selalu memiliki akses penuh ke semua fitur.
                                    Tabel di bawah ini hanya untuk mengatur akses role lainnya.
                                </div>
                            </div>

                            <form action="{{ route('SettingController.updateHakAkses') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th class="text-start" style="width: 30%;">Fitur / Menu</th>
                                                @foreach ($hak_akses['roles'] as $roleId => $roleLabel)
                                                    <th>{{ $roleLabel }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $currentGroup = null;
                                            @endphp

                                            @foreach ($hak_akses['features'] as $fKey => $fMeta)
                                                {{-- Group Header --}}
                                                @if ($currentGroup !== $fMeta['group'])
                                                    @php $currentGroup = $fMeta['group']; @endphp
                                                    <tr class="table-secondary">
                                                        <td colspan="{{ count($hak_akses['roles']) + 1 }}"
                                                            class="fw-bold text-uppercase small px-3 py-2">
                                                            {{ $currentGroup }}
                                                        </td>
                                                    </tr>
                                                @endif

                                                <tr>
                                                    <td class="px-3">
                                                        <span class="fw-medium">{{ $fMeta['label'] }}</span>
                                                        <div class="small text-muted fst-italic">{{ $fKey }}</div>
                                                    </td>
                                                    @foreach ($hak_akses['roles'] as $roleId => $roleLabel)
                                                        <td class="text-center">
                                                            <div class="form-check d-flex justify-content-center">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="permissions[{{ $fKey }}][{{ $roleId }}]" value="on" {{ isset($hak_akses['matrix'][$fKey][$roleId]) && $hak_akses['matrix'][$fKey][$roleId] ? 'checked' : '' }}>
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ti ti-device-floppy me-2"></i>Simpan Hak Akses
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('logoPreview').src = e.target.result;
                        document.getElementById('previewContainer').classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }

            function confirmBackup(btn) {
                if (!confirm('Apakah Anda yakin ingin membuat backup database?')) {
                    return false;
                }
                // Disable AFTER form submits (setTimeout allows submit to fire first)
                setTimeout(function () {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses backup...';
                    // Re-enable after 10s (file download won't navigate away)
                    setTimeout(function () {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-download me-1"></i> Download Backup (.sql)';
                    }, 10000);
                }, 100);
                return true;
            }
        </script>
    @endpush
@endsection