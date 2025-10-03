<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login');
}

$user = getUserById(getCurrentUserId());
?>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <h2>Profile</h2>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Username:</strong> <?= e($user['username']) ?></p>
                <p><strong>Email:</strong> <?= e($user['email']) ?></p>
                <p><strong>Member since:</strong> <?= e($user['created_at']) ?></p>
            </div>
        </div>

        <h3>Change Password</h3>
        <form method="POST" action="<?= SITE_URL ?>/profile">
            <input type="hidden" name="action" value="change_password">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>
