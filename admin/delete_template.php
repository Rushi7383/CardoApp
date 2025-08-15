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

// Check if template ID is provided in the URL
if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $template_id = trim($_GET["id"]);
    $image_path = "";

    // First, get the image path from the database to delete the file from the server
    $sql_select = "SELECT image_path FROM templates WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $template_id);
        if ($stmt_select->execute()) {
            $stmt_select->store_result();
            if ($stmt_select->num_rows == 1) {
                $stmt_select->bind_result($image_path);
                $stmt_select->fetch();
            } else {
                // Template not found, redirect
                header("location: templates.php?error=notfound");
                exit();
            }
        } else {
            // Handle execution error
            header("location: templates.php?error=fetchfailed");
            exit();
        }
        $stmt_select->close();
    }

    // Now, delete the record from the database
    $sql_delete = "DELETE FROM templates WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $template_id);

        if ($stmt_delete->execute()) {
            // If database record deleted successfully, delete the image file
            if (!empty($image_path)) {
                $file_to_delete = '../uploads/templates/' . $image_path;
                if (file_exists($file_to_delete)) {
                    unlink($file_to_delete);
                }
            }
            // Redirect to template list page
            header("location: templates.php?delete=success");
            exit();
        } else {
            header("location: templates.php?error=deletefailed");
            exit();
        }
        $stmt_delete->close();
    }

    // Close connection
    $conn->close();
} else {
    // If no ID was provided, redirect to the templates list
    header("location: templates.php");
    exit();
}
?>
