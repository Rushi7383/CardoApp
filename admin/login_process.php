<?php
// Start the session at the very beginning.
session_start();

// Include the database configuration file.
require_once '../includes/config.php';

// Check if the form was submitted via POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get username and password from the form.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a select statement to prevent SQL injection.
    $sql = "SELECT id, username, password FROM admin WHERE username = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters.
        $stmt->bind_param("s", $param_username);

        // Set parameters.
        $param_username = $username;

        // Attempt to execute the prepared statement.
        if ($stmt->execute()) {
            // Store result.
            $stmt->store_result();

            // Check if username exists, if yes then verify password.
            if ($stmt->num_rows == 1) {
                // Bind result variables.
                $stmt->bind_result($id, $username, $hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, so start a new session.

                        // Store data in session variables.
                        $_SESSION["admin_loggedin"] = true;
                        $_SESSION["admin_id"] = $id;
                        $_SESSION["admin_username"] = $username;

                        // Redirect user to admin dashboard page.
                        header("location: index.php");
                        exit;
                    } else {
                        // Password is not valid, redirect back to login page with an error.
                        header("location: login.php?error=invalid_credentials");
                        exit;
                    }
                }
            } else {
                // Username doesn't exist, redirect back to login page with an error.
                header("location: login.php?error=invalid_credentials");
                exit;
            }
        } else {
            // Something went wrong with the execution.
            header("location: login.php?error=execution_failed");
            exit;
        }

        // Close statement.
        $stmt->close();
    }

    // Close connection.
    $conn->close();
} else {
    // If the request method is not POST, redirect to login page.
    header("location: login.php");
    exit;
}
?>
