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

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    $new_category_name = trim($_POST['category_name']);

    if (!empty($new_category_name)) {
        // Prepare an insert statement
        $sql = "INSERT INTO template_categories (name) VALUES (?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $new_category_name);

            if ($stmt->execute()) {
                // Redirect back with success message
                header("location: templates.php?cat_add=success");
                exit();
            } else {
                // Redirect back with error message
                header("location: templates.php?error=cat_add_failed");
                exit();
            }
            $stmt->close();
        }
    } else {
        // Redirect if name is empty
        header("location: templates.php?error=cat_name_empty");
        exit();
    }
    $conn->close();
} else {
    // If not a POST request, redirect back to the templates page
    header("location: templates.php");
    exit();
}
?>
