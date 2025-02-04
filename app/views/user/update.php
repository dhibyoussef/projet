<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Update Profile</h1>
    <form method="POST" action="../../controllers/user/UpdateController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name"
                value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <div class="invalid-feedback">Please enter your name.</div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <div class="invalid-feedback">Please enter your email.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>