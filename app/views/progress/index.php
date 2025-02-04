<?php include '../../views/layouts/header.php'; ?>
<link rel="stylesheet" href="../../views/progress/assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Your Progress Log</h1>
    <a href="../../views/progress/create.php" class="btn btn-success mb-3">Log New Progress</a>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Weight (kg)</th>
                <th>Body Fat (%)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($progressLogs)): ?>
            <?php foreach ($progressLogs as $progress): ?>
            <tr>
                <td><?php echo htmlspecialchars($progress['id']); ?></td>
                <td><?php echo htmlspecialchars($progress['date']); ?></td>
                <td><?php echo htmlspecialchars($progress['weight']); ?></td>
                <td><?php echo htmlspecialchars($progress['body_fat']); ?></td>
                <td>
                    <a href="../../views/progress/edit.php?id=<?php echo $progress['id']; ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <form action="../../controllers/progress/DeleteController.php" method="POST"
                        style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $progress['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this entry?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No progress entries found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../../views/layouts/footer.php'; ?>