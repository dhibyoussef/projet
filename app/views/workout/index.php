<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new RuntimeException('Failed to set secure session parameters');
        }
        
        if (!session_start()) {
            throw new RuntimeException('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new RuntimeException('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    // Regenerate session ID for security
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }

    if (!include '../../views/layouts/header.php') {
        throw new RuntimeException('Failed to include header file');
    }
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = 'A system error occurred. Please try again later.';
    $_SESSION['error_type'] = 'danger';
} catch (Exception $e) {
    $_SESSION['error_message'] = 'An unexpected error occurred. Please contact support if the problem persists.';
    $_SESSION['error_type'] = 'danger';
}
?>

<link rel="stylesheet" href="../../views/workout/assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container">
    <h1 class="mb-4">Your Workouts</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div
        class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?> animate__animated animate__fadeIn">
        <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    <div
        class="alert alert-<?php echo htmlspecialchars($_SESSION['error_type'], ENT_QUOTES, 'UTF-8'); ?> animate__animated animate__shakeX">
        <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php 
    unset($_SESSION['error_message']);
    unset($_SESSION['error_type']);
    ?>
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
                    <form action="../../controllers/workout/DeleteController.php" method="POST" style="display:inline;"
                        class="delete-form">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id"
                            value="<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation with SweetAlert
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Animate alert messages
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('animate__fadeOut');
            setTimeout(() => alert.remove(), 1000);
        }, 5000);
    });
});
</script>
<?php 
try {
    if (!include '../../views/layouts/footer.php') {
        throw new RuntimeException('Failed to include footer file');
    }
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = 'Failed to load page footer. Please try again.';
    $_SESSION['error_type'] = 'danger';
} catch (Exception $e) {
    $_SESSION['error_message'] = 'An unexpected error occurred while loading page footer.';
    $_SESSION['error_type'] = 'danger';
}
?>