<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-users me-2"></i>Data User</h5>
                </div>
                <div class="card-body">

                    
                    <form method="GET" action="<?php echo e(route('admin.users')); ?>" class="mb-4">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Cari User</label>
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Username, email, atau telepon..." value="<?php echo e($search); ?>">
                            </div>
                            <div class="col-md-3 d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="ti ti-search me-1"></i>Cari
                                </button>
                                <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-outline-secondary btn-sm">
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
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($users->firstItem() + $i); ?></td>
                                        <td class="fw-bold"><?php echo e($user->username); ?></td>
                                        <td><?php echo e($user->email ?? '-'); ?></td>
                                        <td>
                                            <?php if($user->email_verified_at): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($user->telepon ?? '-'); ?></td>
                                        <td>
                                            <?php if($user->telepon_verified_at): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
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
                                            ?>
                                            <span class="badge <?php echo e($type['badge']); ?>"><?php echo e($type['label']); ?></span>
                                        </td>
                                        <td><?php echo e($user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="ti ti-database-off me-1"></i>Tidak ada data user.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <?php if($users->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($users->links('pagination::bootstrap-5')); ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/brian/Files/Web Project/CepatDapat_new/resources/views/admin/users.blade.php ENDPATH**/ ?>