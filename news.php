<?php
require_once 'env/config.php';
include 'comp/nav.php';

$message = "";

// Handle new post creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "create") {
    $author = $_POST['author'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    if (!empty($image) && move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO blog_posts (author, title, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $author, $title, $desc, $image);
        $message = $stmt->execute()
            ? "<div class='alert alert-success'>Post created successfully.</div>"
            : "<div class='alert alert-danger'>Failed to insert into database.</div>";
    } else {
        $message = "<div class='alert alert-warning'>Image upload failed or missing.</div>";
    }
}

// Handle post edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === "edit") {
    $id = $_POST['edit_id'];
    $author = $_POST['edit_author'];
    $title = $_POST['edit_title'];
    $desc = $_POST['edit_description'];

    $newImage = $_FILES['edit_image']['name'];
    if (!empty($newImage)) {
        $target = "uploads/" . basename($newImage);
        move_uploaded_file($_FILES['edit_image']['tmp_name'], $target);
        $stmt = $conn->prepare("UPDATE blog_posts SET author=?, title=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("ssssi", $author, $title, $desc, $newImage, $id);
    } else {
        $stmt = $conn->prepare("UPDATE blog_posts SET author=?, title=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $author, $title, $desc, $id);
    }

    $message = $stmt->execute()
        ? "<div class='alert alert-success'>Post updated successfully.</div>"
        : "<div class='alert alert-danger'>Failed to update post.</div>";
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
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

// Fetch all posts
$posts = $conn->query("SELECT * FROM blog_posts ORDER BY id DESC");
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Blog Post Manager</h1>
    </div>

    <section class="section">
        <div class="container">
            <?= $message ?>

            <!-- Add New Post -->
            <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white mb-5">
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

                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Create Post</button>
            </form>

            <!-- Posts Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">All Blog Posts</h5>
                    <table class="table table-striped">
                        <thead>
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
                                    <td><img src="uploads/<?= $row['image'] ?>" width="60" class="img-thumbnail"></td>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td><?= htmlspecialchars($row['author']) ?></td>
                                    <td><?= $row['created_at'] ?? '—' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="populateEditForm(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                                    </td>
                                    <td>
                                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
