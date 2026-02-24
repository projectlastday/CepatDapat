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
                        <h5 class="mb-0">Manajemen Lelang</h5>
                    </div>

                    {{-- Search Bar --}}
                    <div class="mb-4">
                        <form action="{{ route('admin.manajemen_lelang') }}" method="GET" class="d-flex gap-2">
                            <input type="hidden" name="status" value="{{ $current_filter }}">
                            <input type="text" name="search" class="form-control rounded-pill px-4"
                                placeholder="Cari nama barang..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Cari</button>
                        </form>
                    </div>

                    {{-- Filter Status --}}
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('admin.manajemen_lelang', ['status' => 'all', 'search' => $search]) }}"
                            class="btn btn-sm rounded-pill px-4 fw-bold {{ $current_filter == 'all' ? 'btn-primary shadow' : 'btn-outline-secondary' }}">
                            All
                        </a>

                        @php
                            $statuses = ['pending', 'accepted', 'rejected', 'open', 'sold', 'canceled'];
                        @endphp
                        @foreach ($statuses as $s)
                            <a href="{{ route('admin.manajemen_lelang', ['status' => $s, 'search' => $search]) }}"
                                class="btn btn-sm rounded-pill px-4 fw-bold {{ $current_filter == $s ? 'btn-primary shadow' : 'btn-outline-secondary' }}">
                                {{ ucfirst($s) }}
                            </a>
                        @endforeach
                    </div>

                    <div class="row g-4">
                        @forelse($barang as $item)
                            @php
                                $fotos = array_filter(explode(',', $item->foto));
                                $foto_utama = $fotos[0] ?? null;
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

                                        <p class="text-muted small mb-2">
                                            <i class="ti ti-user me-1"></i> {{ $item->username }}
                                        </p>

                                        <p class="price-tag mb-2">Rp <span
                                                id="card-price-{{ $item->id_lelang }}">{{ number_format($item->harga_awal, 0, ',', '.') }}</span>
                                        </p>

                                        <p class="text-muted small mb-4 flex-grow-1">
                                            {{ Str::limit($item->deskripsi, 50) }}
                                        </p>

                                        <div class="mt-auto">
                                            <button type="button" class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo{{ $item->id_lelang }}">
                                                <i class="ti ti-info-circle me-1"></i> INFO
                                            </button>

                                            @if ($item->status == 'open')
                                                <div class="badge bg-primary w-100 py-2 mb-2 shadow-sm">Sedang Berlangsung
                                                </div>
                                                <div class="text-center">
                                                    <small class="text-danger fw-bold countdown-timer"
                                                        id="timer-{{ $item->id_lelang }}"
                                                        data-target="{{ $item->tgl_status_sold }}" data-style="short"
                                                        style="font-size: 0.85rem;">
                                                        Memuat Countdown...
                                                    </small>
                                                </div>
                                            @else
                                                <div class="badge bg-light text-dark border w-100 py-2 fw-bold">
                                                    {{ ucfirst($item->status) }}</div>
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
                                            <h5 class="fw-bold mb-0">Detail Lelang</h5>
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
                                                            <td class="fw-bold text-success">Rp {{ number_format($item->harga_terjual, 0, ',', '.') }}</td>
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
                                                            <th>Status Saat Ini</th>
                                                            <td><span class="badge bg-secondary">{{ ucfirst($item->status) }}</span></td>
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

                                            {{-- Cancel Logic: Only if status is OPEN or ACCEPTED AND User Type is 1, 6, or 7 --}}
                                            @if (in_array($item->status, ['open', 'accepted']) && in_array(session('id_user_type'), [1, 6, 7]))
                                                <hr>
                                                <div class="d-grid">
                                                    <button class="btn btn-danger fw-bold" type="button" 
                                                        onclick="openCancelModal({{ $item->id_lelang }}, '{{ $item->nama_barang }}')">
                                                        <i class="ti ti-x me-1"></i> Cancel Lelang Ini
                                                    </button>
                                                </div>
                                            @endif

                                            {{-- Delete Logic: Only for Admin (1) and Super Admin (7) --}}
                                            @if (in_array(session('id_user_type'), [1, 7]))
                                                <hr>
                                                <div class="d-grid">
                                                    <button class="btn btn-dark fw-bold" type="button" 
                                                        onclick="openDeleteModal({{ $item->id_lelang }}, '{{ $item->nama_barang }}')">
                                                        <i class="ti ti-trash me-1"></i> Hapus Lelang Ini
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="py-5">
                                    <i class="ti ti-box display-1 text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h5 class="mt-3 text-muted">Tidak ada barang dengan status "{{ ucfirst($current_filter) }}"
                                    </h5>
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
                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1056; filter: invert(1); opacity: 1;"></button>
                            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL CANCEL AUCTION --}}
            <div class="modal fade" id="modalCancelAuction" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold mb-0 text-danger">Konfirmasi Pembatalan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.cancel_auction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_lelang" id="cancelIdLelang">
                            <div class="modal-body p-4">
                                <div class="alert alert-danger border-0 rounded-3 mb-3 d-flex align-items-center" role="alert">
                                    <i class="ti ti-alert-circle fs-4 me-2"></i>
                                    <div>
                                        Anda akan membatalkan lelang: <br>
                                        <strong id="cancelNamaBarang"></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                                    <textarea name="alasan" class="form-control" rows="4" required placeholder="Jelaskan alasan pembatalan lelang ini secara rinci..."></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger fw-bold py-2">Ya, Batalkan Lelang</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MODAL DELETE AUCTION --}}
            <div class="modal fade" id="modalDeleteAuction" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold mb-0 text-dark">Konfirmasi Penghapusan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.delete_auction') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_lelang" id="deleteIdLelang">
                            <div class="modal-body p-4">
                                <div class="alert alert-dark border-0 rounded-3 mb-3 d-flex align-items-center" role="alert">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        Anda akan <strong>menghapus</strong> lelang: <br>
                                        <strong id="deleteNamaBarang"></strong>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alasan Penghapusan <span class="text-danger">*</span></label>
                                    <textarea name="alasan" class="form-control" rows="4" required placeholder="Jelaskan alasan penghapusan lelang ini secara rinci..."></textarea>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark fw-bold py-2">Ya, Hapus Lelang</button>
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

                    function openCancelModal(id, namaBarang) {
                        // Close existing modal if open (optional, but good practice since button is inside info modal)
                        // bootstrap.Modal.getInstance(document.getElementById('modalInfo' + id)).hide(); // Uncomment if you want to close info modal first
                        
                        document.getElementById('cancelIdLelang').value = id;
                        document.getElementById('cancelNamaBarang').innerText = namaBarang;
                        
                        const modal = new bootstrap.Modal(document.getElementById('modalCancelAuction'));
                        modal.show();
                    }

                    function openDeleteModal(id, namaBarang) {
                        document.getElementById('deleteIdLelang').value = id;
                        document.getElementById('deleteNamaBarang').innerText = namaBarang;
                        
                        const modal = new bootstrap.Modal(document.getElementById('modalDeleteAuction'));
                        modal.show();
                    }

                    // SCRIPT COUNTDOWN (Berjalan lokal di halaman ini agar presisi per detik)
                    function updateCountdowns() {
                        const timers = document.querySelectorAll('.countdown-timer');
                        timers.forEach(timer => {
                            const targetTimestamp = timer.getAttribute('data-target');
                            if (!targetTimestamp) return;

                            const targetDate = new Date(targetTimestamp).getTime();
                            const now = new Date().getTime();
                            const distance = targetDate - now;

                            if (distance < 0) {
                                timer.innerHTML = "Lelang Berakhir";
                                return;
                            }

                            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                            let display = "";
                            if (timer.dataset.style === 'short') {
                                if (days > 0) display += days + "h ";
                                display += hours + "j " + minutes + "m " + seconds + "d";
                            } else {
                                if (days > 0) display += days + " Hari ";
                                display += hours + " Jam " + minutes + " Menit " + seconds + " Detik";
                            }
                            timer.innerHTML = display;
                        });
                    }
                    setInterval(updateCountdowns, 1000);
                    document.addEventListener('DOMContentLoaded', updateCountdowns);
                </script>
            @endpush
@endsection
