<?php 
include('layouts/header.php'); 
include('server/connection.php');

if (!isset($_GET['id'])) {
    header("Location: marketplace.php");
    exit;
}

$product_id = (int) $_GET['id'];

// Get selected product
$stmt = $conn->prepare("SELECT * FROM marketplace_products WHERE id = ? AND approved = 1");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    // Product not found or not approved
    echo "<div class='container mt-5'><h3 class='text-center'>Product not found.</h3></div>";
    include('layouts/footer.php');
    exit;
}

// Get related products (excluding current one)
$related_stmt = $conn->prepare("SELECT * FROM marketplace_products WHERE approved = 1 AND id != ? ORDER BY RAND() LIMIT 4");
$related_stmt->bind_param("i", $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result();
?>

<style>
    .btn-custom {
        background-color: rgb(112, 184, 112);
        color: white;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-custom:hover, 
    .btn-custom:focus {
        background-color: black !important;
        color: white !important;
        text-decoration: none;
    }
</style>

<section class="container single-product my-5 pt-5 ">
    <div class="row mt-5">
        <div class="col-lg-5 col-md-6 col-sm-12">
            <img 
                class="img-fluid w-100 pb-1 rounded" 
                src="uploads/marketplace_products/<?php echo htmlspecialchars($product['image']); ?>" 
                alt="<?php echo htmlspecialchars($product['title']); ?>"
                style="object-fit: cover; max-height: 400px;"
            >
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h2 class="mb-3"><?php echo htmlspecialchars($product['title']); ?></h2>
            <h4 class="text-success mb-4">R<?php echo number_format($product['price'], 2); ?></h4>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="d-flex gap-3">
                <a href="marketplace.php" class="btn btn-custom">
                    ← Back to Marketplace
                </a>

                <a href="contact_seller.php?product_id=<?php echo $product['id']; ?>" class="btn btn-custom">
                    Contact Seller
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Suggested Products -->
<section class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Suggested Products</h3>
        <hr class="mx-auto">
    </div>
    <div class="row mx-auto container-fluid">
        <?php while ($related = $related_products->fetch_assoc()) { ?>
            <div class="product text-center col-lg-3 col-md-4 col-sm-6 mb-4">
                <img 
                    class="img-fluid mb-3 rounded" 
                    src="uploads/marketplace_products/<?php echo htmlspecialchars($related['image']); ?>" 
                    alt="<?php echo htmlspecialchars($related['title']); ?>"
                    style="height: 200px; object-fit: cover;"
                >
                <h5 class="p-name"><?php echo htmlspecialchars($related['title']); ?></h5>
                <h4 class="p-price">R<?php echo number_format($related['price'], 2); ?></h4>
                <a href="marketplace_product_details.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-dark btn-sm">Details</a>
            </div>
        <?php } ?>
    </div>
</section>

<?php include('layouts/footer.php'); ?>