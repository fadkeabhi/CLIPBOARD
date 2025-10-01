<div class="row">
    <div class="col-md-10 offset-md-1">
        <!-- Board Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2><?= e($board['name']) ?></h2>
                        <p class="text-muted mb-0">
                            <span class="badge bg-secondary"><?= e($board['default_access']) ?></span>
                            <?php if ($board['password_hash']): ?>
                                <span class="badge bg-warning text-dark">Password Protected</span>
                            <?php endif; ?>
                            <?php if (!$board['is_editable']): ?>
                                <span class="badge bg-info">Read-only clips</span>
                            <?php endif; ?>
                        </p>
                        <small class="text-muted"><?= SITE_URL ?>/b/<?= e($board['suburl']) ?></small>
                    </div>
                    <?php if (isBoardAdmin(getCurrentUserId(), $board)): ?>
                        <a href="<?= SITE_URL ?>/b/<?= e($board['suburl']) ?>/settings" class="btn btn-outline-secondary">
                            Board Settings
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add Clip Form -->
        <?php if (canEditBoard(getCurrentUserId(), $board)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Add New Clip</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= SITE_URL ?>/">
                        <input type="hidden" name="action" value="add_clip">
                        <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="content" rows="4" placeholder="Enter your clip content here..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add Clip</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Clips List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Clips (<?= count($clips) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($clips)): ?>
                    <p class="text-muted">No clips yet. Be the first to add one!</p>
                <?php else: ?>
                    <?php foreach ($clips as $clip): ?>
                        <div class="clip-item mb-3 p-3 border rounded" id="clip-<?= $clip['id'] ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <small class="text-muted">
                                    Posted by <?= $clip['username'] ? e($clip['username']) : 'Guest' ?> 
                                    on <?= date('M j, Y g:i A', strtotime($clip['created_at'])) ?>
                                </small>
                                <?php if (canEditClip(getCurrentUserId(), $board, $clip)): ?>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="editClip(<?= $clip['id'] ?>)">Edit</button>
                                        <button class="btn btn-outline-danger" onclick="deleteClip(<?= $clip['id'] ?>)">Delete</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="clip-content" id="clip-content-<?= $clip['id'] ?>">
                                <pre class="mb-0"><?= e($clip['content']) ?></pre>
                            </div>
                            
                            <div class="clip-edit d-none" id="clip-edit-<?= $clip['id'] ?>">
                                <form method="POST" action="<?= SITE_URL ?>/">
                                    <input type="hidden" name="action" value="edit_clip">
                                    <input type="hidden" name="clip_id" value="<?= $clip['id'] ?>">
                                    
                                    <textarea class="form-control mb-2" name="content" rows="4" required><?= e($clip['content']) ?></textarea>
                                    
                                    <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(<?= $clip['id'] ?>)">Cancel</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function editClip(clipId) {
    document.getElementById('clip-content-' + clipId).classList.add('d-none');
    document.getElementById('clip-edit-' + clipId).classList.remove('d-none');
}

function cancelEdit(clipId) {
    document.getElementById('clip-content-' + clipId).classList.remove('d-none');
    document.getElementById('clip-edit-' + clipId).classList.add('d-none');
}

function deleteClip(clipId) {
    if (confirm('Are you sure you want to delete this clip?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= SITE_URL ?>/';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete_clip';
        
        const clipIdInput = document.createElement('input');
        clipIdInput.type = 'hidden';
        clipIdInput.name = 'clip_id';
        clipIdInput.value = clipId;
        
        form.appendChild(actionInput);
        form.appendChild(clipIdInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
