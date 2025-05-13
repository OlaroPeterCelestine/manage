<?php
include 'comp/nav.php';
require_once 'env/config.php';

$message = '';

// Upload Image (Create)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'upload') {
    $image = $_FILES['image']['name'];
    $target = "gallery/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO gallery_images (image_path) VALUES (?)");
        $stmt->bind_param("s", $image);
        $stmt->execute();
        $stmt->close();
        $message = '<div class="alert alert-success text-center">Image uploaded successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger text-center">Failed to upload image.</div>';
    }
}

// Re-upload Image (Update)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'edit') {
    $id = $_POST['edit_id'];
    $old_image = $_POST['old_image'];

    if (!empty($_FILES['new_image']['name'])) {
        $new_image = $_FILES['new_image']['name'];
        $target = "gallery/" . basename($new_image);

        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target)) {
            // Delete old file
            if (file_exists("gallery/" . $old_image)) {
                unlink("gallery/" . $old_image);
            }

            // Update database
            $stmt = $conn->prepare("UPDATE gallery_images SET image_path = ? WHERE id = ?");
            $stmt->bind_param("si", $new_image, $id);
            $stmt->execute();
            $stmt->close();

            $message = '<div class="alert alert-success text-center">Image updated successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger text-center">Failed to upload new image.</div>';
        }
    } else {
        $message = '<div class="alert alert-warning text-center">No image selected for update.</div>';
    }
}

// Delete Image (Delete)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    if (file_exists("gallery/" . $image_path)) {
        unlink("gallery/" . $image_path);
    }

    $stmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $message = '<div class="alert alert-success text-center">Image deleted successfully!</div>';
}

// Read All Images
$result = $conn->query("SELECT * FROM gallery_images");
?>

<main id="main" class="main">
    <div class="pagetitle"><h1>Manage Images</h1></div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card"><div class="card-body">

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-10">

                                <?= $message ?>

                                <!-- Upload Button -->
                                <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload New Image</button>

                                <!-- Upload Modal -->
                                <div class="modal fade" id="uploadModal" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Upload New Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="action" value="upload">
                                                <div class="mb-3">
                                                    <label class="form-label">Select Image</label>
                                                    <input type="file" name="image" class="form-control" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary w-100">Upload</button>
                                            </form>
                                        </div>
                                    </div></div>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead><tr><th>Image</th><th>Actions</th></tr></thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><img src="gallery/<?= $row['image_path'] ?>" width="100" class="img-thumbnail"></td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                        onclick="editImage(<?= $row['id'] ?>, '<?= $row['image_path'] ?>')">Edit</button>
                                                    <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                </div></div>
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
                    <label class="form-label">Choose New Image</label>
                    <input type="file" name="new_image" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>
        </div>
    </div></div>
</div>

<script>
function editImage(id, imagePath) {
    document.getElementById('edit_id').value = id;
    document.getElementById('old_image').value = imagePath;
}
</script>

<?php include 'comp/footer.php'; ?>
