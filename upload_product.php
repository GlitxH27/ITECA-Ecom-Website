<?php include 'layouts/header.php'; ?>
<?php
include('server/connection.php');

$errors = [];
$success = '';
$productPreview = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    if (!isset($_SESSION['user_id']) || !$_SESSION['is_seller']) {
    header("Location: login.php?error=You must be logged in as a seller to upload products");
    exit();
}

$seller_id = $_SESSION['user_id'];

    // Validate inputs
    if (!$title) $errors[] = "Product title is required.";
    if ($price <= 0) $errors[] = "Price must be greater than zero.";
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Product image is required.";
    }

    if (empty($errors)) {
        // Handle image upload
        $uploadDir = 'assets/uploads/';
        $originalName = basename($_FILES['image']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowedTypes)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            $uniqueFilename = uniqid('img_', true) . '.' . $extension;
            $imagePath = $uploadDir . $uniqueFilename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO marketplace_products (seller_id, title, description, price, image, approved) VALUES (?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("issds", $seller_id, $title, $description, $price, $imagePath);

                if ($stmt->execute()) {
                    $success = "Product uploaded successfully and awaiting approval.";
                    header("Location: marketplace.php?upload=success");
                    exit();
                    
                    $productPreview = [
                        'title' => $title,
                        'description' => $description,
                        'price' => $price,
                        'image' => $imagePath
                    ];
                    // Clear form values
                    $_POST = [];
                } else {
                    $errors[] = "Database error: " . $conn->error;
                    unlink($imagePath); // Roll back file
                }
                $stmt->close();
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }
}
?>

<div class="container-fluid mt-5 pt-5">
    <h2 class="text-center mt-4 mb-4">Upload a Product</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data" class="mx-auto mb-5" style="max-width: 600px;">
        <div class="mb-3">
            <label for="title" class="form-label">Product Title</label>
            <input type="text" name="title" id="title" class="form-control" required
                value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Product Description</label>
            <textarea name="description" id="description" rows="4" class="form-control"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (ZAR)</label>
            <input type="number" name="price" id="price" step="0.01" class="form-control" required
                value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Upload Product</button>
    </form>

    <?php if ($productPreview): ?>
        <div class="card mx-auto shadow" style="max-width: 400px;">
            <img src="<?php echo htmlspecialchars($productPreview['image']); ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($productPreview['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($productPreview['description']); ?></p>
                <p class="fw-bold">R<?php echo number_format($productPreview['price'], 2); ?></p>
                <span class="badge bg-warning text-dark">Pending Approval</span>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layouts/footer.php'; ?>
