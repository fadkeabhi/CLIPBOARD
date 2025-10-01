<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="jumbotron text-center py-5">
            <h1 class="display-4">Welcome to <?= e(SITE_NAME) ?></h1>
            <p class="lead">Create collaborative clipboards and share snippets with your team.</p>
            <?php if (!isLoggedIn()): ?>
                <hr class="my-4">
                <p>Get started by creating an account or logging in.</p>
                <a class="btn btn-primary btn-lg me-2" href="<?= SITE_URL ?>/register" role="button">Register</a>
                <a class="btn btn-secondary btn-lg" href="<?= SITE_URL ?>/login" role="button">Login</a>
            <?php endif; ?>
        </div>

        <?php if (isLoggedIn()): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Create New Board</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= SITE_URL ?>/">
                        <input type="hidden" name="action" value="create_board">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Board Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_access" class="form-label">Public Access</label>
                            <select class="form-select" id="default_access" name="default_access">
                                <option value="private">Private (No public access)</option>
                                <option value="public_view">Public View (Anyone can view)</option>
                                <option value="public_add">Public Add (Anyone can view and add clips)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_editable" name="is_editable" checked>
                            <label class="form-check-label" for="is_editable">
                                Allow editing of clips (uncheck to make clips read-only after creation)
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Protection (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave blank for no password protection.</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Create Board</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Boards</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($userBoards)): ?>
                        <p class="text-muted">You haven't created any boards yet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($userBoards as $board): ?>
                                <a href="<?= SITE_URL ?>/b/<?= e($board['suburl']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= e($board['name']) ?></h6>
                                        <small><?= date('M j, Y', strtotime($board['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-1">
                                        <span class="badge bg-secondary"><?= e($board['default_access']) ?></span>
                                        <?php if ($board['password_hash']): ?>
                                            <span class="badge bg-warning text-dark">Password Protected</span>
                                        <?php endif; ?>
                                    </p>
                                    <small class="text-muted"><?= SITE_URL ?>/b/<?= e($board['suburl']) ?></small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
