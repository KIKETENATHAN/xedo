<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include 'db_connection.php';
// Check if the student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student data
$sql = "SELECT department, name, admission_number, course FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_upload'])) {
    $department = htmlspecialchars($_POST['department']);
    $admission_number = htmlspecialchars($_POST['admission_number']);
    $unit_folder = htmlspecialchars($_POST['unit_folder']); // Retrieve selected unit folder
    
    // Define the target directory
    $target_dir = "uploads/$department/$admission_number/$unit_folder/";
    
    // Ensure the unit folder exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["file_upload"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'png', 'mp4', 'jpeg', 'jpg', 'pptx', 'txt'];

    // Check if the file type is allowed
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['error'] = "Only PDF, DOC, DOCX, PPT, PPTX, and TXT files are allowed.";
    } else if (move_uploaded_file($_FILES["file_upload"]["tmp_name"], $target_file)) {
        $_SESSION['message'] = "The file " . htmlspecialchars(basename($_FILES["file_upload"]["name"])) . " has been uploaded to the $unit_folder folder.";
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
    }
    
    // Redirect to the dashboard
    header("Location: student_dashboard.php");
    exit();
}


// Fetch files for the logged-in student
$department = $student['department'];
$admission_number = $student['admission_number'];
$course = $student['course'];
$folder_path = "uploads/$department/$admission_number/";
$files = is_dir($folder_path) ? array_diff(scandir($folder_path), ['.','..']) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
  <script>
    function toggleMenu() { document.getElementById('sidebar').classList.toggle('hidden'); }
    function toggleModal(id) { document.getElementById(id).classList.toggle('hidden'); }
    function openFileModal(url) {
      document.getElementById('fileIframe').src = url;
      toggleModal('fileListModal');
      toggleModal('fileViewModal');
    }
    function closeFileView() {
      document.getElementById('fileIframe').src = '';
      toggleModal('fileViewModal');
    }
  </script>
</head>
<body class="bg-gray-100 flex flex-col md:flex-row">

  <!-- Mobile Menu -->
  <div class="bg-blue-600 text-white md:hidden flex justify-between items-center p-4 fixed">
    <h2 class="text-2xl font-bold">KNAP</h2>
    <button onclick="toggleMenu()" class="focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>

  <!-- Sidebar -->
  <!--<aside id="sidebar" class="hidden md:block md:w-64 bg-blue-600 text-white h-screen p-6">-->
  <aside id="sidebar" class="hidden md:block fixed top-0 left-0 h-full w-64 bg-blue-600 text-white p-6 overflow-y-auto">

    <h2 class="text-2xl font-bold mb-6">STUDENT POE</h2>
    <ul>
      <li class="mb-4"><a href="student_dashboard.php" class="hover:text-blue-300">Dashboard</a></li>
      <li class="mb-4">
        <button onclick="toggleModal('profileModal')" class="hover:text-blue-300">Profile</button>
      </li>
      <li class="mb-4">
        <button onclick="toggleModal('fileListModal')" class="hover:text-blue-300">View Files</button>
      </li>
     <!-- Button to Open Modal -->
    <button id="openModal" class="px-4 py-2 bg-white text-blue-500 rounded-lg hover:bg-blue-600">
        Add Unit of Competency
    </button>
      <li class="mb-4"><a href="logout.php" class="hover:text-blue-300">Logout</a></li>
    </ul>
  </aside>

  <!-- Main -->
  <main class="flex-grow md:ml-64 p-6">
    <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>
    <!-- Messages -->
    <?php if (isset($_SESSION['message'])): ?>
      <div class="bg-green-100 text-green-700 p-4 rounded mb-4"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-100 text-red-700 p-4 rounded mb-4"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <!-- Info -->
    <div class="bg-blue-100 p-4 rounded-lg mb-6">
      <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
      <p><strong>Admission #:</strong> <?php echo htmlspecialchars($admission_number); ?></p>
      <p><strong>Course :</strong> <?php echo htmlspecialchars($course ?? ''); ?></p>
    </div>
   <!-- Upload Form -->
<div class="bg-white p-6 rounded-lg shadow mb-6">
  <h2 class="text-2xl font-semibold text-blue-600 mb-4">Upload File</h2>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-4">
      <label class="block text-blue-600">Department</label>
      <input name="department" readonly value="<?php echo htmlspecialchars($department); ?>" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-600" required>
    </div>
    <div class="mb-4">
      <label class="block text-blue-600">Admission Number</label>
      <input name="admission_number" readonly value="<?php echo htmlspecialchars($admission_number); ?>" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-600" required>
    </div>
    <div class="mb-4">
      <label class="block text-blue-600">Unit Name</label>
      <select name="unit_folder" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-600" required>
        <option value="">Select a Unit</option>
        <?php
        // Construct the folder path based on department and admission number
        $base_path = "uploads/" . htmlspecialchars($department) . "/" . htmlspecialchars($admission_number);

        if (is_dir($base_path)) {
          $unit_folders = array_filter(scandir($base_path), function ($folder) use ($base_path) {
            return is_dir($base_path . "/" . $folder) && $folder !== '.' && $folder !== '..';
          });

          foreach ($unit_folders as $folder) {
            echo '<option value="' . htmlspecialchars($folder) . '">' . htmlspecialchars($folder) . '</option>';
          }
        } else {
          echo '<option disabled>No units found</option>';
        }
        ?>
      </select>
    </div>
    <div class="mb-4">
      <label class="block text-blue-600">Upload File</label>
      <input type="file" name="file_upload" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-blue-600" required>
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
  </form>
