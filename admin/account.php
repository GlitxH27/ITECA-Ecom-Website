<?php include('header.php'); ?>

<?php
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Fetch admin details
$admin_email = $_SESSION['admin_email'];
$query = $conn->prepare("SELECT admin_name, admin_email FROM admins WHERE admin_email = ?");
$query->bind_param("s", $admin_email);
$query->execute();
$query->bind_result($admin_name, $admin_email);
$query->fetch();
$query->close();

// Handle password update
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Verify current password
    $verify = $conn->prepare("SELECT admin_password FROM admins WHERE admin_email = ?");
    $verify->bind_param("s", $admin_email);
    $verify->execute();
    $verify->bind_result($stored_password);
    $verify->fetch();
    $verify->close();

    if (password_verify($current_password, $stored_password)) {
        $update = $conn->prepare("UPDATE admins SET admin_password = ? WHERE admin_email = ?");
        $update->bind_param("ss", $new_password, $admin_email);
        if ($update->execute()) {
            $_SESSION['message'] = "Password updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update password.";
            $_SESSION['message_type'] = "danger";
        }
        $update->close();
    } else {
        $_SESSION['message'] = "Incorrect current password.";
        $_SESSION['message_type'] = "danger";
    }
}
?>


<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-10 ms-sm-auto px-4 py-4">
      <h2>Admin Account</h2>

      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
          <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message'], $_SESSION['message_type']); 
          ?>
        </div>
      <?php endif; ?>

      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">Profile Information</h5>
          <p><strong>Name:</strong> <?php echo $admin_name; ?></p>
          <p><strong>Email:</strong> <?php echo $admin_email; ?></p>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Change Password</h5>
          <form method="POST" action="">
            <div class="mb-3">
              <label>Current Password</label>
              <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>New Password</label>
              <input type="password" name="new_password" class="form-control" required>
            </div>
            <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
          </form>
        </div>
      </div>

    </main>
  </div>
</div>