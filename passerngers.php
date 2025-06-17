<?php
session_start();
// Optional: Enable errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'config.php';

// Ensure the user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['user_id'];
$admin_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$admin_query->bind_param("i", $admin_id);
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin = $admin_result->fetch_assoc();

// Handle form submission to create a trainer
if (isset($_POST['create_trainer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $department = $_POST['department'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check for duplicate email
    $email_check_query = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $email_check_query->bind_param("s", $email);
    $email_check_query->execute();
    $email_check_result = $email_check_query->get_result();

    if ($email_check_result->num_rows > 0) {
        $_SESSION['error'] = "Email already used. Please use a different email.";
    } else {
        // Insert new trainer
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, department, password, role) VALUES (?, ?, ?, ?, ?, 'trainer')");
        $stmt->bind_param("sssss", $name, $email, $phone_number, $department, $password);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Trainer created successfully.";
        } else {
            $_SESSION['error'] = "Failed to create trainer. Error: " . $conn->error;
        }
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch trainers
$trainer_query = "SELECT id, name, email, phone_number, department FROM users WHERE role = 'trainer'";
$trainer_result = $conn->query($trainer_query);
$trainers = [];
if ($trainer_result->num_rows > 0) {
    while ($row = $trainer_result->fetch_assoc()) {
        $trainers[] = $row;
    }
}
// Handle form submission to edit a trainer
if (isset($_POST['edit_trainer'])) {
    $trainer_id = $_POST['trainer_id'];
    $name = $_POST['edit_name'];
    $email = $_POST['edit_email'];
    $phone_number = $_POST['edit_phone_number'];
    $department = $_POST['edit_department'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, department = ? WHERE id = ? AND role = 'trainer'");
    $stmt->bind_param("ssssi", $name, $email, $phone_number, $department, $trainer_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Trainer updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update trainer. Error: " . $conn->error;
    }
    header("Location: admin_dashboard.php");
    exit();
}
// Handle trainer deletion
if (isset($_POST['delete_trainer'])) {
    $trainer_id = $_POST['trainer_id'];
    $delete_query = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_query->bind_param("i", $trainer_id);
    if ($delete_query->execute()) {
        $_SESSION['message'] = "Trainer deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete trainer.";
    }
    header("Location: admin_dashboard.php");
    exit();
}
// Fetch counts
$trainersQuery = "SELECT COUNT(*) AS trainer_count FROM users WHERE role = 'trainer'";
$studentsQuery = "SELECT COUNT(*) AS student_count FROM users WHERE role = 'student'";

// Execute queries
$trainerResult = $conn->query($trainersQuery);
$studentResult = $conn->query($studentsQuery);

$trainerCount = $trainerResult->fetch_assoc()['trainer_count'] ?? 0;
$studentCount = $studentResult->fetch_assoc()['student_count'] ?? 0;

// Count subfolders in marksheet folder
$marksheetDir = 'marksheet';
$totalClasses = iterator_count(new FilesystemIterator($marksheetDir, FilesystemIterator::SKIP_DOTS));

// Count subfolders in uploads folder
$uploadsDir = 'uploads';
$totalDepartments = iterator_count(new FilesystemIterator($uploadsDir, FilesystemIterator::SKIP_DOTS));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POE Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("hidden");
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle("hidden");
        }
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("hidden");
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.toggle("hidden");
        }

        function fillEditForm(trainer) {
            document.getElementById("trainer_id").value = trainer.id;
            document.getElementById("edit_name").value = trainer.name;
            document.getElementById("edit_email").value = trainer.email;
            document.getElementById("edit_phone_number").value = trainer.phone_number;
            document.getElementById("edit_department").value = trainer.department;
            toggleModal('editTrainerModal');
        }
    </script>
     <script>
        // Update student folders based on selected department
        function updateStudentFolders(department) {
            const studentFolderSelect = document.getElementById('student_folder');
            studentFolderSelect.innerHTML = '<option value="">Select Student Folder</option>'; // Reset student folder dropdown
            
            if (department) {
                fetch('get_student_folders.php?department=' + department)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function(folder) {
                            const option = document.createElement('option');
                            option.value = folder;
                            option.textContent = folder;
                            studentFolderSelect.appendChild(option);
                        });
                    });
            }
        }
    </script>
    <!-- Include Chart.js -->
    <script>
    document.querySelector('#openFileViewerButton').addEventListener('click', function () {
    const iframe = document.querySelector('#fileViewerIframe');
    iframe.src = 'view.html'; // Dynamically set the file URL
});
</script>
</head>
<body class="bg-gray-100">
    <!-- Mobile Header -->
    <header class="bg-blue-900 text-white flex justify-between items-center p-4 md:hidden">
        <button onclick="toggleSidebar()" class="text-xl">&#9776;</button>
        <h1 class="text-lg font-bold">POE Dashboard</h1>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-blue-900 text-white fixed h-screen hidden md:block">
    <div>
        <div class="text-center text-xl font-bold py-6 border-b border-yellow-700">POE Dashboard</div>
        <nav class="px-4 py-6">
            <ul>
                <li class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11M9 21V3M15 21v-6a3 3 0 013-3h3" />
                    </svg>
                    <a href="admin_dashboard.php" class="block py-2 px-4 rounded hover:bg-yellow-700">Dashboard</a>
                </li>
                <li class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.866-4 6-4 6s4-2.134 4-6 4-2.134 4-6-4 2.134-4 6zm0 0v10m-4 0h8" />
                    </svg>
                    <button onclick="toggleModal('createTrainerModal')" class="block w-full text-left py-2 px-4 rounded hover:bg-yellow-700">
                        Create Trainer
                    </button>
                </li>
            <li class="mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 20.023 2 15.5 6.477 4.5 12 4.5zm0 1.5C7.96 6 4.5 9.46 4.5 13.5S7.96 21 12 21s7.5-3.46 7.5-7.5S16.04 6 12 6zm0 2.25a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 1.5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                </svg>
                <button id="openMarksheetBtn" class="block py-2 px-4 rounded hover:bg-yellow-700">
                    Trainer Marksheet
              </button>
            </li>

            <li class="mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 20.023 2 15.5 6.477 4.5 12 4.5zm0 1.5C7.96 6 4.5 9.46 4.5 13.5S7.96 21 12 21s7.5-3.46 7.5-7.5S16.04 6 12 6zm0 2.25a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 1.5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z" />
                </svg>
                  <button class="btn btn-primary block py-2 px-4 rounded hover:bg-yellow-700" data-bs-toggle="modal" data-bs-target="#viewFileModal">
                    Student POE
                </button>
            </li>

