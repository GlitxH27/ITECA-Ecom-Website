<?php include('layouts/header.php'); ?>

<?php
include('server/connection.php');

if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

if (isset($_GET['logout'])) {
    if (isset($_SESSION['logged_in'])) {
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        header('location: login.php');
        exit;
    }
}

if (isset($_POST['change_password'])) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $user_email = $_SESSION['user_email'];

    if ($password !== $confirmPassword) {
        header('location: account.php?error=Passwords do not match');
    } else if (strlen($password) < 6) {
        header('location: account.php?error=Password must be at least 6 characters');
    } else {
        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $stmt->bind_param('ss', md5($password), $user_email);
        if ($stmt->execute()) {
            header('location: account.php?message=Password has been updated successfully');
        } else {
            header('location: account.php?error=Could not update password');
        }
    }
}

if (isset($_SESSION['logged_in'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id =?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>

<!-- Account Page -->
<section class="my-5 py-5">
  <div class="container">
    <?php if (isset($_GET['payment_message'])): ?>
      <div class="alert alert-success text-center"><?php echo htmlspecialchars($_GET['payment_message']); ?></div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Account Info -->
      <div class="col-12 col-lg-6">
        <div class="bg-light p-4 rounded shadow-sm h-100">
          <h3 class="mb-3 mt-5 text-center">Account Info</h3>
          <hr class="mx-auto">
          

          <p><strong>Name:</strong> <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span></p>
          <p><strong>Email:</strong> <span><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></span></p>

          <?php if (!empty($_SESSION['is_seller']) && $_SESSION['is_seller'] == 1): ?>
            <p class="mb-3"><a href="seller_dashboard.php" class="btn btn-outline-primary w-100">Go to Seller Dashboard</a></p>
          <?php endif; ?>

          <p class="mb-3"><a href="#orders" class="btn btn-outline-secondary w-100">Your Orders</a></p>

          <p><a href="account.php?logout=1" class="btn btn-danger w-100">Logout</a></p>
        </div>
      </div>

      <!-- Change Password Form -->
      <div class="col-12 col-lg-6">
        <div class="bg-light p-4 rounded shadow-sm h-100">
          <h3 class="mb-3 mt-5 text-center">Change Password</h3>
          <hr class="mx-auto">

          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
          <?php endif; ?>

          <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
          <?php endif; ?>

          <form method="POST" action="account.php" novalidate>
            <div class="mb-3">
              <label for="account-password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="account-password" name="password" placeholder="Enter new password" required minlength="6">
            </div>

            <div class="mb-3">
              <label for="account-password-confirm" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="account-password-confirm" name="confirmPassword" placeholder="Confirm new password" required minlength="6">
            </div>

            <button type="submit" name="change_password" class="btn btn-primary w-100">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Orders Section -->
<section id="orders" class="my-5 py-3">
  <div class="container">
    <h2 class="text-center mb-4">Your Orders</h2>
    <hr class="mx-auto">

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th style="background-color: rgb(112, 184, 112);">Order ID</th>
            <th style="background-color: rgb(112, 184, 112);">Order Cost</th>
            <th style="background-color: rgb(112, 184, 112);">Status</th>
            <th style="background-color: rgb(112, 184, 112);">Date</th>
            <th style="background-color: rgb(112, 184, 112);">Details</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($orders && $orders->num_rows > 0): ?>
            <?php while ($row = $orders->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td>R<?php echo number_format($row['order_cost'], 2); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($row['order_status'])); ?></td>
                <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['order_date']))); ?></td>
                <td>
                  <form method="POST" action="order_details.php" class="m-0 p-0">
                    <input type="hidden" name="order_status" value="<?php echo htmlspecialchars($row['order_status']); ?>">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                    <button type="submit" name="order_details_btn" class="btn btn-sm btn-outline-primary">Details</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">You have no orders yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>