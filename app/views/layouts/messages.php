<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Check for both flash messages and direct success/error messages
$messages = [];
if (!empty($_SESSION['flash_messages'])) {
    $messages = $_SESSION['flash_messages'];
    unset($_SESSION['flash_messages']);
} else {
    if (!empty($_SESSION['success_message'])) {
        $messages[] = ['type' => 'success', 'text' => $_SESSION['success_message']];
        unset($_SESSION['success_message']);
    }
    if (!empty($_SESSION['error_message'])) {
        $messages[] = ['type' => 'danger', 'text' => $_SESSION['error_message']];
        unset($_SESSION['error_message']);
    }
}

if (!empty($messages)): ?>
<?php foreach ($messages as $message): ?>
<div class="alert alert-<?php echo htmlspecialchars($message['type']); ?> alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($message['text']); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endforeach; ?>
<?php endif; ?>