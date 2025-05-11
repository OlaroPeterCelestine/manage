<?php
include 'comp/nav.php';
?>
<?php
require_once 'env/config.php';

$message = '';

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'upload') {
    $image = $_FILES['image']['name'];
    $target = "gallery/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO gallery_images (image_path) VALUES (?)");
        $stmt->bind_param("s", $image);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success text-center">Image uploaded successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger text-center">Database error: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger text-center">Failed to upload image.</div>';
    }
}

// Handle image delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file
    if (file_exists("gallery/" . $image_path)) {
        unlink("gallery/" . $image_path);
    }

    // Delete the image record from the database
    $stmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success text-center">Image deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger text-center">Error deleting image.</div>';
    }
    $stmt->close();
}

// Get all images from the database
$result = $conn->query("SELECT * FROM gallery_images");
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Manage Images</h1>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                   

                        <div class="container ">
                            <div class="row justify-content-center">
                                <div class="col-md-10">

                                        <h4 class="mb-4 text-center">Manage Gallery Images</h4>

                                        <?= $message ?>

                                        <!-- Button to trigger the upload modal -->
                                        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                            Upload New Image
                                        </button>

                                        <!-- Modal for uploading image -->
                                        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="uploadModalLabel">Upload New Image</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" enctype="multipart/form-data">
                                                            <input type="hidden" name="action" value="upload">
                                                            <div class="mb-3">
                                                                <label for="image" class="form-label">Select Image</label>
                                                                <input type="file" name="image" id="image" class="form-control" required>
                                                            </div>

                                                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Table of images -->
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><img src="gallery/<?= $row['image_path'] ?>" alt="Image" width="100" class="img-thumbnail"></td>
                                                            <td>
                                                                <!-- Edit Button -->
                                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editImage(<?= $row['id'] ?>, '<?= $row['image_path'] ?>')">Edit</button>

                                                                <!-- Delete Button -->
                                                                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this image?');">Delete</a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<!-- Modal for editing image -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <input type="hidden" name="action" value="edit">
                    <div class="mb-3">
                        <label for="image_name" class="form-label">Image Name</label>
                        <input type="text" name="image_name" id="image_name" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Fill the edit modal with image data
    function editImage(id, image_path) {
        document.getElementById('edit_id').value = id;
        document.getElementById('image_name').value = image_path;
    }
</script>

<?php
include 'comp/footer.php';
?>
