<?php include('layouts/header.php'); ?>

<?php

include('server/connection.php');

if (isset($_POST['order_details_btn']) && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_details = $stmt->get_result();

    $order_total_price = calculateTotalOrderPrice($order_details);
} else {
    header('location: account.php');
    exit;
}

function calculateTotalOrderPrice($order_details) {
    $total = 0;
    foreach ($order_details as $row) {
        $total += $row['product_price'] * $row['product_quantity'];
    }
    return $total;
}

?>

<!--order details-->
<section id="orders" class="orders container my-5 py-3 ">
  <div class="container mt-5">
    <h3 class="font-weight-bold text-center">Order Details</h3>
    <hr class="mx-auto">
  </div>

  <!-- Responsive wrapper for table on small screens -->
  <div class="table-responsive mt-5 pt-5">

    <table class="table table-striped mx-auto">
      <thead>
        <tr>
          <th>Product</th>
          <th class="text-center">Price</th>
          <th class="text-center">Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order_details as $row) { ?>
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <img 
                  src="assets/imgs/<?php echo htmlspecialchars($row['product_image']); ?>" 
                  alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                  class="img-fluid rounded" 
                  style="max-width: 80px; height: auto; margin-right: 15px;"
                >
                <span><?php echo htmlspecialchars($row['product_name']); ?></span>
              </div>
            </td>

            <td class="text-center">
              R<?php echo number_format($row['product_price'], 2); ?>
            </td>

            <td class="text-center">
              <?php echo intval($row['product_quantity']); ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

  </div>

  <!-- Pay Now button aligned right and responsive -->
  <?php if ($order_status == "not paid") { ?>
    <div class="d-flex justify-content-end mt-4">
      <form method="POST" action="payment.php" class="w-100 w-md-auto">
          <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
          <input type="hidden" name="order_total_price" value="<?php echo $order_total_price; ?>">
          <input type="hidden" name="order_status" value="<?php echo $order_status; ?>">
          <input type="submit" name="order_pay_btn" class="btn btn-primary btn-lg" value="Pay Now">
      </form>
    </div>
  <?php } ?>

</section>

<?php include('layouts/footer.php'); ?>
