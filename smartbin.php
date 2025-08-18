<?php
// smartbin.php

// Get data from Arduino via GET request
$frontDistance = isset($_GET['front']) ? floatval($_GET['front']) : 0;
$depthDistance = isset($_GET['depth']) ? floatval($_GET['depth']) : 0;
$lidStatus = isset($_GET['lid']) ? $_GET['lid'] : "closed";

// Determine bin status based on depth sensor
if ($depthDistance >= 100) {
    $binStatus = "EMPTY";
} elseif ($depthDistance >= 50) {
    $binStatus = "HALF FULL";
} elseif ($depthDistance >= 20) {
    $binStatus = "FULL";
} else {
    $binStatus = "OVERFLOWING";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Bin Status</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .status { padding: 20px; border-radius: 10px; background: #fff; max-width: 400px; margin: auto; }
        h1 { text-align: center; }
        p { font-size: 1.2em; }
        .lid-open { color: green; font-weight: bold; }
        .lid-closed { color: red; font-weight: bold; }
    </style>
    <meta http-equiv="refresh" content="2"> <!-- Refresh every 2 seconds -->
</head>
<body>
    <div class="status">
        <h1>Smart Bin Status</h1>
        <p><strong>Front Sensor Distance:</strong> <?php echo $frontDistance; ?> cm</p>
        <p><strong>Depth Sensor Distance:</strong> <?php echo $depthDistance; ?> cm</p>
        <p><strong>Lid Status:</strong> 
            <span class="<?php echo $lidStatus === 'open' ? 'lid-open' : 'lid-closed'; ?>">
                <?php echo strtoupper($lidStatus); ?>
            </span>
        </p>
        <p><strong>Bin Status:</strong> <?php echo $binStatus; ?></p>
    </div>
</body>
</html>
