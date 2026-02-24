@extends('layouts.app')

@section('content')
    <style>
        /* Custom styles for image upload that might not be in the template */
        .image-upload-box {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }

        .image-upload-box:hover {
            border-color: #5e72e4;
            /* Berry Primary or similar */
            background: #e9ecef;
        }

        .file-input-hidden {
            display: none;
        }

        .preview-img {
            max-width: 100%;
            max-height: 160px;
            border-radius: 8px;
            display: none;
            object-fit: cover;
        }

        .btn-remove-img {
            position: absolute;
            top: 10px;
            right: 10px;
            display: none;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 14px;
            line-height: 25px;
            text-align: center;
            z-index: 10;
            padding: 0;
        }

        /* Adjusting for dark mode if needed by the template, assuming default light for now */
    </style>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Pasang Lelang Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('auction.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- Kolom Kiri: Detail Barang --}}
                            <div class="col-lg-7">
                                <h5 class="mb-3 text-uppercase text-muted small fw-bold">Detail Barang</h5>
                                <hr class="mt-0 mb-4">

                                <div class="mb-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_barang" class="form-control"
                                        placeholder="Apa nama barang yang ingin Anda lelang?" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Harga Awal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="harga_awal"
                                            placeholder="Tentukan harga mulai lelang" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Barang</label>
                                    <textarea class="form-control" name="deskripsi" rows="5"
                                        placeholder="Jelaskan kondisi dan spesifikasi barang Anda di sini..."></textarea>
                                </div>

                                <div class="d-none d-lg-block mt-4">
                                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                                        <i class="ti ti-shield-check fs-2 me-2"></i>
                                        <div>
                                            Barang Anda akan ditinjau terlebih dahulu sebelum tampil secara publik.
                                        </div>
                                    </div>
                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                        <i class="ti ti-alert-octagon fs-2 me-2"></i>
                                        <div>
                                            Penyalahgunaan fitur lelang akan berakibat pada <strong>Suspend Akun</strong>.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Foto Produk --}}
                            <div class="col-lg-5">
                                <h5 class="mb-3 text-uppercase text-muted small fw-bold">Foto Produk</h5>
                                <hr class="mt-0 mb-4">
                                <p class="text-muted small mb-3">Pilih foto terbaik max 3 gambar size 5mb per foto.</p>

                                <div class="row g-3 mb-4">
                                    @for ($i = 1; $i <= 3; $i++)
                                        <div class="{{ $i == 1 ? 'col-12' : 'col-6' }}">
                                            <div class="image-upload-wrapper position-relative">
                                                <button type="button" class="btn-remove-img shadow" id="remove-{{ $i }}"
                                                    onclick="removeImage({{ $i }})">
                                                    <i class="bi bi-x"></i> {{-- Fallback if ti-x not working, but plan said
                                                    ti-x --}}
                                                    {{-- Let's use Ti Icon --}}
                                                    <i class="ti ti-x" style="font-style: normal;"></i>
                                                </button>

                                                <label class="image-upload-box shadow-sm" id="box-{{ $i }}"
                                                    for="input-{{ $i }}">
                                                    <img id="preview-{{ $i }}" class="preview-img">
                                                    <div class="icon-label" id="label-{{ $i }}">
                                                        <i class="ti ti-photo text-muted"
                                                            style="font-size: {{ $i == 1 ? '3rem' : '2rem' }};"></i>
                                                        <span class="d-block mt-2 fw-bold small text-uppercase">
                                                            {{ $i == 1 ? 'Foto Utama' : 'Optional' }}
                                                        </span>
                                                    </div>
                                                </label>

                                                <input type="file" name="foto[]" id="input-{{ $i }}" class="file-input-hidden"
                                                    accept="image/*" onchange="previewImage(this, {{ $i }})" {{ $i == 1 ? 'required' : '' }}>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg shadow">
                                        DAFTARKAN LELANG
                                    </button>
                                    <a href="{{ route('dashboard') }}"
                                        class="btn btn-link text-muted text-decoration-none text-center">Batal</a>
                                </div>

                                <div class="d-lg-none mt-4">
                                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                                        <i class="ti ti-shield-check fs-2 me-2"></i>
                                        <div>
                                            Barang Anda akan ditinjau terlebih dahulu.
                                        </div>
                                    </div>
                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                        <i class="ti ti-alert-octagon fs-2 me-2"></i>
                                        <div>
                                            Penyalahgunaan fitur lelang = <strong>Suspend</strong>.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(input, id) {
                const preview = document.getElementById('preview-' + id);
                const label = document.getElementById('label-' + id);
                const removeBtn = document.getElementById('remove-' + id);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        label.style.display = 'none';
                        removeBtn.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            function removeImage(id) {
                const input = document.getElementById('input-' + id);
                const preview = document.getElementById('preview-' + id);
                const label = document.getElementById('label-' + id);
                const removeBtn = document.getElementById('remove-' + id);
                input.value = "";
                preview.src = "";
                preview.style.display = 'none';
                label.style.display = 'block';
                removeBtn.style.display = 'none';
            }
        </script>
    @endpush
@endsection