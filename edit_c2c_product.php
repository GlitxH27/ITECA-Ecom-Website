<?php include 'layouts/header.php'; ?>
<?php

include('server/connection.php');

// Only allow logged-in sellers
if (!isset($_SESSION['logged_in']) || $_SESSION['is_seller'] != 1) {
    header('Location: account.php?error=Access denied. Seller account required.');
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if product_id is provided
if (!isset($_GET['product_id'])) {
    header('Location: seller_dashboard.php?error=Product ID is required.');
    exit;
}

$product_id = intval($_GET['product_id']);

// Fetch product details (only if owned by this seller)
$stmt = $conn->prepare("SELECT id, title, description, price, image FROM marketplace_products WHERE id = ? AND seller_id = ?");
$stmt->bind_param("ii", $product_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: seller_dashboard.php?error=Product not found or access denied.');
    exit;
}

$product = $result->fetch_assoc();

$error = '';
$msg = '';

// Handle form submission
if (isset($_POST['update_product'])) {
    $title = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = floatval($_POST['product_price']);

    // Validate inputs
    if (empty($title) || empty($description) || $price <= 0) {
        $error = "Please fill all fields correctly.";
    } else {
        // Handle optional image upload
        $new_image = $product['image']; // Default to old image if no new upload

        if (!empty($_FILES['product_image']['name'])) {
            $image = basename($_FILES['product_image']['name']);
            $target_dir = "uploads/marketplace_products/";
            $target_file = $target_dir . $image;

            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Validate image file type (basic check)
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    // Delete old image file if exists and different from new
                    if ($product['image'] && $product['image'] !== $image && file_exists($target_dir . $product['image'])) {
                        unlink($target_dir . $product['image']);
                    }
                    $new_image = $image;
                } else {
                    $error = "Failed to upload new image.";
                }
            }
        }

        if (empty($error)) {
            // Update product record
            $stmt = $conn->prepare("UPDATE marketplace_products SET title = ?, description = ?, price = ?, image = ?, approved = 0 WHERE id = ? AND seller_id = ?");
            $stmt->bind_param("ssdiii", $title, $description, $price, $new_image, $product_id, $user_id);

            if ($stmt->execute()) {
                $msg = "Product updated successfully and is awaiting re-approval.";

                // Reload product info with updated data
                $product['title'] = $title;
                $product['description'] = $description;
                $product['price'] = $price;
                $product['image'] = $new_image;
            } else {
                $error = "Failed to update product.";
            }
        }
    }
}

?>



<div class="container mt-5 pt-5">
    <h2>Edit Product</h2>

    <?php if (!empty($msg)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required value="<?php echo htmlspecialchars($product['title']); ?>">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="product_description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Price (ZAR)</label>
            <input type="number" step="0.01" name="product_price" class="form-control" required value="<?php echo htmlspecialchars($product['price']); ?>">
        </div>
        <div class="mb-3">
            <label>Current Image</label><br>
            <img src="uploads/marketplace_products/<?php echo htmlspecialchars($product['image']); ?>" width="150" alt="Current Product Image">
        </div>
        <div class="mb-3">
            <label>Upload New Image (optional)</label>
            <input type="file" name="product_image" accept="image/*" class="form-control">
        </div>

        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
        <a href="seller_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include('layouts/footer.php'); ?>
