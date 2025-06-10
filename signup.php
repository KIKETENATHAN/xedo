<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->password)
) {
    $name = $conn->real_escape_string($data->name);
    $email = $conn->real_escape_string($data->email);
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->query("SELECT email FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        http_response_code(400);
        echo json_encode(array("message" => "Email already exists."));
        exit();
    }

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) {
        http_response_code(201);
        echo json_encode(array("message" => "User was successfully registered."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to register user."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to register user. Data is incomplete."));
}

$conn->close();
?>