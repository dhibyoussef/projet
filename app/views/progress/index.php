<?php
// Start session with secure settings if not already started
if (session_status() === PHP_SESSION_NONE) {
    try {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new Exception('Failed to set session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour
        }
    } catch (Exception $e) {
        error_log('Session initialization error: ' . $e->getMessage());
        die(htmlspecialchars('An error occurred while initializing the session. Please try again later.', ENT_QUOTES, 'UTF-8'));
    }
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

try {
    if (!file_exists('../../views/layouts/header.php')) {
        throw new Exception('Header file not found');
    }
    include '../../views/layouts/header.php'; 
} catch (Exception $e) {
    error_log('Header inclusion error: ' . $e->getMessage());
    die(htmlspecialchars('An error occurred while loading the page header. Please try again later.', ENT_QUOTES, 'UTF-8'));
}
?>
<link rel="stylesheet"
    href="<?php echo htmlspecialchars('../../views/progress/assets/bootstrap.css', ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<div class="container mt-5">
    <h1 class="mb-4">Your Progress Log</h1>
    <div id="message-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible animate__animated animate__fadeInRight"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
    <a href="<?php echo htmlspecialchars('../../views/progress/create.php', ENT_QUOTES, 'UTF-8'); ?>"
        class="btn btn-success mb-3">Log New Progress</a>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Weight (kg)</th>
                    <th>Body Fat (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($progressLogs)): ?>
                <?php foreach ($progressLogs as $progress): ?>
                <tr>
                    <td><?php echo htmlspecialchars($progress['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(date('F j, Y', strtotime($progress['date'])), ENT_QUOTES, 'UTF-8'); ?>
                    </td>
                    <td><?php echo htmlspecialchars($progress['weight'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($progress['body_fat'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars('../../views/progress/edit.php?id=' . $progress['id'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="btn btn-warning btn-sm">Edit</a>
                        <form
                            action="<?php echo htmlspecialchars('../../controllers/progress/DeleteController.php', ENT_QUOTES, 'UTF-8'); ?>"
                            method="POST" style="display:inline;" onsubmit="return handleDelete(event, this)">
                            <input type="hidden" name="id"
                                value="<?php echo htmlspecialchars($progress['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($_SESSION['csrf_token'])): ?>
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                            <div class="alert alert-danger animate__animated animate__shakeX">CSRF token missing. Please
                                refresh the page and try again.
                            </div>
                            <?php endif; ?>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center animate__animated animate__fadeIn">No progress entries found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible animate__animated animate__fadeInRight`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    container.appendChild(alert);

    setTimeout(() => {
        alert.classList.remove('animate__fadeInRight');
        alert.classList.add('animate__fadeOutRight');
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}

async function handleDelete(event, form) {
    event.preventDefault();

    if (!confirm('Are you sure you want to delete this entry?')) {
        return false;
    }

    try {
        const response = await axios({
            method: 'POST',
            url: form.action,
            data: new FormData(form)
        });

        if (response.data.success) {
            showMessage(response.data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showMessage(response.data.message, 'danger');
        }
    } catch (error) {
        showMessage('An error occurred while deleting the entry', 'danger');
    }
}
</script>
<?php 
try {
    if (!file_exists('../../views/layouts/footer.php')) {
        throw new Exception('Footer file not found');
    }
    include '../../views/layouts/footer.php'; 
} catch (Exception $e) {
    error_log('Footer inclusion error: ' . $e->getMessage());
    die(htmlspecialchars('An error occurred while loading the page footer. Please try again later.', ENT_QUOTES, 'UTF-8'));
}
?>