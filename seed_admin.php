<?php
// Database connection
$servername = "localhost"; // Your server name or IP
$username = "qfmnqxie_premium"; // Your database username
$password = "Premium@2024"; // Your database password
$database = "qfmnqxie_client_registration"; // Your database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash the password using password_hash()
$hashed_password = password_hash('demo@2025', PASSWORD_DEFAULT);

// SQL query to insert the admin data
$sql = "INSERT INTO users (name, email, password, role) 
        VALUES ('Admin', 'demoadmin@gmail.com', '$hashed_password', 'admin')";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Admin data seeded successfully!";
} else {
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>
