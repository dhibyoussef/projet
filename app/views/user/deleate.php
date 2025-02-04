<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Delete Account</h1>
    <p>Are you sure you want to delete your account? This action cannot be undone.</p>
    <form method="POST" action="../../controllers/user/DeleteController.php">
        <button type="submit" class="btn btn-danger" name="ok">Delete My Account</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>