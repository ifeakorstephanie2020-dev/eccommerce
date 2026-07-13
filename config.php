<?php
// SECURE CONFIG - Use environment variables in production
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'adaez';

// Create connection with error handling
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8 for security
$conn->set_charset("utf8mb4");

// Enable error reporting for development only
// In production, set display_errors = Off in php.ini
?>