</div>
<!-- Footer -->
<footer class="text-center text-sm text-gray-500">© 2025 TVETPrime. All Rights Reserved</footer>

<!-- File List Modal -->
<div id="fileListModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white p-6 rounded-lg shadow w-3/4 h-3/4 overflow-auto relative">
    <button onclick="toggleModal('fileListModal')" class="absolute top-2 right-2 text-red-600 font-bold">&times;</button>
    <h2 class="text-2xl font-semibold text-blue-600 mb-4">Your Files</h2>

    <?php
    foreach ($files as $unit_folder) {
        $unit_path = "$folder_path/$unit_folder";
        if (is_dir($unit_path)) {
            $unit_files = array_diff(scandir($unit_path), ['.', '..']);
            $folder_id = 'folder_' . md5($unit_folder); // Unique ID for JS toggling

            echo '<div class="mb-4 border rounded-lg p-3">';
            echo '<div class="flex justify-between items-center cursor-pointer" onclick="toggleFolder(\'' . $folder_id . '\', \'' . $folder_id . '_icon\')">';
            echo '<div class="flex items-center space-x-2">';
            echo '<span id="' . $folder_id . '_icon" class="text-xl">📁</span>';
            echo '<h3 class="text-lg font-semibold text-gray-800">Unit: ' . htmlspecialchars($unit_folder) . '</h3>';
            echo '</div>';
            echo '<span class="text-blue-600 text-sm">Open</span>';
            echo '</div>';

            echo '<ul id="' . $folder_id . '" class="hidden mt-2 ml-6 list-disc text-sm text-gray-700">';
            if (count($unit_files) === 0) {
                echo '<li class="text-gray-500">No files in this unit.</li>';
            } else {
                foreach ($unit_files as $unit_file) {
                    $file_url = htmlspecialchars("$unit_path/$unit_file");
                    echo '<li class="mb-1 flex justify-between items-center">';
                    echo '<span>' . htmlspecialchars($unit_file) . '</span>';
                    echo '<button onclick="openFileModal(\'' . $file_url . '\')" class="ml-2 px-2 py-1 bg-blue-600 text-white rounded text-xs">View</button>';
                    echo '</li>';
                }
            }
            echo '</ul>';
            echo '</div>';
        }
    }
    ?>
  </div>
</div>

<!-- JavaScript for toggling folders -->
<script>
  function toggleFolder(folderId, iconId) {
    const folder = document.getElementById(folderId);
    const icon = document.getElementById(iconId);
    const isHidden = folder.classList.contains('hidden');

    folder.classList.toggle('hidden');
    icon.textContent = isHidden ? '📂' : '📁'; // Open vs closed folder icons
  }
</script>




  <!-- File View Modal -->
  <div id="fileViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow w-3/4 h-3/4 relative">
      <button onclick="closeFileView()" class="absolute top-2 right-2 text-red-600 font-bold">&times;</button>
      <iframe id="fileIframe" class="w-full h-full"></iframe>
    </div>
  </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow w-96 relative">
      <button onclick="toggleModal('profileModal')" class="absolute top-2 right-2 text-red-600 font-bold">&times;</button>
      <h3 class="text-xl font-semibold mb-4">Profile</h3>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
      <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
      <p><strong>Admission #:</strong> <?php echo htmlspecialchars($admission_number); ?></p>
      <div class="mt-6 flex justify-between">
        <a href="reset_password.php" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Reset Password</a>
        <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Logout</a>
      </div>
    </div>
  </div>
 <!-- Unit of competency Modal -->
    <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Add Unit of Competency</h2>
            <form id="unitForm">
        
                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" id="department" name="department" hidden  readonly value="<?php echo htmlspecialchars($department); ?>"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter department name" required>
                </div>
                <div class="mb-4">
                    <label for="admissionNumber" class="block text-sm font-medium text-gray-700">Admission Number</label>
                    <input type="text" id="admissionNumber" name="admissionNumber" hidden readonly  value="<?php echo htmlspecialchars($admission_number); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter admission number" required>
                </div>
                <div class="mb-4">
                    <label for="unitName" class="block text-sm font-medium text-gray-700">Unit Name</label>
                    <input type="text" id="unitName" name="unitName" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Enter unit name" required>
                </div>
                <div class="flex justify-end">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
 <script>
        // Open and close modal functionality
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const modal = document.getElementById('modal');

        openModal.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Form submission
        const unitForm = document.getElementById('unitForm');

        unitForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const department = document.getElementById('department').value;
            const admissionNumber = document.getElementById('admissionNumber').value;
            const unitName = document.getElementById('unitName').value;
           


            try {
                const response = await fetch('create_subfolder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({unitName,admissionNumber ,department}),
                });

                const result = await response.json();
                alert(result.message);

                if (result.success) {
                    modal.classList.add('hidden');
                    unitForm.reset();
                }
            } catch (error) {
                alert('An error occurred: ' + error.message);
            }
        });
    </script>
</html>
