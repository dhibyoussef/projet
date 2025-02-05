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
<link rel="stylesheet" href="/fitness_tracker/public/assets/css/bootstrap.min.css">
<div class="container">
    <h1 class="mb-4">Nutrition Entry Details</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?> alert-dismissible fade show"
        role="alert">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
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
        <a href="edit.php?id=<?php echo $nutrition['id']; ?>" class="btn btn-warning">Edit Entry</a>
        <a href="index.php" class="btn btn-secondary">Back to Nutrition Log</a>
    </div>
</div>
<?php include '../layouts/footer.php'; ?>