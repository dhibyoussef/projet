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

    if (!include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    error_log('Error in workout edit view: ' . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['error_type'] = 'danger';
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translate(-50%, -40%);
    }

    100% {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}
</style>
<div class="container">
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="error-window">
        <h4 class="text-danger">An Error Occurred</h4>
        <p><?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?></p>
        <button class="btn btn-secondary" onclick="this.parentElement.remove()">Close</button>
    </div>
    <?php 
    unset($_SESSION['error_message']);
    unset($_SESSION['error_type']);
    endif; 
    ?>
    <h1 class="mb-4">Edit Workout</h1>
    <div id="message-container"></div>
    <form method="POST"
        action="../../controllers/workout/UpdateController.php?id=<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate onsubmit="return handleFormSubmit(event)">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="name">Workout Name</label>
            <input type="text" class="form-control" name="name" id="name"
                value="<?php echo htmlspecialchars($workout['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3"
                required><?php echo htmlspecialchars($workout['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="duration">Duration (minutes)</label>
            <input type="number" class="form-control" name="duration" id="duration"
                value="<?php echo htmlspecialchars($workout['duration'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please enter the duration of the workout.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Workout</button>
    </form>
</div>

<script>
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const messageContainer = document.getElementById('message-container');

    fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('success', data.message);
                setTimeout(() => {
                    window.location.href = '/workouts';
                }, 1500);
            } else {
                showMessage('danger', data.message);
            }
        })
        .catch(error => {
            showMessage('danger', 'An error occurred. Please try again.');
        });
}

function showMessage(type, message) {
    const messageContainer = document.getElementById('message-container');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} animate__animated animate__fadeIn`;
    alertDiv.textContent = message;
    messageContainer.innerHTML = '';
    messageContainer.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.classList.remove('animate__fadeIn');
        alertDiv.classList.add('animate__fadeOut');
        setTimeout(() => {
            alertDiv.remove();
        }, 500);
    }, 5000);
}
</script>

<?php 
try {
    if (!include '../layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    error_log('Error including footer: ' . $e->getMessage());
    echo '<div class="container mt-3">';
    echo '<div class="alert alert-warning animate__animated animate__shakeX">';
    echo '<p>Warning: Some page elements may not display correctly.</p>';
    echo '</div>';
    echo '</div>';
}
?>