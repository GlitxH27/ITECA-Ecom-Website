<?php include('header.php'); ?>
<?php
include('../server/connection.php'); // Adjust path if needed

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Check if product_id is passed
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Optional: Fetch image to delete from filesystem (if needed)
    $stmt = $conn->prepare("SELECT product_image FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Delete the product
    $delete_stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $delete_stmt->bind_param("i", $product_id);

    if ($delete_stmt->execute()) {
        // Optionally delete the image file from server
        if (!empty($product['product_image']) && file_exists("../assets/imgs/" . $product['product_image'])) {
            unlink("../assets/imgs/" . $product['product_image']);
        }

        $_SESSION['message'] = "Product deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete product.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: products.php");
    exit();
} else {
    $_SESSION['message'] = "No product ID provided.";
    $_SESSION['message_type'] = "danger";
    header("Location: products.php");
    exit();
}
?>