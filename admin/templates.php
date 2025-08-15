<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

// --- Feedback Messages Handling ---
$feedback_message = "";
$error_message = "";

if (isset($_GET['cat_add']) && $_GET['cat_add'] == 'success') {
    $feedback_message = "Category added successfully!";
}
if (isset($_GET['cat_delete']) && $_GET['cat_delete'] == 'success') {
    $feedback_message = "Category deleted successfully!";
}
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'category_not_empty':
            $error_message = "Cannot delete a category that still contains templates.";
            break;
        case 'cat_delete_failed':
            $error_message = "Failed to delete the category.";
            break;
        case 'cat_add_failed':
            $error_message = "Failed to add the new category.";
            break;
        case 'cat_name_empty':
            $error_message = "Category name cannot be empty.";
            break;
        default:
            $error_message = "An unknown error occurred.";
            break;
    }
}
?>

<style>
    .page-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    .btn { padding: 0.7rem 1.5rem; border-radius: 4px; text-decoration: none; color: white; font-weight: bold; cursor: pointer; border: none; }
    .btn-primary { background-color: #007bff; }
    .btn-danger { background-color: #dc3545; font-size: 0.8rem; padding: 0.4rem 0.8rem; }
    .btn-secondary { background-color: #6c757d; }
    .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }

    .management-wrapper { display: grid; grid-template-columns: 300px 1fr; gap: 2rem; align-items: start; }
    .panel { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }

    ul.category-list { list-style-type: none; padding: 0; max-height: 400px; overflow-y: auto; }
    ul.category-list li { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid #eee; }

    .template-category-group { margin-bottom: 2rem; }
    .template-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; }
    .template-card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; }
    .template-card img { width: 100%; height: 150px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px; }
    .template-card .info { padding: 1rem; }
    .template-card .info h5 { margin: 0 0 0.5rem 0; }
    .template-card .actions { display: flex; justify-content: space-evenly; padding-bottom: 1rem; }
</style>

<div class="page-title">
    <h1><i class="fa-solid fa-palette"></i> Template Management</h1>
    <a href="add_template.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add New Template</a>
</div>

<?php if (!empty($feedback_message)) echo "<div class='alert alert-success'>{$feedback_message}</div>"; ?>
<?php if (!empty($error_message)) echo "<div class='alert alert-danger'>{$error_message}</div>"; ?>

<div class="management-wrapper">
    <!-- Left Panel for Category Management -->
    <div class="panel">
        <h3>Manage Categories</h3>
        <div class="form-wrapper">
            <form action="add_category.php" method="post">
                <div class="form-group">
                    <label for="category_name">New Category Name</label>
                    <input type="text" name="category_name" id="category_name" required>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Category</button>
            </form>
        </div>
        <hr>
        <h4>Existing Categories</h4>
        <ul class="category-list">
            <?php
            $cat_sql = "SELECT id, name FROM template_categories ORDER BY name";
            $cat_result = $conn->query($cat_sql);
            if ($cat_result && $cat_result->num_rows > 0) {
                while($cat_row = $cat_result->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($cat_row['name']) .
                         " <a href='delete_category.php?id=" . $cat_row['id'] . "' class='btn btn-danger'><i class='fa-solid fa-trash'></i></a></li>";
                }
            } else {
                echo "<li>No categories found.</li>";
            }
            ?>
        </ul>
    </div>

    <!-- Right Panel for Template Display -->
    <div class="panel">
        <h3>Templates</h3>
        <?php
        $template_sql = "SELECT t.id, t.name, t.image_path, c.name as category_name, c.id as category_id
                         FROM templates t
                         JOIN template_categories c ON t.category_id = c.id
                         ORDER BY c.name, t.name";
        $template_result = $conn->query($template_sql);

        $templates_by_category = [];
        if ($template_result && $template_result->num_rows > 0) {
            while($row = $template_result->fetch_assoc()) {
                $templates_by_category[$row['category_name']][] = $row;
            }
        }

        if (empty($templates_by_category)) {
            echo "<p>No templates found. Add a category and a template to get started.</p>";
        } else {
            foreach ($templates_by_category as $category_name => $templates) {
                echo "<div class='template-category-group'>";
                echo "<h4>" . htmlspecialchars($category_name) . "</h4>";
                echo "<div class='template-grid'>";
                foreach ($templates as $template) {
                    $image_url = '../uploads/templates/' . basename($template['image_path']);
                    echo "<div class='template-card'>";
                    echo "<img src='" . htmlspecialchars($image_url) . "' alt='" . htmlspecialchars($template['name']) . "'>";
                    echo "<div class='info'>";
                    echo "<h5>" . htmlspecialchars($template['name']) . "</h5>";
                    echo "<div class='actions'>";
                    echo "<a href='edit_template.php?id=" . $template['id'] . "' class='btn btn-secondary'><i class='fa-solid fa-pencil-alt'></i> Edit</a>";
                    echo "<a href='delete_template.php?id=" . $template['id'] . "' class='btn btn-danger'><i class='fa-solid fa-trash'></i> Delete</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
                echo "</div>";
                echo "</div>";
            }
        }
        $conn->close();
        ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
