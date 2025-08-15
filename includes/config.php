<?php
/*
 * Database Configuration and Connection
 *
 * This file defines the database credentials and creates a connection
 * to the MySQL database. This file should be included in any PHP script
 * that needs to interact with the database.
 */

// --- Database Credentials ---
// Replace with your actual database credentials.
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'cardo_db');

// --- Create Database Connection ---
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// --- Check Connection ---
if ($conn->connect_error) {
    // Stop execution and display an error message if the connection fails.
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 to support a wide range of characters
$conn->set_charset("utf8mb4");

?>
