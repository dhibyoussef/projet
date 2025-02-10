<?php
declare(strict_types=1);
session_start();
include '../../views/layouts/header.php';

// Enhanced CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    try {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            throw new InvalidArgumentException('Invalid or missing CSRF token');
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['error_message'] = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        header('Location: /app/views/workout/index.php');
        exit();
    }
}
?>

<main class="dashboard-container" role="main"
    style="background: radial-gradient(circle at 50% 0%, rgba(18,25,45,0.97) 20%, rgba(9,12,24,0.99)), url('/assets/images/quantum-fabric.jpg') fixed; background-size: cover; perspective: 2000px">

    <!-- Enhanced Navigation System -->
    <nav class="navbar navbar-expand-lg navbar-dark neuro-nav py-4" aria-label="Main navigation" data-aos="fade-down">
        <div class="container">
            <a class="navbar-brand neuro-pulse" href="/workout" aria-label="NeuroSynth Home">
                <div class="hologram-logo" role="img" aria-label="Holographic logo"></div>
                <span class="neon-cyber-text">NEUROSYNTH 4D</span>
            </a>

            <div class="dashboard-quickstats ms-4">
                <div class="stats-badge neuro-scan">
                    <div class="brainwave-animation" aria-hidden="true"></div>
                    <span class="badge bg-neuro">94% FOCUS</span>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item neuro-tab">
                        <a class="nav-link" href="/dashboard" aria-current="page">
                            <div class="nav-hologram" aria-hidden="true"></div>
                            <i class="fas fa-brain-circuit" aria-hidden="true"></i>
                            <span>Cortex Hub</span>
                        </a>
                    </li>
                    <li class="nav-item neuro-tab">
                        <a class="nav-link" href="/program-store">
                            <div class="nav-hologram" aria-hidden="true"></div>
                            <i class="fas fa-cubes-stacked" aria-hidden="true"></i>
                            <span>Program Nexus</span>
                        </a>
                    </li>
                    <li class="nav-item neuro-tab">
                        <a class="nav-link" href="/my-programs">
                            <div class="nav-hologram" aria-hidden="true"></div>
                            <i class="fas fa-dna-helix" aria-hidden="true"></i>
                            <span>Neuro Genome</span>
                        </a>
                    </li>
                </ul>

                <div class="user-controls">
                    <div class="cart-preview">
                        <form action="/cart/update" method="POST" class="cart-form">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button class="btn btn-neuro hologram-portal" id="cartPreview" aria-label="View cart">
                                <div class="cart-particle-field" aria-hidden="true"></div>
                                <i class="fas fa-cube" aria-hidden="true"></i>
                                <span class="badge bg-neuro">
                                    <?= count($_SESSION['cart'] ?? []) ?>/8 SLOTS
                                </span>
                                <div class="hologram-progress"
                                    style="--progress: <?= min((count($_SESSION['cart'] ?? [])/8)*100, 100) ?>%"
                                    aria-hidden="true"></div>
                            </button>

                            <div class="cart-dropdown neuro-card">
                                <h2 class="cart-heading">Neural Cache</h2>
                                <div class="cart-items">
                                    <?php if(isset($_SESSION['cart'])): ?>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="cart-item neuro-scan">
                                        <div class="dna-spiral" aria-hidden="true"></div>
                                        <i class="fas fa-dumbbell" aria-hidden="true"></i>
                                        <span><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <div class="cart-item-controls">
                                            <button class="btn btn-neuro-xs" name="adjust_quantity"
                                                value="<?= $item['id'] ?>" data-action="decrease">
                                                <span class="visually-hidden">Decrease quantity</span>
                                                <i class="fas fa-minus" aria-hidden="true"></i>
                                            </button>
                                            <span class="quantity"><?= $item['quantity'] ?></span>
                                            <button class="btn btn-neuro-xs" name="adjust_quantity"
                                                value="<?= $item['id'] ?>" data-action="increase">
                                                <span class="visually-hidden">Increase quantity</span>
                                                <i class="fas fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="cart-summary">
                                    <span>Neural Charge:</span>
                                    <span class="total"><?= number_format($_SESSION['neural_charge'] ?? 0) ?> NC</span>
                                </div>
                                <button class="btn btn-neuro" name="update_cart">
                                    <i class="fas fa-sync" aria-hidden="true"></i>
                                    <span>Sync Cart</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Optimized Dashboard Header -->
    <header class="dashboard-header" data-aos="fade-up">
        <div class="performance-card neuro-card">
            <div class="quantum-orbits" aria-hidden="true"></div>
            <h1 class="dashboard-title">Neural Forge Matrix</h1>
            <div class="performance-metrics">
                <div class="metric-card">
                    <div class="metric-visualization">
                        <i class="fas fa-pulse" aria-hidden="true"></i>
                        <span>5.2GHz</span>
                    </div>
                    <div class="metric-details">
                        <span>Cortex Speed</span>
                        <div class="live-data" aria-live="polite"></div>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-visualization">
                        <i class="fas fa-bolt" aria-hidden="true"></i>
                        <span>1,480 NC</span>
                    </div>
                    <div class="metric-details">
                        <span>Neuro Charge</span>
                        <div class="live-data" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Finalized Program Matrix -->
    <div class="program-matrix">
        <div class="matrix-controls">
            <h2>Neural Protocol Matrix</h2>
            <div class="filter-controls">
                <button class="btn btn-neuro" data-filter="all">All Dimensions</button>
                <button class="btn btn-neuro" data-filter="active">Active Protocols</button>
            </div>
        </div>

        <div class="program-grid">
            <?php if(isset($_SESSION['workout_plan'])): ?>
            <?php foreach ($_SESSION['workout_plan'] as $day => $exercises): ?>
            <article class="program-card neuro-card">
                <div class="card-header">
                    <h3><?= htmlspecialchars($day, ENT_QUOTES, 'UTF-8') ?></h3>
                    <form action="/cart/add" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="program_id" value="<?= $day ?>">
                        <button class="btn btn-neuro add-to-cart" aria-label="Add to cart">
                            <i class="fas fa-cube" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                <ul class="exercise-list">
                    <?php foreach ($exercises as $exercise => $videoId): ?>
                    <li class="exercise-item">
                        <button class="exercise-preview" data-video-id="<?= $videoId ?>" aria-label="Preview exercise">
                            <div class="exercise-visualization">
                                <div class="quantum-spinner" aria-hidden="true"></div>
                            </div>
                            <div class="exercise-details">
                                <span><?= htmlspecialchars($exercise, ENT_QUOTES, 'UTF-8') ?></span>
                                <div class="performance-indicator">
                                    <div class="progress-bar" style="width: 91%"></div>
                                </div>
                            </div>
                            <span class="ai-badge">AI Analysis</span>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Performance Analytics Module -->
    <section class="analytics-section">
        <div class="analytics-grid">
            <div class="analytics-card neuro-card">
                <h3>Performance Calculator</h3>
                <div class="calculator-interface">
                    <div class="calculator-display">
                        <div class="matrix-visualization" aria-hidden="true"></div>
                    </div>
                    <div class="calculator-controls">
                        <button class="btn btn-neuro" data-calculation="1rm">
                            <i class="fas fa-rocket-launch" aria-hidden="true"></i>
                            <span>1RM</span>
                        </button>
                        <button class="btn btn-neuro" data-calculation="volume">
                            <i class="fas fa-wave-pulse" aria-hidden="true"></i>
                            <span>Volume</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="analytics-card neuro-card">
                <h3>Biometric Visualization</h3>
                <div class="biometric-graph">
                    <svg class="neural-path" aria-labelledby="graphTitle">
                        <title id="graphTitle">Performance Metrics Visualization</title>
                        <path class="quantum-glow-path" d="M10 50 Q 40 15 50 50 T 90 50" />
                    </svg>
                </div>
                <div class="graph-legend">
                    <span class="legend-item">Synaptic Load</span>
                    <span class="legend-item">Quantum Output</span>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Enhanced Analysis Modal -->
