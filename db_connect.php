<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection settings
$host = "localhost";      // Usually "localhost" if the PHP and MySQL are on the same server
$username = "root";       // Default XAMPP/WAMP username is "root"
$password = "";           // Default XAMPP/WAMP password is empty
$database = "wtms_db";    // Your database name

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "error" => "Database connection failed: " . mysqli_connect_error()
    ]));
}
?>