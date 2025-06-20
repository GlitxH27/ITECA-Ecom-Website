<?php include('header.php'); ?>
<?php

include('../server/connection.php'); // Adjust the path if needed

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Get order ID from URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        $_SESSION['message'] = "Order not found.";
        $_SESSION['message_type'] = "danger";
        header('location: dashboard.php');
        exit();
    }
} else {
    $_SESSION['message'] = "No order ID provided.";
    $_SESSION['message_type'] = "danger";
    header('location: dashboard.php');
    exit();
}

// Handle update
if (isset($_POST['update_order'])) {
    $status = $_POST['order_status'];

    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to update order.";
        $_SESSION['message_type'] = "danger";
    }
}
?>


<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-10 ms-sm-auto px-4 py-4">
      <h2>Edit Order #<?= $order['order_id']; ?></h2>

      <form method="POST" action="" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
          <label>Order ID</label>
          <input type="text" class="form-control" value="<?= $order['order_id']; ?>" readonly>
        </div>

        <div class="mb-3">
          <label>Order Price</label>
          <input type="text" class="form-control" value="R <?= number_format($order['order_cost'], 2); ?>" readonly>
        </div>

        <div class="mb-3">
          <label>Order Date</label>
          <input type="text" class="form-control" value="<?= $order['order_date']; ?>" readonly>
        </div>

        <div class="mb-3">
          <label>Order Status</label>
          <select name="order_status" class="form-select" required>
            <option value="Not Paid" <?= $order['order_status'] == 'Not Paid' ? 'selected' : ''; ?>>Not Paid</option>
            <option value="Paid" <?= $order['order_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
            <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
            <option value="Delivered" <?= $order['order_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
            <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
          </select>
        </div>

        <button type="submit" name="update_order" class="btn btn-primary">Update Order</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
      </form>
    </main>
  </div>
</div>