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
// CSRF token handling with error checking
const csrfToken =
    "<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') : ''; ?>";

// Window-based error notification system
const windowErrorSystem = {
    show(message, type = 'error') {
        const errorWindow = document.createElement('div');
        errorWindow.className = `error-window animate__animated animate__shakeX`;
        errorWindow.innerHTML = `
            <div class="error-content">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles for the error window
        const style = document.createElement('style');
        style.innerHTML = `
            .error-window {
                position: fixed;
                top: 20%;
                left: 50%;
                transform: translateX(-50%);
                background: #ffebee;
                border: 1px solid #ff4444;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                z-index: 1000;
            }
            .error-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .bi-exclamation-triangle-fill {
                color: #ff4444;
                font-size: 24px;
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(errorWindow);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            errorWindow.remove();
            style.remove();
        }, 5000);
    }
};

// Initialize error system on DOM load
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Enhanced session check with real-time error feedback
        const sessionCheckInterval = setInterval(checkSession, 300000);

        function checkSession() {
            try {
                if (!csrfToken) {
                    throw new Error('CSRF token is missing');
                }

                fetch('/fitness_tracker/public/session_check.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrfToken
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data) {
                            throw new Error('Invalid response data');
                        }
                        if (data.expired) {
                            clearInterval(sessionCheckInterval);
                            windowErrorSystem.show('Session expired. Redirecting...', 'warning');
                            setTimeout(() => {
                                window.location.href = '/fitness_tracker/public/logout';
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        windowErrorSystem.show('Session check failed. Please refresh the page.');
                    });
            } catch (error) {
                windowErrorSystem.show(error.message);
            }
        }

        // Add CSRF token to all forms with validation
        document.querySelectorAll('form').forEach(form => {
            try {
                if (!csrfToken) {
                    throw new Error('CSRF token is missing');
                }

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            } catch (error) {
                windowErrorSystem.show('Security error: Form submission disabled');
                form.querySelector('button[type="submit"]')?.setAttribute('disabled', 'true');
            }
        });

        // Global error handler for uncaught exceptions
        window.addEventListener('error', function(event) {
            windowErrorSystem.show('An unexpected error occurred. Please try again.');
        });

        // Handle session error messages
        <?php if (isset($_SESSION['error_message'])): ?>
        windowErrorSystem.show(`<?php echo addslashes($_SESSION['error_message']); ?>`);
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

    } catch (error) {
        windowErrorSystem.show('Initialization error: ' + error.message);
    }
});
</script>
</body>

</html>