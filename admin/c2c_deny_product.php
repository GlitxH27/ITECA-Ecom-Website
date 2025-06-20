<?php include('header.php'); ?>
<?php
include('../server/connection.php');

if(!isset($_SESSION['admin_logged_in'])){
    header('location: login.php');
    exit();
}

// Check if form was submitted via POST and id is set
if (isset($_POST['id']) && isset($_POST['denied_reason'])) {
    $product_id = intval($_POST['id']);
    $denied_reason = trim($_POST['denied_reason']);

    // Prepare update query with denial reason
    $stmt = $conn->prepare("UPDATE marketplace_products SET approved = 0, denied_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $denied_reason, $product_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Product denied successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to deny product.";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "No product ID or denial reason provided.";
    $_SESSION['message_type'] = "warning";
}

header("Location: c2c_products.php");
exit();
?>
