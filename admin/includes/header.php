<?php
// Initialize the session
session_start();

// Check if the admin is logged in, if not then redirect to login page
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardo Admin Panel</title>
    <style>
        :root {
            --sidebar-width: 250px;
            --main-bg-color: #f8f9fa;
            --sidebar-bg-color: #343a40;
            --sidebar-text-color: #f8f9fa;
            --sidebar-link-hover-color: #495057;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            background-color: var(--main-bg-color);
        }
        .admin-wrapper {
            display: flex;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg-color);
            color: var(--sidebar-text-color);
            padding: 1rem;
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-top: 0;
            border-bottom: 1px solid #495057;
            padding-bottom: 1rem;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .sidebar ul li a {
            display: block;
            padding: 0.8rem 1rem;
            color: var(--sidebar-text-color);
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: var(--sidebar-link-hover-color);
        }
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 2rem;
        }
        .main-content .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <nav class="sidebar">
            <h2>Cardo Admin</h2>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="banners.php">Banners</a></li>
                <li><a href="templates.php">Templates</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="payments.php">Payments</a></li>
                <li><a href="queries.php">Queries</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <div class="header">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>!</h3>
            </div>
            <!-- Page-specific content starts here -->
