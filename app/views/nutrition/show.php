<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Nutrition Entry Details</h1>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($nutrition['date']); ?></p>
    <p><strong>Food Item:</strong> <?php echo htmlspecialchars($nutrition['food_item']); ?></p>
    <p><strong>Calories:</strong> <?php echo htmlspecialchars($nutrition['calories']); ?></p>
    <p><strong>Protein:</strong> <?php echo htmlspecialchars($nutrition['protein']); ?> g</p>
    <p><strong>Carbs:</strong> <?php echo htmlspecialchars($nutrition['carbs']); ?> g</p>
    <p><strong>Fats:</strong> <?php echo htmlspecialchars($nutrition['fats']); ?> g</p>
    <div class="d-flex justify-content-between">
        <a href="edit.php?id=<?php echo $nutrition['id']; ?>" class="btn btn-warning">Edit Entry</a>
        <a href="index.php" class="btn btn-secondary">Back to Nutrition Log</a>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>