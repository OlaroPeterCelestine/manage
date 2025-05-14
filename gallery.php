<?php
include 'comp/nav.php';
require_once 'env/config.php';

$message = "";
$messageType = "";

// Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'upload') {
    $image = $_FILES['image']['name'];
    $target = "gallery/" . time() . '-' . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO gallery_images (image_path) VALUES (?)");
        $stmt->bind_param("s", $target);
        if ($stmt->execute()) {
            $message = "Image uploaded successfully!";
            $messageType = "success";
        } else {
            $message = "Failed to save image to database.";
            $messageType = "danger";
        }
        $stmt->close();
    } else {
        $message = "Failed to upload image.";
        $messageType = "danger";
    }
}

// Edit (Re-upload)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'edit') {
    $id = $_POST['edit_id'];
    $old_image = $_POST['old_image'];

    if (!empty($_FILES['new_image']['name'])) {
        $newImage = $_FILES['new_image']['name'];
        $newPath = "gallery/" . time() . '-' . basename($newImage);

        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $newPath)) {
            if (file_exists($old_image)) unlink($old_image);

            $stmt = $conn->prepare("UPDATE gallery_images SET image_path = ? WHERE id = ?");
            $stmt->bind_param("si", $newPath, $id);
            if ($stmt->execute()) {
                $message = "Image updated successfully!";
                $messageType = "success";
            } else {
                $message = "Database update failed.";
                $messageType = "danger";
            }
            $stmt->close();
        } else {
            $message = "Failed to upload new image.";
            $messageType = "danger";
        }
    } else {
        $message = "No image selected.";
        $messageType = "warning";
    }
}

// Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $stmt = $conn->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if ($imagePath && file_exists($imagePath)) unlink($imagePath);

    $stmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Image deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Failed to delete image.";
        $messageType = "danger";
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM gallery_images ORDER BY id DESC");
?>

<main id="main" class="main">
    <div class="pagetitle"><h1>Gallery Management</h1></div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Add Image</h5>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload">
                            <div class="mb-3">
                                <label class="form-label">Select Image</label>
                                <input type="file" class="form-control" name="image" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload Image</button>
                        </form>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> mt-3"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Images</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><img src="<?= htmlspecialchars($row['image_path']) ?>" height="60"></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                                    onclick="editImage(<?= $row['id'] ?>, '<?= htmlspecialchars($row['image_path']) ?>')">Edit</button>
                                                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No images found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Image</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="edit_id" id="edit_id">
                <input type="hidden" name="old_image" id="old_image">
                <div class="mb-3">
                    <label class="form-label">Select New Image</label>
                    <input type="file" name="new_image" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>
        </div>
    </div></div>
</div>

<script>
function editImage(id, path) {
    document.getElementById('edit_id').value = id;
    document.getElementById('old_image').value = path;
}
</script>

<?php include 'comp/footer.php'; ?>
