﻿<?php
require_once 'env/config.php';
include 'comp/nav.php';

$message = "";
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 2 * 1024 * 1024;

// Create Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "create") {
    $author = $_POST['author'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $image  = $_FILES['image']['name'];
    $tmp    = $_FILES['image']['tmp_name'];
    $type   = $_FILES['image']['type'];
    $size   = $_FILES['image']['size'];

    if (!empty($image) && in_array($type, $allowedTypes) && $size <= $maxSize) {
        $target = "uploads/" . basename($image);

        if (move_uploaded_file($tmp, $target)) {
            $stmt = $conn->prepare("INSERT INTO blog_posts (author, title, description, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $author, $title, $desc, $image);

            $message = $stmt->execute()
                ? "<div class='alert alert-success'>Post created successfully.</div>"
                : "<div class='alert alert-danger'>Failed to insert into database.</div>";
        } else {
            $message = "<div class='alert alert-warning'>Failed to move uploaded image.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Invalid image (JPG/PNG/GIF, ≤ 2MB).</div>";
    }
}

// Edit Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "edit") {
    $id     = $_POST['edit_id'];
    $author = $_POST['edit_author'];
    $title  = $_POST['edit_title'];
    $desc   = $_POST['edit_description'];

    $newImage = $_FILES['edit_image']['name'];
    $tmp      = $_FILES['edit_image']['tmp_name'];
    $type     = $_FILES['edit_image']['type'];
    $size     = $_FILES['edit_image']['size'];

    if (!empty($newImage) && in_array($type, $allowedTypes) && $size <= $maxSize) {
        $target = "uploads/" . basename($newImage);
        if (move_uploaded_file($tmp, $target)) {
            $stmt = $conn->prepare("UPDATE blog_posts SET author=?, title=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("ssssi", $author, $title, $desc, $newImage, $id);
        } else {
            $message = "<div class='alert alert-warning'>Failed to upload image.</div>";
        }
    } else {
        $stmt = $conn->prepare("UPDATE blog_posts SET author=?, title=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $author, $title, $desc, $id);
    }

    if (isset($stmt)) {
        $message = $stmt->execute()
            ? "<div class='alert alert-success'>Post updated successfully.</div>"
            : "<div class='alert alert-danger'>Failed to update post.</div>";
    }
}

// Delete Post
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $res = $conn->query("SELECT image FROM blog_posts WHERE id = $id");
    $row = $res ? $res->fetch_assoc() : null;

    if ($row && !empty($row['image'])) {
        $filePath = "uploads/" . $row['image'];
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    $conn->query("DELETE FROM blog_posts WHERE id = $id");
    $message = "<div class='alert alert-success'>Post deleted successfully.</div>";
}

// Fetch Posts
$posts = $conn->query("SELECT * FROM blog_posts ORDER BY id DESC");
?>

<main id="main" class="main">
   
    <section class="section">
        <div class="container-fluid px-3 px-md-5">
            <?= $message ?>

            <div class="row">
                <div class="col-12 col-lg-10 col-xl-8 mx-auto">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header bg-success text-white rounded-top-4">
                            <h5 class="mb-0">Create a New Blog Post</h5>
                        </div>
                        <form method="POST" enctype="multipart/form-data" class="card-body p-4">
                            <input type="hidden" name="action" value="create">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Author</label>
                                    <input type="text" name="author" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100 rounded-pill">Create Post</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title text-success">All Blog Posts</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-success text-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Story</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $posts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td>
                                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="60" class="rounded shadow-sm">
                                        </td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= htmlspecialchars($row['description']) ?></td>
                                        <td><?= htmlspecialchars($row['author']) ?></td>
                                        <td><?= htmlspecialchars($row['created_at'] ?? '—') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning" onclick="populateEditForm(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
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
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="edit_id" id="edit_id">

            <div class="modal-header">
                <h5 class="modal-title">Edit Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Author</label>
                    <input type="text" name="edit_author" id="edit_author" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="edit_title" id="edit_title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="edit_description" id="edit_description" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Change Image (optional)</label>
                    <input type="file" name="edit_image" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function populateEditForm(row) {
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = row.id;
        document.getElementById('edit_author').value = row.author;
        document.getElementById('edit_title').value = row.title;
        document.getElementById('edit_description').value = row.description;
        modal.show();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'comp/footer.php'; ?>
