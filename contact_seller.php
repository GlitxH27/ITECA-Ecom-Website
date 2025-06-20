<?php
include 'layouts/header.php';
include 'server/connection.php';

// Check if product_id is set
if (!isset($_GET['product_id'])) {
    echo "<p class='text-center mt-5'>Invalid product selection.</p>";
    include 'layouts/footer.php';
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product info
$product_query = "SELECT * FROM marketplace_products WHERE id = $product_id";
$product_result = mysqli_query($conn, $product_query);

if (!$product_result || mysqli_num_rows($product_result) === 0) {
    echo "<p class='text-center mt-5'>Product not found.</p>";
    include 'layouts/footer.php';
    exit();
}

$product = mysqli_fetch_assoc($product_result);

// Get seller info
$seller_id = $product['seller_id'];
$seller_query = "SELECT * FROM users WHERE user_id = $seller_id";
$seller_result = mysqli_query($conn, $seller_query);

if (!$seller_result || mysqli_num_rows($seller_result) === 0) {
    echo "<p class='text-center mt-5'>Seller not found.</p>";
    include 'layouts/footer.php';
    exit();
}

$seller = mysqli_fetch_assoc($seller_result);
?>

<div class="container mt-5 pt-5">
    <h2 class="mb-4 mt-4 text-center">Contact Seller</h2>
    <hr class="mx-auto">
    <div class="card p-4 shadow">
        <h4>Product: <?php echo htmlspecialchars($product['title']); ?></h4>
        <p>Description: <?php echo htmlspecialchars($product['description']); ?></p>
        <p>Price: R<?php echo number_format($product['price'], 2); ?></p>

        <hr>

        <h5>Seller Information</h5>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['user_name']); ?></p>
        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($seller['user_email']); ?>"><?php echo htmlspecialchars($seller['user_email']); ?></a></p>

        <a href="mailto:<?php echo htmlspecialchars($seller['user_email']); ?>?subject=Interested in <?php echo urlencode($product['title']); ?>" class="btn btn-outline-primary mt-3">Email the Seller</a>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>
