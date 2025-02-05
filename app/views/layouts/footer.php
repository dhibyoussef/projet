<footer class="footer mt-auto py-3 bg-dark">
    <div class="container text-center">
        <p class="mb-0 text-white">&copy; <?php echo date("Y"); ?> Fitness Tracker. All rights reserved.</p>
        <nav class="mt-2">
            <ul class="list-inline mb-0">
                <li class="list-inline-item"><a href="/fitness_tracker/public/privacy" class="text-light">Privacy
                        Policy</a></li>
                <li class="list-inline-item"><span class="text-light">|</span></li>
                <li class="list-inline-item"><a href="/fitness_tracker/public/terms" class="text-light">Terms of
                        Service</a></li>
                <li class="list-inline-item"><span class="text-light">|</span></li>
                <li class="list-inline-item"><a href="/fitness_tracker/public/contact" class="text-light">Contact Us</a>
                </li>
            </ul>
        </nav>
    </div>
</footer>
<script src="/fitness_tracker/public/assets/js/bootstrap.bundle.min.js"></script>
<script>
// Session management script
document.addEventListener('DOMContentLoaded', function() {
    // Check session status every 5 minutes
    const sessionCheckInterval = setInterval(checkSession, 300000);

    function checkSession() {
        fetch('/fitness_tracker/public/session_check.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.expired) {
                    clearInterval(sessionCheckInterval);
                    window.location.href = '/fitness_tracker/public/logout';
                }
            })
            .catch(error => {
                console.error('Error checking session:', error);
            });
    }

    // Cleanup interval when page unloads
    window.addEventListener('beforeunload', () => {
        clearInterval(sessionCheckInterval);
    });
});
</script>
</body>

</html>