<?php include('header.php'); ?>
<?php
include('../server/connection.php'); // Adjust path if needed

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Get product ID from query
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "Product not found.";
        exit();
    }
} else {
    echo "No product ID provided.";
    exit();
}

// Handle form submission
if (isset($_POST['update_product'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $offer = $_POST['product_special_offer'];
    $category = $_POST['product_category'];
    $color = $_POST['product_color'];
    $image = $product['product_image'];
    $description = $_POST['product_description'];

    // Handle image upload
    if (!empty($_FILES['product_image']['name'])) {
        $image = time() . '_' . basename($_FILES['product_image']['name']);
        $image_tmp = $_FILES['product_image']['tmp_name'];
        move_uploaded_file($image_tmp, "../assets/imgs/$image");
    }

    // Update product
    $stmt = $conn->prepare("UPDATE products SET product_name=?, product_price=?, product_special_offer=?, product_category=?, product_color=?, product_description=?, product_image=? WHERE product_id=?");
    $stmt->bind_param("sdsssssi", $name, $price, $offer, $category, $color, $description, $image, $product_id);

      if ($stmt->execute()) {
          $_SESSION['message'] = "Product updated successfully!";
          $_SESSION['message_type'] = "success";
          header("Location: products.php");
          exit();
          } else {
          $_SESSION['message'] = "Failed to update product.";
          $_SESSION['message_type'] = "danger";
          header("Location: edit_product.php?product_id=$product_id");
          exit();
      }
    }
?>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Content -->
    <main class="col-md-10 ms-sm-auto px-4 py-4">
    <h2>Edit Product</h2>
    <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="product_name" class="form-control" value="<?= $product['product_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Product Price</label>
            <input type="number" step="0.01" name="product_price" class="form-control" value="<?= $product['product_price']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Product Offer (%)</label>
            <input type="number" name="product_special_offer" class="form-control" value="<?= $product['product_special_offer']; ?>">
        </div>
        <div class="mb-3">
            <label>Product Category</label>
            <input type="text" name="product_category" class="form-control" value="<?= $product['product_category']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Product Color</label>
            <input type="text" name="product_color" class="form-control" value="<?= $product['product_color']; ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="product_description" class="form-control" rows="5" required><?php echo htmlspecialchars($product['product_description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Product Image</label><br>
            <img src="../assets/imgs/<?= $product['product_image']; ?>" width="100" height="100"><br><br>
            <input type="file" name="product_image" class="form-control">
        </div>
        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>
