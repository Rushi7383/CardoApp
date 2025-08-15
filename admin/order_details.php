<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: orders.php");
    exit;
}
$order_id = $_GET['id'];

// Fetch order details
$sql = "SELECT
            o.id as order_id, o.owner_name, o.business_name, o.address, o.mobile, o.status, o.created_at as order_date, o.final_card_path,
            u.name as user_name, u.email as user_email,
            t.name as template_name,
            p.utr_number, p.screenshot_path, p.created_at as payment_date, p.status as payment_status
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN templates t ON o.template_id = t.id
        LEFT JOIN payments p ON o.id = p.order_id
        WHERE o.id = ?";

$order = null;
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $order = $result->fetch_assoc();
    } else {
        header("location: orders.php?error=notfound");
        exit;
    }
    $stmt->close();
}
$conn->close();
?>

<style>
    .btn { padding: 0.5rem 1rem; border-radius: 4px; text-decoration: none; color: white; font-weight: bold; border: none; cursor: pointer; }
    .btn-primary { background-color: #007bff; }
    .btn-secondary { background-color: #6c757d; }
    .btn-info { background-color: #17a2b8; }
    .btn-copy { font-size: 0.8rem; padding: 0.2rem 0.5rem; margin-left: 10px; }
    .btn i { margin-right: 5px; }

    .details-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start; }
    .panel { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .panel h3 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 1rem; }
    .detail-item { display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #f2f2f2; }
    .detail-item strong { color: #555; }
    .screenshot-link { color: #007bff; text-decoration: none; font-weight: bold; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group select, .form-group input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
</style>

<div class="page-title">
    <h1><i class="fa-solid fa-file-invoice"></i> Order Details for #<?php echo htmlspecialchars($order['order_id']); ?></h1>
    <a href="orders.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Orders List</a>
</div>

<div class="details-grid">
    <div class="panel">
        <h3><i class="fa-solid fa-user-tie"></i> Customer & Order Information</h3>
        <div class="detail-item"><strong>Owner Name:</strong> <span id="owner_name"><?php echo htmlspecialchars($order['owner_name']); ?></span><button class="btn btn-secondary btn-copy" onclick="copyToClipboard('owner_name')"><i class="fa-solid fa-copy"></i></button></div>
        <div class="detail-item"><strong>Business Name:</strong> <span id="business_name"><?php echo htmlspecialchars($order['business_name']); ?></span><button class="btn btn-secondary btn-copy" onclick="copyToClipboard('business_name')"><i class="fa-solid fa-copy"></i></button></div>
        <div class="detail-item"><strong>Address:</strong> <span id="address"><?php echo htmlspecialchars($order['address']); ?></span><button class="btn btn-secondary btn-copy" onclick="copyToClipboard('address')"><i class="fa-solid fa-copy"></i></button></div>
        <div class="detail-item"><strong>Mobile Number:</strong> <span id="mobile"><?php echo htmlspecialchars($order['mobile']); ?></span><button class="btn btn-secondary btn-copy" onclick="copyToClipboard('mobile')"><i class="fa-solid fa-copy"></i></button></div>
        <hr>
        <div class="detail-item"><strong>User:</strong> <span><?php echo htmlspecialchars($order['user_name']); ?> (<?php echo htmlspecialchars($order['user_email']); ?>)</span></div>
        <div class="detail-item"><strong>Template:</strong> <span><?php echo htmlspecialchars($order['template_name']); ?></span></div>
        <div class="detail-item"><strong>Order Date:</strong> <span><?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($order['order_date']))); ?></span></div>
        <hr>
        <h3><i class="fa-solid fa-credit-card"></i> Payment Information</h3>
        <div class="detail-item"><strong>Payment Status:</strong> <span><?php echo htmlspecialchars($order['payment_status'] ?? 'N/A'); ?></span></div>
        <div class="detail-item"><strong>UTR Number:</strong> <span id="utr_number"><?php echo htmlspecialchars($order['utr_number'] ?? 'N/A'); ?></span><button class="btn btn-secondary btn-copy" onclick="copyToClipboard('utr_number')"><i class="fa-solid fa-copy"></i></button></div>
        <div class="detail-item"><strong>Screenshot:</strong> <span><?php echo $order['screenshot_path'] ? "<a href='../uploads/" . htmlspecialchars($order['screenshot_path']) . "' target='_blank' class='screenshot-link'><i class='fa-solid fa-image'></i> View Screenshot</a>" : "N/A"; ?></span></div>
    </div>

    <div class="panel">
        <h3><i class="fa-solid fa-cogs"></i> Actions</h3>
        <div class="form-group">
            <form action="update_order_status.php" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <label for="status">Change Order Status</label>
                <select name="status" id="status">
                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Order Submitted" <?php echo ($order['status'] == 'Order Submitted') ? 'selected' : ''; ?>>Order Submitted</option>
                    <option value="Order Completed" <?php echo ($order['status'] == 'Order Completed') ? 'selected' : ''; ?> disabled>Order Completed (via upload)</option>
                    <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-info" style="margin-top: 10px; width: 100%;"><i class="fa-solid fa-save"></i> Update Status</button>
            </form>
        </div>
        <hr>
        <?php if ($order['status'] == 'Order Submitted'): ?>
            <div class="form-group">
                <form action="upload_final_card.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <label for="final_card">Upload Completed Card</label>
                    <input type="file" name="final_card" id="final_card" required>
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%;"><i class="fa-solid fa-upload"></i> Upload & Mark as Completed</button>
                </form>
            </div>
        <?php else: ?>
            <p>You can upload the final card once the order status is 'Order Submitted'.</p>
            <?php if (!empty($order['final_card_path'])): ?>
                <a href="../uploads/completed_cards/<?php echo htmlspecialchars($order['final_card_path']); ?>" target="_blank" class="btn btn-primary"><i class="fa-solid fa-download"></i> View Final Card</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    const textToCopy = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(textToCopy).then(() => {
        alert('Copied: ' + textToCopy);
    }).catch(err => {
        alert('Failed to copy text.');
        console.error('Clipboard copy failed: ', err);
    });
}
</script>

<?php
require_once 'includes/footer.php';
?>
