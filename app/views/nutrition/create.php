<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /fitness_tracker/views/user/login.php');
    exit();
}

include '../layouts/header.php'; 
?>
<div class="container mt-5">
    <h1 class="mb-4">Log Your Meal</h1>
    <form method="POST" action="../../controllers/nutrition/CreateController.php" class="needs-validation" novalidate>
        <?php if (isset($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">CSRF token missing. Please refresh the page and try again.</div>
        <?php endif; ?>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
            <div class="invalid-feedback">Please select a date.</div>
        </div>
        <div class="form-group">
            <label for="food_item">Food Item</label>
            <input type="text" class="form-control" name="food_item" id="food_item" required>
            <div class="invalid-feedback">Please enter the food item.</div>
        </div>
        <div class="form-group">
            <label for="calories">Calories</label>
            <input type="number" class="form-control" name="calories" id="calories" required>
            <div class="invalid-feedback">Please enter the calorie count.</div>
        </div>
        <div class="form-group">
            <label for="protein">Protein (g)</label>
            <input type="number" class="form-control" name="protein" id="protein" required>
            <div class="invalid-feedback">Please enter the protein amount.</div>
        </div>
        <div class="form-group">
            <label for="carbs">Carbs (g)</label>
            <input type="number" class="form-control" name="carbs" id="carbs" required>
            <div class="invalid-feedback">Please enter the carbohydrate amount.</div>
        </div>
        <div class="form-group">
            <label for="fats">Fats (g)</label>
            <input type="number" class="form-control" name="fats" id="fats" required>
            <div class="invalid-feedback">Please enter the fat amount.</div>
        </div>
        <button type="submit" class="btn btn-success" name="ok">Log Meal</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>