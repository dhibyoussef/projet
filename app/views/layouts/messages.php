<?php if (!empty($_SESSION['flash_messages'])): ?>
<?php foreach ($_SESSION['flash_messages'] as $message): ?>
<div class="alert alert-<?php echo htmlspecialchars($message['type']); ?>" role="alert">
    <?php echo htmlspecialchars($message['text']); ?>
</div>
<?php endforeach; ?>
<?php unset($_SESSION['flash_messages']); // Clear messages after displaying ?>
<?php endif; ?>