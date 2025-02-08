<?php
try {
    // Start session with secure settings if not already started
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
            throw new Exception('Failed to set session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        if (!headers_sent()) {
            header('Location: /login');
            exit();
        } else {
            throw new Exception('Headers already sent, unable to redirect to login');
        }
    }

    // Include header with error handling
    if (!@include '../../views/layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
    ?>
<link rel="stylesheet" href="../../views/nutrition/assets/bootstrap.css">
<style>
.alert-animated {
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    0% {
        transform: translateY(-100%);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.message-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 5px;
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}
</style>
<div class="container mt-5">
    <h1 class="mb-4">Your Nutrition Log</h1>
    <div class="message-container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-animated">
            <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-animated">
            <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    </div>
    <a href="../../views/nutrition/create.php" class="btn btn-success mb-3">Log New Meal</a>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Food Item</th>
                    <th>Calories</th>
                    <th>Protein (g)</th>
                    <th>Carbs (g)</th>
                    <th>Fats (g)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($nutritionLogs)): ?>
                <?php foreach ($nutritionLogs as $nutrition): ?>
                <tr>
                    <td><?php echo htmlspecialchars($nutrition['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(date('F j, Y', strtotime($nutrition['date'])), ENT_QUOTES, 'UTF-8'); ?>
                    </td>
                    <td><?php echo htmlspecialchars($nutrition['food_item'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($nutrition['calories'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($nutrition['protein'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($nutrition['carbs'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($nutrition['fats'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="../../views/nutrition/edit.php?id=<?php echo htmlspecialchars($nutrition['id'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="btn btn-warning btn-sm">Edit</a>
                        <form action="../../controllers/nutrition/DeleteController.php" method="POST"
                            style="display:inline;">
                            <input type="hidden" name="id"
                                value="<?php echo htmlspecialchars($nutrition['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (isset($_SESSION['csrf_token'])): ?>
                            <input type="hidden" name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                            <div id="errorModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" onclick="closeModal('errorModal')">&times;</span>
                                    <p>CSRF token missing. Please refresh the page and try again.</p>
                                </div>
                            </div>
                            <script>
                            function closeModal(modalId) {
                                document.getElementById(modalId).style.display = 'none';
                            }
                            document.getElementById('errorModal').style.display = 'block';
                            </script>
                            <?php endif; ?>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm"
                                onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No nutrition entries found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
// Auto-dismiss messages after 5 seconds
document.querySelectorAll('.alert-animated').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

function confirmDelete() {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
            <p>Are you sure you want to delete this entry?</p>
            <div class="text-right mt-3">
                <button class="btn btn-secondary mr-2" onclick="this.parentElement.parentElement.parentElement.remove()">Cancel</button>
                <button class="btn btn-danger" onclick="this.parentElement.parentElement.parentElement.remove(); return true;">Delete</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    return false;
}
</script>
<?php 
    // Include footer with error handling
    if (!@include '../../views/layouts/footer.php') {
        echo '<div class="alert alert-danger">Failed to include footer file</div>';
    }
} catch (Exception $e) {
    // Log the error and show a user-friendly message
    error_log('Error in nutrition index view: ' . $e->getMessage());
    echo '<div class="alert alert-danger alert-animated">An error occurred while loading this page. Please try again later.</div>';
}
?>