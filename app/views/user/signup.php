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
            throw new Exception('Failed to set secure session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour
        }
    }

    // Regenerate session ID for security
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }

    // Include header with error handling
    if (!@include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    error_log('Error in signup.php: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    die(htmlspecialchars('An error occurred while processing your request. Please try again later.', ENT_QUOTES, 'UTF-8'));
}
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars('assets/bootstrap.css', ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet"
    href="<?php echo htmlspecialchars('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', ENT_QUOTES, 'UTF-8'); ?>">
<div class="container">
    <h1 class="mb-4">Sign Up</h1>
    <div id="message-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible animate__animated animate__fadeInRight"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
    <form method="POST"
        action="<?php echo htmlspecialchars('../../controllers/user/SignupController.php', ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" name="name" id="name" required maxlength="100">
            <div class="invalid-feedback">Please enter your full name.</div>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" name="email" id="email" required maxlength="255">
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required minlength="8"
                maxlength="255">
            <div class="invalid-feedback">Please enter a strong password (minimum 8 characters).</div>
        </div>
        <button type="submit" class="btn btn-success" name="signup">Sign Up</button>
    </form>
    <p class="mt-3">Already have an account? <a
            href="<?php echo htmlspecialchars('login.php', ENT_QUOTES, 'UTF-8'); ?>">Login here</a>.</p>
</div>
<script src="<?php echo htmlspecialchars('https://code.jquery.com/jquery-3.6.0.min.js', ENT_QUOTES, 'UTF-8'); ?>">
</script>
<script>
$(document).ready(function() {
    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                let alertClass = data.success ? 'alert-success' : 'alert-danger';
                let message = `<div class="alert ${alertClass} alert-dismissible animate__animated animate__fadeIn" role="alert">
                    ${$('<div>').text(data.message).html()}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
                $('#message-container').html(message);
                setTimeout(function() {
                    $('.alert').addClass('animate__fadeOut');
                    setTimeout(function() {
                        $('.alert').remove();
                    }, 1000);
                }, 5000);
            },
            error: function(xhr, status, error) {
                let message = `<div class="alert alert-danger alert-dismissible animate__animated animate__fadeIn" role="alert">
                    An error occurred: ${$('<div>').text(error).html()}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
                $('#message-container').html(message);
                setTimeout(function() {
                    $('.alert').addClass('animate__fadeOut');
                    setTimeout(function() {
                        $('.alert').remove();
                    }, 1000);
                }, 5000);
            }
        });
    });
});
</script>
<?php 
try {
    include '../layouts/footer.php';
} catch (Exception $e) {
    error_log('Error including footer: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>