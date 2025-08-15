<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

$template_name = "";
$category_id = 0;
$error_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $template_name = trim($_POST["template_name"]);
    $category_id = $_POST["category_id"];
    $new_category_name = trim($_POST["new_category_name"]);

    // --- 1. Determine the Category ID ---
    if (!empty($new_category_name)) {
        // Admin is creating a new category.
        $sql_cat = "INSERT INTO template_categories (name) VALUES (?)";
        if ($stmt_cat = $conn->prepare($sql_cat)) {
            $stmt_cat->bind_param("s", $new_category_name);
            if ($stmt_cat->execute()) {
                $category_id = $conn->insert_id; // Get the ID of the new category
            } else {
                $error_message = "Error creating new category.";
            }
            $stmt_cat->close();
        }
    } elseif (empty($category_id)) {
        $error_message = "Please select an existing category or create a new one.";
    }

    // --- 2. Handle File Upload ---
    if (empty($error_message)) {
        if (isset($_FILES["template_image"]) && $_FILES["template_image"]["error"] == 0) {
            $target_dir = "../uploads/templates/";
            $image_name = uniqid() . '_' . basename($_FILES["template_image"]["name"]);
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Basic validation
            $check = getimagesize($_FILES["template_image"]["tmp_name"]);
            if ($check === false) {
                $error_message = "File is not an image.";
            } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            }

            if (empty($error_message)) {
                if (move_uploaded_file($_FILES["template_image"]["tmp_name"], $target_file)) {
                    // --- 3. Insert Template Record ---
                    $sql_template = "INSERT INTO templates (name, category_id, image_path) VALUES (?, ?, ?)";
                    if ($stmt_template = $conn->prepare($sql_template)) {
                        $stmt_template->bind_param("sis", $template_name, $category_id, $image_name);
                        if ($stmt_template->execute()) {
                            // Success, redirect
                            header("location: templates.php");
                            exit();
                        } else {
                            $error_message = "Error saving template to database.";
                        }
                        $stmt_template->close();
                    }
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            $error_message = "Please select an image to upload.";
        }
    }
}

// Fetch existing categories for the dropdown
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
.form-wrapper { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); max-width: 700px; margin: auto; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
.form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
.alert-danger { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
.btn { padding: 0.7rem 1.5rem; border: none; border-radius: 4px; color: white; font-weight: bold; cursor: pointer; text-decoration: none; }
.btn-primary { background-color: #007bff; }
.btn-secondary { background-color: #6c757d; }
</style>

<div class="form-wrapper">
    <h2>Add New Template</h2>
    <p>Fill out the form to add a new business card template.</p>
    <?php if (!empty($error_message)) echo "<div class='alert alert-danger'>{$error_message}</div>"; ?>
    <form action="add_template.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="template_name">Template Name</label>
            <input type="text" name="template_name" id="template_name" required>
        </div>
        <div class="form-group">
            <label for="template_image">Template Image</label>
            <input type="file" name="template_image" id="template_image" required>
        </div>
        <hr>
        <div class="form-group">
            <label for="category_id">Assign to Existing Category</label>
            <select name="category_id" id="category_id">
                <option value="">-- Select a Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <p style="text-align: center; font-weight: bold;">OR</p>
        <div class="form-group">
            <label for="new_category_name">Create a New Category</label>
            <input type="text" name="new_category_name" id="new_category_name" placeholder="e.g., 'Modern', 'Classic', etc.">
        </div>
        <hr>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Template">
            <a href="templates.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
