<?php include('header.php'); ?>
<?php
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Static categories — you can replace this with a DB fetch if needed
$categories = ["Electronics", "Fashion", "Home & Garden", "Books", "Toys", "Beauty"];

// Handle form submission
if (isset($_POST['add_product'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $offer = $_POST['product_special_offer'];
    $category = $_POST['product_category'];
    $color = $_POST['product_color'];
    $description = $_POST['product_description'];

    // Sanitize product name for filenames
$sanitized_name = preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($name));

// Allowed file extensions
$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
$uploaded_images = [];

// Match form field names to DB field order
$image_fields = ['product_image', 'product_image2', 'product_image3', 'product_image4'];

foreach ($image_fields as $index => $input_name) {
    if (!empty($_FILES[$input_name]['name'])) {
        $file_tmp = $_FILES[$input_name]['tmp_name'];
        $file_name = basename($_FILES[$input_name]['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = $sanitized_name . '_' . ($index + 1) . '.' . $file_ext;
            $destination = "../assets/imgs/$new_file_name";

            if (move_uploaded_file($file_tmp, $destination)) {
                $uploaded_images[] = $new_file_name;
            } else {
                $_SESSION['message'] = "Failed to upload image " . ($index + 1);
                $_SESSION['message_type'] = "danger";
                header("Location: add_product.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Invalid file type for image " . ($index + 1);
            $_SESSION['message_type'] = "danger";
            header("Location: add_product.php");
            exit();
        }
    } else {
        $uploaded_images[] = null;
    }
}

    // Insert product into DB
    $stmt = $conn->prepare("INSERT INTO products 
    (product_name, product_price, product_special_offer, product_category, product_color, product_description, product_image, product_image2, product_image3, product_image4)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssssssss", 
    $name, 
    $price, 
    $offer, 
    $category, 
    $color, 
    $description, 
    $uploaded_images[0], 
    $uploaded_images[1], 
    $uploaded_images[2], 
    $uploaded_images[3]
);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: products.php");
        exit();
    } else {
        $_SESSION['message'] = "Failed to add product.";
        $_SESSION['message_type'] = "danger";
    }
}
?>


<div class="container-fluid">
  <div class="row">
    <?php include('sidemenu.php'); ?>

    <main class="col-md-10 ms-sm-auto px-4 py-4">
      <h2>Add New Product</h2>

      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
          <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message'], $_SESSION['message_type']); 
          ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-4 shadow rounded">
        <div class="mb-3">
          <label>Product Name</label>
          <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Product Price (R)</label>
          <input type="number" step="0.01" name="product_price" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Special Offer (%)</label>
          <input type="number" name="product_special_offer" class="form-control" placeholder="Optional">
        </div>
        <div class="mb-3">
          <label>Category</label>
          <select name="product_category" class="form-select" required>
            <option value="" disabled selected>Select a category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Color</label>
          <input type="text" name="product_color" class="form-control" placeholder="Optional">
        </div>
        <div class="mb-3">
          <label>Description</label>
          <textarea name="product_description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label>Product Image 1</label>
          <input type="file" name="product_image" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Product Image 2</label>
          <input type="file" name="product_image2" class="form-control">
        </div>
        <div class="mb-3">
          <label>Product Image 3</label>
          <input type="file" name="product_image3" class="form-control">
        </div>
        <div class="mb-3">
          <label>Product Image 4</label>
          <input type="file" name="product_image4" class="form-control">
        </div>

        <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
      </form>
    </main>
  </div>
</div>