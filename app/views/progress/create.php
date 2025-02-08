<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token with expiration
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    // Include header with error handling
    if (!@include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    error_log('Error in progress/create.php: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    die(htmlspecialchars('An error occurred while processing your request. Please try again later.', ENT_QUOTES, 'UTF-8'));
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
.error-window {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #dc3545;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}
</style>
<div class="container mt-5">
    <h1 class="mb-4">Log Your Progress</h1>
    <div id="message-container"></div>
    <form method="POST" action="../../controllers/progress/CreateController.php" class="needs-validation" novalidate
        id="progress-form">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
            <div class="invalid-feedback">Please select a date.</div>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" class="form-control" name="weight" id="weight" step="0.1" required min="0">
            <div class="invalid-feedback">Please enter a valid weight.</div>
        </div>
        <div class="form-group">
            <label for="body_fat">Body Fat (%)</label>
            <input type="number" class="form-control" name="body_fat" id="body_fat" step="0.1" required min="0"
                max="100">
            <div class="invalid-feedback">Please enter a valid body fat percentage (0-100).</div>
        </div>
        <div class="form-group">
            <label for="muscle_mass">Muscle Mass (kg)</label>
            <input type="number" class="form-control" name="muscle_mass" id="muscle_mass" step="0.1" min="0">
            <div class="invalid-feedback">Please enter a valid muscle mass.</div>
        </div>
        <button type="submit" class="btn btn-success" name="create">Log Progress</button>
        <a href="/progress" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#progress-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    let result = JSON.parse(response);
                    if (result.success) {
                        $('#progress-form')[0].reset();
                        let successMessage = $('<div>').text(result.message).html();
                        let successWindow = $(`<div class="error-window animate__animated animate__fadeIn">
                            <div class="alert alert-success">${successMessage}</div>
                            <button class="btn btn-sm btn-success w-100" onclick="$(this).parent().addClass('animate__fadeOut').remove()">OK</button>
                        </div>`);
                        $('body').append(successWindow);
                    } else {
                        let errorMessage = $('<div>').text(result.message).html();
                        let errorWindow = $(`<div class="error-window animate__animated animate__shakeX">
                            <div class="alert alert-danger">${errorMessage}</div>
                            <button class="btn btn-sm btn-danger w-100" onclick="$(this).parent().addClass('animate__fadeOut').remove()">Close</button>
                        </div>`);
                        $('body').append(errorWindow);
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                }
            },
            error: function() {
                let errorWindow = $(`<div class="error-window animate__animated animate__shakeX">
                    <div class="alert alert-danger">An error occurred while processing your request.</div>
                    <button class="btn btn-sm btn-danger w-100" onclick="$(this).parent().addClass('animate__fadeOut').remove()">Close</button>
                </div>`);
                $('body').append(errorWindow);
            }
        });
    });
});
</script>

<?php 
try {
    include '../layouts/footer.php';
} catch (Exception $e) {
    error_log('Error including footer: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>