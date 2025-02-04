<?php include '../../views/layouts/header.php'; ?>

<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Add New Workout</h1>
    <form method="POST" action="../../controllers/workout/CreateController.php" class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="workoutName">Workout Name</label>
            <input type="text" class="form-control" name="workoutName" id="workoutName" placeholder="Enter workout name"
                required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="workoutDescription">Description</label>
            <textarea class="form-control" name="workoutDescription" id="workoutDescription" rows="3"
                placeholder="Enter a brief description" required></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="workoutDuration">Duration (minutes)</label>
            <input type="number" class="form-control" name="workoutDuration" id="workoutDuration" required>
            <div class="invalid-feedback">Please enter the duration of the workout.</div>
        </div>
        <button type="submit" class="btn btn-primary " name="ok">Add Workout</button>
    </form>
</div>
<?php include '../../views/layouts/footer.php'; ?>