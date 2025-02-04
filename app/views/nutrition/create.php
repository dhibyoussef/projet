<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Log Your Meal</h1>
    <form method="POST" action="../../controllers/nutrition/CreateController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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