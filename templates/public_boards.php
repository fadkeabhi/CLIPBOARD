<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Public Boards</h2>
                <form method="GET" class="d-flex" style="gap:8px;">
                    <input type="text" name="q" class="form-control" placeholder="Search boards..." value="<?= e($_GET['q'] ?? '') ?>">
                    <select name="type" class="form-select">
                        <option value="">Any access</option>
                        <option value="public_view" <?= (isset($_GET['type']) && $_GET['type'] === 'public_view') ? 'selected' : '' ?>>Public View</option>
                        <option value="public_add" <?= (isset($_GET['type']) && $_GET['type'] === 'public_add') ? 'selected' : '' ?>>Public Add</option>
                    </select>
                    <button class="btn btn-primary" type="submit">Filter</button>
                </form>
            </div>
        </div>

        <?php if (empty($publicBoards)): ?>
            <p class="text-muted">No public boards found.</p>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($publicBoards as $board): ?>
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
