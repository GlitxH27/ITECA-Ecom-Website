<?php include('header.php'); ?>

<?php



include('../server/connection.php');

if(isset($_SESSION['admin_logged_in'])){
    header('location: dashboard.php');
    exit;

}


if(isset($_POST['login_btn'])){

  $email = $_POST['email'];
  $password = md5($_POST['password']);

  $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email, admin_password FROM admins WHERE admin_email=? AND admin_password = ? LIMIT 1");

  $stmt->bind_param('ss',$email,$password);

  if($stmt->execute()){
    $stmt->bind_result($admin_id, $admin_name, $admin_email, $admin_password);
    $stmt->store_result();

    if($stmt->num_rows() == 1){
      $row = $stmt->fetch();

      $_SESSION['admin_id'] = $admin_id;
      $_SESSION['admin_name'] = $admin_name;
      $_SESSION['admin_email'] = $admin_email;
      $_SESSION['admin_logged_in'] = true;

      header('location: dashboard.php?login_success=logged in successfully');

    }else{

      header('location: login.php?error=could not verify your account');
    }
    

  }else{
    //error

    header('location: login.php?error=something went wrong');
    
  }

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body>



<!-- Login Form -->
<div class="container login-container shadow-sm p-4 bg-white rounded">
    <h3 class="text-center mb-4">Login</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>

        <button type="submit" name="login_btn" class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>
</html>