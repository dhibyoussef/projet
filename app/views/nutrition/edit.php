<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

include '../layouts/header.php'; 
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Edit Nutrition Entry</h1>
    <form method="POST"
        action="../../controllers/nutrition/UpdateController.php?id=<?php echo htmlspecialchars($nutrition['id']); ?>"
        class="needs-validation" novalidate>
        <?php if (isset($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">CSRF token missing. Please refresh the page and try again.</div>
        <?php endif; ?>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date"
                value="<?php echo htmlspecialchars($nutrition['date']); ?>" required>
            <div class="invalid-feedback">Please select a valid date.</div>
        </div>

        <div class="form-group">
            <label for="food_item">Food Item</label>
            <input type="text" class="form-control" name="food_item" id="food_item"
                value="<?php echo htmlspecialchars($nutrition['food_item']); ?>" required>
            <div class="invalid-feedback">Please enter the food item.</div>
        </div>

        <div class="form-group">
            <label for="calories">Calories</label>
            <input type="number" class="form-control" name="calories" id="calories"
                value="<?php echo htmlspecialchars($nutrition['calories']); ?>" required min="0">
            <div class="invalid-feedback">Please enter a valid calorie count.</div>
        </div>

        <div class="form-group">
            <label for="protein">Protein (g)</label>
            <input type="number" class="form-control" name="protein" id="protein"
                value="<?php echo htmlspecialchars($nutrition['protein']); ?>" required min="0">
            <div class="invalid-feedback">Please enter a valid protein amount.</div>
        </div>

        <div class="form-group">
            <label for="carbs">Carbs (g)</label>
            <input type="number" class="form-control" name="carbs" id="carbs"
                value="<?php echo htmlspecialchars($nutrition['carbs']); ?>" required min="0">
            <div class="invalid-feedback">Please enter a valid carbohydrate amount.</div>
        </div>

        <div class="form-group">
            <label for="fats">Fats (g)</label>
            <input type="number" class="form-control" name="fats" id="fats"
                value="<?php echo htmlspecialchars($nutrition['fats']); ?>" required min="0">
            <div class="invalid-feedback">Please enter a valid fat amount.</div>
        </div>

        <button type="submit" class="btn btn-primary" name="ok">Update Entry</button>
        <a href="/fitness_tracker/public/nutrition" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>