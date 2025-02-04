<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4"><?php echo htmlspecialchars($workout['name']); ?></h1>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($workout['description']); ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($workout['duration']); ?> minutes</p>
    <a href="edit.php?id=<?php echo $workout['id']; ?>" class="btn btn-warning">Edit Workout</a>
    <a href="index.php" class="btn btn-secondary">Back to Workouts</a>
</div>
<?php include '../layouts/footer.php'; ?>