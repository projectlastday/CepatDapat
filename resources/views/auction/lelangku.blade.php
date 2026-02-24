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
                <h5 class="mb-0">Lelangku</h5>
            </div>

            {{-- Filter Status --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="{{ route('auction.lelangku', ['status' => 'all']) }}"
                    class="btn btn-sm rounded-pill px-4 fw-bold {{ $current_filter == 'all' ? 'btn-primary shadow' : 'btn-outline-secondary' }}">
                    All
                </a>

                @php
                    $statuses = ['pending', 'accepted', 'rejected', 'open', 'sold', 'unsold', 'canceled'];
                @endphp
                @foreach($statuses as $s)
                    <a href="{{ route('auction.lelangku', ['status' => $s]) }}"
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

                                // Normalize phone for wa.me link
                                $rawPhone = preg_replace('/[^0-9]/', '', $item->buyer_telepon ?? '');
                                if ($rawPhone && str_starts_with($rawPhone, '0')) {
                                    $rawPhone = '62' . substr($rawPhone, 1);
                                }
                                $waLink = $rawPhone ? 'https://wa.me/' . $rawPhone : null;
                            @endphp
                            <div class="col-sm-6 col-md-4 col-xl-3">
                                <div class="card auction-card shadow-sm h-100 position-relative">
                                    <div class="card-img-container">
                                        @if($foto_utama)
                                            <img src="{{ asset('img_barang/' . $foto_utama) }}" alt="{{ $item->nama_barang }}"
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-photo text-muted fs-1" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold text-dark text-truncate mb-1">{{ $item->nama_barang }}</h5>

                                        <p class="price-tag mb-4">Rp <span
                                                id="card-price-{{ $item->id_lelang }}">{{ number_format($item->harga_awal, 0, ',', '.') }}</span>
                                        </p>

                                        <div class="mt-auto">
                                            @if($item->status == 'sold')
                                                <p class="text-muted small mb-2 text-center">
                                                    Pemenang: <strong>{{ $item->buyer_username ?? 'Tidak diketahui' }}</strong>
                                                </p>
                                                <div class="badge bg-success w-100 py-2 mb-2 shadow-sm fs-6">Sold</div>
                                            @elseif($item->status == 'rejected')
                                                <div class="alert alert-danger p-2 mb-2 small text-center rounded-3 fw-bold">Ditolak
                                                    Moderator</div>
                                            @elseif($item->status == 'open')
                                                <div class="badge bg-primary w-100 py-2 mb-2 shadow-sm">Sedang Berlangsung</div>
                                            @elseif($item->status != 'accepted')
                                                <div class="badge bg-light text-dark border w-100 py-2 fw-bold mb-2">
                                                    {{ ucfirst($item->status) }}</div>
                                            @endif

                                            {{-- Info Button --}}
                                            <button type="button" class="btn btn-info w-100 py-2 mb-2 shadow-sm fw-bold text-white d-flex justify-content-center align-items-center gap-2"
                                                data-bs-toggle="modal" data-bs-target="#modalInfo{{ $item->id_lelang }}">
                                                <i class="ti ti-info-circle fs-5"></i>
                                                <span>INFO</span>
                                            </button>

                                            @if($item->status == 'accepted')
                                                <button type="button" class="btn btn-success w-100 py-2 shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#modalMulai{{ $item->id_lelang }}">
                                                    Mulai Lelang
                                                </button>
                                            @elseif($item->status == 'open')
                                                <div class="text-center">
                                                    <small class="text-danger fw-bold countdown-timer"
                                                        id="timer-{{ $item->id_lelang }}" data-target="{{ $item->tgl_status_sold }}" data-style="short"
                                                        style="font-size: 0.85rem;">
                                                        Memuat Countdown...
                                                    </small>
                                                </div>
                                            @elseif($item->status == 'sold')
                                                @if ($waLink)
                                                    <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                                        class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                                        <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penawar
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary w-100 py-2 fw-bold" disabled>
                                                        <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Penawar
                                                    </button>
                                                    <small class="text-muted d-block text-center mt-1">Nomor WA penawar tidak tersedia</small>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL MULAI LELANG --}}
                                @if($item->status == 'accepted')
                                    <div class="modal fade" id="modalMulai{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="fw-bold mb-0">Atur Durasi Lelang</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('auction.start') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id_lelang" value="{{ $item->id_lelang }}">
                                                    <div class="modal-body text-center p-4">
                                                        <div class="alert alert-info border-0 rounded-4 mb-4"
                                                            style="background-color: #e7f3ff;">
                                                            <i class="ti ti-info-circle text-primary mb-2 d-block fs-4"></i>
                                                            <p class="small text-dark mb-0">
                                                                <strong>Informasi:</strong> Username Anda akan ditampilkan
                                                                sebagai identitas pelelang pada halaman Katalog setelah lelang
                                                                dimulai.
                                                            </p>
                                                        </div>

                                                        <p class="text-muted mb-4">Pilih berapa hari lelang ini akan berlangsung:
                                                        </p>
                                                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                            @for($i = 1; $i <= 7; $i++)
                                                                <input type="radio" class="btn-check" name="durasi"
                                                                    id="day{{ $item->id_lelang }}{{ $i }}" value="{{ $i }}" {{ $i == 7 ? 'checked' : '' }}>
                                                                <label
                                                                    class="btn btn-outline-success btn-lg rounded-4 px-4 py-3 fw-bold"
                                                                    for="day{{ $item->id_lelang }}{{ $i }}">
                                                                    {{ $i }} <br> <span class="small"
                                                                        style="font-size: 0.6em;">Hari</span>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                        <div class="mt-4">
                                                            <p class="text-muted small mb-0">Lelang akan berakhir pukul
                                                                <strong>24:00</strong> di hari terakhir.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="submit"
                                                            class="btn btn-success btn-lg w-100 fw-bold rounded-4 shadow">Mulai
                                                            Lelang</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- MODAL INFO --}}
                                <div class="modal fade" id="modalInfo{{ $item->id_lelang }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mb-0">Detail Barang Lelang</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                            @if($item->status == 'sold')
                                                                <tr>
                                                                    <th>Harga Terjual</th>
                                                                    <td class="fw-bold text-success">Rp {{ number_format($item->harga_terjual ?? 0, 0, ',', '.') }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Pemenang</th>
                                                                    <td>{{ $item->buyer_username ?? '-' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Telepon Pemenang</th>
                                                                    <td>{{ $item->buyer_telepon ?? '-' }}</td>
                                                                </tr>
                                                            @endif
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
                                                                <th>Tanggal Berakhir</th>
                                                                <td>{{ $item->tgl_status_sold ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Status</th>
                                                                <td>
                                                                    <div class="badge bg-secondary">{{ ucfirst($item->status) }}</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- WhatsApp Button inside modal --}}
                                                @if ($item->status == 'sold' && $waLink)
                                                    <div class="d-grid mt-2">
                                                        <a href="{{ $waLink }}" target="_blank" rel="noopener"
                                                            class="btn btn-success fw-bold py-2">
                                                            <i class="ti ti-brand-whatsapp me-1"></i> Hubungi Pemenang via WhatsApp
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
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

    @push('scripts')
        <script>
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