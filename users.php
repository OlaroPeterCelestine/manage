<?php
session_start();
require_once 'env/config.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users to the login page (or another page of your choice)
    header("Location: login.php");
    exit(); // Ensure no further code is executed
}

// Handle registration, edit, and delete actions
$message = '';
$modalType = '';
$redirectURL = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['register'])) {
        // Register new user
        $username = $conn->real_escape_string($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        if ($conn->query($sql)) {
            $message = "User registered successfully!";
            $modalType = 'success';
        } else {
            $message = "Error: " . $conn->error;
            $modalType = 'error';
        }
    } elseif (isset($_POST['edit_user'])) {
        // Edit user
        $userId = $_POST['user_id'];
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Hash password if updated
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $username, $hashedPassword, $role, $userId);

        if ($stmt->execute()) {
            $message = "User updated successfully!";
            $modalType = 'success';
        } else {
            $message = "Error: " . $stmt->error;
            $modalType = 'error';
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $userId = $_POST['user_id'];
        $sql = "DELETE FROM users WHERE id = $userId";

        if ($conn->query($sql)) {
            $message = "User deleted successfully!";
            $modalType = 'success';
        } else {
            $message = "Error: " . $conn->error;
            $modalType = 'error';
        }
    }
}

// Fetch all users for display in the table
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'comp/nav.php'; ?>

<main id="main" class="main">
  <div class="pagetitle">
    <h1>General Tables</h1>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Table with Stripped Rows</h5>

            <!-- Table with Stripped Rows -->
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Username</th>
                  <th scope="col">Password</th>
                  <th scope="col">Role</th>
                  <th scope="col">Created At</th>
                  <th scope="col">Edit</th>
                  <th scope="col">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <th scope="row"><?= $user['id'] ?></th>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['password']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editUser(<?= $user['id'] ?>)">Edit</button>
                    </td>
                    <td>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <!-- End Table with Stripped Rows -->

            <!-- Button to Open Register Modal -->
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerModal">Register New User</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</main><!-- End #main -->

<!-- Modal for Registering New User -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="registerModalLabel">Register New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
          </div>
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-control" id="role" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Editing User -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="" method="POST">
          <div class="mb-3">
            <label for="edit_username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="edit_username" required>
          </div>
          <div class="mb-3">
            <label for="edit_password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="edit_password" required>
          </div>
          <div class="mb-3">
            <label for="edit_role" class="form-label">Role</label>
            <select name="role" class="form-control" id="edit_role" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <input type="hidden" name="user_id" id="edit_user_id">
          <button type="submit" name="edit_user" class="btn btn-primary w-100">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Edit user function
  function editUser(userId) {
    // Fetch user data from the database and populate the modal fields
    // For now, let's simulate it with sample data
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_username').value = 'Sample Username';
    document.getElementById('edit_password').value = 'Sample Password';
    document.getElementById('edit_role').value = 'user'; // Example
  }
</script>

<?php
include 'comp/footer.php';
?>