<!-- Add more links as needed -->

                <!-- Add more links as needed -->
          
                <li class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 00-8 0v3H7a1 1 0 00-1 1v7a1 1 0 001 1h10a1 1 0 001-1v-7a1 1 0 00-1-1h-1V7zm0 0V5a4 4 0 00-8 0v2" />
                    </svg>
                   <a href="javascript:void(0);" onclick="openModal()" class="block py-2 px-4 rounded hover:bg-yellow-700">Trainer List</a>
                </li>
                <li class="mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
                    </svg>
                    <a href="logout.php" class="block py-2 px-4 bg-red-600 rounded hover:bg-red-700">Logout</a>
                </li>
            </ul>
        </nav>
    </div>
    <footer class="text-center py-4 bg-blue-800">&copy; 2025 TVETPrime</footer>
</aside>


        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-6 md:ml-64">
            <header class="flex items-center justify-between bg-white p-4 rounded shadow">
                <h1 class="text-2xl font-bold text-blue-900">Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
            </header>

            <!-- Success and Error Messages -->
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
            
            <!--summary cards start-->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
                <!-- Trainers Card -->
                <div class="bg-blue-500 text-white rounded-lg shadow-lg p-4">
                    <h2 class="text-lg font-semibold">Trainers</h2>
                    <p class="text-4xl font-bold"><?php echo htmlspecialchars($trainerCount); ?></p>
                </div>
            
                <!-- Students Card -->
                <div class="bg-green-500 text-white rounded-lg shadow-lg p-4">
                    <h2 class="text-lg font-semibold">Students</h2>
                    <p class="text-4xl font-bold"><?php echo htmlspecialchars($studentCount); ?></p>
                </div>
            
                <!-- Total Classes Card -->
                <div class="bg-yellow-500 text-white rounded-lg shadow-lg p-4">
                    <h2 class="text-lg font-semibold">Total Classes</h2>
                    <p class="text-4xl font-bold"><?php echo htmlspecialchars($totalClasses); ?></p>
                </div>
            
                <!-- Departments Card -->
                <div class="bg-purple-500 text-white rounded-lg shadow-lg p-4">
                    <h2 class="text-lg font-semibold">Departments</h2>
                    <p class="text-4xl font-bold"><?php echo htmlspecialchars($totalDepartments); ?></p>
                </div>
            </div>
 <!--summary cards end-->
            
            <body class="flex items-center justify-center h-screen bg-gradient-to-br from-blue-900 to-blue-600">
    <!-- Futuristic Quote Card -->
        <div id="quoteCard" class="max-w-sm p-8 bg-gradient-to-br from-blue-700 to-blue-500 rounded-lg shadow-2xl text-center transform hover:scale-105 transition duration-300">
            <p id="quoteText" class="text-2xl font-semibold text-white tracking-wide">Education is the key to success.</p>
        </div>
 <!--graphs stat-->
 
 <!--graphs end-->
            
