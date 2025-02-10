<?php
include '../../views/layouts/header.php';
try {
    // [Security code remains unchanged]
} catch (RuntimeException $e) {
    $_SESSION['error_message'] = htmlspecialchars('A system error occurred. Please try again later.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
} catch (Exception $e) {
    $_SESSION['error_message'] = htmlspecialchars('An unexpected error occurred. Please contact support if the problem persists.', ENT_QUOTES, 'UTF-8');
    $_SESSION['error_type'] = 'danger';
}
?>

<link rel="stylesheet" href="<?= htmlspecialchars('../../views/workout/assets/bootstrap.css', ENT_QUOTES, 'UTF-8') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

<style>
:root {
    --perspective: 1000px;
    --card-tilt: 8deg;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-secondary: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

body {
    background: var(--gradient-primary);
    min-height: 100vh;
}

.workout-sidebar {
    position: -webkit-sticky;
    position: sticky;
    top: 100px;
    height: calc(100vh - 120px);
    overflow-y: auto;
    padding-right: 1.5rem;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-tool-card {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    margin-bottom: 1.5rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    padding: 1.5rem;
    perspective: var(--perspective);
    transform-style: preserve-3d;
    border-radius: 15px;
    backdrop-filter: blur(5px);
}

.sidebar-tool-card:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    transform: translateX(15px) rotateZ(2deg) scale(1.02);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg,
            rgba(255, 255, 255, 0.15) 0%,
            rgba(255, 255, 255, 0.05) 50%,
            rgba(255, 255, 255, 0.15) 100%);
    z-index: -1;
}

.search-section {
    max-width: 800px;
    margin: 3rem auto;
    padding: 0 1.5rem;
}

.search-input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 1.25rem 2rem;
    border-radius: 50px;
    width: 100%;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.search-input:focus {
    background: rgba(255, 255, 255, 0.2);
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.filter-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
    margin: 2rem 0;
}

.filter-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 30px;
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.filter-btn:hover {
    background: var(--gradient-secondary);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67, 233, 123, 0.3);
}

.workout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 2rem 0;
}

.program-card {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    transform-style: preserve-3d;
}

.program-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg,
            rgba(255, 255, 255, 0.1) 0%,
            rgba(255, 255, 255, 0.05) 50%,
            rgba(255, 255, 255, 0.1) 100%);
    z-index: 1;
}

.program-content {
    position: relative;
    z-index: 2;
    padding: 2.5rem;
    text-align: center;
}

.icon-container {
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease;
}

.icon-container span {
    display: inline-block;
    transition: transform 0.3s ease;
}

.program-card:hover .icon-container span {
    transform: scale(1.2) rotate(-5deg);
}
</style>

