<?php include '../../views/layouts/header.php'; ?>
<link rel="stylesheet" href="../../views/nutrition/assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Your Nutrition Log</h1>
    <a href="../../views/nutrition/create.php" class="btn btn-success mb-3">Log New Meal</a>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Food Item</th>
                <th>Calories</th>
                <th>Protein (g)</th>
                <th>Carbs (g)</th>
                <th>Fats (g)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($nutritionLogs)): ?>
            <?php foreach ($nutritionLogs as $nutrition): ?>
            <tr>
                <td><?php echo htmlspecialchars($nutrition['id']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['date']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['food_item']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['calories']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['protein']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['carbs']); ?></td>
                <td><?php echo htmlspecialchars($nutrition['fats']); ?></td>
                <td>
                    <a href="../../views/nutrition/edit.php?id=<?php echo $nutrition['id']; ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <form action="../../controllers/nutrition/DeleteController.php" method="POST"
                        style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $nutrition['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this entry?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No nutrition entries found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../../views/layouts/footer.php'; ?>