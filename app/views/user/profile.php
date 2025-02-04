<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Your Profile</h1>
    <p><strong>Name:</strong> <?php echo isset($user['name']) ? htmlspecialchars($user['name']) : 'N/A'; ?></p>
    <p><strong>Email:</strong> <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'N/A'; ?></p>
    <a href="update.php" class="btn btn-warning">Edit Profile</a>
    <a href="../../views/user/deleate.php" class="btn btn-danger" Name="ok">Delete Account</a>
</div>
<?php include '../layouts/footer.php'; ?>