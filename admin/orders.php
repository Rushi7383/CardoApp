<?php
require_once 'includes/header.php';
require_once '../includes/config.php';
?>

<style>
    .page-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; color: white; font-weight: bold; border: none; cursor: pointer; }
    .btn i { margin-right: 5px; }
    .btn-primary { background-color: #007bff; }
    .btn-secondary { background-color: #6c757d; }
    .search-bar { margin-bottom: 1.5rem; }
    .search-bar form { display: flex; }
    .search-bar input { padding: 0.5rem; width: 300px; border: 1px solid #ccc; border-top-left-radius: 4px; border-bottom-left-radius: 4px; border-right: none;}
    .search-bar button { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
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
    <h1><i class="fa-solid fa-box-open"></i> Order Management</h1>
</div>

<div class="panel">
    <div class="search-bar">
        <form action="orders.php" method="GET">
            <input type="text" name="search" placeholder="Search by Order ID, User Name, or Email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-search"></i> Search</button>
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
            $sql = "SELECT
                        o.id as order_id, o.status, o.created_at as order_date,
                        u.name as user_name, u.email as user_email,
                        t.name as template_name
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    JOIN templates t ON o.template_id = t.id";

            $search_term = $_GET['search'] ?? '';
            if (!empty($search_term)) {
                $sql .= " WHERE o.id LIKE ? OR u.name LIKE ? OR u.email LIKE ?";
            }
            $sql .= " ORDER BY o.created_at DESC";

            if ($stmt = $conn->prepare($sql)) {
                if (!empty($search_term)) {
                    $like_term = "%" . $search_term . "%";
                    // For ID search, we don't want partial matches, but LIKE works for numbers.
                    // For a more precise ID search, we'd cast, but this is simpler and safe.
                    $stmt->bind_param("sss", $like_term, $like_term, $like_term);
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $status_class = 'status-' . str_replace(' ', '-', $row['status']);
                        echo "<tr>";
                        echo "<td>#" . htmlspecialchars($row['order_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_name']) . "<br><small>" . htmlspecialchars($row['user_email']) . "</small></td>";
                        echo "<td>" . htmlspecialchars($row['template_name']) . "</td>";
                        echo "<td><span class='status " . htmlspecialchars($status_class) . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                        echo "<td>" . htmlspecialchars(date('M d, Y h:i A', strtotime($row['order_date']))) . "</td>";
                        echo "<td><a href='order_details.php?id=" . $row['order_id'] . "' class='btn btn-primary'><i class='fa-solid fa-eye'></i> View Details</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No orders found.</td></tr>";
                }
                $stmt->close();
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>
