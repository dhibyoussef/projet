        <?php
        session_start();
        $isLoggedIn = isset($_SESSION['user_id']);
        $currentLang = $_SESSION['lang'] ?? 'en';
        ?>

        <!DOCTYPE html>
        <html lang="<?php echo $currentLang; ?>" class="h-100" data-theme="dark">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description"
                content="Fitness tracking platform with workout programs, nutrition plans, and progress analytics">
            <title>Fitness Tracker | Transform Your Body</title>
            <link rel="stylesheet" href="public/assets/css/home/bootstrap.css">
            <link rel="stylesheet" href="/public/assets/css/styles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
            <meta name="csrf-token"
                content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        </head>

        <body class="d-flex flex-column h-100">
            <?php if ($isLoggedIn): ?>
            <div class="user-menu"
                style="position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
                <form action="/app/controllers/LanguageController.php" method="POST" class="language-selector">
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="en" <?php echo $currentLang === 'en' ? 'selected' : ''; ?>>English</option>
                        <option value="fr" <?php echo $currentLang === 'fr' ? 'selected' : ''; ?>>Français</option>
                        <option value="ar" <?php echo $currentLang === 'ar' ? 'selected' : ''; ?>>العربية</option>
                    </select>
                </form>
                <button class="btn btn-glass" onclick="toggleTheme()">
                    <i class="fas fa-moon"></i>
                </button>
                <a href="/app/views/user/profile.php" class="btn btn-glass">
                    <i class="fas fa-user-circle"></i>
                </a>
                <form action="/app/views/user/logout.php" method="POST">
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-glass-danger">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>

            <main class="container py-5" style="margin-top: 80px;">
                <div class="dashboard-section">
                    <a href="/app/controllers/workout/ReadController.php"
                        class="glass-container dashboard-card workout-card">
                        <div class="card-content text-center p-4">
                            <i class="fas fa-dumbbell fa-3x mb-3"></i>
                            <h2>Workouts</h2>
                            <p>4-day & 5-day programs</p>
                        </div>
                    </a>
                    <a href="/app/controllers/nutrition/ReadController.php"
                        class="glass-container dashboard-card nutrition-card">
                        <div class="card-content text-center p-4">
                            <i class="fas fa-utensils fa-3x mb-3"></i>
                            <h2>Nutrition</h2>
                            <p>Meal plans & tracking</p>
                        </div>
                    </a>
                    <a href="/app/controllers/progress/ReadController.php"
                        class="glass-container dashboard-card progress-card">
                        <div class="card-content text-center p-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <h2>Progress</h2>
                            <p>Analytics & reports</p>
                        </div>
                    </a>
                </div>
            </main>
            <?php else: ?>
            <nav class="navbar navbar-expand-lg navbar-dark bg-glass">
                <div class="container">
                    <a class="navbar-brand" href="/">
                        <i class="fas fa-heartbeat"></i> FitTrack
                    </a>

                </div>
            </nav>

            <main class="flex-grow-1">
                <div class="hero-section glass-container text-center py-5 my-auto">
                    <?php include 'app/views/layouts/messages.php'; ?>
                    <h1 class="display-4 fw-bold mb-4">Transform Your Fitness Journey</h1>
                    <div class="cta-buttons">
                        <a href="/app/views/user/login.php" class="btn btn-glass-primary btn-lg mx-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            </main>
            <?php endif; ?>

            <footer class="footer glass-container mt-auto">
                <div class="container py-3">
                    <div class="text-center">
                        <p>&copy; <?php echo date("Y"); ?> FitTrack. All rights reserved.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/privacy" class="btn btn-link">Privacy</a>
                            <a href="/terms" class="btn btn-link">Terms</a>
                            <a href="/contact" class="btn btn-link">Contact</a>
                        </div>
                    </div>
                </div>
            </footer>

            <script>
            function toggleTheme() {
                const htmlEl = document.documentElement;
                const currentTheme = htmlEl.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                htmlEl.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            }
            </script>
        </body>

        </html>