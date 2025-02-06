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
            throw new Exception('Failed to set secure session parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    if (!isset($nutrition) || empty($nutrition)) {
        throw new Exception('Nutrition data not found');
    }

    if (!@include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
?>
<link rel="stylesheet" href="/fitness_tracker/public/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<div class="container">
    <h1 class="mb-4">Nutrition Entry Details</h1>
    <div id="message-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?> alert-dismissible animate__animated animate__slideInRight"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <button type="button" class="close" onclick="dismissMessage()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
    <div class="card">
        <div class="card-body">
            <p><strong>Date:</strong> <?php echo htmlspecialchars($nutrition['date']); ?></p>
            <p><strong>Food Item:</strong> <?php echo htmlspecialchars($nutrition['food_item']); ?></p>
            <p><strong>Calories:</strong> <?php echo htmlspecialchars($nutrition['calories']); ?></p>
            <p><strong>Protein:</strong> <?php echo htmlspecialchars($nutrition['protein']); ?> g</p>
            <p><strong>Carbs:</strong> <?php echo htmlspecialchars($nutrition['carbs']); ?> g</p>
            <p><strong>Fats:</strong> <?php echo htmlspecialchars($nutrition['fats']); ?> g</p>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <a href="edit.php?id=<?php echo htmlspecialchars($nutrition['id']); ?>" class="btn btn-warning">Edit Entry</a>
        <a href="index.php" class="btn btn-secondary">Back to Nutrition Log</a>
    </div>
</div>
<script>
function dismissMessage() {
    const messageDiv = document.querySelector('#message-container .alert');
    messageDiv.classList.remove('animate__slideInRight');
    messageDiv.classList.add('animate__fadeOutRight');
    setTimeout(() => messageDiv.remove(), 500);
}

// Auto-dismiss message after 5 seconds
const message = document.querySelector('#message-container .alert');
if (message) {
    setTimeout(dismissMessage, 5000);
}
</script>
<?php 
    if (!@include '../layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    error_log('Error in nutrition/show.php: ' . $e->getMessage());
    echo '<div id="error-notification" class="alert alert-danger animate__animated animate__shakeX fixed-top" style="margin-top: 56px; z-index: 9999;">
            An error occurred while processing your request. Please try again later.
            <button type="button" class="close" onclick="dismissError()">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
    echo '<script>
            function dismissError() {
                const errorDiv = document.getElementById("error-notification");
                errorDiv.classList.remove("animate__shakeX");
                errorDiv.classList.add("animate__fadeOutUp");
                setTimeout(() => errorDiv.remove(), 500);
            }
            setTimeout(dismissError, 5000);
          </script>';
    exit();
}
?>