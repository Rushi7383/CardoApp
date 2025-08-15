<?php
require_once 'includes/header.php';
require_once '../includes/config.php';
?>

<style>
    .page-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; color: white; font-weight: bold; }
    .btn-primary { background-color: #007bff; }
    .btn-secondary { background-color: #6c757d; }
    .search-bar { margin-bottom: 1.5rem; }
    .search-bar input { padding: 0.5rem; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
    .panel { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    th { background-color: #f2f2f2; }
    .status { padding: 0.2rem 0.5rem; border-radius: 10px; color: white; font-size: 0.8rem; }
    .status-Pending { background-color: #ffc107; color: #333; }
    .status-Order-Submitted { background-color: #17a2b8; }
    .status-Order-Completed { background-color: #28a745; }
    .status-Cancelled { background-color: #dc3545; }
</style>

<div class="page-title">
    <h1>Order Management</h1>
</div>

<div class="panel">
    <div class="search-bar">
        <form action="orders.php" method="GET">
            <input type="text" name="search" placeholder="Search by Order ID, User Name, or Email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Template Name</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // The search logic will be added in a later step. This is the base query.
            $sql = "SELECT
                        o.id as order_id,
                        o.status,
                        o.created_at as order_date,
                        u.name as user_name,
                        u.email as user_email,
                        t.name as template_name
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    JOIN templates t ON o.template_id = t.id
                    ORDER BY o.created_at DESC";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status_class = 'status-' . str_replace(' ', '-', $row['status']);
                    echo "<tr>";
                    echo "<td>#" . htmlspecialchars($row['order_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_name']) . "<br><small>" . htmlspecialchars($row['user_email']) . "</small></td>";
                    echo "<td>" . htmlspecialchars($row['template_name']) . "</td>";
                    echo "<td><span class='status " . htmlspecialchars($status_class) . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                    echo "<td>" . htmlspecialchars(date('M d, Y h:i A', strtotime($row['order_date']))) . "</td>";
                    echo "<td><a href='order_details.php?id=" . $row['order_id'] . "' class='btn btn-primary'>View Details</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No orders found.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>
