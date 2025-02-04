<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/">Fitness Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/signup">Sign Up</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/workout">Workouts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/nutrition">Nutrition</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/progress">Progress</a>
                </li>
            </ul>
        </div>
    </div>
</nav>