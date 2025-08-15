<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: templates.php");
    exit;
}

$template_id = $_GET['id'];
$template_name = '';
$category_id = 0;
$current_image_path = '';
$error_message = '';
$success_message = '';

// --- Form Processing on POST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $template_id = $_POST['template_id'];
    $template_name = trim($_POST['template_name']);
    $category_id = $_POST['category_id'];
    $current_image_path = $_POST['current_image_path'];
    $new_image_name = $current_image_path; // Default to old image

    // Handle file upload if a new image is provided
    if (isset($_FILES["new_image"]) && $_FILES["new_image"]["error"] == 0) {
        $target_dir = "../uploads/templates/";
        $image_name = uniqid() . '_' . basename($_FILES["new_image"]["name"]);
        $target_file = $target_dir . $image_name;

        // Validation
        $check = getimagesize($_FILES["new_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file)) {
                // New image uploaded, so set new image name and delete old one
                $new_image_name = $image_name;
                if (!empty($current_image_path) && file_exists($target_dir . $current_image_path)) {
                    unlink($target_dir . $current_image_path);
                }
            } else {
                $error_message = "Error uploading new image.";
            }
        } else {
            $error_message = "Uploaded file is not a valid image.";
        }
    }

    // Update database if no upload error
    if (empty($error_message)) {
        $sql = "UPDATE templates SET name = ?, category_id = ?, image_path = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sisi", $template_name, $category_id, $new_image_name, $template_id);
            if ($stmt->execute()) {
                $success_message = "Template updated successfully!";
                // Redirect can be used here, or show a success message
                header("location: templates.php?update=success");
                exit();
            } else {
                $error_message = "Error updating template.";
            }
            $stmt->close();
        }
    }
}

// --- Fetch current template data on GET ---
$sql_get = "SELECT name, category_id, image_path FROM templates WHERE id = ?";
if ($stmt_get = $conn->prepare($sql_get)) {
    $stmt_get->bind_param("i", $template_id);
    if ($stmt_get->execute()) {
        $stmt_get->bind_result($template_name, $category_id, $current_image_path);
        if (!$stmt_get->fetch()) {
            header("location: templates.php"); // Not found
            exit;
        }
    }
    $stmt_get->close();
}

// Fetch all categories for the dropdown
$categories = [];
$sql_fetch_cat = "SELECT id, name FROM template_categories ORDER BY name";
$result = $conn->query($sql_fetch_cat);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
$conn->close();
?>

<style>
.form-wrapper { background: white; padding: 2rem; border-radius: 8px; max-width: 700px; margin: auto; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
.form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
.current-image-preview { margin-bottom: 1rem; }
.current-image-preview img { max-width: 200px; border: 1px solid #ddd; padding: 5px; border-radius: 4px; }
.alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
.alert-danger { background-color: #f8d7da; color: #721c24; }
.alert-success { background-color: #d4edda; color: #155724; }
.btn { padding: 0.7rem 1.5rem; border: none; border-radius: 4px; color: white; font-weight: bold; cursor: pointer; text-decoration: none; }
.btn-primary { background-color: #007bff; }
.btn-secondary { background-color: #6c757d; }
</style>

<div class="form-wrapper">
    <h2>Edit Template #<?php echo $template_id; ?></h2>
    <?php if (!empty($error_message)) echo "<div class='alert alert-danger'>{$error_message}</div>"; ?>
    <?php if (!empty($success_message)) echo "<div class='alert alert-success'>{$success_message}</div>"; ?>

    <form action="edit_template.php?id=<?php echo $template_id; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="template_id" value="<?php echo $template_id; ?>">
        <input type="hidden" name="current_image_path" value="<?php echo htmlspecialchars($current_image_path); ?>">

        <div class="form-group">
            <label for="template_name">Template Name</label>
            <input type="text" name="template_name" id="template_name" value="<?php echo htmlspecialchars($template_name); ?>" required>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                <option value="">-- Select a Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Current Image</label>
            <div class="current-image-preview">
                <img src="../uploads/templates/<?php echo htmlspecialchars($current_image_path); ?>" alt="Current Template Image">
            </div>
        </div>

        <div class="form-group">
            <label for="new_image">Upload New Image (Optional)</label>
            <input type="file" name="new_image" id="new_image">
            <small>If you upload a new image, the old one will be replaced.</small>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update Template">
            <a href="templates.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
