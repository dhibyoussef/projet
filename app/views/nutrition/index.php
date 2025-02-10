<?php
include '../../views/layouts/header.php';

try {
    // Enhanced secure session initialization with CSRF rotation
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        session_start();
        session_regenerate_id(true);

        // Generate CSRF token with expiration
        if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expiry']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expiry'] = time() + 3600;
            $_SESSION['session_fingerprint'] = hash_hmac('sha256', $_SERVER['HTTP_USER_AGENT'], $_SESSION['csrf_token']);
        }
    }

    // Multi-factor session validation
    if (!isset($_SESSION['logged_in']) || 
        $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR'] ||
        $_SESSION['session_fingerprint'] !== hash_hmac('sha256', $_SERVER['HTTP_USER_AGENT'], $_SESSION['csrf_token'])) {
        header('Location: /login?error=session_timeout');
        exit();
    }
} catch (Exception $e) {
    error_log('Security failure: ' . $e->getMessage());
    $_SESSION['system_message'] = 'System security error - please reauthenticate';
    header('Location: /error');
    exit();
}
?>

<div class="dashboard-container" data-theme="dark">
    <header class="dashboard-header text-center" data-aos="fade-down">
        <div class="glass-card mx-auto mb-4" style="max-width: 800px; padding: 2rem">
            <h1 class="neon-heading mb-3">Nutrition Tracker</h1>
            <p class="lead text-muted">Visualizing Your Nutritional Journey</p>
            <div class="language-switcher">
                <select class="form-select holographic-select" id="languageSelect">
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                    <option value="ar">العربية</option>
                </select>
            </div>
        </div>
    </header>

    <main class="nutrition-main">
        <div class="container-fluid px-lg-5">
            <div class="glass-card p-4" data-aos="zoom-in">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 glow-heading"><i class="fas fa-utensils me-2"></i>Nutrition Log</h2>
                    <div class="toolbar-group">
                        <a href="/nutrition/create" class="btn-holographic btn-sm" data-tooltip="Add New Entry">
                            <i class="fas fa-plus-circle me-2"></i>Log Meal
                        </a>
                        <button class="btn-holographic btn-sm" data-action="toggle-theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>

                <div id="nutrition-chart" class="mb-5" style="height: 300px;"></div>

                <?php if (!empty($ai_recommendations)): ?>
                <div class="glass-card mb-4 p-3" data-aos="fade-up">
                    <h4 class="glow-heading"><i class="fas fa-robot me-2"></i>AI Recommendations</h4>
                    <p class="mb-0"><?= htmlspecialchars($ai_recommendations, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="glow-head">
                            <tr>
                                <th>Date</th>
                                <th>Food Item</th>
                                <th>Calories</th>
                                <th>Protein</th>
                                <th>Carbs</th>
                                <th>Fats</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nutritionLogs as $log): ?>
                            <tr data-aos="fade-right">
                                <td><?= htmlspecialchars(date('M j, Y', strtotime($log['date'])), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td><?= htmlspecialchars($log['food_item'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['calories'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['protein'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['carbs'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($log['fats'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/nutrition/edit?id=<?= $log['id'] ?>" class="btn-sm warning"
                                            data-tooltip="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form class="ajax-form" method="POST" action="/nutrition/delete">
                                            <input type="hidden" name="id" value="<?= $log['id'] ?>">
                                            <input type="hidden" name="csrf_token"
                                                value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn-sm danger" data-tooltip="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script type="module">
import {
    confirmAction,
    initNutritionChart
} from '/assets/js/ui-helpers.js';
import {
    init3DEffects
} from '/assets/js/three-module.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize 3D effects
    init3DEffects('#nutrition-chart', {
        type: '3d_pie',
        data: <?= json_encode(array_column($nutritionLogs, 'calories')) ?>
    });

    // Initialize nutrition chart
    initNutritionChart('nutrition-chart', {
        labels: <?= json_encode(array_column($nutritionLogs, 'date')) ?>,
        protein: <?= json_encode(array_column($nutritionLogs, 'protein')) ?>,
        carbs: <?= json_encode(array_column($nutritionLogs, 'carbs')) ?>,
        fats: <?= json_encode(array_column($nutritionLogs, 'fats')) ?>
    });

    // Form handling with haptic feedback
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (await confirmAction('Confirm deletion?')) {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?>'
                    },
                    body: new FormData(form)
                });

                if (response.ok) {
                    form.closest('tr').style.opacity = 0;
                    setTimeout(() => form.closest('tr').remove(), 500);
                }
            }
        });
    });

    // Theme toggler
    document.querySelector('[data-action="toggle-theme"]').addEventListener('click', () => {
        document.documentElement.classList.toggle('dark-theme');
    });
});
</script>

<?php
include '../../views/layouts/footer.php';
?>