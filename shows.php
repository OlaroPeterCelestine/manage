<?php
include 'comp/nav.php';
require_once 'env/config.php';

$message = "";
$messageType = "";

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT image_path FROM programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if ($imagePath && file_exists("image/$imagePath")) {
        unlink("image/$imagePath");
    }

    $stmt = $conn->prepare("DELETE FROM programs WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Show deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Failed to delete show.";
        $messageType = "danger";
    }
    $stmt->close();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $newImage = $_FILES['image']['name'];

    if (!empty($newImage)) {
        $stmt = $conn->prepare("SELECT image_path FROM programs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($oldImage);
        $stmt->fetch();
        $stmt->close();

        if ($oldImage && file_exists("image/$oldImage")) {
            unlink("image/$oldImage");
        }

        $target = "image/" . time() . '-' . basename($newImage);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $stmt = $conn->prepare("UPDATE programs SET image_path=? WHERE id=?");
        $stmt->bind_param("si", $target, $id);
    }

    if ($stmt->execute()) {
        $message = "Show updated successfully.";
        $messageType = "success";
    } else {
        $message = "Update failed.";
        $messageType = "danger";
    }
    $stmt->close();
}

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $image = $_FILES['image']['name'];
    $target = "image/" . time() . '-' . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO programs (image_path) VALUES (?)");
        $stmt->bind_param("s", $target);

        if ($stmt->execute()) {
            $message = "Show added successfully!";
            $messageType = "success";
        } else {
            $message = "Database error: " . $stmt->error;
            $messageType = "danger";
        }

        $stmt->close();
    } else {
        $message = "Failed to upload image.";
        $messageType = "danger";
    }
}

$result = $conn->query("SELECT * FROM programs ORDER BY created_at DESC");
$editMode = isset($_GET['edit']);
$editProgram = null;

if ($editMode) {
    $editId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM programs WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editProgram = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Shows Management</h1>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= $editMode ? 'Edit' : 'Add' ?> Show</h5>
                        <form method="post" enctype="multipart/form-data">
                            <?php if ($editMode): ?>
                                <input type="hidden" name="id" value="<?= $editProgram['id'] ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label"><?= $editMode ? 'Change Image (optional):' : 'Image:' ?></label>
                                <input type="file" class="form-control" name="image" <?= $editMode ? '' : 'required' ?>>
                                <?php if ($editMode && $editProgram['image_path']): ?>
                                    <p class="mt-2">Current: <img src="<?= $editProgram['image_path'] ?>" height="60"></p>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="<?= $editMode ? 'update' : 'submit' ?>" class="btn btn-<?= $editMode ? 'warning' : 'primary' ?>">
                                <?= $editMode ? 'Update' : 'Add' ?> Show
                            </button>
                            <?php if ($editMode): ?>
                                <a href="shows.php" class="btn btn-secondary ms-2">Cancel</a>
                            <?php endif; ?>
                        </form>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> mt-3"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Shows</h5>
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
                                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this show?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center">No shows found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>

<?php include 'comp/footer.php'; ?>
