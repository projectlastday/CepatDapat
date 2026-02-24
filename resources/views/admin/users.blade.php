@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-users me-2"></i>Data User</h5>
                </div>
                <div class="card-body">

                    {{-- ===== SEARCH FORM ===== --}}
                    <form method="GET" action="{{ route('admin.users') }}" class="mb-4">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Cari User</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Username, email, atau telepon..." value="{{ $search }}">
                            </div>
                            <div class="col-md-3 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-search me-1"></i>Cari
                                </button>
                                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">
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
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Email Verified</th>
                                    <th>Telepon</th>
                                    <th>Telepon Verified</th>
                                    <th>User Type</th>
                                    <th>Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $i => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $i }}</td>
                                        <td class="fw-bold">{{ $user->username }}</td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>
                                            @if ($user->email_verified_at)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-secondary">Belum</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->telepon ?? '-' }}</td>
                                        <td>
                                            @if ($user->telepon_verified_at)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-secondary">Belum</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $typeMap = [
                                                    1 => ['label' => 'Admin', 'badge' => 'bg-primary'],
                                                    2 => ['label' => 'Moderator', 'badge' => 'bg-info'],
                                                    3 => ['label' => 'Member', 'badge' => 'bg-success'],
                                                    4 => ['label' => 'Suspended', 'badge' => 'bg-danger'],
                                                    5 => ['label' => 'Manager', 'badge' => 'bg-warning'],
                                                    6 => ['label' => 'Super Moderator', 'badge' => 'bg-secondary'],
                                                    7 => ['label' => 'Super Admin', 'badge' => 'bg-dark'],
                                                ];
                                                $type = $typeMap[$user->id_user_type] ?? ['label' => 'Unknown', 'badge' => 'bg-secondary'];
                                            @endphp
                                            <span class="badge {{ $type['badge'] }}">{{ $type['label'] }}</span>
                                        </td>
                                        <td>{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="ti ti-database-off me-1"></i>Tidak ada data user.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ===== PAGINATION ===== --}}
                    @if ($users->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