<!-- Trainer table  Pop up Modal -->
<div id="trainerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white w-3/4 rounded shadow-lg overflow-auto max-h-screen">
        <!-- Modal Header -->
        <div class="flex justify-between items-center bg-blue-600 text-white px-4 py-2 rounded-t">
            <h2 class="text-lg font-semibold">Trainer List</h2>
            <button onclick="closeModal()" class="text-white font-bold">&times;</button>
        </div>
        <!-- Modal Content -->
        <div class="p-4">
            <section id="trainer-list">
                <table class="w-full border-collapse border border-gray-300 mt-4 bg-white rounded shadow">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border border-gray-300 p-2">Name</th>
                            <th class="border border-gray-300 p-2">Email</th>
                            <th class="border border-gray-300 p-2">Phone Number</th>
                            <th class="border border-gray-300 p-2">Department</th>
                            <th class="border border-gray-300 p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trainers as $trainer): ?>
                            <tr>
                                <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($trainer['name']); ?></td>
                                <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($trainer['email']); ?></td>
                                <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($trainer['phone_number']); ?></td>
                                <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($trainer['department']); ?></td>
                                <td class="border border-gray-300 p-2">
                                    <form action="admin_dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this trainer?');">
                                        <input type="hidden" name="trainer_id" value="<?php echo $trainer['id']; ?>">
                                        <button type="submit" name="delete_trainer" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                                <td> 
                                    <button onclick="fillEditForm(<?php echo htmlspecialchars(json_encode($trainer)); ?>)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</div>

    <!-- Create Trainer Modal -->
    <div id="createTrainerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-blue-700">Create Trainer</h3>
                <button onclick="toggleModal('createTrainerModal')" class="text-red-500 font-bold text-lg">&times;</button>
            </div>
            
            <form action="admin_dashboard.php" method="POST" class="space-y-4 mt-4">
                <input type="text" name="name" placeholder="Trainer Name" class="block w-full border rounded p-2" required>
                <input type="email" name="email" placeholder="Trainer Email" class="block w-full border rounded p-2" required>
                <input type="text" name="phone_number" placeholder="Phone Number" class="block w-full border rounded p-2" required>
                <input type="text" name="department" placeholder="Department" class="block w-full border rounded p-2" required>
                <input type="password" name="password" placeholder="Password" class="block w-full border rounded p-2" required>
                <button type="submit" name="create_trainer" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Create Trainer</button>
            </form>
        </div>
    </div>
     <!-- Edit Trainer Modal -->
    <div id="editTrainerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded shadow w-11/12 sm:w-1/2">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-blue-700">Edit Trainer</h3>
                <button onclick="toggleModal('editTrainerModal')" class="text-red-500 font-bold text-lg">&times;</button>
            </div>
            
            <form action="admin_dashboard.php" method="POST" class="space-y-4 mt-4">
                <input type="hidden" id="trainer_id" name="trainer_id">
                <input type="text" id="edit_name" name="edit_name" placeholder="Trainer Name" class="block w-full border rounded p-2" required>
                <input type="email" id="edit_email" name="edit_email" placeholder="Trainer Email" class="block w-full border rounded p-2" required>
                <input type="text" id="edit_phone_number" name="edit_phone_number" placeholder="Phone Number" class="block w-full border rounded p-2" required>
                <input type="text" id="edit_department" name="edit_department" placeholder="Department" class="block w-full border rounded p-2" required>
                <button type="submit" name="edit_trainer" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Update Trainer</button>
            </form>
        </div>
    </div>
 
