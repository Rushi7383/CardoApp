<?php
require_once 'includes/header.php';
require_once '../includes/config.php';

$upload_message = "";
$upload_error = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["banner_image"]) && $_FILES["banner_image"]["error"] == 0) {
        $target_dir = "../uploads/banners/";
        // Create a unique filename to prevent overwriting
        $image_name = uniqid() . '_' . basename($_FILES["banner_image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["banner_image"]["tmp_name"]);
        if ($check === false) {
            $upload_error = "File is not an image.";
        }
        // Check file size (e.g., 5MB limit)
        elseif ($_FILES["banner_image"]["size"] > 5000000) {
            $upload_error = "Sorry, your file is too large.";
        }
        // Allow certain file formats
        elseif (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            $upload_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $upload_error is set to empty
        if (empty($upload_error)) {
            if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $target_file)) {
                // File uploaded successfully, now insert into database
                $banner_text = trim($_POST["banner_text"]);
                $sql = "INSERT INTO banners (image_path, text) VALUES (?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ss", $image_name, $banner_text);
                    if ($stmt->execute()) {
                        $upload_message = "The banner has been uploaded successfully.";
                        // To show the new banner, we don't redirect immediately,
                        // but let the rest of the page render. A redirect would be fine too.
                        // header("location: banners.php");
                    } else {
                        $upload_error = "Error inserting data into database.";
                    }
                    $stmt->close();
                }
            } else {
                $upload_error = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $upload_error = "No file was uploaded or an error occurred.";
    }
}
?>

<style>
    .page-title { margin-bottom: 2rem; }
    .form-wrapper, .banners-list { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input[type="text"], .form-group input[type="file"] { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .btn { padding: 0.7rem 1.5rem; border: none; border-radius: 4px; color: white; font-weight: bold; cursor: pointer; text-decoration: none; }
    .btn-primary { background-color: #007bff; }
    .btn-danger { background-color: #dc3545; }
    .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
    .alert-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    .banner-card { display: flex; align-items: center; gap: 1.5rem; padding: 1rem; border-bottom: 1px solid #eee; }
    .banner-card:last-child { border-bottom: none; }
    .banner-card img { max-width: 200px; max-height: 100px; border-radius: 4px; object-fit: cover; }
    .banner-card .info { flex-grow: 1; }
    .banner-card .info p { margin: 0; color: #666; }
</style>

<div class="page-title">
    <h1>Banner Management</h1>
</div>

<!-- Add Banner Form -->
<div class="form-wrapper">
    <h2>Add New Banner</h2>
    <?php if (!empty($upload_message)) echo "<div class='alert alert-success'>{$upload_message}</div>"; ?>
    <?php if (!empty($upload_error)) echo "<div class='alert alert-danger'>{$upload_error}</div>"; ?>
    <form action="banners.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="banner_image">Banner Image (16:9 recommended)</label>
            <input type="file" name="banner_image" id="banner_image" required>
        </div>
        <div class="form-group">
            <label for="banner_text">Optional Text</label>
            <input type="text" name="banner_text" id="banner_text" placeholder="e.g., Special Offer!">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Banner">
        </div>
    </form>
</div>

<!-- Display Existing Banners -->
<div class="banners-list">
    <h2>Current Banners</h2>
    <?php
    $sql = "SELECT id, image_path, text FROM banners ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $image_url = '../uploads/banners/' . basename($row['image_path']);
            echo '<div class="banner-card">';
            echo '<img src="' . htmlspecialchars($image_url) . '" alt="Banner Image">';
            echo '<div class="info">';
            echo '<h4>Banner #' . htmlspecialchars($row['id']) . '</h4>';
            echo '<p>' . htmlspecialchars($row['text'] ?: 'No text provided') . '</p>';
            echo '</div>';
            echo '<a href="delete_banner.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-danger">Delete</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No banners have been uploaded yet.</p>";
    }
    $conn->close();
    ?>
</div>

<?php
require_once 'includes/footer.php';
?>
