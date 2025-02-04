<?php include '../layouts/header.php'; ?>
<div class="container">
    <h1 class="mb-4">Notifications</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Message</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
            <tr>
                <td><?php echo htmlspecialchars($notification['id']); ?></td>
                <td><?php echo htmlspecialchars($notification['message']); ?></td>
                <td><?php echo htmlspecialchars(date(' F j, Y', strtotime($notification['date']))); ?></td>
                <td><?php echo htmlspecialchars($notification['status']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No notifications available.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../layouts/footer.php'; ?>