<!-- Login form -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Login</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="login-username">Username or Email</label>
                <input id="login-username" type="text" name="username" class="form-control" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label for="login-password">Password</label>
                <input id="login-password" type="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>
            <button class="btn btn-success">Login</button>
            <a href="/register" class="btn btn-link">Register</a>
        </form>
    </div>
</div>
