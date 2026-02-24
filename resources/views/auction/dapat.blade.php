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
            </style>

            <div class="row">
                <div class="col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0"><i class="ti ti-trophy me-2"></i>Dapat / Barang Menang</h5>
                    </div>

                    {{-- WhatsApp Transaction Alert --}}
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-start shadow-sm mb-4" role="alert">
                        <i class="ti ti-brand-whatsapp fs-4 me-2 mt-1"></i>
                        <div>
                            <strong>Info Penyelesaian Transaksi</strong><br>
                            Lakukan penyelesaian transaksi di WhatsApp dengan menghubungi pemilik lelang.
                            Klik tombol <strong>"Hubungi Penjual"</strong> pada setiap barang yang Anda menangkan.
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($barang as $item)
                            @php
                                $fotos = array_filter(explode(',', $item->foto));
                                $foto_utama = $fotos[0] ?? null;

                                // Normalize phone for wa.me link
                                $rawPhone = preg_replace('/[^0-9]/', '', $item->owner_telepon ?? '');
                                if ($rawPhone && str_starts_with($rawPhone, '0')) {
                                    $rawPhone = '62' . substr($rawPhone, 1);
                                }
                                $waLink = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
                            @endphp
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card auction-card shadow-sm h-100 position-relative">
                                    <div class="card-img-container">
                                        @if ($foto_utama)
                                            <img src="{{ asset('img_barang/' . $foto_utama) }}"
                                                alt="{{ $item->nama_barang }}"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-photo text-muted fs-1" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold text-dark text-truncate mb-1">{{ $item->nama_barang }}</h5>

                                        <p class="price-tag mb-3 mt-2">Rp {{ number_format($item->winning_bid, 0, ',', '.') }}</p>

                                        <div class="mt-auto">
                                            {{-- Info Button --}}
                                            <button type="button" class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white d-flex justify-content-center align-items-center gap-2"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo{{ $item->id_lelang }}">
                                                <i class="ti ti-info-circle fs-5"></i>
                                                <span>INFO</span>
                                            </button>

                                            {{-- WhatsApp Button --}}
                                            @if ($waLink)
                                                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                                    class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                                    <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penjual
                                                </a>
                                            @else
                                                <button class="btn btn-secondary w-100 py-2 fw-bold" disabled>
                                                    <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penjual
                                                </button>
                                                <small class="text-muted d-block text-center mt-1">Nomor WA penjual tidak tersedia</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL INFO --}}
                            <div class="modal fade" id="modalInfo{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="fw-bold mb-0">Detail Barang Menang</h5>
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
                                                        <tr>
                                                            <th>Harga Menang</th>
                                                            <td class="fw-bold text-success">Rp {{ number_format($item->winning_bid, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Penjual</th>
                                                            <td>{{ $item->owner_username }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Telepon Penjual</th>
                                                            <td>{{ $item->owner_telepon ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Foto</th>
                                                            <td>
                                                                @php
                                                                    $modalFotos = array_filter(explode(',', $item->foto));
                                                                @endphp
                                                                @if(count($modalFotos) > 0)
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach($modalFotos as $f)
                                                                            <a href="javascript:void(0)" onclick="viewImage('{{ asset('img_barang/' . trim($f)) }}')">
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
                                                            <th>Tanggal Terjual</th>
                                                            <td>{{ $item->tgl_status_sold ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- WhatsApp Button inside modal --}}
                                            @if ($waLink)
                                                <div class="d-grid mt-2">
                                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                                        class="btn btn-success fw-bold py-2">
                                                        <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penjual via WhatsApp
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="py-5">
                                    <i class="ti ti-trophy display-1 text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Belum ada barang yang Anda menangkan.</h5>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if ($barang->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $barang->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- MODAL PREVIEW IMAGE (LIGHTBOX) --}}
            <div class="modal fade" id="modalViewImage" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-transparent border-0 shadow-none">
                        <div class="modal-body p-0 text-center position-relative">
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1056; filter: invert(1); opacity: 1;"></button>
                            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                        </div>
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
                </script>
            @endpush
    </div>
</div>
@endsection
