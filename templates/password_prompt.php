<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Password Protected Board</h4>
            </div>
            <div class="card-body">
                <p>This board is password protected. Please enter the password to access it.</p>
                
                <form method="POST" action="<?= SITE_URL ?>/">
                    <input type="hidden" name="action" value="verify_password">
                    <input type="hidden" name="board_id" value="<?= $board['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required autofocus>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Access Board</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="<?= SITE_URL ?>/">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
