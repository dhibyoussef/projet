<?php include '../../views/layouts/header.php'; 


?>

<link rel="stylesheet" href="../../views/workout/assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Your Workouts</h1>
    <a href="../../views/workout/create.php" class="btn btn-success mb-3">Add New Workout</a>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Duration (minutes)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($workouts)): ?>
            <?php foreach ($workouts as $workout): ?>
            <tr>
                <td><?php echo htmlspecialchars($workout['id']); ?></td>
                <td><?php echo htmlspecialchars($workout['name']); ?></td>
                <td><?php echo htmlspecialchars($workout['description']); ?></td>
                <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                <td>
                    <a href="../../views/workout/edit.php?id<?php echo $workout['id']; ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <form action="../../controllers/workout/DeleteController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $workout['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this workout?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No workouts found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../../views/layouts/footer.php'; ?>