<?php
include "db_connect.php";

// Get the raw POST data
$json = file_get_contents('php://input');

// Check if data was received
if (empty($json)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "No data received"]);
    exit;
}

// Decode the JSON data
$data = json_decode($json, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Invalid JSON data"]);
    exit;
}

// Validate required fields
$required = ['full_name', 'email', 'password', 'phone', 'address'];
$missing = [];

foreach ($required as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Missing required fields: " . implode(', ', $missing)
    ]);
    exit;
}

// Sanitize inputs
$full_name = $conn->real_escape_string(trim($data['full_name']));
$email = $conn->real_escape_string(trim($data['email']));
$phone = $conn->real_escape_string(trim($data['phone']));
$address = $conn->real_escape_string(trim($data['address']));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => "Invalid email format"
    ]);
    exit;
}

// Check if email already exists
$check_sql = "SELECT id FROM workers WHERE email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "error" => "Email already registered"
    ]);
    $stmt->close();
    exit;
}
$stmt->close();

// Hash password securely
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert new worker using prepared statement
$sql = "INSERT INTO workers (full_name, email, password, phone, address) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $full_name, $email, $password, $phone, $address);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode([
        "success" => true,
        "message" => "Registration successful"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database error: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>