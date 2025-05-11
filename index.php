<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="imgs/Record-App-Logo-512.png" type="image/x-icon">
</head>
<body class="bg-dark">

  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100">
      <div class="col-12 col-md-6 col-lg-4 mx-auto">
        <div class="card shadow-lg rounded-3">
          <div class="card-body p-5">
            <h2 class="text-center mb-4 text-danger">Login</h2>
            <form action="#" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control form-control-lg" id="username" placeholder="Enter your username">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control form-control-lg" id="password" placeholder="Enter your password">
              </div>
              <button type="submit" class="btn btn-danger w-100 py-2">Login</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
