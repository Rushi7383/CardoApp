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

// Check if this is a POST request and if required data is present
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {

    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // --- Validate the new status ---
    $allowed_statuses = ['Pending', 'Order Submitted', 'Cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        // Invalid status provided
        header("location: order_details.php?id={$order_id}&error=invalid_status");
        exit();
    }

    // --- Prepare and execute the UPDATE statement ---
    $sql = "UPDATE orders SET status = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $new_status, $order_id);

        if ($stmt->execute()) {
            // Success, redirect back to the order details page
            header("location: order_details.php?id={$order_id}&update=success");
            exit();
        } else {
            // Execution failed
            header("location: order_details.php?id={$order_id}&error=update_failed");
            exit();
        }
        $stmt->close();
    }

    $conn->close();

} else {
    // If not a POST request or data is missing, redirect to the orders list
    header("location: orders.php");
    exit();
}
?>
