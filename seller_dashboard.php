<?php include 'layouts/header.php'; ?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);



include('server/connection.php');

// Only allow logged-in sellers
if (!isset($_SESSION['logged_in']) || $_SESSION['is_seller'] != 1) {
    header('Location: account.php?error=Access denied. Seller account required.');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch categories for dropdown
$category_stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
$category_stmt->execute();
$categories = $category_stmt->get_result();

if (!$categories) {
    die("Category query failed: " . $conn->error);
}

// Handle product deletion if requested
if (isset($_GET['delete_product_id'])) {
    $product_id = intval($_GET['delete_product_id']);

    // Delete product only if it belongs to this seller
    $stmt = $conn->prepare("DELETE FROM marketplace_products WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();

    header("Location: seller_dashboard.php?msg=Product deleted successfully");
    exit;
}

// Handle product upload form submission
if (isset($_POST['upload_product'])) {
    $title = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = floatval($_POST['product_price']);
    $category_id = intval($_POST['product_category']);

    // Handle image upload
    $image = $_FILES['product_image']['name'];
    $target_dir = "uploads/marketplace_products/";
    $target_file = $target_dir . basename($image);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
        // Insert product record with category_id
        $stmt = $conn->prepare("INSERT INTO marketplace_products (seller_id, title, description, price, image, category_id, approved) VALUES (?, ?, ?, ?, ?, ?, 0)");
        if (!$stmt) {
            $error = "Prepare failed: " . $conn->error;
        } else {
            // Bind parameters: i=integer, s=string, d=double
            $stmt->bind_param("issdsi", $user_id, $title, $description, $price, $image, $category_id);

            if ($stmt->execute()) {
                // Redirect after successful insert to prevent form resubmission
                header("Location: seller_dashboard.php?msg=Product uploaded and awaiting approval.");
                exit;
            } else {
                $error = "Error inserting product: " . $stmt->error;
            }
        }
    } else {
        $error = "Failed to upload image.";
    }
}

// Fetch all products by this seller along with category name
$stmt = $conn->prepare("SELECT p.id, p.title, p.description, p.price, p.image, p.approved, p.denied_reason, c.name AS category_name 
                        FROM marketplace_products p
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.seller_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container mt-5 pt-5">
    <h2 class="text-center mt-5">Seller Dashboard</h2>
    <hr class="mx-auto">

    <?php if (!empty($_GET['msg'])) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if (!empty($msg)) : ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <h4>Upload New Product</h4>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="product_description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Price (ZAR)</label>
            <input type="number" step="0.01" name="product_price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Category</label>
            <select name="product_category" class="form-control" required>
                <option value="" disabled selected>Select a category</option>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="product_image" accept="image/*" class="form-control" required>
        </div>
        <button type="submit" name="upload_product" class="btn btn-primary">Upload Product</button>
    </form>

    <hr>

    <h4>Your Products</h4>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (ZAR)</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Denied Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['title']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td>R<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></td>
                        <td><img src="uploads/marketplace_products/<?php echo htmlspecialchars($product['image']); ?>" width="100" alt="Product Image"></td>
                        <td>
                            <?php 
                                if ($product['approved']) {
                                    echo '<span class="badge bg-success">Approved</span>';
                                } elseif (!empty($product['denied_reason'])) {
                                    echo '<span class="badge bg-danger">Denied</span>';
                                } else {
                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <?php 
                                echo !empty($product['denied_reason']) ? htmlspecialchars($product['denied_reason']) : '-'; 
                            ?>
                        </td>
                        <td>
                            <a href="edit_c2c_product.php?product_id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="seller_dashboard.php?delete_product_id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no products listed yet.</p>
    <?php endif; ?>
</div>

<?php include('layouts/footer.php'); ?>
