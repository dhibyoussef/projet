<?php include '../layouts/header.php'; ?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Edit Workout</h1>
    <form method="POST" action="../../controllers/workout/UpdateController.php?id=<?php echo $workout['id']; ?>"
        class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="form-group">
            <label for="name">Workout Name</label>
            <input type="text" class="form-control" name="name" id="name"
                value="<?php echo htmlspecialchars($workout['name']); ?>" required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3"
                required><?php echo htmlspecialchars($workout['description']); ?></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="duration">Duration (minutes)</label>
            <input type="number" class="form-control" name="duration" id="duration"
                value="<?php echo htmlspecialchars($workout['duration']); ?>" required>
            <div class="invalid-feedback">Please enter the duration of the workout.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Workout</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>