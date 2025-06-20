<?php include('header.php'); ?>
<?php
include('../server/connection.php');

if(!isset($_SESSION['admin_logged_in'])){
    header('location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE marketplace_products SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product approved successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to approve product.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "No product ID provided.";
    $_SESSION['message_type'] = "warning";
}

header("Location: c2c_products.php");
exit();
