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
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token: ' . $e->getMessage());
            }
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /fitness_tracker/views/user/login.php');
        exit();
    }

    include '../layouts/header.php'; 
} catch (Exception $e) {
    error_log('Error in nutrition/create.php: ' . $e->getMessage());
    header('Location: /fitness_tracker/public/error?code=500');
    exit();
}
?>
<div class="container mt-5">
    <h1 class="mb-4">Log Your Meal</h1>
    <form method="POST" action="../../controllers/nutrition/CreateController.php" class="needs-validation" novalidate
        id="meal-form">
        <?php if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_token_expire']) && time() < $_SESSION['csrf_token_expire']): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="csrf_token_expire"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token_expire'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">CSRF token expired or missing. Please refresh the page and try again.</div>
        <?php endif; ?>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required max="<?php echo date('Y-m-d'); ?>">
            <div class="invalid-feedback">Please select a valid date.</div>
        </div>
        <div class="form-group">
            <label for="food_item">Food Item</label>
            <input type="text" class="form-control" name="food_item" id="food_item" required maxlength="100">
            <div class="invalid-feedback">Please enter a valid food item (max 100 characters).</div>
        </div>
        <div class="form-group">
            <label for="calories">Calories</label>
            <input type="number" class="form-control" name="calories" id="calories" required min="0" max="10000">
            <div class="invalid-feedback">Please enter a valid calorie count (0-10000).</div>
        </div>
        <div class="form-group">
            <label for="protein">Protein (g)</label>
            <input type="number" class="form-control" name="protein" id="protein" required min="0" max="1000">
            <div class="invalid-feedback">Please enter a valid protein amount (0-1000g).</div>
        </div>
        <div class="form-group">
            <label for="carbs">Carbs (g)</label>
            <input type="number" class="form-control" name="carbs" id="carbs" required min="0" max="1000">
            <div class="invalid-feedback">Please enter a valid carbohydrate amount (0-1000g).</div>
        </div>
        <div class="form-group">
            <label for="fats">Fats (g)</label>
            <input type="number" class="form-control" name="fats" id="fats" required min="0" max="1000">
            <div class="invalid-feedback">Please enter a valid fat amount (0-1000g).</div>
        </div>
        <button type="submit" class="btn btn-success" name="ok">Log Meal</button>
    </form>
</div>
<script>
document.getElementById('meal-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                windowErrorSystem.show(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            windowErrorSystem.show('An unexpected error occurred. Please try again.');
        });
});
</script>
<?php include '../layouts/footer.php'; ?>