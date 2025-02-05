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

// Regenerate session ID for security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

include '../../views/layouts/header.php'; 

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}
?>

<link rel="stylesheet" href="../../views/workout/assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Your Workouts</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
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
                <td><?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($workout['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($workout['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($workout['duration'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href="../../views/workout/edit.php?id=<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="btn btn-warning btn-sm">Edit</a>
                    <form action="../../controllers/workout/DeleteController.php" method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id"
                            value="<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>">
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