<?php include 'menu.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>OpenSenseMap Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; margin-bottom: 20px; }
    </style>
</head>
<body class="container my-4">
    <h1 class="mb-4">OpenSenseMap Dashboard</h1>
    <?php include 'box.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
