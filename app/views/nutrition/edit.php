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
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    include '../layouts/header.php'; 
} catch (Exception $e) {
    error_log('Error in nutrition/edit.php: ' . $e->getMessage());
    header('Location: /error?code=500');
    exit();
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<div class="container">
    <h1 class="mb-4">Edit Nutrition Entry</h1>

    <form method="POST"
        action="../../controllers/nutrition/UpdateController.php?id=<?php echo htmlspecialchars($nutrition['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date"
                class="form-control <?php echo isset($_SESSION['errors']['date']) ? 'is-invalid' : ''; ?>" name="date"
                id="date" value="<?php echo htmlspecialchars($nutrition['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required>
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['date']) ? htmlspecialchars($_SESSION['errors']['date'], ENT_QUOTES, 'UTF-8') : 'Please select a valid date.'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="food_item">Food Item</label>
            <input type="text"
                class="form-control <?php echo isset($_SESSION['errors']['food_item']) ? 'is-invalid' : ''; ?>"
                name="food_item" id="food_item"
                value="<?php echo htmlspecialchars($nutrition['food_item'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['food_item']) ? htmlspecialchars($_SESSION['errors']['food_item'], ENT_QUOTES, 'UTF-8') : 'Please enter the food item.'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="calories">Calories</label>
            <input type="number"
                class="form-control <?php echo isset($_SESSION['errors']['calories']) ? 'is-invalid' : ''; ?>"
                name="calories" id="calories"
                value="<?php echo htmlspecialchars($nutrition['calories'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                min="0">
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['calories']) ? htmlspecialchars($_SESSION['errors']['calories'], ENT_QUOTES, 'UTF-8') : 'Please enter a valid calorie count.'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="protein">Protein (g)</label>
            <input type="number"
                class="form-control <?php echo isset($_SESSION['errors']['protein']) ? 'is-invalid' : ''; ?>"
                name="protein" id="protein"
                value="<?php echo htmlspecialchars($nutrition['protein'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                min="0">
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['protein']) ? htmlspecialchars($_SESSION['errors']['protein'], ENT_QUOTES, 'UTF-8') : 'Please enter a valid protein amount.'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="carbs">Carbs (g)</label>
            <input type="number"
                class="form-control <?php echo isset($_SESSION['errors']['carbs']) ? 'is-invalid' : ''; ?>" name="carbs"
                id="carbs" value="<?php echo htmlspecialchars($nutrition['carbs'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required min="0">
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['carbs']) ? htmlspecialchars($_SESSION['errors']['carbs'], ENT_QUOTES, 'UTF-8') : 'Please enter a valid carbohydrate amount.'; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="fats">Fats (g)</label>
            <input type="number"
                class="form-control <?php echo isset($_SESSION['errors']['fats']) ? 'is-invalid' : ''; ?>" name="fats"
                id="fats" value="<?php echo htmlspecialchars($nutrition['fats'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                required min="0">
            <div class="invalid-feedback">
                <?php echo isset($_SESSION['errors']['fats']) ? htmlspecialchars($_SESSION['errors']['fats'], ENT_QUOTES, 'UTF-8') : 'Please enter a valid fat amount.'; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" name="ok">Update Entry</button>
        <a href="/fitness_tracker/public/nutrition" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
// Enhanced window-based error system
const windowErrorSystem = {
    show(message, type = 'error') {
        const errorWindow = document.createElement('div');
        errorWindow.className = `error-window animate__animated animate__shakeX`;
        errorWindow.innerHTML = `
            <div class="error-content">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles for the error window
        const style = document.createElement('style');
        style.innerHTML = `
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
        `;
        document.head.appendChild(style);
        document.body.appendChild(errorWindow);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            errorWindow.remove();
            style.remove();
        }, 5000);
    }
};

// Show errors from PHP session
<?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
<?php foreach ($_SESSION['errors'] as $error): ?>
windowErrorSystem.show('<?php echo addslashes(htmlspecialchars($error, ENT_QUOTES, 'UTF-8')); ?>');
<?php endforeach; ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
windowErrorSystem.show('<?php echo addslashes(htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8')); ?>');
<?php unset($_SESSION['error']); ?>
<?php endif; ?>
</script>

<?php 
// Clear errors after displaying them
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
include '../layouts/footer.php'; 
?>