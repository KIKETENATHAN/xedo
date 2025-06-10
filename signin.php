<?php
include 'config.php';

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->email) &&
    !empty($data->password)
) {
    $email = $conn->real_escape_string($data->email);
    
    $sql = "SELECT id, name, email, password FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $name = $row['name'];
        $email = $row['email'];
        $hashed_password = $row['password'];
        
        if (password_verify($data->password, $hashed_password)) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful",
                "user" => array(
                    "id" => $id,
                    "name" => $name,
                    "email" => $email
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Login failed. Invalid password."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "User not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
}

$conn->close();
?>