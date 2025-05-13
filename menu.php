<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">IoT Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="countryDropdown" role="button" data-bs-toggle="dropdown">
                        Locations
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php?location=brunei">Brunei</a></li>
                        <li><a class="dropdown-item" href="index.php?location=london">London</a></li>
                        <!-- Add more as needed -->
                    </ul>
                </li>
            </ul>
            <span class="navbar-text text-white">
                Real-time Sensor Monitoring
            </span>
        </div>
    </div>
</nav>
