<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if it doesn't exist
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker'); ?></title>
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container">
        <?php include 'messages.php'; ?>
        <?php echo $content; ?>
    </main>
    <?php include 'footer.php'; ?>
    <script src="/fitness_tracker/public/assets/js/main.js"></script>
    <script>
    // Store CSRF token in JavaScript variable for AJAX requests
    const csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
    </script>
</body>

</html>