<!--poe view-->
<!--poe  File Browser Modal -->
<div class="modal fade" id="viewFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe 
                    src="view.html" 
                    frameborder="0" 
                    style="width: 70%; height: 500px;" 
                    id="fileViewerIframe">
                </iframe>
            </div>
            <div class="modal-footer">
             
            </div>
        </div>
    </div>
</div>

 <!-- Modal Backdrop -->
        <div id="marksheetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-3/4 max-w-4xl max-h-[80vh] flex flex-col">
                <!-- Modal Header -->
                <div class="flex justify-between items-center border-b p-4">
                    <h3 class="text-xl font-semibold">Marksheet Contents</h3>
                    <button class="close-modal-btn text-blue-600 hover:text-yellow-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
        
                <!-- Modal Content -->
                <div id="marksheetContent" class="p-4 overflow-y-auto flex-grow">
                    <!-- Content will be loaded here via PHP/JS -->
                    <div class="text-center py-8 text-gray-500">
                        Loading contents...
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="border-t p-4 flex justify-between items-center">
                    <div id="currentPath" class="text-sm text-gray-600">POE Trainers Marksheets</div>
                    <button class="close-modal-btn bg-blue-700 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">Close</button>
                </div>
            </div>
        </div>
    </div>
    </body>
<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Modal elements
    const modal = document.getElementById('marksheetModal');
    const contentDiv = document.getElementById('marksheetContent');
    const currentPathDiv = document.getElementById('currentPath');
    const closeButtons = document.querySelectorAll('.close-modal-btn');

    // Open modal when button is clicked
    document.getElementById('openMarksheetBtn').addEventListener('click', function () {
        modal.classList.remove('hidden');
        loadFolderContents('');
    });

    // Close modal when X or Close button is clicked
    if (closeButtons) {
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });
        });
    }

    // Close modal when clicking outside content
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});


// Global functions
window.loadFolderContents = async function(relativePath) {
    const modalContent = document.getElementById('marksheetContent');
    const currentPathDiv = document.getElementById('currentPath');
    
    try {
        // Update current path display
        currentPathDiv.textContent = `Path: /marksheet${relativePath ? '/' + relativePath : ''}`;
        
        // Show loading state
        modalContent.innerHTML = '<div class="text-center py-8">Loading contents...</div>';
        
        // Fetch folder contents
        const response = await fetch(`get_contents.php?path=${encodeURIComponent(relativePath)}`);
        if (!response.ok) throw new Error('Failed to load contents');
        
        const items = await response.json();
        
        // Build HTML content
        let html = `
            <div class="overflow-y-auto max-h-[60vh] space-y-2">
                ${relativePath ? `
                <button onclick="loadFolderContents('${relativePath.split('/').slice(0, -1).join('/')}')" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded mb-2 text-sm">
                    ← Back
                </button>
                ` : ''}
        `;
        
        // Add folders
        items.filter(item => item.type === 'directory').forEach(item => {
            html += `
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded border">
                    <div class="flex items-center min-w-0">
                        <svg class="h-5 w-5 text-yellow-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        <span class="truncate">${item.name}</span>
                    </div>
                    <button onclick="loadFolderContents('${relativePath ? relativePath + '/' : ''}${item.name}')" 
                            class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-1 px-3 rounded text-sm whitespace-nowrap">
                        Open
                    </button>
                </div>
            `;
        });
        
        // Add files
        items.filter(item => item.type === 'file').forEach(item => {
            html += `
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded border">
                    <div class="flex items-center min-w-0">
                        <svg class="h-5 w-5 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="truncate">${item.name}</span>
                    </div>
                    <button onclick="viewFile('${relativePath ? relativePath + '/' : ''}${item.name}')" 
                            class="bg-green-100 hover:bg-green-200 text-green-800 py-1 px-3 rounded text-sm whitespace-nowrap">
                        View
                    </button>
                </div>
            `;
        });
        
        html += '</div>';
        modalContent.innerHTML = html;
        
    } catch (error) {
        modalContent.innerHTML = `
            <div class="text-red-500 p-4">
                Error loading contents: ${error.message}
            </div>
        `;
        console.error('Error:', error);
    }
};

