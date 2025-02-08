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

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
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
        throw new Exception('Failed to load header');
    }
} catch (Exception $e) {
    // Show error message in the same page with animation
    $_SESSION['message'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'));
    exit();
}
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars('assets/bootstrap.css', ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet"
    href="<?php echo htmlspecialchars('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', ENT_QUOTES, 'UTF-8'); ?>">
<script src="<?php echo htmlspecialchars('https://unpkg.com/axios/dist/axios.min.js', ENT_QUOTES, 'UTF-8'); ?>">
</script>
<div class="container">
    <h1 class="mb-4">Your Profile</h1>
    <div id="message-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'] ?? 'danger', ENT_QUOTES, 'UTF-8'); ?> alert-dismissible animate__animated animate__fadeInRight"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message'] ?? 'An error occurred', ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
    <p><strong>Name:</strong>
        <?php echo isset($user['name']) ? htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
    <p><strong>Email:</strong>
        <?php echo isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') : 'N/A'; ?></p>
    <form action="<?php echo htmlspecialchars('update.php', ENT_QUOTES, 'UTF-8'); ?>" method="get"
        style="display: inline;" onsubmit="return handleFormSubmit(event)">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-warning">Edit Profile</button>
    </form>
    <form action="<?php echo htmlspecialchars('../../views/user/delete.php', ENT_QUOTES, 'UTF-8'); ?>" method="get"
        style="display: inline;" onsubmit="return handleFormSubmit(event)">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-danger" name="ok"
            onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete
            Account</button>
    </form>
</div>
<script>
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    axios({
            method: form.method,
            url: form.action,
            data: formData
        })
        .then(response => {
            if (response.data.message) {
                showMessage(response.data.message, response.data.message_type || 'success');
            }
            if (response.data.redirect) {
                window.location.href = response.data.redirect;
            }
        })
        .catch(error => {
            const message = error.response?.data?.message || 'An error occurred';
            showMessage(message, 'danger');
        });
}

function showMessage(message, type) {
    const messageContainer = document.getElementById('message-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible animate__animated animate__fadeInRight`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    messageContainer.appendChild(alert);

    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        alert.classList.remove('animate__fadeInRight');
        alert.classList.add('animate__fadeOutRight');
        setTimeout(() => alert.remove(), 1000);
    }, 5000);
}
</script>
<?php 
try {
    include '../layouts/footer.php';
} catch (Exception $e) {
    $_SESSION['message'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $_SESSION['message_type'] = 'danger';
    header('Location: ' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'));
    exit();
}
?>