<div class="dashboard-container">
    <header class="dashboard-header text-center" data-aos="fade-down">
        <div class="glass-card mx-auto mb-4" style="max-width: 800px; padding: 3rem 2rem">
            <h1 class="display-4 font-weight-bold mb-3"
                style="background: var(--gradient-secondary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Fitness Nexus Pro
            </h1>
            <p class="lead text-light mb-0" style="opacity: 0.9; font-size: 1.25rem;">
                Precision Training Platform
            </p>
        </div>
    </header>

    <main class="workout-main">
        <div class="container-fluid px-lg-5">
            <div class="row">
                <!-- Professional Sidebar -->
                <div class="col-xl-3 col-lg-4 workout-sidebar" data-aos="fade-right">
                    <h3 class="h4 text-light mb-4"><i class="fas fa-analytics me-2"></i>Performance Suite</h3>
                    <?php
                    $toolCards = [
                        [
                            'title' => 'Biomechanics Calculator',
                            'description' => 'AI-Powered Load Optimization',
                            'link' => '#',
                            'icon' => '📊',
                            'color' => 'rgba(70, 130, 180, 0.2)'
                        ],
                        [
                            'title' => 'Strength Analytics',
                            'description' => '1RM Prediction Models',
                            'link' => '#',
                            'icon' => '📈',
                            'color' => 'rgba(60, 179, 113, 0.2)'
                        ],
                        [
                            'title' => 'Progress Matrix',
                            'description' => '3D Performance Tracking',
                            'link' => '#',
                            'icon' => '📅',
                            'color' => 'rgba(255, 165, 0, 0.2)'
                        ],
                        [
                            'title' => 'Mobility Protocol',
                            'description' => 'Dynamic Warm-up Systems',
                            'link' => '#',
                            'icon' => '🔥',
                            'color' => 'rgba(147, 51, 234, 0.2)'
                        ]
                    ];
                    
                    foreach ($toolCards as $card): ?>
                    <div class="sidebar-tool-card"
                        onclick="location.href='<?= htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8') ?>';"
                        style="background: <?= $card['color'] ?>">
                        <div class="d-flex align-items-center">
                            <span
                                class="h3 mb-0 me-3"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                            <div>
                                <h4 class="h6 mb-1"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                                <p class="small text-light mb-0 opacity-75">
                                    <?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Main Content -->
                <div class="col-xl-9 col-lg-8">
                    <!-- Enhanced Search -->
                    <div class="search-section" data-aos="fade-up">
                        <div class="position-relative">
                            <input type="text" class="search-input" placeholder="🔍 Search 5,000+ Exercises...">
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3">⌘/</span>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn">Biomechanical Focus</button>
                            <button class="filter-btn">Periodization</button>
                            <button class="filter-btn">Recovery</button>
                            <button class="filter-btn">Progression</button>
                        </div>
                    </div>

                    <!-- Training Programs -->
                    <div class="section-header mb-5" data-aos="fade-up">
                        <h2 class="h1"><i class="fas fa-microchip me-3"></i>Smart Training Systems</h2>
                    </div>
                    <div class="workout-grid">
                        <?php
                        $workoutCards = [
                            [
                                'title' => 'Neuro-Muscular Program',
                                'description' => '4-Day CNS Optimization Protocol',
                                'link' => '/app/views/workout/4d.php',
                                'icon' => '🧠',
                                'color' => 'rgba(46, 204, 113, 0.2)'
                            ],
                            [
                                'title' => 'Hypertrophy Matrix', 
                                'description' => '5-Day Metabolic Stress System',
                                'link' => '/app/views/workout/5d.php',
                                'icon' => '💥',
                                'color' => 'rgba(52, 152, 219, 0.2)'
                            ],
                            [
                                'title' => 'Adaptive Builder',
                                'description' => 'AI-Powered Program Design',
                                'link' => '/app/views/workout/custom.php',
                                'icon' => '🤖',
                                'color' => 'rgba(155, 89, 182, 0.2)'
                            ]
                        ];
                        
                        foreach ($workoutCards as $index => $card): ?>
                        <div class="glass-card program-card" data-aos="zoom-in" data-aos-delay="<?= $index * 150 ?>"
                            onclick="location.href='<?= htmlspecialchars($card['link'], ENT_QUOTES, 'UTF-8') ?>';"
                            style="background: <?= $card['color'] ?>">
                            <div class="program-content">
                                <div class="icon-container">
                                    <span
                                        class="display-3"><?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <h3 class="h2 mb-3"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="text-light opacity-85 mb-0">
                                    <?= htmlspecialchars($card['description'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-out-quint'
    });

    // Advanced Card Interactions
    document.querySelectorAll('.glass-card, .sidebar-tool-card').forEach(card => {
        const updateTransform = (e, element) => {
            const rect = element.getBoundingClientRect();
            const centerX = (rect.left + rect.right) / 2;
            const centerY = (rect.top + rect.bottom) / 2;
            const rotateX = -(e.clientY - centerY) / 25;
            const rotateY = (e.clientX - centerX) / 25;

            gsap.to(element, {
                duration: 0.8,
                rotateX: rotateX,
                rotateY: rotateY,
                ease: 'power2.out'
            });
        };

        card.addEventListener('mousemove', (e) => updateTransform(e, card));
        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                duration: 0.8,
                rotateX: 0,
                rotateY: 0,
                scale: 1,
                ease: 'power2.out'
            });
        });
    });

    // Dynamic Sidebar Parallax
    const sidebar = document.querySelector('.workout-sidebar');
    if (sidebar) {
        let scrollVelocity = 0;
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.scrollY;
            scrollVelocity = (currentScroll - lastScroll) * 0.1;
            lastScroll = currentScroll;

            gsap.to(sidebar, {
                duration: 0.8,
                y: Math.min(currentScroll * 0.1, 50) + scrollVelocity,
                ease: 'power2.out'
            });
        });
    }
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