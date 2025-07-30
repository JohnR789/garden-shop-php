<!-- Registration form -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1>Register</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" class="mt-4">
            <div class="mb-3">
                <label for="register-name">Full Name</label>
                <input id="register-name" type="text" name="name" class="form-control" autocomplete="name" required>
            </div>
            <div class="mb-3">
                <label for="register-email">Email</label>
                <input id="register-email" type="email" name="email" class="form-control" autocomplete="email" required>
            </div>
            <div class="mb-3">
                <label for="register-username">Username</label>
                <input id="register-username" type="text" name="username" class="form-control" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label for="register-password">Password</label>
                <input id="register-password" type="password" name="password" class="form-control" autocomplete="new-password" required>
            </div>
            <div class="mb-3">
                <label for="register-confirm">Confirm Password</label>
                <input id="register-confirm" type="password" name="confirm" class="form-control" autocomplete="new-password" required>
            </div>
            <button class="btn btn-success">Register</button>
            <a href="/login" class="btn btn-link">Login</a>
        </form>
    </div>
</div>