window.viewFile = async function(filePath) {
    const modalContent = document.getElementById('marksheetContent');
    
    try {
        modalContent.innerHTML = '<div class="text-center py-8">Loading file...</div>';
        
        // Get file extension
        const fileExt = filePath.split('.').pop().toLowerCase();
        const isViewable = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt'].includes(fileExt);
        
        if (isViewable) {
            // For viewable files, open in new tab
            window.open(`view_filem.php?path=${encodeURIComponent(filePath)}`, '_blank');
            
            // Show message in modal
            modalContent.innerHTML = `
                <div class="text-center p-8">
                    <p class="mb-4">The file is opening in a new tab...</p>
                    <button onclick="loadFolderContents('${filePath.split('/').slice(0, -1).join('/')}')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        ← Back to folder
                    </button>
                </div>
            `;
        } else {
            // For non-viewable files, force download
            const a = document.createElement('a');
            a.href = `view_filem.php?path=${encodeURIComponent(filePath)}`;
            a.download = filePath.split('/').pop();
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            // Show message in modal
            modalContent.innerHTML = `
                <div class="text-center p-8">
                    <p class="mb-4">The file is downloading...</p>
                    <button onclick="loadFolderContents('${filePath.split('/').slice(0, -1).join('/')}')" 
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        ← Back to folder
                    </button>
                </div>
            `;
        }
        
    } catch (error) {
        console.error('File load error:', error);
        modalContent.innerHTML = `
            <div class="text-red-500 p-4">
                Error handling file: ${error.message}
                <div class="mt-4">
                    <button onclick="loadFolderContents('${filePath.split('/').slice(0, -1).join('/')}')" 
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded">
                        ← Back to folder
                    </button>
                </div>
            </div>
        `;
    }
};
</script>

<!-- JavaScript -->
<script>
    // Get modal and buttons
    const modal = document.getElementById('viewModal');
    const openModalButton = document.getElementById('openViewModal');
    const closeModalButton = document.getElementById('closeViewModal');
    const modalContent = document.getElementById('modalContent');

    // Open modal and load content
    openModalButton.addEventListener('click', () => {
        fetch('pages/view.php')
            .then(response => response.text())
            .then(data => {
                modalContent.innerHTML = data; // Load the content of view.php
                modal.classList.remove('hidden'); // Show the modal
            })
            .catch(error => {
                console.error('Error loading content:', error);
                modalContent.innerHTML = '<p class="text-red-600">Failed to load content.</p>';
            });
    });

    // Close modal
    closeModalButton.addEventListener('click', () => {
        modal.classList.add('hidden');
        modalContent.innerHTML = ''; // Clear modal content
    });

    // Close modal when clicking outside the modal
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            modalContent.innerHTML = ''; // Clear modal content
        }
    });

</script>
<!-- JavaScript for Modal -->
<script>
    function openModal() {
        document.getElementById('trainerModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('trainerModal').classList.add('hidden');
    }

    // Close modal when clicking outside content
    document.getElementById('trainerModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<script>
        // Array of education quotes
        const quotes = [
            "Education is the most powerful weapon you can use to change the world. – Nelson Mandela",
            "The roots of education are bitter, but the fruit is sweet. – Aristotle",
            "An investment in knowledge pays the best interest. – Benjamin Franklin",
            "Education is the passport to the future, for tomorrow belongs to those who prepare for it today. – Malcolm X",
            "Live as if you were to die tomorrow. Learn as if you were to live forever. – Mahatma Gandhi",
            "The purpose of education is to replace an empty mind with an open one. – Malcolm Forbes",
            "Education is not preparation for life; education is life itself. – John Dewey"
        ];

        let currentIndex = 0;

        // Function to update the quote
        function updateQuote() {
            const quoteText = document.getElementById('quoteText');
            quoteText.textContent = quotes[currentIndex];
            currentIndex = (currentIndex + 1) % quotes.length; // Cycle through the quotes
        }

        // Update the quote every 2 seconds
        setInterval(updateQuote, 3000);
    </script>
   
</html>
