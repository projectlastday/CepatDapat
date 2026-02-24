@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-history me-2"></i>History Data</h5>
                </div>
                <div class="card-body">

                    {{-- ===== NAV TABS ===== --}}
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        @php
                            $tabs = [
                                'login' => 'Login',
                                'activity' => 'Activity User',
                                'cancel' => 'Cancel Lelang',
                                'delete' => 'Delete Lelang',
                                'uncancel' => 'Uncancel Lelang',
                                'restore' => 'Restore Lelang',
                                'setting' => 'Setting Website',
                            ];
                        @endphp
                        @foreach ($tabs as $key => $label)
                            <li class="nav-item">
                                <a class="nav-link {{ $tab === $key ? 'active' : '' }}"
                                    href="{{ route('history.index', ['tab' => $key]) }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- ===== FILTER FORM ===== --}}
                    <form method="GET" action="{{ route('history.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Cari Username</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Username..." value="{{ $search }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control form-control-sm"
                                    value="{{ $start_date }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control form-control-sm"
                                    value="{{ $end_date }}">
                            </div>
                            @if (in_array($tab, $lelangTabs))
                                <div class="col-md-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_lelang" class="form-control form-control-sm"
                                        placeholder="Nama barang..." value="{{ $nama_lelang }}">
                                </div>
                            @endif
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('history.index', ['tab' => $tab]) }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-x"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- ===== TABLE ===== --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:40px">No</th>

                                    @if ($tab === 'login')
                                        <th>Username</th>
                                        <th>IP Address</th>
                                        <th>Device</th>
                                        <th style="width:150px">Waktu</th>
                                    @elseif ($tab === 'activity')
                                        <th>Username</th>
                                        <th>URL</th>
                                        <th style="width:150px">Waktu</th>
                                    @elseif (in_array($tab, $lelangTabs))
                                        <th>Pelaku</th>
                                        <th>Nama Barang</th>
                                        <th>Alasan</th>
                                        <th style="width:150px">Waktu</th>
                                        <th style="width:130px">Status Saat Ini</th>
                                    @elseif ($tab === 'setting')
                                        <th>Pelaku</th>
                                        <th>Type</th>
                                        <th>Data Lama</th>
                                        <th>Data Baru</th>
                                        <th style="width:150px">Waktu</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $i => $row)
                                    <tr>
                                        <td>{{ $data->firstItem() + $i }}</td>

                                        @if ($tab === 'login')
                                            <td>{{ $row->username }}</td>
                                            <td>{{ $row->ip_address ?? '-' }}</td>
                                            <td>{{ $row->user_agent ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>

                                        @elseif ($tab === 'activity')
                                            <td>{{ $row->username }}</td>
                                            <td title="{{ $row->url }}">
                                                {{ \Illuminate\Support\Str::limit($row->url, 80) }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>

                                        @elseif (in_array($tab, $lelangTabs))
                                            <td>{{ $row->pelaku }}</td>
                                            <td>{{ $row->nama_barang ?? '-' }}</td>
                                            <td title="{{ $row->alasan }}">
                                                {{ \Illuminate\Support\Str::limit($row->alasan, 60) }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if ($row->nama_barang === null)
                                                    <span class="badge bg-secondary">Tidak Ditemukan</span>
                                                @elseif ($row->status_delete == 1)
                                                    <span class="badge bg-dark">Deleted</span>
                                                @else
                                                    @php
                                                        $badgeMap = [
                                                            'pending' => 'bg-warning',
                                                            'accepted' => 'bg-info',
                                                            'rejected' => 'bg-secondary',
                                                            'open' => 'bg-primary',
                                                            'sold' => 'bg-success',
                                                            'unsold' => 'bg-dark',
                                                            'canceled' => 'bg-danger',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $badgeMap[$row->current_status] ?? 'bg-secondary' }}">
                                                        {{ ucfirst($row->current_status) }}
                                                    </span>
                                                @endif
                                            </td>

                                        @elseif ($tab === 'setting')
                                            <td>{{ $row->pelaku }}</td>
                                            <td>{{ $row->type }}</td>
                                            <td title="{{ $row->data_lama }}">
                                                {{ \Illuminate\Support\Str::limit($row->data_lama, 50) ?: '-' }}
                                            </td>
                                            <td title="{{ $row->data_baru }}">
                                                {{ \Illuminate\Support\Str::limit($row->data_baru, 50) ?: '-' }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="ti ti-database-off me-1"></i>Tidak ada data untuk filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== PAGINATION ===== --}}
                    @if ($data->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection