<?php
// Start session with secure settings if not already started
if (session_status() === PHP_SESSION_NONE) {
    try {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new Exception('Failed to set secure session parameters');
        }
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
    } catch (Exception $e) {
        error_log('Session initialization error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
        die(htmlspecialchars('An error occurred while initializing the session. Please try again later.', ENT_QUOTES, 'UTF-8'));
    }
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

// Include header with error handling
try {
    include '../layouts/header.php';
} catch (Exception $e) {
    error_log('Error loading header: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    die(htmlspecialchars('An error occurred while loading the page. Please try again later.', ENT_QUOTES, 'UTF-8'));
}
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars('assets/bootstrap.css', ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet"
    href="<?php echo htmlspecialchars('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', ENT_QUOTES, 'UTF-8'); ?>">
<style>
.error-window {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #dc3545;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}
</style>
<div class="container">
    <h1 class="mb-4"><?php echo htmlspecialchars('Delete Account', ENT_QUOTES, 'UTF-8'); ?></h1>
    <div id="error-container" class="error-window animate__animated" style="display: none;">
        <div class="alert alert-danger" role="alert">
            <span id="error-message"></span>
            <button type="button" class="close" onclick="dismissError()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <p><?php echo htmlspecialchars('Are you sure you want to delete your account? This action cannot be undone.', ENT_QUOTES, 'UTF-8'); ?>
    </p>
    <form method="POST"
        action="<?php echo htmlspecialchars('../../controllers/user/DeleteController.php', ENT_QUOTES, 'UTF-8'); ?>"
        id="delete-form">
        <?php 
        // Generate CSRF token with expiration time (15 minutes)
        $csrfExpiration = 15 * 60; // 15 minutes in seconds
        try {
            if (!isset($_SESSION['csrf_token']) || 
                !isset($_SESSION['csrf_token_time']) || 
                (time() - $_SESSION['csrf_token_time']) > $csrfExpiration) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['csrf_token_time'] = time();
            }
        } catch (Exception $e) {
            error_log('CSRF token generation error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            die(htmlspecialchars('An error occurred while processing your request. Please try again.', ENT_QUOTES, 'UTF-8'));
        }
        ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="csrf_token_time"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-danger" name="ok"
            onclick="return confirm('<?php echo htmlspecialchars('Are you sure you want to delete your account? This action cannot be undone.', ENT_QUOTES, 'UTF-8'); ?>');"><?php echo htmlspecialchars('Delete My Account', ENT_QUOTES, 'UTF-8'); ?></button>
    </form>
</div>
<script>
function showError(message) {
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');
    errorMessage.textContent = message;
    errorContainer.style.display = 'block';
    errorContainer.classList.add('animate__shakeX');
}

function dismissError() {
    const errorContainer = document.getElementById('error-container');
    errorContainer.classList.remove('animate__shakeX');
    errorContainer.classList.add('animate__fadeOut');
    setTimeout(() => {
        errorContainer.style.display = 'none';
        errorContainer.classList.remove('animate__fadeOut');
    }, 500);
}

// Handle form submission errors
document.getElementById('delete-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showError(data.error);
            } else {
                window.location.href = data.redirect || '/';
            }
        })
        .catch(error => {
            showError(
                '<?php echo htmlspecialchars('An error occurred while processing your request. Please try again.', ENT_QUOTES, 'UTF-8'); ?>'
                );
        });
});
</script>
<?php 
try {
    include '../layouts/footer.php';
} catch (Exception $e) {
    error_log('Error loading footer: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    // Continue execution even if footer fails
}
?>