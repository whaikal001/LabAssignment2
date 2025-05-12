<?php
header("Content-Type: application/json");

// Connect to the database
$host = 'localhost';
$db = 'wtms'; // your database name
$user = 'root'; // your DB username
$pass = ''; // your DB password
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Read the incoming JSON data
$input = json_decode(file_get_contents("php://input"), true);

$email = $conn->real_escape_string($input['email']);
$password = sha1($input['password']); // same hashing as in registration

// Query the database
$sql = "SELECT id, name, email FROM workers WHERE email='$email' AND password='$password' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $worker = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "worker" => $worker
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
}

$conn->close();
?>
