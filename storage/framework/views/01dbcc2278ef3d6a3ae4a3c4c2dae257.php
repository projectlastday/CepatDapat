

<?php $__env->startSection('content'); ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-file-analytics me-2"></i>Laporan Lelang</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('laporan.index')); ?>" id="filterForm">
                        <div class="row g-3 align-items-end">
                            
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control"
                                    value="<?php echo e($filters['start_date']); ?>" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control"
                                    value="<?php echo e($filters['end_date']); ?>" />
                            </div>

                            
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status Lelang</label>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                        type="button" id="statusDropdown" data-bs-toggle="dropdown"
                                        aria-expanded="false" data-bs-auto-close="outside">
                                        <span id="statusLabel"><?php echo e(count($filters['statuses']) == count($allStatuses) ? 'Semua Status' : count($filters['statuses']) . ' status dipilih'); ?></span>
                                    </button>
                                    <ul class="dropdown-menu w-100 p-2" aria-labelledby="statusDropdown">
                                        <li class="mb-1">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll"
                                                    <?php echo e(count($filters['statuses']) == count($allStatuses) ? 'checked' : ''); ?>>
                                                <label class="form-check-label fw-bold" for="selectAll">Pilih
                                                    Semua</label>
                                            </div>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <?php $__currentLoopData = $allStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input status-checkbox" type="checkbox"
                                                        name="statuses[]" value="<?php echo e($status); ?>"
                                                        id="status_<?php echo e($status); ?>"
                                                        <?php echo e(in_array($status, $filters['statuses']) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label"
                                                        for="status_<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></label>
                                                </div>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>

                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ti ti-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    
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

    
    <div class="row">
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Sebaran Status Lelang</h6>
                    <div id="statusPieChart"></div>
                </div>
            </div>
        </div>

        
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
        
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Top 10 Penawar Aktif</h6>
                    <div id="biddersBarChart"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Detail Lelang</h6>
                    <?php if($tableData->isEmpty()): ?>
                        <div class="alert alert-info mb-0">Tidak ada data untuk filter yang dipilih.</div>
                    <?php else: ?>
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
                                    <?php $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($tableData->firstItem() + $i); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($row->tgl_input)->format('d/m/Y')); ?></td>
                                            <td><?php echo e($row->nama_barang); ?></td>
                                            <td><?php echo e($row->pemilik); ?></td>
                                            <td><?php echo e($row->pemenang ?? '-'); ?></td>
                                            <td>Rp <?php echo e(number_format($row->harga_akhir ?? $row->harga_awal, 0, ',', '.')); ?></td>
                                            <td>
                                                <?php
                                                    $badgeMap = [
                                                        'pending'  => 'bg-warning',
                                                        'accepted' => 'bg-info',
                                                        'rejected' => 'bg-secondary',
                                                        'open'     => 'bg-primary',
                                                        'sold'     => 'bg-success',
                                                        'unsold'   => 'bg-dark',
                                                        'canceled' => 'bg-danger',
                                                    ];
                                                ?>
                                                <span class="badge <?php echo e($badgeMap[$row->status] ?? 'bg-secondary'); ?>">
                                                    <?php echo e(ucfirst($row->status)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($tableData->links('pagination::bootstrap-5')); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/plugins/apexcharts.min.js')); ?>"></script>
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
            const statusData = <?php echo json_encode($statusChartData, 15, 512) ?>;
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
            const revenueDates = <?php echo json_encode($revenueDates, 15, 512) ?>;
            const revenueValues = <?php echo json_encode($revenueValues, 15, 512) ?>;

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
            const bidderNames = <?php echo json_encode($bidderNames, 15, 512) ?>;
            const bidderCounts = <?php echo json_encode($bidderCounts, 15, 512) ?>;

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
                window.location.href = buildExportUrl('<?php echo e(route("laporan.export.pdf")); ?>');
            });

            document.getElementById('exportExcelBtn').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = buildExportUrl('<?php echo e(route("laporan.export.excel")); ?>');
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/brian/Files/Web Project/CepatDapat_new/resources/views/reports/index.blade.php ENDPATH**/ ?>