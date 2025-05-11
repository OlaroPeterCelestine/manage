<?php
session_start();
require_once 'env/config.php';

$message = '';
$modalType = '';
$redirectURL = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Sanitize input to prevent SQL Injection
  $username = $conn->real_escape_string($_POST['username']);
  $password = $_POST['password'];

  // Fetch the user from the database
  $sql = "SELECT * FROM users WHERE username='$username'";
  $result = $conn->query($sql);

  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password using password_verify()
    if (password_verify($password, $user['password'])) {
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];

      $message = "Welcome " . ucfirst($user['role']) . " " . htmlspecialchars($user['username']);
      $modalType = 'success';

      // Set redirect URL based on role
      $redirectURL = ($user['role'] === 'admin') ? 'admin_dashboard.php' : 'user_dashboard.php';
    } else {
      $message = "Incorrect password!";
      $modalType = 'error';
    }
  } else {
    $message = "User not found!";
    $modalType = 'error';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100">
      <div class="col-12 col-md-6 col-lg-4 mx-auto">
        <div class="card shadow-lg rounded-3">
          <div class="card-body p-5">
            <h2 class="text-center mb-4 text-danger">Login</h2>

            <form action="" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control form-control-lg" id="username" placeholder="Enter your username" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="Enter your password" required>
              </div>
              <button type="submit" class="btn btn-danger w-100 py-2">Login</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Modals -->
  <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content <?= $modalType === 'success' ? 'border-success' : 'border-danger' ?>">
        <div class="modal-header <?= $modalType === 'success' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
          <h5 class="modal-title" id="responseModalLabel">
            <?= $modalType === 'success' ? 'Login Successful' : 'Login Failed' ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <?= $message ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn <?= $modalType === 'success' ? 'btn-success' : 'btn-danger' ?>" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <?php if (!empty($modalType)): ?>
  <script>
    const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
    responseModal.show();

    <?php if ($modalType === 'success'): ?>
    // Redirect after 2.5 seconds
    setTimeout(() => {
      window.location.href = '<?= $redirectURL ?>';
    }, 2500);
    <?php endif; ?>
  </script>
  <?php endif; ?>
</body>
</html>
