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
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.8rem 1rem;
            color: var(--sidebar-text-color);
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s;
        }
        .sidebar ul li a .fa-fw {
            width: 1.25em;
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
                <li><a href="index.php"><i class="fa-solid fa-fw fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fa-solid fa-fw fa-users"></i> Users</a></li>
                <li><a href="banners.php"><i class="fa-solid fa-fw fa-image"></i> Banners</a></li>
                <li><a href="templates.php"><i class="fa-solid fa-fw fa-palette"></i> Templates</a></li>
                <li><a href="orders.php"><i class="fa-solid fa-fw fa-box-open"></i> Orders</a></li>
                <li><a href="payments.php"><i class="fa-solid fa-fw fa-credit-card"></i> Payments</a></li>
                <li><a href="queries.php"><i class="fa-solid fa-fw fa-circle-question"></i> Queries</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-fw fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </nav>
        <main class="main-content">
            <div class="header">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>!</h3>
            </div>
            <!-- Page-specific content starts here -->
