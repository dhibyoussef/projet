<nav class="navbar navbar-expand-lg navbar-dark neuro-nav py-4" aria-label="Main navigation" data-aos="fade-down"
    style="background: rgba(0,10,20,0.98); backdrop-filter: blur(25px); border-bottom: 1px solid rgba(0,255,136,0.15);">
    <div class="container">
        <a class="navbar-brand neuro-pulse" href="/workout" aria-label="Training Nexus Home">
            <div class="hologram-logo" role="img" aria-label="Holographic logo"></div>
            <span class="neon-cyber-text">NEXUS 5D TRAINING PLATFORM</span>
        </a>

        <div class="dashboard-quickstats ms-4">
            <div class="stats-badge neuro-scan">
                <div class="brainwave-animation" aria-hidden="true"></div>
                <span class="badge bg-neuro"><?= number_format($programIntensity, 0) ?>% INTENSITY</span>
            </div>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto neuro-tabs">
                <?php foreach ($navItems as $navItem): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $navItem['active'] ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($navItem['link'], ENT_QUOTES, 'UTF-8') ?>"
                        aria-label="<?= htmlspecialchars($navItem['text'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="nav-hologram" aria-hidden="true"></div>
                        <i class="fas fa-<?= htmlspecialchars($navItem['icon'], ENT_QUOTES, 'UTF-8') ?>"
                            aria-hidden="true"></i>
                        <?= htmlspecialchars($navItem['text'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="neuro-controls">
                <div class="cart-preview neuro-scan">
                    <a class="btn btn-icon position-relative" href="/cart" data-bs-toggle="tooltip"
                        data-neuro-tooltip="Training Program Cart">
                        <div class="neural-glow"></div>
                        <i class="fas fa-shopping-basket"></i>
                        <span class="badge rounded-pill bg-accent hologram-badge">
                            <?= htmlspecialchars($cartItems, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </a>
                </div>
                <a class="btn btn-icon profile-link neuro-scan" href="/profile">
                    <div class="neural-glow"></div>
                    <i class="fas fa-user-shield"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="training-interface container-fluid neuro-interface"
    style="background: linear-gradient(180deg, rgba(0,15,30,0.98) 0%, rgba(0,7,14,0.95) 100%);">
    <div class="container pt-5">
        <div class="dashboard-grid" data-aos="fade-in">
            <!-- Performance Metrics Card -->
            <div class="dashboard-card performance-metrics neuro-card">
                <div class="card-header">
                    <h3><i class="fas fa-analytics me-2"></i>Performance Metrics</h3>
                    <div class="neural-pulse"></div>
                </div>
                <div class="hologram-progress" style="--progress: <?= number_format($progress, 0) ?>%">
                    <div class="progress-label neuro-text">
                        <?= number_format($progress, 0) ?>% COMPLETE
                    </div>
                </div>
                <div class="metrics-grid">
                    <div class="metric-card neuro-card">
                        <div class="metric-header">
                            <i class="fas fa-heartbeat"></i>
                            <span>Heart Rate</span>
                        </div>
                        <div class="metric-value neuro-text">
                            <?= number_format($currentHeartRate, 0) ?> BPM
                        </div>
                    </div>
                    <div class="metric-card neuro-card">
                        <div class="metric-header">
                            <i class="fas fa-burn"></i>
                            <span>Caloric Expenditure</span>
                        </div>
                        <div class="metric-value neuro-text">
                            <?= number_format($caloriesBurned, 0) ?> kcal
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Program Modules -->
            <?php foreach ($trainingDays as $index => $day): ?>
            <div class="training-module neuro-card">
                <div class="module-header">
                    <h4><?= htmlspecialchars($day['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                    <span class="badge bg-accent hologram-badge">DAY <?= $index + 1 ?></span>
                </div>
                <div class="exercise-list">
                    <?php foreach ($day['exercises'] as $exercise): ?>
                    <div class="exercise-item neuro-card">
                        <div class="exercise-info">
                            <span
                                class="exercise-name"><?= htmlspecialchars($exercise['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <div class="hologram-progress"
                                style="--progress: <?= ($exercise['setsCompleted']/$exercise['totalSets'])*100 ?>%">
                                <span class="progress-text neuro-text">
                                    <?= $exercise['setsCompleted'] ?>/<?= $exercise['totalSets'] ?> SETS
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-expand neuro-scan" onclick="expandModule(<?= $index ?>)"
                    data-neuro-tooltip="View exercise details">
                    <div class="neural-glow"></div>
                    VIEW DETAILS <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
            <?php endforeach; ?>

            <!-- Recovery Analytics -->
            <div class="dashboard-card recovery-analytics neuro-card">
                <div class="card-header">
                    <h3><i class="fas fa-regeneration me-2"></i>Recovery Analysis</h3>
                </div>
                <div class="recovery-progress">
                    <div class="circular-progress"
                        style="--progress: <?= htmlspecialchars($recoveryProgress, ENT_QUOTES, 'UTF-8') ?>%">
                        <span
                            class="progress-value"><?= htmlspecialchars($recoveryProgress, ENT_QUOTES, 'UTF-8') ?>%</span>
                    </div>
                    <div class="recovery-stats">
                        <div class="stat-item">
                            <span class="stat-label">Muscle Recovery</span>
                            <span class="stat-value">82%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">CNS Readiness</span>
                            <span
                                class="stat-value"><?= htmlspecialchars($recoveryProgress, ENT_QUOTES, 'UTF-8') ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Action Menu -->
        <div class="action-menu neuro-interface">
            <div class="menu-item neuro-scan" data-neuro-tooltip="Training Cart">
                <div class="neural-glow"></div>
                <i class="fas fa-shopping-basket"></i>
            </div>
            <div class="menu-item neuro-scan" data-neuro-tooltip="Performance Metrics">
                <div class="neural-glow"></div>
                <i class="fas fa-chart-network"></i>
            </div>
            <div class="menu-item neuro-scan" data-neuro-tooltip="AI Coach">
                <div class="neural-glow"></div>
                <i class="fas fa-robot"></i>
            </div>
        </div>
    </div>
</div>