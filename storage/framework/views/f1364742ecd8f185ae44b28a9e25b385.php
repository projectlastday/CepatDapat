<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div
        style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333; margin-top: 0;">Reset Password</h2>
        <p style="color: #666;">Halo <strong><?php echo e($user->username ?? 'Pengguna'); ?></strong>,</p>
        <p style="color: #666;">Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e($resetUrl); ?>"
                style="display: inline-block; background-color: #4e73df; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">
                Reset Password
            </a>
        </div>

        <p style="color: #999; font-size: 13px;">Link ini berlaku selama 60 menit.</p>
        <p style="color: #999; font-size: 13px;">Jika Anda tidak meminta reset password, abaikan email ini.</p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #aaa; font-size: 12px; text-align: center;">&copy; <?php echo e(date('Y')); ?> CepatDapat</p>
    </div>
</body>

</html><?php /**PATH C:\Users\jason\Documents\Brian\CepatDapat_new\resources\views\emails\reset_password.blade.php ENDPATH**/ ?>