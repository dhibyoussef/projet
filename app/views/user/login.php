<?php
// Initialize session with error handling
if (session_status() === PHP_SESSION_NONE) {
    try {
        // Configure secure session cookie parameters
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Start session
        if (!session_start()) {
            throw new RuntimeException('Failed to start session');
        }
        
        // Regenerate session ID for security
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }

        // Generate CSRF token with expiration
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
        }
    } catch (Exception $e) {
        error_log('Session initialization error: ' . $e->getMessage());
        http_response_code(500);
        die('System error. Please try again later.');
    }
}

// Include header
try {
    require '../layouts/header.php';
} catch (Exception $e) {
    error_log('Header inclusion error: ' . $e->getMessage());
    http_response_code(500);
    die('System error. Please try again later.');
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<style>
body {
    background-image: url('assets/g.jpg');
    background-size: cover;
}

.alert-animate {
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-animate {
    animation: zoomIn 0.3s ease-out;
}

@keyframes zoomIn {
    0% {
        transform: scale(0.8);
        opacity: 0;
    }

    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
<div class="container">
    <h1 class="mb-4">Login</h1>
    <div id="message-container"></div>
    <form method="POST" action="../../controllers/user/LoginController.php" class="needs-validation" novalidate>
        <?php if (!empty($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">Security token missing. Please refresh the page.</div>
        <?php endif; ?>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Please enter your email.</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <div class="invalid-feedback">Please enter your password.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="login">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-animate">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalMessage"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Handle form submission
    $('form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    showErrorModal(response.message);
                }
            },
            error: function() {
                showErrorModal('An error occurred. Please try again.');
            }
        });
    });

    // Show error modal with animation
    function showErrorModal(message) {
        $('#modalMessage').text(message);
        $('#errorModal').modal('show');
    }

    // Show initial session message if exists
    <?php if (!empty($_SESSION['message'])): ?>
    showErrorModal('<?php echo addslashes($_SESSION['message']); ?>');
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
});
</script>

<?php
try {
    require '../layouts/footer.php';
} catch (Exception $e) {
    error_log('Footer inclusion error: ' . $e->getMessage());
    http_response_code(500);
    die('System error. Please try again later.');
}
?>