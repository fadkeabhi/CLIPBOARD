<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="mb-3">
            <a href="<?= SITE_URL ?>/b/<?= e($board['suburl']) ?>" class="btn btn-outline-secondary">
                &larr; Back to Board
            </a>
        </div>

        <h2 class="mb-4">Board Settings: <?= e($board['name']) ?></h2>

        <!-- General Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= SITE_URL ?>/">
                    <input type="hidden" name="action" value="update_board_settings">
                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Board Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($board['name']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="default_access" class="form-label">Public Access</label>
                        <select class="form-select" id="default_access" name="default_access">
                            <option value="private" <?= $board['default_access'] === 'private' ? 'selected' : '' ?>>
                                Private (No public access)
                            </option>
                            <option value="public_view" <?= $board['default_access'] === 'public_view' ? 'selected' : '' ?>>
                                Public View (Anyone can view)
                            </option>
                            <option value="public_add" <?= $board['default_access'] === 'public_add' ? 'selected' : '' ?>>
                                Public Add (Anyone can view and add clips)
                            </option>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <?php $isPrivate = ($board['default_access'] === 'private'); ?>
                        <input type="checkbox" class="form-check-input" id="list_publically" name="list_publically" <?= $board['list_publically'] ? 'checked' : '' ?> <?= $isPrivate ? 'disabled' : '' ?>>
                        <label class="form-check-label" for="list_publically">
                            List this board publicly on the boards page
                        </label>
                        <?php if ($isPrivate): ?>
                            <div class="form-text text-muted">This board is private, private boards cannot be listed publicly. Change Public Access to enable this option.</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_editable" name="is_editable" 
                            <?= $board['is_editable'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_editable">
                            Allow editing of clips (uncheck to make clips read-only after creation)
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Protection</label>
                        <?php if ($board['password_hash']): ?>
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>Status:</strong> Password protection is currently <strong>enabled</strong>.
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary">
                                <strong>Status:</strong> No password protection set.
                            </div>
                        <?php endif; ?>
                        <input type="password" class="form-control" id="password" name="password" 
                            placeholder="<?= $board['password_hash'] ? 'Enter new password to change it' : 'Enter password to enable protection' ?>">
                        <div class="form-text">
                            <?php if ($board['password_hash']): ?>
                                Enter a new password to change it, or leave blank to keep the current password.
                            <?php else: ?>
                                Enter a password to enable password protection for this board.
                            <?php endif; ?>
                        </div>
                        <?php if ($board['password_hash']): ?>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="remove_password" name="remove_password" value="1">
                                <label class="form-check-label text-danger" for="remove_password">
                                    <strong>Remove password protection</strong> (board will no longer require a password)
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Settings</button>
                </form>
            </div>
        </div>

        <!-- Collaborators -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Collaborators</h5>
            </div>
            <div class="card-body">
                <h6>Current Collaborators</h6>
                <?php if (empty($collaborators)): ?>
                    <p class="text-muted">No collaborators added yet.</p>
                <?php else: ?>
                    <div class="list-group mb-4">
                        <?php foreach ($collaborators as $collaborator): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= e($collaborator['username']) ?></strong>
                                    <span class="badge bg-secondary ms-2"><?= e($collaborator['permission_level']) ?></span>
                                </div>
                                <form method="POST" action="<?= SITE_URL ?>/" class="d-inline">
                                    <input type="hidden" name="action" value="remove_collaborator">
                                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $collaborator['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Remove this collaborator?')">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <h6>Add Collaborator</h6>
                <form method="POST" action="<?= SITE_URL ?>/">
                    <input type="hidden" name="action" value="add_collaborator">
                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select a user...</option>
                                <?php foreach ($allUsers as $user): ?>
                                    <?php 
                                    // Don't show owner or existing collaborators
                                    $isOwner = $user['id'] == $board['owner_id'];
                                    $isCollaborator = false;
                                    foreach ($collaborators as $collab) {
                                        if ($collab['id'] == $user['id']) {
                                            $isCollaborator = true;
                                            break;
                                        }
                                    }
                                    if (!$isOwner && !$isCollaborator):
                                    ?>
                                        <option value="<?= $user['id'] ?>"><?= e($user['username']) ?> (<?= e($user['email']) ?>)</option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="permission_level" class="form-label">Permission Level</label>
                            <select class="form-select" id="permission_level" name="permission_level" required>
                                <option value="view">View (Can only view clips)</option>
                                <option value="edit">Edit (Can view, add, edit, delete clips)</option>
                                <option value="admin">Admin (Full access including settings)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Danger Zone</h5>
            </div>
            <div class="card-body">
                <p>Once you delete a board, there is no going back. This will permanently delete the board and all its clips.</p>
                <form method="POST" action="<?= SITE_URL ?>/" onsubmit="return confirm('Are you ABSOLUTELY sure? This cannot be undone!');">
                    <input type="hidden" name="action" value="delete_board">
                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                    <button type="submit" class="btn btn-danger">Delete This Board</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Handle password removal checkbox interaction
document.addEventListener('DOMContentLoaded', function() {
    const removePasswordCheckbox = document.getElementById('remove_password');
    const passwordInput = document.getElementById('password');
    
    if (removePasswordCheckbox && passwordInput) {
        removePasswordCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordInput.value = '';
                passwordInput.disabled = true;
                passwordInput.placeholder = 'Password will be removed';
            } else {
                passwordInput.disabled = false;
                passwordInput.placeholder = 'Enter new password to change it';
            }
        });
    }
    // Disable/enable listing checkbox when default access changes
    const defaultAccessSelect = document.getElementById('default_access');
    const listCheckbox = document.getElementById('list_publically');
    if (defaultAccessSelect && listCheckbox) {
        defaultAccessSelect.addEventListener('change', function() {
            if (this.value === 'private') {
                listCheckbox.checked = false;
                listCheckbox.disabled = true;
            } else {
                listCheckbox.disabled = false;
            }
        });
    }
});
</script>