<div class="modal fade" id="analysisModal" tabindex="-1" aria-labelledby="analysisModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content neuro-card">
            <div class="modal-header">
                <h4 class="modal-title">Exercise Analytics</h4>
            </div>
            <div class="modal-body">
                <div class="biometric-visualization">
                    <div class="muscle-engagement-map" role="img" aria-label="Muscle activation heatmap"></div>
                    <div class="performance-metrics">
                        <div class="metric">
                            <span>Neural Activation</span>
                            <span class="value">96%</span>
                        </div>
                        <div class="metric">
                            <span>Form Efficiency</span>
                            <span class="value">5.1σ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new NeuroDashboard({
        particleSystem: {
            selector: '.neuro-particles',
            maxParticles: 50
        },
        metrics: {
            refreshInterval: 5000,
            endpoints: {
                performance: '/api/performance'
            }
        }
    });

    const throttle = (func, limit) => {
        let lastFunc;
        let lastRan;
        return function() {
            const context = this;
            const args = arguments;
            if (!lastRan) {
                func.apply(context, args);
                lastRan = Date.now();
            } else {
                clearTimeout(lastFunc);
                lastFunc = setTimeout(function() {
                    if ((Date.now() - lastRan) >= limit) {
                        func.apply(context, args);
                        lastRan = Date.now();
                    }
                }, limit - (Date.now() - lastRan));
            }
        };
    };

    const initCardInteractions = () => {
        document.querySelectorAll('.neuro-card').forEach(card => {
            const updateCardTransform = (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width;
                const y = (e.clientY - rect.top) / rect.height;

                card.style.transform = `
                    perspective(1500px)
                    rotateX(${(y - 0.5) * 15}deg)
                    rotateY(${(x - 0.5) * -15}deg)
                    translateZ(${Math.abs(x - 0.5) * 30}px)
                `;
            };

            card.addEventListener('mousemove', throttle(updateCardTransform, 50));
            card.addEventListener('mouseleave', () => {
                card.style.transform =
                    'perspective(1500px) rotateX(0) rotateY(0) translateZ(0)';
            });
        });
    };

    const initCartSystem = () => {
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const form = e.target.closest('form');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    updateCartUI(data);
                    showNotification('Program added to neural cache');

                } catch (error) {
                    console.error('Cart update error:', error);
                    showNotification('Failed to update neural cache', 'error');
                }
            });
        });
    };

    const updateCartUI = (data) => {
        const cartBadge = document.querySelector('#cartPreview .badge');
        const progressBar = document.querySelector('.hologram-progress');

        if (cartBadge) cartBadge.textContent = `${data.cartCount}/8 SLOTS`;
        if (progressBar) progressBar.style.setProperty('--progress', `${data.progress}%`);
    };

    const showNotification = (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `neuro-notification ${type}`;
        notification.textContent = message;
        notification.setAttribute('role', 'alert');
        document.body.appendChild(notification);

        setTimeout(() => notification.remove(), 3000);
    };

    initCardInteractions();
    initCartSystem();
});
</script>

<?php
include '../../views/layouts/footer.php';
?>