<?php include('layouts/header.php'); ?>

<?php
if (empty($_SESSION['cart'])) {
    header('location: index.php');
    exit;
}
?>

<!-- Checkout -->
<section class="my-5 py-5">
    <div class="container">
        <div class="text-center mt-3 pt-5">
            <h2 class="font-weight-bold">Check Out</h2>
            <hr class="mx-auto" style="max-width: 150px; border-top: 3px solid #000;">
        </div>

        <div class="mx-auto" style="max-width: 600px;">
            <form id="checkout-form" method="POST" action="server/place_order.php" novalidate>
                <p class="text-center text-danger">
                    <?php if (isset($_GET['message'])) echo htmlspecialchars($_GET['message']); ?>
                </p>

                <?php if (isset($_GET['message'])): ?>
                <div class="text-center mb-3">
                    <a href="login.php" class="btn btn-primary">Login</a>
                </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="checkout-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Name" required>
                    </div>

                    <div class="col-md-6">
                        <label for="checkout-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="checkout-email" name="email" placeholder="Email" required>
                    </div>

                    <div class="col-md-6">
                        <label for="checkout-phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="Phone" required>
                    </div>

                    <div class="col-md-6">
                        <label for="checkout-city" class="form-label">City</label>
                        <input type="text" class="form-control" id="checkout-city" name="city" placeholder="City" required>
                    </div>

                    <div class="col-12">
                        <label for="checkout-address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Address" required>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="fs-5">Total amount: <strong>R <?php echo number_format($_SESSION['total'], 2); ?></strong></p>
                    <input type="submit" class="btn btn-success btn-lg px-5" id="checkout-btn" name="place_order" value="Place Order">
                </div>
            </form>
        </div>
    </div>
</section>

<?php include('layouts/footer.php'); ?>