@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <style>
                .auction-card {
                    border: none;
                    border-radius: 16px;
                    transition: transform 0.3s ease;
                    background-color: #ffffff;
                    position: relative;
                }

                .auction-card:hover {
                    transform: translateY(-8px);
                    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
                }

                .card-img-container {
                    height: 200px;
                    border-radius: 16px 16px 0 0;
                    overflow: hidden;
                    background-color: #f8f9fa;
                }

                .price-tag {
                    color: #dc3545;
                    font-size: 1.1rem;
                    font-weight: 800;
                }

                .deleted-badge {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    z-index: 2;
                }
            </style>

            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Lelang Terhapus</h5>
                    </div>

                    {{-- Search Bar --}}
                    <div class="mb-4">
                        <form action="{{ route('admin.deleted_auctions') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control rounded-pill px-4"
                                placeholder="Cari nama barang..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                        </form>
                    </div>

                    <div class="row g-4">
                        @forelse($barang as $item)
                            @php
                                $fotos = array_filter(explode(',', $item->foto));
                                $foto_utama = $fotos[0] ?? null;
                            @endphp
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card auction-card shadow-sm h-100 position-relative">
                                    <span class="badge bg-dark deleted-badge"><i class="ti ti-trash me-1"></i>Terhapus</span>
                                    <div class="card-img-container">
                                        @if ($foto_utama)
                                            <img src="{{ asset('img_barang/' . $foto_utama) }}" alt="{{ $item->nama_barang }}"
                                                style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-photo text-muted fs-1" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold text-dark text-truncate mb-1">{{ $item->nama_barang }}</h5>

                                        <p class="text-muted small mb-2">
                                            <i class="ti ti-user me-1"></i> {{ $item->username }}
                                        </p>

                                        <p class="price-tag mb-2">Rp
                                            <span>{{ number_format($item->harga_awal, 0, ',', '.') }}</span></p>

                                        <p class="text-muted small mb-4 flex-grow-1">
                                            {{ Str::limit($item->deskripsi, 50) }}
                                        </p>

                                        <div class="mt-auto">
                                            <button type="button"
                                                class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo{{ $item->id_lelang }}">
                                                <i class="ti ti-info-circle me-1"></i> INFO
                                            </button>

                                            <div class="badge bg-dark w-100 py-2 fw-bold">
                                                {{ ucfirst($item->status) }} (Terhapus)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL INFO --}}
                            <div class="modal fade" id="modalInfo{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold mb-0">Detail Lelang (Terhapus)</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <th width="35%">ID Lelang</th>
                                                            <td>#{{ $item->id_lelang }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Pemilik (ID {{ $item->id_user }})</th>
                                                            <td>{{ $item->username }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Moderator (ID {{ $item->moderator_id ?? '-' }})</th>
                                                            <td>{{ $item->moderator_username ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Penawar Tertinggi</th>
                                                            <td>{{ $item->buyer_username ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Nama Barang</th>
                                                            <td>{{ $item->nama_barang }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Deskripsi</th>
                                                            <td>{{ $item->deskripsi }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Harga Awal</th>
                                                            <td>Rp {{ number_format($item->harga_awal, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @if($item->harga_terjual)
                                                            <tr>
                                                                <th>Harga Terjual</th>
                                                                <td class="fw-bold text-success">Rp
                                                                    {{ number_format($item->harga_terjual, 0, ',', '.') }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr>
                                                            <th>Foto</th>
                                                            <td>
                                                                @php
                                                                    $fotos = array_filter(explode(',', $item->foto));
                                                                @endphp
                                                                @if(count($fotos) > 0)
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach($fotos as $f)
                                                                            <a href="javascript:void(0)"
                                                                                onclick="viewImage('{{ asset('img_barang/' . trim($f)) }}')">
                                                                                <img src="{{ asset('img_barang/' . trim($f)) }}"
                                                                                    class="img-thumbnail"
                                                                                    style="height: 80px; width: 80px; object-fit: cover;"
                                                                                    alt="Foto Barang">
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Status Terakhir</th>
                                                            <td><span
                                                                    class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Input</th>
                                                            <td>{{ $item->tgl_input }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Diterima (Accepted)</th>
                                                            <td>{{ $item->tgl_status_accepted ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Ditolak (Rejected)</th>
                                                            <td>{{ $item->tgl_status_rejected ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Mulai (Open)</th>
                                                            <td>{{ $item->tgl_status_open ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Tanggal Dibatalkan (Canceled)</th>
                                                            <td>{{ $item->tgl_status_canceled ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Batas Akhir (Sold)</th>
                                                            <td>{{ $item->tgl_status_sold ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- Restore Button --}}
                                            <hr>
                                            <div class="d-grid">
                                                <button class="btn btn-success fw-bold" type="button"
                                                    onclick="openRestoreModal({{ $item->id_lelang }}, '{{ $item->nama_barang }}')">
                                                    <i class="ti ti-restore me-1"></i> Restore Lelang Ini
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="py-5">
                                    <i class="ti ti-box display-1 text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada lelang yang terhapus</h5>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- MODAL PREVIEW IMAGE (LIGHTBOX) --}}
            <div class="modal fade" id="modalViewImage" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-transparent border-0 shadow-none">
                        <div class="modal-body p-0 text-center position-relative">
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                                data-bs-dismiss="modal" aria-label="Close"
                                style="z-index: 1056; filter: invert(1); opacity: 1;"></button>
                            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow-lg"
                                style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL RESTORE AUCTION --}}
            <div class="modal fade" id="modalRestoreAuction" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold mb-0 text-success">Konfirmasi Restore</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.restore_auction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_lelang" id="restoreIdLelang">
                            <div class="modal-body p-4">
                                <div class="alert alert-success border-0 rounded-3 mb-3 d-flex align-items-center"
                                    role="alert">
                                    <i class="ti ti-restore fs-4 me-2"></i>
                                    <div>
                                        Anda akan <strong>merestore</strong> lelang: <br>
                                        <strong id="restoreNamaBarang"></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Restore <span
                                            class="text-danger">*</span></label>
                                    <textarea name="alasan" class="form-control" rows="4" required
                                        placeholder="Jelaskan alasan restore lelang ini secara rinci..."></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success fw-bold py-2">Ya, Restore Lelang</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @push('scripts')
                <script>
                    function viewImage(url) {
                        const modal = new bootstrap.Modal(document.getElementById('modalViewImage'));
                        document.getElementById('previewImage').src = url;
                        modal.show();
                    }

                    function openRestoreModal(id, namaBarang) {
                        document.getElementById('restoreIdLelang').value = id;
                        document.getElementById('restoreNamaBarang').innerText = namaBarang;

                        const modal = new bootstrap.Modal(document.getElementById('modalRestoreAuction'));
                        modal.show();
                    }
                </script>
            @endpush
@endsection