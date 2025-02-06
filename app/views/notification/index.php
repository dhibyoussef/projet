<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Redirect if not logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: /fitness_tracker/public/login');
        exit();
    }

    // Include header with error handling
    if (!@include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
    ?>
<div class="container mt-5">
    <h1 class="mb-4">Notifications</h1>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Message</th>
                    <th scope="col">Date</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                <tr>
                    <td><?php echo htmlspecialchars($notification['id']); ?></td>
                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
                    <td><?php echo htmlspecialchars(date('M j, Y g:i a', strtotime($notification['date']))); ?></td>
                    <td>
                        <span
                            class="badge badge-<?php echo $notification['status'] === 'read' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars(ucfirst($notification['status'])); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">No notifications available.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
    // Include footer with error handling
    if (!@include '../layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    // Show error in the same page with animated window
    echo '<div id="error-window" class="error-window animate__animated animate__shakeX">
            <div class="error-content">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>An error occurred: ' . htmlspecialchars($e->getMessage()) . '</span>
            </div>
          </div>';
    echo '<style>
            .error-window {
                position: fixed;
                top: 20%;
                left: 50%;
                transform: translateX(-50%);
                background: #ffebee;
                border: 1px solid #ff4444;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 1000;
            }
            .error-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .bi-exclamation-triangle-fill {
                color: #ff4444;
                font-size: 24px;
            }
          </style>';
    echo '<script>
            // Auto-remove error window after 5 seconds
            setTimeout(() => {
                const errorWindow = document.getElementById("error-window");
                errorWindow.classList.remove("animate__shakeX");
                errorWindow.classList.add("animate__fadeOut");
                setTimeout(() => errorWindow.remove(), 500);
            }, 5000);
          </script>';
    error_log('Notification page error: ' . $e->getMessage());
}
?>