<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'db_connection.php';

// Ensure admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get admin name
$admin_id = $_SESSION['user_id'];
$admin_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$admin_query->bind_param("i", $admin_id);
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin = $admin_result->fetch_assoc();

// Handle section switching
$section = $_GET['section'] ?? 'dashboard';

// Handle add forms (example for drivers, repeat for others)
if (isset($_POST['add_driver'])) {
    $name = $_POST['name'];
    $license = $_POST['license'];
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("INSERT INTO drivers (name, license, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $license, $phone);
    $stmt->execute();
    $_SESSION['message'] = "Driver added!";
    header("Location: admin_dashboard.php?section=drivers");
    exit();
}
if (isset($_POST['add_passenger'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("INSERT INTO passengers (name, phone) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $phone);
    $stmt->execute();
    $_SESSION['message'] = "Passenger added!";
    header("Location: admin_dashboard.php?section=passengers");
    exit();
}
if (isset($_POST['add_sacco'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO saccos (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $_SESSION['message'] = "Sacco added!";
    header("Location: admin_dashboard.php?section=saccos");
    exit();
}
if (isset($_POST['add_vehicle'])) {
    $plate = $_POST['plate'];
    $sacco_id = $_POST['sacco_id'];
    $stmt = $conn->prepare("INSERT INTO vehicles (plate, sacco_id) VALUES (?, ?)");
    $stmt->bind_param("si", $plate, $sacco_id);
    $stmt->execute();
    $_SESSION['message'] = "Vehicle added!";
    header("Location: admin_dashboard.php?section=vehicles");
    exit();
}
if (isset($_POST['add_trip'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $driver_id = $_POST['driver_id'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $stmt = $conn->prepare("INSERT INTO trips (vehicle_id, driver_id, origin, destination) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $vehicle_id, $driver_id, $from, $to);
    $stmt->execute();
    $_SESSION['message'] = "Trip added!";
    header("Location: admin_dashboard.php?section=trips");
    exit();
}

// Fetch summary counts
function get_count($conn, $table) {
    $result = $conn->query("SELECT COUNT(*) AS cnt FROM $table");
    return $result->fetch_assoc()['cnt'] ?? 0;
}
$driver_count = get_count($conn, 'drivers');
$passenger_count = get_count($conn, 'passengers');
$sacco_count = get_count($conn, 'saccos');
$vehicle_count = get_count($conn, 'vehicles');
$transaction_count = get_count($conn, 'transactions');
$trip_count = get_count($conn, 'trips');

// Fetch tables for each section
if ($section === 'drivers') {
    $table_data = $conn->query("SELECT * FROM drivers");
} elseif ($section === 'passengers') {
    $table_data = $conn->query("SELECT * FROM passengers");
} elseif ($section === 'saccos') {
    $table_data = $conn->query("SELECT * FROM saccos");
} elseif ($section === 'vehicles') {
    $table_data = $conn->query("SELECT v.*, s.name as sacco FROM vehicles v LEFT JOIN saccos s ON v.sacco_id=s.id");
} elseif ($section === 'transactions') {
    $table_data = $conn->query("SELECT * FROM transactions");
} elseif ($section === 'trips') {
    $table_data = $conn->query("SELECT t.*, d.name as driver, v.plate as vehicle FROM trips t LEFT JOIN drivers d ON t.driver_id=d.id LEFT JOIN vehicles v ON t.vehicle_id=v.id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100">
<div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white h-screen fixed">
        <div class="text-center text-xl font-bold py-6 border-b border-yellow-700">Admin Dashboard</div>
        <nav class="px-4 py-6">
            <ul>
                <li class="mb-4"><a href="?section=dashboard" class="block py-2 px-4 rounded hover:bg-yellow-700">Dashboard</a></li>
                <li class="mb-4"><a href="?section=drivers" class="block py-2 px-4 rounded hover:bg-yellow-700">Drivers</a></li>
                <li class="mb-4"><a href="?section=passengers" class="block py-2 px-4 rounded hover:bg-yellow-700">Passengers</a></li>
                <li class="mb-4"><a href="?section=saccos" class="block py-2 px-4 rounded hover:bg-yellow-700">Saccos</a></li>
                <li class="mb-4"><a href="?section=vehicles" class="block py-2 px-4 rounded hover:bg-yellow-700">Vehicles</a></li>
                <li class="mb-4"><a href="?section=transactions" class="block py-2 px-4 rounded hover:bg-yellow-700">Transactions</a></li>
                <li class="mb-4"><a href="?section=trips" class="block py-2 px-4 rounded hover:bg-yellow-700">Trips</a></li>
                <li class="mb-4"><a href="logout.php" class="block py-2 px-4 bg-red-600 rounded hover:bg-red-700">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-1 p-6 ml-64">
        <header class="flex items-center justify-between bg-white p-4 rounded shadow">
            <h1 class="text-2xl font-bold text-blue-900">Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
        </header>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mt-4">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mt-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($section === 'dashboard'): ?>
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <div class="bg-blue-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Drivers</h2><p class="text-4xl font-bold"><?php echo $driver_count; ?></p></div>
                <div class="bg-green-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Passengers</h2><p class="text-4xl font-bold"><?php echo $passenger_count; ?></p></div>
                <div class="bg-yellow-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Saccos</h2><p class="text-4xl font-bold"><?php echo $sacco_count; ?></p></div>
                <div class="bg-purple-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Vehicles</h2><p class="text-4xl font-bold"><?php echo $vehicle_count; ?></p></div>
                <div class="bg-pink-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Transactions</h2><p class="text-4xl font-bold"><?php echo $transaction_count; ?></p></div>
                <div class="bg-indigo-500 text-white rounded-lg shadow-lg p-4"><h2 class="text-lg font-semibold">Trips</h2><p class="text-4xl font-bold"><?php echo $trip_count; ?></p></div>
            </div>
        <?php elseif ($section === 'drivers'): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Drivers</h2>
                <button onclick="toggleModal('addDriverModal')" class="bg-blue-600 text-white px-4 py-2 rounded">Add Driver</button>
            </div>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-blue-100">
                        <th class="border p-2">Name</th>
                        <th class="border p-2">License</th>
                        <th class="border p-2">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['license']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Add Driver Modal -->
            <div id="addDriverModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-blue-700">Add Driver</h3>
                        <button onclick="toggleModal('addDriverModal')" class="text-red-500 font-bold text-lg">&times;</button>
                    </div>
                    <form action="" method="POST" class="space-y-4 mt-4">
                        <input type="text" name="name" placeholder="Driver Name" class="block w-full border rounded p-2" required>
                        <input type="text" name="license" placeholder="License" class="block w-full border rounded p-2" required>
                        <input type="text" name="phone" placeholder="Phone" class="block w-full border rounded p-2" required>
                        <button type="submit" name="add_driver" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Add Driver</button>
                    </form>
                </div>
            </div>
        <?php elseif ($section === 'passengers'): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Passengers</h2>
                <button onclick="toggleModal('addPassengerModal')" class="bg-green-600 text-white px-4 py-2 rounded">Add Passenger</button>
            </div>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-green-100">
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Add Passenger Modal -->
            <div id="addPassengerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-green-700">Add Passenger</h3>
                        <button onclick="toggleModal('addPassengerModal')" class="text-red-500 font-bold text-lg">&times;</button>
                    </div>
                    <form action="" method="POST" class="space-y-4 mt-4">
                        <input type="text" name="name" placeholder="Passenger Name" class="block w-full border rounded p-2" required>
                        <input type="text" name="phone" placeholder="Phone" class="block w-full border rounded p-2" required>
                        <button type="submit" name="add_passenger" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Add Passenger</button>
                    </form>
                </div>
            </div>
        <?php elseif ($section === 'saccos'): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Saccos</h2>
                <button onclick="toggleModal('addSaccoModal')" class="bg-yellow-600 text-white px-4 py-2 rounded">Add Sacco</button>
            </div>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-yellow-100">
                        <th class="border p-2">Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Add Sacco Modal -->
            <div id="addSaccoModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-yellow-700">Add Sacco</h3>
                        <button onclick="toggleModal('addSaccoModal')" class="text-red-500 font-bold text-lg">&times;</button>
                    </div>
                    <form action="" method="POST" class="space-y-4 mt-4">
                        <input type="text" name="name" placeholder="Sacco Name" class="block w-full border rounded p-2" required>
                        <button type="submit" name="add_sacco" class="bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700">Add Sacco</button>
                    </form>
                </div>
            </div>
        <?php elseif ($section === 'vehicles'): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Vehicles</h2>
                <button onclick="toggleModal('addVehicleModal')" class="bg-purple-600 text-white px-4 py-2 rounded">Add Vehicle</button>
            </div>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-purple-100">
                        <th class="border p-2">Plate</th>
                        <th class="border p-2">Sacco</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['plate']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['sacco']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Add Vehicle Modal -->
            <div id="addVehicleModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-purple-700">Add Vehicle</h3>
                        <button onclick="toggleModal('addVehicleModal')" class="text-red-500 font-bold text-lg">&times;</button>
                    </div>
                    <form action="" method="POST" class="space-y-4 mt-4">
                        <input type="text" name="plate" placeholder="Plate Number" class="block w-full border rounded p-2" required>
                        <select name="sacco_id" class="block w-full border rounded p-2" required>
                            <option value="">Select Sacco</option>
                            <?php
                            $saccos = $conn->query("SELECT id, name FROM saccos");
                            while ($s = $saccos->fetch_assoc()) {
                                echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['name']).'</option>';
                            }
                            ?>
                        </select>
                        <button type="submit" name="add_vehicle" class="bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700">Add Vehicle</button>
                    </form>
                </div>
            </div>
        <?php elseif ($section === 'transactions'): ?>
            <h2 class="text-xl font-bold mb-4">Transactions</h2>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-pink-100">
                        <th class="border p-2">ID</th>
                        <th class="border p-2">Amount</th>
                        <th class="border p-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php elseif ($section === 'trips'): ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Trips</h2>
                <button onclick="toggleModal('addTripModal')" class="bg-indigo-600 text-white px-4 py-2 rounded">Add Trip</button>
            </div>
            <table class="w-full border-collapse border border-gray-300 bg-white rounded shadow">
                <thead>
                    <tr class="bg-indigo-100">
                        <th class="border p-2">Vehicle</th>
                        <th class="border p-2">Driver</th>
                        <th class="border p-2">Origin</th>
                        <th class="border p-2">Destination</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $table_data->fetch_assoc()): ?>
                        <tr>
                            <td class="border p-2"><?php echo htmlspecialchars($row['vehicle']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['driver']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['origin']); ?></td>
                            <td class="border p-2"><?php echo htmlspecialchars($row['destination']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Add Trip Modal -->
            <div id="addTripModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-indigo-700">Add Trip</h3>
                        <button onclick="toggleModal('addTripModal')" class="text-red-500 font-bold text-lg">&times;</button>
                    </div>
                    <form action="" method="POST" class="space-y-4 mt-4">
                        <select name="vehicle_id" class="block w-full border rounded p-2" required>
                            <option value="">Select Vehicle</option>
                            <?php
                            $vehicles = $conn->query("SELECT id, plate FROM vehicles");
                            while ($v = $vehicles->fetch_assoc()) {
                                echo '<option value="'.$v['id'].'">'.htmlspecialchars($v['plate']).'</option>';
                            }
                            ?>
                        </select>
                        <select name="driver_id" class="block w-full border rounded p-2" required>
                            <option value="">Select Driver</option>
                            <?php
                            $drivers = $conn->query("SELECT id, name FROM drivers");
                            while ($d = $drivers->fetch_assoc()) {
                                echo '<option value="'.$d['id'].'">'.htmlspecialchars($d['name']).'</option>';
                            }
                            ?>
                        </select>
                        <input type="text" name="from" placeholder="Origin" class="block w-full border rounded p-2" required>
                        <input type="text" name="to" placeholder="Destination" class="block w-full border rounded p-2" required>
                        <button type="submit" name="add_trip" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">Add Trip</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>