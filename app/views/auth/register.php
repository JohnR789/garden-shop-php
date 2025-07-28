<!-- Registration form -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Register</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" class="mt-4">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm" class="form-control" autocomplete="new-password" required>
            </div>
            <button class="btn btn-success">Register</button>
            <a href="/login" class="btn btn-link">Login</a>
        </form>
    </div>
</div>
