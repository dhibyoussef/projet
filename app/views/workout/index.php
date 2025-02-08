<?php
include '../../views/layouts/header.php';
try {
    // Enhanced secure session initialization
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new RuntimeException('Failed to set secure session parameters');
        }
        
        if (!session_start()) {
            throw new RuntimeException('Failed to start session');
        }
        
        // Generate CSRF token with expiration and additional security
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new RuntimeException('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_expire'] = time() + 3600;
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        }
    }

    // Enhanced authentication check
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || 
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
        $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        header('Location: /login');
        exit();
    }

    // Session security with fingerprinting
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = htmlspecialchars('A system error occurred. Please try again later.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
} catch (Exception $e) {
    $_SESSION['error_message'] = htmlspecialchars('An unexpected error occurred. Please contact support if the problem persists.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
}
?>
<!-- Enhanced 3D design assets -->
<link rel="stylesheet" href="<?= htmlspecialchars('../../views/workout/assets/bootstrap.css', ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://unpkg.com/css-doodle@0.38.3/css-doodle.min.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/css-doodle@0.38.3/css-doodle.min.js"></script>

<style>

</style>

<div class="dashboard-container">
    <div class="dashboard-header text-center mb-5" data-aos="fade-down" data-aos-duration="800">
        <h1 class="display-4 font-weight-bold mb-3">Workout Dashboard</h1>
        <p class="lead text-muted">Your 3D Fitness Universe</p>
    </div>

    <?php
    $workoutCards = [
        [
            'title' => '4 Days Workout',
            'description' => 'Holographic Training Interface',
            'link' => 'app/controllers/workout/ReadController.php?workout=4day',
            'animation' => 'flip-up',
            'icon' => '🏋️',
            'color' => 'hsla(145, 63%, 49%, 0.8)'
        ],
        [
            'title' => '5 Days Workout',
            'description' => 'Neural Network Optimized Plan',
            'link' => 'app/controllers/workout/ReadController.php?workout=5day',
            'animation' => 'flip-left',
            'icon' => '💪',
            'color' => 'hsla(217, 89%, 61%, 0.8)'
        ],
        [
            'title' => 'AI Workout Architect',
            'description' => '3D Drag-and-Drop Builder',
            'link' => 'app/views/workout/personalized_workout.php',
            'animation' => 'flip-right',
            'icon' => '✨',
            'color' => 'hsla(45, 100%, 51%, 0.8)'
        ]
    ];
    ?>

    <div class="workout-grid">
        <?php foreach ($workoutCards as $card): ?>
        <div class="card workout-card" data-aos="<?= htmlspecialchars($card['animation'], ENT_QUOTES, 'UTF-8') ?>"
            data-aos-delay="<?= rand(100, 300) ?>" data-aos-duration="1000"
            onclick="location.href='<?= htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8') ?>';"
            style="--card-color: <?= htmlspecialchars($card['color'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="card-body">
                <div class="icon-container">
                    <span class="card-icon"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <h3 class="card-title h3 mb-4"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p class="card-text text-muted"><?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
    <div
        class="alert alert-<?= htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8') ?> animate__animated animate__fadeIn">
        <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    <div
        class="alert alert-<?= htmlspecialchars($_SESSION['error_type'], ENT_QUOTES, 'UTF-8') ?> animate__animated animate__shakeX">
        <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['error_message'], $_SESSION['error_type']); endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1200,
        once: true,
        easing: 'ease-out-back'
    });

    // Alert auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('animate__fadeOut');
            setTimeout(() => alert.remove(), 1000);
        }, 5000);
    });

    // Smooth card interactions
    document.querySelectorAll('.workout-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const centerX = (rect.left + rect.right) / 2;
            const centerY = (rect.top + rect.bottom) / 2;
            const rotateX = -(e.clientY - centerY) / 15;
            const rotateY = (e.clientX - centerX) / 15;

            card.style.setProperty('--rotate-x', `${rotateX}deg`);
            card.style.setProperty('--rotate-y', `${rotateY}deg`);
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                duration: 0.8,
                rotateX: 0,
                rotateY: 0,
                ease: 'elastic.out(1, 0.5)'
            });
        });
    });

    // [Previous alert and animation code remains unchanged]
});
</script>

<?php
try {
    $footerPath = '../../views/layouts/footer.php';
    if (!file_exists($footerPath)) {
        throw new RuntimeException('Footer file not found');
    }
    include $footerPath;
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = htmlspecialchars('Failed to load page footer. Please try again.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
}
?>