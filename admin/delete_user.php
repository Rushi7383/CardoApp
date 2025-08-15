<?php
// Start the session to check for authentication
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database configuration
require_once '../includes/config.php';

// Check if user ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {

    // Prepare a delete statement
    $sql = "DELETE FROM users WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $param_id);

        // Set parameters
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records deleted successfully. Redirect to landing page.
            header("location: users.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    $stmt->close();

    // Close connection
    $conn->close();
} else {
    // If no ID was provided, redirect to the users list with an error (optional)
    // Or just redirect to the list as if nothing happened.
    header("location: users.php");
    exit();
}
?>
