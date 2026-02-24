

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Setting Website</h5>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="ti ti-photo me-2"></i>Logo Website</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        
                        <div class="col-md-5 text-center mb-4 mb-md-0">
                            <p class="text-muted fw-bold mb-3">Logo Saat Ini</p>
                            <div class="p-4 bg-light rounded-4 d-inline-block" style="min-width: 200px;">
                                <img src="<?php echo e(asset($logo ?? 'assets/images/CepatDapat.png')); ?>" alt="Current Logo"
                                    class="img-fluid" style="max-height: 120px;">
                            </div>
                        </div>

                        
                        <div class="col-md-7">
                            <form action="<?php echo e(route('setting.update_logo')); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Upload Logo Baru</label>
                                    <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg"
                                        required onchange="previewLogo(event)">
                                    <small class="text-muted">Format: PNG, JPG, JPEG. Maks: 2MB.</small>
                                    <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                
                                <div class="mb-3 d-none" id="previewContainer">
                                    <p class="text-muted fw-bold mb-2">Preview Logo Baru</p>
                                    <div class="p-3 bg-light rounded-4 d-inline-block">
                                        <img id="logoPreview" src="" alt="Preview" class="img-fluid"
                                            style="max-height: 100px;">
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary fw-bold py-2">
                                        <i class="ti ti-upload me-1"></i> Simpan Logo Baru
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="ti ti-database-export me-2"></i>Backup Database</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo e(route('setting.backup')); ?>" method="POST" id="backupForm">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-warning fw-bold py-2" id="backupBtn"
                            onclick="return confirmBackup(this)">
                            <i class="ti ti-download me-1"></i> Download Backup (.sql)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function previewLogo(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById('logoPreview').src = e.target.result;
                        document.getElementById('previewContainer').classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }

            function confirmBackup(btn) {
                if (!confirm('Apakah Anda yakin ingin membuat backup database?')) {
                    return false;
                }
                // Disable AFTER form submits (setTimeout allows submit to fire first)
                setTimeout(function() {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses backup...';
                    // Re-enable after 10s (file download won't navigate away)
                    setTimeout(function() {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-download me-1"></i> Download Backup (.sql)';
                    }, 10000);
                }, 100);
                return true;
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\admin\setting_website.blade.php ENDPATH**/ ?>