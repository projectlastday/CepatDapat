@extends('layouts.app')

@section('content')
    {{-- ===== FILTER SECTION ===== --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-file-analytics me-2"></i>Laporan Lelang</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
                        <div class="row g-3 align-items-end">
                            {{-- Date Range --}}
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="{{ $filters['start_date'] }}" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="{{ $filters['end_date'] }}" />
                            </div>

                            {{-- Status Multi-select Dropdown --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Lelang</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                        type="button" id="statusDropdown" data-bs-toggle="dropdown"
                                        aria-expanded="false" data-bs-auto-close="outside">
                                        <span id="statusLabel">{{ count($filters['statuses']) == count($allStatuses) ? 'Semua Status' : count($filters['statuses']) . ' status dipilih' }}</span>
                                    </button>
                                    <ul class="dropdown-menu w-100 p-2" aria-labelledby="statusDropdown">
                                        <li class="mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll"
                                                    {{ count($filters['statuses']) == count($allStatuses) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="selectAll">Pilih
                                                    Semua</label>
                                            </div>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        @foreach ($allStatuses as $status)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input status-checkbox" type="checkbox"
                                                        name="statuses[]" value="{{ $status }}"
                                                        id="status_{{ $status }}"
                                                        {{ in_array($status, $filters['statuses']) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="status_{{ $status }}">{{ ucfirst($status) }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Export Buttons --}}
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" id="exportPdfBtn" class="btn btn-danger btn-sm">
                            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                        </a>
                        <a href="#" id="exportExcelBtn" class="btn btn-success btn-sm">
                            <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== CHARTS SECTION ===== --}}
    <div class="row">
        {{-- Chart 1: Status Pie --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Sebaran Status Lelang</h6>
                    <div id="statusPieChart"></div>
                </div>
            </div>
        </div>

        {{-- Chart 2: Revenue Line --}}
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pendapatan Harian (Lelang Terjual)</h6>
                    <div id="revenueLineChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Chart 3: Top Bidders Bar --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Top 10 Penawar Aktif</h6>
                    <div id="biddersBarChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABLE SECTION ===== --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Detail Lelang</h6>
                    @if ($tableData->isEmpty())
                        <div class="alert alert-info mb-0">Tidak ada data untuk filter yang dipilih.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Barang</th>
                                        <th>Pemilik</th>
                                        <th>Pemenang</th>
                                        <th>Harga Akhir</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tableData as $i => $row)
                                        <tr>
                                            <td>{{ $tableData->firstItem() + $i }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->tgl_input)->format('d/m/Y') }}</td>
                                            <td>{{ $row->nama_barang }}</td>
                                            <td>{{ $row->pemilik }}</td>
                                            <td>{{ $row->pemenang ?? '-' }}</td>
                                            <td>Rp {{ number_format($row->harga_akhir ?? $row->harga_awal, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $badgeMap = [
                                                        'pending'  => 'bg-warning',
                                                        'accepted' => 'bg-info',
                                                        'rejected' => 'bg-secondary',
                                                        'open'     => 'bg-primary',
                                                        'sold'     => 'bg-success',
                                                        'unsold'   => 'bg-dark',
                                                        'canceled' => 'bg-danger',
                                                    ];
                                                @endphp
                                                <span class="badge {{ $badgeMap[$row->status] ?? 'bg-secondary' }}">
                                                    {{ ucfirst($row->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $tableData->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===== Status color mapping =====
            const statusColors = {
                'pending': '#ffc107',
                'accepted': '#0dcaf0',
                'rejected': '#6c757d',
                'open': '#0d6efd',
                'sold': '#198754',
                'unsold': '#212529',
                'canceled': '#dc3545',
            };

            // ===== CHART 1: Status Pie =====
            const statusData = @json($statusChartData);
            const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
            const statusValues = Object.values(statusData);
            const statusKeys = Object.keys(statusData);
            const pieColors = statusKeys.map(k => statusColors[k] || '#adb5bd');

            if (statusValues.some(v => v > 0)) {
                new ApexCharts(document.querySelector('#statusPieChart'), {
                    chart: { type: 'pie', height: 320 },
                    labels: statusLabels,
                    series: statusValues,
                    colors: pieColors,
                    legend: { position: 'bottom', fontSize: '13px' },
                    tooltip: { theme: 'dark' },
                    responsive: [{ breakpoint: 480, options: { chart: { width: 280 }, legend: { position: 'bottom' } } }]
                }).render();
            } else {
                document.querySelector('#statusPieChart').innerHTML =
                    '<div class="text-center text-muted py-5">Tidak ada data</div>';
            }

            // ===== CHART 2: Revenue Line =====
            const revenueDates = @json($revenueDates);
            const revenueValues = @json($revenueValues);

            new ApexCharts(document.querySelector('#revenueLineChart'), {
                chart: { type: 'area', height: 320, toolbar: { show: false } },
                series: [{ name: 'Pendapatan', data: revenueValues }],
                xaxis: { categories: revenueDates, labels: { rotate: -45, rotateAlways: revenueDates.length > 15 } },
                yaxis: {
                    labels: {
                        formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                    }
                },
                colors: ['#673ab7'],
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] }
                },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) }
                },
                grid: { strokeDashArray: 4 },
            }).render();

            // ===== CHART 3: Top Bidders Bar =====
            const bidderNames = @json($bidderNames);
            const bidderCounts = @json($bidderCounts);

            if (bidderNames.length > 0) {
                new ApexCharts(document.querySelector('#biddersBarChart'), {
                    chart: { type: 'bar', height: 320, toolbar: { show: false } },
                    series: [{ name: 'Total Penawaran', data: bidderCounts }],
                    xaxis: { categories: bidderNames },
                    colors: ['#ff6f00'],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                    dataLabels: { enabled: true },
                    tooltip: { theme: 'dark' },
                    grid: { strokeDashArray: 4 },
                }).render();
            } else {
                document.querySelector('#biddersBarChart').innerHTML =
                    '<div class="text-center text-muted py-5">Tidak ada data penawaran</div>';
            }

            // ===== STATUS DROPDOWN: Select All / Individual logic =====
            const selectAllCb = document.getElementById('selectAll');
            const statusCbs = document.querySelectorAll('.status-checkbox');
            const statusLabel = document.getElementById('statusLabel');

            function updateLabel() {
                const checked = document.querySelectorAll('.status-checkbox:checked').length;
                const total = statusCbs.length;
                statusLabel.textContent = checked === total ? 'Semua Status' : checked + ' status dipilih';
                selectAllCb.checked = checked === total;
                selectAllCb.indeterminate = checked > 0 && checked < total;
            }

            selectAllCb.addEventListener('change', function() {
                statusCbs.forEach(cb => cb.checked = this.checked);
                updateLabel();
            });

            statusCbs.forEach(cb => cb.addEventListener('change', updateLabel));

            // ===== EXPORT BUTTONS: Carry current filters =====
            function buildExportUrl(base) {
                const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
                return base + '?' + params.toString();
            }

            document.getElementById('exportPdfBtn').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildExportUrl('{{ route("laporan.export.pdf") }}');
            });

            document.getElementById('exportExcelBtn').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildExportUrl('{{ route("laporan.export.excel") }}');
            });
        });
    </script>
@endpush
