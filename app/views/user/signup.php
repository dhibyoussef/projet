<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Sign Up</h1>
    <form method="POST" action="../../controllers/user/SignupController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
            <div class="invalid-feedback">Please enter your full name.</div>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <div class="invalid-feedback">Please enter a strong password.</div>
        </div>
        <button type="submit" class="btn btn-success" name="signup">Sign Up</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>