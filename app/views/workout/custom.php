<?php
include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layouts/header.php';
try {
    session_start();
    
    // CSRF protection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new RuntimeException('Invalid CSRF token');
        }
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Input sanitization
    $muscle_group = filter_input(INPUT_GET, 'muscle_group', FILTER_SANITIZE_STRING);
    $search_query = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
    $post_data = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

} catch (RuntimeException $e) {
    $_SESSION['error_message'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
} catch (Exception $e) {
    $_SESSION['error_message'] = htmlspecialchars('Application error detected. Our engineering team has been notified.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
}
?>

<link rel="stylesheet" href="<?= htmlspecialchars('../../views/workout/assets/bootstrap.css', ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<div class="dashboard-container"
    style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= htmlspecialchars('/assets/images/gym-bg-3d.jpg', ENT_QUOTES, 'UTF-8') ?>') fixed; background-size: cover; background-position: center">

    <!-- Primary Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark glass-nav py-3" data-aos="fade-down"
        style="transform-style: preserve-3d; backdrop-filter: blur(25px)">
        <div class="container">
            <a class="navbar-brand brand-logo" href="<?= htmlspecialchars('/workout', ENT_QUOTES, 'UTF-8') ?>"
                style="transform: translateZ(30px)">
                <i class="fas fa-dumbbell me-2"></i>FITBUILDER PRO
                <div class="logo-highlight"></div>
            </a>
            <div class="d-flex align-items-center gap-4">
                <div class="cart-indicator hover-3d animate__animated animate__pulse" data-bs-toggle="offcanvas"
                    data-bs-target="#workoutCart">
                    <div class="cart-indicator__orbit">
                        <div class="orbit-dot" style="--i:1"></div>
                        <div class="orbit-dot" style="--i:2"></div>
                    </div>
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-indicator__badge">3</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Performance Dashboard -->
    <header class="dashboard-header" data-aos="fade-down">
        <div class="container">
            <div class="metrics-grid mb-5">
                <div class="glass-card hover-3d performance-widget" data-aos="zoom-in">
                    <div class="widget-connection"></div>
                    <div class="card-body">
                        <h5 class="metric-title"><i class="fas fa-brain me-2"></i>Performance Readiness</h5>
                        <div class="progress-indicator">
                            <div class="progress-bar" style="width: 92%">
                                <div class="progress-bar__liquid"></div>
                            </div>
                            <div class="progress-bar__pulse"></div>
                        </div>
                        <div class="h2 mt-2 metric-value">92%</div>
                    </div>
                </div>
            </div>

            <div class="main-card glass-card hover-3d mx-auto program-interface" data-aos="zoom-in">
                <div class="interface-glare"></div>
                <div class="card-body text-center">
                    <h1 class="display-5 mb-3 interface-title">
                        Workout Architect v3.1.4
                    </h1>
                    <form method="post" action="/workout/create">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle me-2"></i>Create Program
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Workout Composition Interface -->
    <main class="dashboard-main">
        <div class="container-fluid">
            <div class="workflow-grid">
                <div class="exercise-palette glass-card hover-3d exercise-panel">
                    <div class="card-body">
                        <h4 class="panel-title"><i class="fas fa-list-ul me-2"></i>Exercise Catalog</h4>
                        <div class="exercise-selector">
                            <div class="exercise-option"
                                data-muscle-group="<?= htmlspecialchars($muscle_group ?? 'chest', ENT_QUOTES, 'UTF-8') ?>">
                                <span>Chest</span>
                                <div class="option-highlight"></div>
                            </div>
                        </div>
                        <div class="search-container">
                            <input type="text" class="form-control search-input"
                                placeholder="<?= htmlspecialchars('Search exercises...', ENT_QUOTES, 'UTF-8') ?>"
                                data-search-predict="true"
                                value="<?= htmlspecialchars($search_query ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="search-effects"></div>
                        </div>
                    </div>
                </div>

                <div class="workout-canvas glass-card hover-3d program-builder">
                    <div class="card-body">
                        <h4 class="builder-title"><i class="fas fa-layer-group me-2"></i>Workout Builder</h4>
                        <div class="program-timeline">
                            <div class="phase-container warmup" data-phase="warmup">
                                <div class="phase-connector"></div>
                                <div class="phase-header">
                                    <i class="fas fa-fire me-2"></i>Warmup Phase
                                </div>
                                <div class="exercise-container">
                                    <div class="container-effects"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Training Program Cart -->
    <div class="offcanvas offcanvas-end glass-nav program-cart" tabindex="-1" id="workoutCart">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title"><i class="fas fa-list-alt me-2"></i>Program Summary</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="cart-items">
                <div class="cart-item exercise-preview">
                    <div class="preview-visual">
                        <div class="muscle-visual chest"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --perspective: 1000px;
    --card-tilt: 8deg;
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.float-effect {
    animation: subtle-float 3s ease-in-out infinite;
}

@keyframes subtle-float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-5px);
    }
}

.exercise-panel {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
}
</style>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    AOS.init({
        duration: 800,
        once: true,
        easing: 'ease-out-quint',
        disable: 'mobile'
    });

    // Card interaction logic
    document.querySelectorAll('.glass-card').forEach(cardElement => {
        const updateCardTransform = (event) => {
            const rect = cardElement.getBoundingClientRect();
            const rotateY = (event.clientX - rect.left - rect.width / 2) / 25;
            const rotateX = -(event.clientY - rect.top - rect.height / 2) / 25;

            gsap.to(cardElement, {
                duration: 0.6,
                rotateX: rotateX,
                rotateY: rotateY,
                ease: 'power2.out'
            });
        };

        cardElement.addEventListener('mousemove', updateCardTransform);
        cardElement.addEventListener('mouseleave', () => {
            gsap.to(cardElement, {
                duration: 0.6,
                rotateX: 0,
                rotateY: 0,
                ease: 'power2.out'
            });
        });
    });
});
</script>

<?php
try {
    include $_SERVER['DOCUMENT_ROOT'] . '/app/views/layouts/footer.php';
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = htmlspecialchars('Failed to load page footer. Please try again.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
}
?>