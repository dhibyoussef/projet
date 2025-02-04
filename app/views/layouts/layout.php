<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/assets/css/style.css">
</head>

<body>
    <?php include '../app/views/layouts/header.php'; ?>
    <main class="container">
        <?php include '../app/views/layouts/messages.php'; ?>
        <?php echo $content; ?>
    </main>
    <?php include '../app/views/layouts/footer.php'; ?>
    <script src="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/assets/js/main.js"></script>
</body>

</html>