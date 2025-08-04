<?php
require 'config.php';

// --- DELETE SENSOR ---
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM sensors WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    header("Location: sensor_dashboard.php");
    exit();
}

// --- EDIT SENSOR ---
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM sensors WHERE id = :id");
    $stmt->execute([':id' => $edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- UPDATE SENSOR (from Edit form) ---
if (isset($_POST['update_sensor'])) {
    $id = (int)$_POST['sensor_id'];
    $sensor_name = trim($_POST['sensor_name'] ?? '');
    $sensor_type = trim($_POST['sensor_type'] ?? '');
    $sensor_value = trim($_POST['sensor_value'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = trim($_POST['status'] ?? 'Inactive');
    $image = trim($_POST['image'] ?? '');

    if (is_numeric($sensor_value) && $sensor_name && $sensor_type && $location) {
        $stmt = $pdo->prepare("UPDATE sensors SET sensor_name=:name, sensor_type=:type, sensor_value=:value, status=:status, image=:image, location=:location WHERE id=:id");
        $stmt->execute([
            ':name' => $sensor_name,
            ':type' => $sensor_type,
            ':value' => $sensor_value,
            ':status' => $status,
            ':image' => $image,
            ':location' => $location,
            ':id' => $id
        ]);
        header("Location: sensor_dashboard.php");
        exit();
    } else {
        $edit_error = "Please enter valid numeric value and fill all required fields.";
        $edit_data = [
            'id' => $id,
            'sensor_name' => $sensor_name,
            'sensor_type' => $sensor_type,
            'sensor_value' => $sensor_value,
            'status' => $status,
            'image' => $image,
            'location' => $location
        ];
    }
}

// --- ADD SENSOR ---
if (isset($_POST['add_sensor'])) {
    $sensor_name = trim($_POST['sensor_name'] ?? '');
    $sensor_type = trim($_POST['sensor_type'] ?? '');
    $sensor_value = trim($_POST['sensor_value'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $status = trim($_POST['status'] ?? 'Inactive');
    $image = trim($_POST['image'] ?? '');

    if (is_numeric($sensor_value) && $sensor_name && $sensor_type && $location) {
        $stmt = $pdo->prepare("INSERT INTO sensors (sensor_name, sensor_type, sensor_value, status, image, location) VALUES (:name, :type, :value, :status, :image, :location)");
        $stmt->execute([
            ':name' => $sensor_name,
            ':type' => $sensor_type,
            ':value' => $sensor_value,
            ':status' => $status,
            ':image' => $image,
            ':location' => $location
        ]);
        header("Location: sensor_dashboard.php");
        exit();
    } else {
        $add_error = "Please enter valid numeric value and fill all required fields.";
    }
}

// --- SEARCH & FILTER ---
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM sensors WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (sensor_name ILIKE :search OR sensor_type ILIKE :search OR location ILIKE :search)";
    $params[':search'] = "%$search%";
}
if ($status_filter && $status_filter !== 'All') {
    $sql .= " AND status = :status";
    $params[':status'] = $status_filter;
}
$sql .= " ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sensors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sensor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            min-height: 100vh;
            background: url('pic.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1060px;
            margin: 0 auto;
            padding: 2.1rem 1.3rem 0.5rem 1.3rem;
        }
        .back-btn, .logout-btn {
            display: inline-block;
            padding: 0.65rem 1.4rem;
            border-radius: 9px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(44,113,88,0.09);
            transition: background 0.18s;
        }
        .back-btn {
            background: #228e55;
            color: #fff;
            margin-right: 10px;
        }
        .back-btn:hover {
            background: #1d7948;
        }
        .logout-btn {
            background: #c93434;
            color: #fff;
            margin-left: 10px;
        }
        .logout-btn:hover {
            background: #a31d1d;
        }
        .main-content {
            max-width: 1060px;
            margin: 0 auto;
            padding: 0 1.3rem 2rem 1.3rem;
        }
        .action-link {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Top navigation bar -->
    <div class="top-bar">
        <a href="dashboard.php" class="back-btn">&larr; Back to Dashboard</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <div class="main-content">
        <h1 class="text-3xl font-bold mb-2 text-white">Welcome, Dream Team!</h1>
        <h2 class="text-xl mb-4 text-white">Sensor Data</h2>
        
        <!-- Add or Edit Sensor Form -->
        <?php if ($edit_data): ?>
            <h3 class="text-lg font-semibold mb-2">Edit Sensor</h3>
            <?php if (isset($edit_error)): ?>
                <div class="mb-2 text-red-600"><?= htmlspecialchars($edit_error) ?></div>
            <?php endif; ?>
            <form class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded shadow p-4" method="POST" action="sensor_dashboard.php">
                <input type="hidden" name="update_sensor" value="1">
                <input type="hidden" name="sensor_id" value="<?= htmlspecialchars($edit_data['id']) ?>">
                <div>
                    <label class="block text-sm">Sensor Name</label>
                    <input type="text" name="sensor_name" required class="px-2 py-1 border rounded" value="<?= htmlspecialchars($edit_data['sensor_name']) ?>">
                </div>
                <div>
                    <label class="block text-sm">Sensor Type</label>
                    <input type="text" name="sensor_type" required class="px-2 py-1 border rounded" value="<?= htmlspecialchars($edit_data['sensor_type']) ?>">
                </div>
                <div>
                    <label class="block text-sm">Value</label>
                    <input type="number" name="sensor_value" step="any" required class="px-2 py-1 border rounded" value="<?= htmlspecialchars($edit_data['sensor_value']) ?>">
                </div>
                <div>
                    <label class="block text-sm">Location</label>
                    <input type="text" name="location" required class="px-2 py-1 border rounded" value="<?= htmlspecialchars($edit_data['location']) ?>">
                </div>
                <div>
                    <label class="block text-sm">Status</label>
                    <select name="status" class="px-2 py-1 border rounded">
                        <option value="Active" <?= $edit_data['status']=='Active'?'selected':''; ?>>Active</option>
                        <option value="Inactive" <?= $edit_data['status']=='Inactive'?'selected':''; ?>>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm">Image URL (optional)</label>
                    <input type="text" name="image" class="px-2 py-1 border rounded" value="<?= htmlspecialchars($edit_data['image']) ?>">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Sensor</button>
                <a href="sensor_dashboard.php" class="ml-2 px-4 py-2 rounded bg-gray-400 text-white">Cancel</a>
            </form>
        <?php else: ?>
            <?php if (isset($add_error)): ?>
                <div class="mb-2 text-red-600"><?= htmlspecialchars($add_error) ?></div>
            <?php endif; ?>
            <form class="flex flex-wrap items-end gap-3 mb-6 bg-white rounded shadow p-4" method="POST" action="sensor_dashboard.php">
                <input type="hidden" name="add_sensor" value="1">
                <div>
                    <label class="block text-sm">Sensor Name</label>
                    <input type="text" name="sensor_name" required class="px-2 py-1 border rounded">
                </div>
                <div>
                    <label class="block text-sm">Sensor Type</label>
                    <input type="text" name="sensor_type" required class="px-2 py-1 border rounded">
                </div>
                <div>
                    <label class="block text-sm">Value</label>
                    <input type="number" name="sensor_value" step="any" required class="px-2 py-1 border rounded">
                </div>
                <div>
                    <label class="block text-sm">Location</label>
                    <input type="text" name="location" required class="px-2 py-1 border rounded" placeholder="e.g. Server Room, Lab, etc.">
                </div>
                <div>
                    <label class="block text-sm">Status</label>
                    <select name="status" class="px-2 py-1 border rounded">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm">Image URL (optional)</label>
                    <input type="text" name="image" class="px-2 py-1 border rounded">
                </div>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Add Sensor</button>
            </form>
        <?php endif; ?>

        <!-- Search/filter -->
        <form class="flex gap-2 mb-4" method="get">
            <input type="text" name="search" placeholder="Search by name, type, or location" value="<?=htmlspecialchars($search)?>" class="px-3 py-2 border rounded w-64">
            <select name="status" class="px-3 py-2 border rounded">
                <option value="All">All Status</option>
                <option value="Active" <?= $status_filter==='Active'?'selected':''; ?>>Active</option>
                <option value="Inactive" <?= $status_filter==='Inactive'?'selected':''; ?>>Inactive</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        </form>
        
        <!-- Sensor Data Table -->
        <div class="overflow-x-auto rounded shadow">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-3 text-left">No</th>
                        <th class="py-2 px-3 text-left">Image</th>
                        <th class="py-2 px-3 text-left">Sensor Name</th>
                        <th class="py-2 px-3 text-left">Sensor Type</th>
                        <th class="py-2 px-3 text-left">Sensor Value</th>
                        <th class="py-2 px-3 text-left">Location</th>
                        <th class="py-2 px-3 text-left">Status</th>
                        <th class="py-2 px-3 text-left">Created At</th>
                        <th class="py-2 px-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sensors) === 0): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">No sensor data found.</td>
                        </tr>
                    <?php else: $no = 1; foreach ($sensors as $sensor): ?>
                    <tr>
                        <td class="py-2 px-3"><?= $no++; ?></td>
                        <td class="py-2 px-3">
                            <?php if (!empty($sensor['image'])): ?>
                                <img src="<?= htmlspecialchars($sensor['image']) ?>" alt="sensor" class="w-8 h-8">
                            <?php else: ?>
                                sensor image
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-3"><?= htmlspecialchars($sensor['sensor_name']) ?></td>
                        <td class="py-2 px-3"><?= htmlspecialchars($sensor['sensor_type']) ?></td>
                        <td class="py-2 px-3"><?= htmlspecialchars($sensor['sensor_value']) ?></td>
                        <td class="py-2 px-3"><?= htmlspecialchars($sensor['location']) ?></td>
                        <td class="py-2 px-3 font-bold <?= $sensor['status']=='Active'?'text-green-600':'text-red-600' ?>">
                            <?= htmlspecialchars($sensor['status']) ?>
                        </td>
                        <td class="py-2 px-3"><?= htmlspecialchars($sensor['created_at']) ?></td>
                        <td class="py-2 px-3">
                            <a href="sensor_dashboard.php?edit=<?= $sensor['id'] ?>" class="text-blue-500 hover:underline action-link">Edit</a> |
                            <a href="sensor_dashboard.php?delete=<?= $sensor['id'] ?>" onclick="return confirm('Delete this sensor?')" class="text-red-500 hover:underline action-link">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
