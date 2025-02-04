<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tracker - Your Path to Wellness</title>
    <link rel="stylesheet" href="public/assets/css/home/bootstrap.css">
    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="index.php">Fitness Tracker</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="app/controllers/workout/ReadController.php">Workouts</a>
                    <a class="nav-link" href="/app/views/nutrition/index.php">Nutrition</a>
                    <a class="nav-link" href="/app/views/progress/index.php">Progress</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container text-center" style="margin-top: 100px;">
        <?php include 'app/views/layouts/messages.php'; ?>
        <h1>Welcome to Your Fitness Tracker</h1>
        <p>Please log in or sign up to continue.</p>
        <div class="d-flex justify-content-center">
            <a href="/app/views/user/login.php" class="btn btn-lg btn-primary">Log In</a>
            <a href="/app/views/user/signup.php" class="btn btn-lg btn-secondary">Sign Up</a>
        </div>
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> Fitness Tracker. All rights reserved.</p>
            <nav class="mt-2">
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="" class="text-light">Privacy Policy</a></li>
                    <li class="list-inline-item"><a href="" class="text-light">Terms of Service</a></li>
                    <li class="list-inline-item"><a href="" class="text-light">Contact Us</a></li>
                </ul>
            </nav>
        </div>
    </footer>
</body>

</html>