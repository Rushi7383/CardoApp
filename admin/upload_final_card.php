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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_FILES['final_card'])) {

    $order_id = $_POST['order_id'];

    // --- Handle File Upload ---
    if ($_FILES["final_card"]["error"] == 0) {
        $target_dir = "../uploads/completed_cards/";
        // Create a unique filename
        $file_name = $order_id . '_' . uniqid() . '_' . basename($_FILES["final_card"]["name"]);
        $target_file = $target_dir . $file_name;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type (allow images and PDF)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (!in_array($fileType, $allowed_types)) {
            header("location: order_details.php?id={$order_id}&error=invalid_file_type");
            exit();
        }

        // Move the file
        if (move_uploaded_file($_FILES["final_card"]["tmp_name"], $target_file)) {
            // --- Update Database ---
            $new_status = 'Order Completed';
            $sql = "UPDATE orders SET status = ?, final_card_path = ? WHERE id = ?";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $new_status, $file_name, $order_id);

                if ($stmt->execute()) {
                    // Success, redirect back to the order details page
                    header("location: order_details.php?id={$order_id}&update=success");
                    exit();
                } else {
                    // Database update failed
                    header("location: order_details.php?id={$order_id}&error=db_update_failed");
                    exit();
                }
                $stmt->close();
            }
        } else {
            // File move failed
            header("location: order_details.php?id={$order_id}&error=file_move_failed");
            exit();
        }
    } else {
        // File upload error
        header("location: order_details.php?id={$order_id}&error=file_upload_error");
        exit();
    }

    $conn->close();

} else {
    // If not a POST request or data is missing, redirect to the orders list
    header("location: orders.php");
    exit();
}
?>
