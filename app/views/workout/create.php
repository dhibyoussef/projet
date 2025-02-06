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
            throw new Exception('Failed to set secure session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    // Regenerate session ID for security
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }

    // Include header with error handling
    if (!@include '../../views/layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    error_log('Error in workout/create.php: ' . $e->getMessage());
    die('An error occurred while processing your request. Please try again later.');
}
?>

<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<div class="container">
    <h1 class="mb-4">Add New Workout</h1>
    <div id="message-container"></div>
    <form method="POST" action="../../controllers/workout/CreateController.php" class="needs-validation" novalidate
        id="workout-form">
        <?php 
        try {
            // Generate CSRF token with expiration time (15 minutes)
            $csrf_expiration = 15 * 60; // 15 minutes in seconds
            if (!isset($_SESSION['csrf_token']) || 
                !isset($_SESSION['csrf_token_time']) || 
                (time() - $_SESSION['csrf_token_time']) > $csrf_expiration) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                if (!$_SESSION['csrf_token']) {
                    throw new Exception('Failed to generate CSRF token');
                }
                $_SESSION['csrf_token_time'] = time();
            }
        } catch (Exception $e) {
            error_log('CSRF token generation error: ' . $e->getMessage());
            die('An error occurred while generating security token. Please try again.');
        }
        ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="workoutName">Workout Name</label>
            <input type="text" class="form-control" name="workoutName" id="workoutName" placeholder="Enter workout name"
                required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="workoutDescription">Description</label>
            <textarea class="form-control" name="workoutDescription" id="workoutDescription" rows="3"
                placeholder="Enter a brief description" required></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="workoutDuration">Duration (minutes)</label>
            <input type="number" class="form-control" name="workoutDuration" id="workoutDuration" min="1" required>
            <div class="invalid-feedback">Please enter a valid duration (minimum 1 minute).</div>
        </div>
        <button type="submit" class="btn btn-primary" name="ok">Add Workout</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle form submission
    $('#workout-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    // Parse the JSON response
                    var result = JSON.parse(response);

                    // Create modal window
                    var modalHtml = `
                        <div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="resultModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="resultModalLabel">${result.success ? 'Success' : 'Error'}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        ${result.message}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    // Add modal to container
                    $('#message-container').html(modalHtml);

                    // Show modal with animation
                    $('#resultModal').modal('show').addClass(
                        'animate__animated animate__fadeIn');

                    // If successful, reset the form
                    if (result.success) {
                        $('#workout-form')[0].reset();
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                }
            },
            error: function(xhr) {
                var modalHtml = `
                    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    An error occurred while processing your request.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('#message-container').html(modalHtml);
                $('#errorModal').modal('show').addClass(
                'animate__animated animate__fadeIn');
            }
        });
    });
});
</script>

<?php 
try {
    if (!@include '../../views/layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    error_log('Error including footer: ' . $e->getMessage());
    die('An error occurred while processing your request. Please try again later.');
}
?>