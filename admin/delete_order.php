<?php include('header.php'); ?>
<?php
include('../server/connection.php'); 

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Delete order if order_id is provided
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Order #$order_id deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Failed to delete order.";
        $_SESSION['message_type'] = "danger";
    }

    header("Location: dashboard.php");
    exit();
} else {
    $_SESSION['message'] = "No order ID provided.";
    $_SESSION['message_type'] = "danger";
    header("Location: dashboard.php");
    exit();
}
?>
