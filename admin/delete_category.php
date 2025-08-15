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

// Check if category ID is provided
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $category_id = trim($_GET["id"]);

    // 1. Check if any templates are using this category
    $sql_check = "SELECT COUNT(id) as template_count FROM templates WHERE category_id = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $category_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        $row = $result->fetch_assoc();
        $template_count = $row['template_count'];
        $stmt_check->close();

        if ($template_count > 0) {
            // Category is not empty, redirect with an error
            header("location: templates.php?error=category_not_empty");
            exit();
        }
    } else {
        // SQL error
        header("location: templates.php?error=check_failed");
        exit();
    }

    // 2. If check passes, delete the category
    $sql_delete = "DELETE FROM template_categories WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $category_id);

        if ($stmt_delete->execute()) {
            // Success
            header("location: templates.php?cat_delete=success");
            exit();
        } else {
            // Deletion failed
            header("location: templates.php?error=cat_delete_failed");
            exit();
        }
        $stmt_delete->close();
    }

    $conn->close();
} else {
    // No ID provided, redirect
    header("location: templates.php");
    exit();
}
?>
