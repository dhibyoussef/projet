<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

include '../../views/layouts/header.php'; 
?>
<link rel="stylesheet" href="../../views/progress/assets/bootstrap.css">
<div class="container mt-5">
    <h1 class="mb-4">Your Progress Log</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?> alert-dismissible fade show"
        role="alert">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <a href="../../views/progress/create.php" class="btn btn-success mb-3">Log New Progress</a>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
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
                    <td><?php echo htmlspecialchars(date('F j, Y', strtotime($progress['date']))); ?></td>
                    <td><?php echo htmlspecialchars($progress['weight']); ?></td>
                    <td><?php echo htmlspecialchars($progress['body_fat']); ?></td>
                    <td>
                        <a href="../../views/progress/edit.php?id=<?php echo $progress['id']; ?>"
                            class="btn btn-warning btn-sm">Edit</a>
                        <form action="../../controllers/progress/DeleteController.php" method="POST"
                            style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $progress['id']; ?>">
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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
</div>
<?php include '../../views/layouts/footer.php'; ?>