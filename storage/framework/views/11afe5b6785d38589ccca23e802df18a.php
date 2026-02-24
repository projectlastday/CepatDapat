

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-history me-2"></i>History Data</h5>
                </div>
                <div class="card-body">

                    
                    <ul class="nav nav-tabs mb-4" role="tablist">
                        <?php
                            $tabs = [
                                'login' => 'Login',
                                'activity' => 'Activity User',
                                'cancel' => 'Cancel Lelang',
                                'delete' => 'Delete Lelang',
                                'uncancel' => 'Uncancel Lelang',
                                'restore' => 'Restore Lelang',
                                'setting' => 'Setting Website',
                            ];
                        ?>
                        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo e($tab === $key ? 'active' : ''); ?>"
                                    href="<?php echo e(route('history.index', ['tab' => $key])); ?>">
                                    <?php echo e($label); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    
                    <form method="GET" action="<?php echo e(route('history.index')); ?>" class="mb-4">
                        <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Cari Username</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Username..." value="<?php echo e($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control form-control-sm"
                                    value="<?php echo e($start_date); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control form-control-sm"
                                    value="<?php echo e($end_date); ?>">
                            </div>
                            <?php if(in_array($tab, $lelangTabs)): ?>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Barang</label>
                                    <input type="text" name="nama_lelang" class="form-control form-control-sm"
                                        placeholder="Nama barang..." value="<?php echo e($nama_lelang); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="col-md-2 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-search me-1"></i>Filter
                                </button>
                                <a href="<?php echo e(route('history.index', ['tab' => $tab])); ?>"
                                    class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-x"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:40px">No</th>

                                    <?php if($tab === 'login'): ?>
                                        <th>Username</th>
                                        <th>IP Address</th>
                                        <th>User Agent</th>
                                        <th style="width:150px">Waktu</th>
                                    <?php elseif($tab === 'activity'): ?>
                                        <th>Username</th>
                                        <th>URL</th>
                                        <th style="width:150px">Waktu</th>
                                    <?php elseif(in_array($tab, $lelangTabs)): ?>
                                        <th>Pelaku</th>
                                        <th>Nama Barang</th>
                                        <th>Alasan</th>
                                        <th style="width:150px">Waktu</th>
                                        <th style="width:130px">Status Saat Ini</th>
                                    <?php elseif($tab === 'setting'): ?>
                                        <th>Pelaku</th>
                                        <th>Type</th>
                                        <th>Data Lama</th>
                                        <th>Data Baru</th>
                                        <th style="width:150px">Waktu</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($data->firstItem() + $i); ?></td>

                                        <?php if($tab === 'login'): ?>
                                            <td><?php echo e($row->username); ?></td>
                                            <td><?php echo e($row->ip_address ?? '-'); ?></td>
                                            <td title="<?php echo e($row->user_agent); ?>">
                                                <?php echo e(\Illuminate\Support\Str::limit($row->user_agent, 60)); ?>

                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i')); ?></td>

                                        <?php elseif($tab === 'activity'): ?>
                                            <td><?php echo e($row->username); ?></td>
                                            <td title="<?php echo e($row->url); ?>">
                                                <?php echo e(\Illuminate\Support\Str::limit($row->url, 80)); ?>

                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i')); ?></td>

                                        <?php elseif(in_array($tab, $lelangTabs)): ?>
                                            <td><?php echo e($row->pelaku); ?></td>
                                            <td><?php echo e($row->nama_barang ?? '-'); ?></td>
                                            <td title="<?php echo e($row->alasan); ?>">
                                                <?php echo e(\Illuminate\Support\Str::limit($row->alasan, 60)); ?>

                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i')); ?></td>
                                            <td>
                                                <?php if($row->nama_barang === null): ?>
                                                    <span class="badge bg-secondary">Tidak Ditemukan</span>
                                                <?php elseif($row->status_delete == 1): ?>
                                                    <span class="badge bg-dark">Deleted</span>
                                                <?php else: ?>
                                                    <?php
                                                        $badgeMap = [
                                                            'pending' => 'bg-warning',
                                                            'accepted' => 'bg-info',
                                                            'rejected' => 'bg-secondary',
                                                            'open' => 'bg-primary',
                                                            'sold' => 'bg-success',
                                                            'unsold' => 'bg-dark',
                                                            'canceled' => 'bg-danger',
                                                        ];
                                                    ?>
                                                    <span class="badge <?php echo e($badgeMap[$row->current_status] ?? 'bg-secondary'); ?>">
                                                        <?php echo e(ucfirst($row->current_status)); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                        <?php elseif($tab === 'setting'): ?>
                                            <td><?php echo e($row->pelaku); ?></td>
                                            <td><?php echo e($row->type); ?></td>
                                            <td title="<?php echo e($row->data_lama); ?>">
                                                <?php echo e(\Illuminate\Support\Str::limit($row->data_lama, 50) ?: '-'); ?>

                                            </td>
                                            <td title="<?php echo e($row->data_baru); ?>">
                                                <?php echo e(\Illuminate\Support\Str::limit($row->data_baru, 50) ?: '-'); ?>

                                            </td>
                                            <td><?php echo e(\Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i')); ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="ti ti-database-off me-1"></i>Tidak ada data untuk filter ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php if($data->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($data->links('pagination::bootstrap-5')); ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\admin\history_data.blade.php ENDPATH**/ ?>