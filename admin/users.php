<?php
require_once 'includes/header.php';
require_once '../includes/config.php'; // Path is relative to users.php
?>

<style>
    .page-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        color: white;
        font-weight: bold;
    }
    .btn-primary {
        background-color: #007bff;
    }
    .btn-danger {
        background-color: #dc3545;
    }
    .btn-secondary {
        background-color: #6c757d;
    }
    .btn i {
        margin-right: 5px;
    }
    .search-bar {
        margin-bottom: 1.5rem;
        display: flex;
    }
    .search-bar input {
        padding: 0.5rem;
        width: 300px;
        border: 1px solid #ccc;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-right: none;
    }
    .search-bar button {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    th {
        background-color: #f2f2f2;
    }
    .password-col {
        word-break: break-all;
        max-width: 200px;
    }
</style>

<div class="page-title">
    <h1><i class="fa-solid fa-users"></i> User Management</h1>
    <a href="add_user.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New User</a>
</div>

<div class="search-bar">
    <form action="users.php" method="GET" style="display: flex;">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-search"></i> Search</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Password (Hashed)</th>
            <th>Last Login</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Base SQL query
        $sql = "SELECT id, name, email, phone, password, last_login FROM users";

        // Check if search term is provided
        $search_term = $_GET['search'] ?? '';
        if (!empty($search_term)) {
            $sql .= " WHERE name LIKE ? OR email LIKE ?";
        }

        if ($stmt = $conn->prepare($sql)) {
            // Bind search parameter if it exists
            if (!empty($search_term)) {
                $like_term = "%" . $search_term . "%";
                $stmt->bind_param("ss", $like_term, $like_term);
            }

            // Execute the statement
            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                        echo "<td class='password-col'>" . htmlspecialchars($row["password"]) . "</td>"; // SECURITY RISK
                        echo "<td>" . htmlspecialchars($row["last_login"] ?? 'Never') . "</td>";
                        echo "<td>";
                        echo "<a href='edit_user.php?id=" . $row["id"] . "' class='btn btn-secondary' style='margin-right: 5px;'><i class='fa-solid fa-pencil-alt'></i> Edit</a>";
                        echo "<a href='delete_user.php?id=" . $row["id"] . "' class='btn btn-danger'><i class='fa-solid fa-trash'></i> Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>No users found matching your search.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>Error executing query.</td></tr>";
            }
            $stmt->close();
        }
        $conn->close();
        ?>
    </tbody>
</table>

<?php
require_once 'includes/footer.php';
?>
