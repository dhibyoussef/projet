<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<style>
body {
    background-image: url('assets/g.png');
    background-size: cover;
}
</style>
<div class="container">
    <h1 class="mb-4">Login</h1>
    <form method="POST" action="../../controllers/user/LoginController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Please enter your email.</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <div class="invalid-feedback">Please enter your password.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="login">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</div>
<?php include '../layouts/footer.php'; ?>