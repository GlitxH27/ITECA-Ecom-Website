<?php
session_start();
include('../server/connection.php');

// Check if user is logged in as seller
if (!isset($_SESSION['user_id'])) {
    header('Location: ../users/login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // First, verify that the product belongs to the logged-in user (seller_id instead of user_id)
    $stmt = $conn->prepare("SELECT image FROM marketplace_products WHERE id = ? AND seller_id = ?");
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($image_file);
        $stmt->fetch();
        $stmt->close();

        // Delete image file from server
        if ($image_file) {
            $image_path = "../uploads/marketplace_products/" . $image_file;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete product record from database
        $stmt_del = $conn->prepare("DELETE FROM marketplace_products WHERE id = ? AND seller_id = ?");
        $stmt_del->bind_param("ii", $product_id, $user_id);

        if ($stmt_del->execute()) {
            $_SESSION['message'] = "Product deleted successfully.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to delete product.";
            $_SESSION['message_type'] = "danger";
        }

        $stmt_del->close();

    } else {
        // Product doesn't exist or doesn't belong to this user
        $_SESSION['message'] = "Unauthorized or product not found.";
        $_SESSION['message_type'] = "warning";
    }

} else {
    $_SESSION['message'] = "No product ID specified.";
    $_SESSION['message_type'] = "warning";
}

// Redirect back to seller's products page (adjust the path as needed)
header("Location: ../admin/c2c_products.php");
exit();