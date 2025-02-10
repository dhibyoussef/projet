<?php
include '../../views/layouts/header.php';

try {
    // Enhanced secure session initialization
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => 86400,
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
        session_regenerate_id(true);
    }

    // CSRF token with expiration
    if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expire']) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expire'] = time() + 3600;
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    }

    // Session fingerprint validation
    $session_fingerprint = hash_hmac('sha256', $_SESSION['user_agent'], $_SESSION['csrf_token']);
    if (!isset($_SESSION['logged_in']) || $_SESSION['session_fingerprint'] !== $session_fingerprint) {
        header('Location: /login?error=session_timeout');
        exit();
    }
} catch (RuntimeException $e) {
    error_log('Security failure: ' . $e->getMessage());
    $_SESSION['system_message'] = htmlspecialchars('System security error - please reauthenticate', ENT_QUOTES, 'UTF-8');
    header('Location: /error');
    exit();
}
?>

<div class="dashboard-container" data-theme="dark">
    <header class="dashboard-header text-center" data-aos="fade-down">
        <div class="glass-card mx-auto mb-4" style="max-width: 800px; padding: 2rem">
            <h1 class="neon-heading mb-3"><?= htmlspecialchars($lang['progress_title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="lead text-muted"><?= htmlspecialchars($lang['progress_subtitle'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </header>

    <main class="progress-main">
        <div class="container-fluid px-lg-5">
            <div id="progress-viz-container" class="mb-5" data-aos="zoom-in"></div>

            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 glow-heading"><i
                            class="fas fa-chart-line me-2"></i><?= htmlspecialchars($lang['progress_history'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <div class="toolbar-group">
                        <a href="/progress/create" class="btn-holographic btn-sm"
                            data-tooltip="<?= htmlspecialchars($lang['add_entry'], ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-plus-circle"></i>
                        </a>
                        <button class="btn-holographic btn-sm" data-action="toggle-theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="glow-head">
                            <tr>
                                <th><?= htmlspecialchars($lang['date'], ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang['weight'], ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang['body_fat'], ENT_QUOTES, 'UTF-8') ?></th>
                                <th><?= htmlspecialchars($lang['trend'], ENT_QUOTES, 'UTF-8') ?></th>
                                <th class="text-end"><?= htmlspecialchars($lang['actions'], ENT_QUOTES, 'UTF-8') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($progressLogs as $entry): ?>
                            <tr class="hover-3d" data-entry-id="<?= $entry['id'] ?>">
                                <td><?= htmlspecialchars(date('M j, Y', strtotime($entry['date'])), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td><?= htmlspecialchars($entry['weight'], ENT_QUOTES, 'UTF-8') ?> kg</td>
                                <td><?= htmlspecialchars($entry['body_fat'], ENT_QUOTES, 'UTF-8') ?>%</td>
                                <td>
                                    <div class="sparkline"
                                        data-values="<?= htmlspecialchars($entry['trend_data'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-color="<?= $entry['trend'] > 0 ? '#4CAF50' : '#F44336' ?>"></div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group-holographic">
                                        <a href="/progress/edit/<?= $entry['id'] ?>" class="btn-sm"
                                            data-tooltip="<?= htmlspecialchars($lang['edit'], ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="/progress/delete/<?= $entry['id'] ?>"
                                            class="ajax-form">
                                            <input type="hidden" name="csrf_token"
                                                value="<?= $_SESSION['csrf_token'] ?>">
                                            <button type="submit" class="btn-sm danger"
                                                data-tooltip="<?= htmlspecialchars($lang['delete'], ENT_QUOTES, 'UTF-8') ?>">
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
    initProgressViz
} from '/assets/js/viz-module.js';
import {
    confirmAction
} from '/assets/js/ui-helpers.js';
import {
    init3DEffects
} from '/assets/js/three-helpers.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize 3D visualization with WebGL
    initProgressViz('#progress-viz-container', {
        dataPoints: <?= json_encode(array_column($progressLogs, 'weight')) ?>,
        labels: <?= json_encode(array_column($progressLogs, 'date')) ?>,
        theme: document.documentElement.getAttribute('data-theme') || 'dark'
    });

    // 3D hover effects
    init3DEffects('.hover-3d', {
        rotationIntensity: 15,
        scaleIntensity: 1.05
    });

    // Interactive form handling with haptic feedback
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (await confirmAction(
                    '<?= htmlspecialchars($lang['confirm_delete'], ENT_QUOTES, 'UTF-8') ?>'
                    )) {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?>',
                        'Accept-Language': '<?= $_SESSION['lang'] ?? 'en' ?>'
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
});
</script>

<?php
try {
    include '../../views/layouts/footer.php';
} catch (RuntimeException $e) {
    error_log('Footer error: ' . $e->getMessage());
    $_SESSION['system_message'] = htmlspecialchars('Failed to load page footer', ENT_QUOTES, 'UTF-8');
}
?>