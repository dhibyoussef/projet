<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Log Your Progress</h1>
    <form method="POST" action="../../controllers/progress/CreateController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
            <div class="invalid-feedback">Please select a date.</div>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" class="form-control" name="weight" id="weight" required>
            <div class="invalid-feedback">Please enter your weight.</div>
        </div>
        <div class="form-group">
            <label for="body_fat">Body Fat (%)</label>
            <input type="number" class="form-control" name="body_fat" id="body_fat" required>
            <div class="invalid-feedback">Please enter your body fat percentage.</div>
        </div>
        <button type="submit" class="btn btn-success" name="create">Log Progress</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>