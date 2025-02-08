<?php
try {
    // Check if session is already started
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
    }

    // Generate CSRF token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            throw new Exception('Failed to generate CSRF token: ' . $e->getMessage());
        }
    }

    // Check for both flash messages and direct success/error messages
    $messages = [];
    if (!empty($_SESSION['flash_messages'])) {
        if (!is_array($_SESSION['flash_messages'])) {
            throw new Exception('Invalid flash messages format');
        }
        $messages = $_SESSION['flash_messages'];
        unset($_SESSION['flash_messages']);
    } else {
        $messageTypes = [
            'success_message' => 'success',
            'error_message' => 'danger',
            'csrf_token_error' => 'danger'
        ];

        foreach ($messageTypes as $sessionKey => $messageType) {
            if (!empty($_SESSION[$sessionKey])) {
                if (!is_string($_SESSION[$sessionKey])) {
                    throw new Exception("Invalid message format for $sessionKey");
                }
                $messages[] = [
                    'type' => $messageType,
                    'text' => htmlspecialchars($_SESSION[$sessionKey], ENT_QUOTES, 'UTF-8'),
                    'animation' => htmlspecialchars($_SESSION[$sessionKey.'_animation'] ?? 'windowShake', ENT_QUOTES, 'UTF-8')
                ];
                unset($_SESSION[$sessionKey]);
                unset($_SESSION[$sessionKey.'_animation']);
            }
        }
    }

    if (!empty($messages)): ?>
<style>
@keyframes windowShake {
    0% {
        transform: translate(0, 0);
    }

    25% {
        transform: translate(-10px, 10px);
    }

    50% {
        transform: translate(10px, -10px);
    }

    75% {
        transform: translate(-10px, 10px);
    }

    100% {
        transform: translate(0, 0);
    }
}

.message-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 400px;
}

.alert-animated {
    animation-duration: 0.5s;
    animation-fill-mode: both;
    box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
}
</style>

<div class="message-container">
    <?php foreach ($messages as $message): 
        if (!isset($message['type']) || !isset($message['text'])) {
            continue;
        }
        $animationClass = 'animate__' . htmlspecialchars($message['animation'], ENT_QUOTES, 'UTF-8');
    ?>
    <div class="alert alert-<?php echo htmlspecialchars($message['type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show alert-animated animate__animated <?php echo $animationClass; ?>"
        role="alert" style="margin-bottom: 10px;">
        <?php echo $message['text']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to close buttons
    document.querySelectorAll('.alert .close').forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            alert.classList.remove('animate__fadeIn');
            alert.classList.add('animate__fadeOut');
            setTimeout(() => alert.remove(), 500);
        });
    });

    // Auto-dismiss messages after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('animate__fadeIn');
            alert.classList.add('animate__fadeOut');
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>
<?php endif; ?>
<?php
} catch (Exception $e) {
    error_log('Message display error: ' . $e->getMessage());
    ?>
<div class="alert alert-danger alert-dismissible fade show animate__animated animate__windowShake" role="alert">
    An error occurred while displaying messages. Please try again.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php
}
?>