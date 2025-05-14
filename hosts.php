<?php
include 'comp/nav.php';
require_once 'env/config.php';

$message = "";
$messageType = "";

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT image_path FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if ($imagePath && file_exists("TEAM/$imagePath")) {
        unlink("TEAM/$imagePath");
    }

    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Member deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Failed to delete member.";
        $messageType = "danger";
    }
    $stmt->close();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $newImage = $_FILES['image']['name'];

    if (!empty($newImage)) {
        $stmt = $conn->prepare("SELECT image_path FROM team_members WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($oldImage);
        $stmt->fetch();
        $stmt->close();

        if ($oldImage && file_exists("TEAM/$oldImage")) {
            unlink("TEAM/$oldImage");
        }

        $target = "TEAM/" . basename($newImage);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        $stmt = $conn->prepare("UPDATE team_members SET name=?, role=?, image_path=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $role, $newImage, $id);
    } else {
        $stmt = $conn->prepare("UPDATE team_members SET name=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $role, $id);
    }

    if ($stmt->execute()) {
        $message = "Member updated successfully.";
        $messageType = "success";
    } else {
        $message = "Update failed.";
        $messageType = "danger";
    }
    $stmt->close();
}

// ADD
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $image = $_FILES['image']['name'];
    $target = "TEAM/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO team_members (name, role, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $role, $image);

        if ($stmt->execute()) {
            $message = "Team member added successfully!";
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

$result = $conn->query("SELECT * FROM team_members");
$editMode = isset($_GET['edit']);
$editMember = null;

if ($editMode) {
    $editId = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editMember = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Team Management</h1>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?= $editMode ? 'Edit' : 'Add' ?> Team Member</h5>
                        <form method="post" enctype="multipart/form-data">
                            <?php if ($editMode): ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($editMember['id']) ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label">Name:</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($editMember['name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role / Show:</label>
                                <input type="text" class="form-control" name="role" value="<?= htmlspecialchars($editMember['role'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?= $editMode ? 'Change Image (optional):' : 'Image:' ?></label>
                                <input type="file" class="form-control" name="image" <?= $editMode ? '' : 'required' ?>>
                                <?php if ($editMode && $editMember['image_path']): ?>
                                    <p class="mt-2">Current: <img src="TEAM/<?= htmlspecialchars($editMember['image_path']) ?>" height="60"></p>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="<?= $editMode ? 'update' : 'submit' ?>" class="btn btn-<?= $editMode ? 'warning' : 'primary' ?>">
                                <?= $editMode ? 'Update' : 'Add' ?> Member
                            </button>
                            <?php if ($editMode): ?>
                                <a href="team.php" class="btn btn-secondary ms-2">Cancel</a>
                            <?php endif; ?>
                        </form>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType ?> mt-3"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Team Members</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['role']) ?></td>
                                            <td><img src="TEAM/<?= htmlspecialchars($row['image_path']) ?>" height="60"></td>
                                            <td>
                                                <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this member?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">No team members found.</td></tr>
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
