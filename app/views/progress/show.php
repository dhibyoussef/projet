<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Progress Entry Details</h1>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($progress['date']); ?></p>
    <p><strong>Weight:</strong> <?php echo htmlspecialchars($progress['weight']); ?> kg</p>
    <p><strong>Body Fat:</strong> <?php echo htmlspecialchars($progress['body_fat']); ?> %</p>
    <a href="edit.php?id=<?php echo $progress['id']; ?>" class="btn btn-warning">Edit Entry</a>
    <a href="index.php" class="btn btn-secondary">Back to Progress Log</a>
</div>
<?php include '../layouts/footer.php'; ?>