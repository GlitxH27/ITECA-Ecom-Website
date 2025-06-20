<?php include('layouts/header.php'); ?>

<?php


/* Remove any invalid cart items with empty keys
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if (empty($key)) {
            unset($_SESSION['cart'][$key]);
        }
    }
} */



if(isset($_POST['add_to_cart'])){

  //if user has alredy added a product to the cart
  if(isset($_SESSION['cart'])){

    $products_array_ids = array_column($_SESSION['cart'], "product_id"); 

    //if product has already been added to the cart or not
    if( !empty($_POST['product_id']) ){ // replaced if( !in_array($_POST['product_id'], $products_array_ids) ){

        $product_id = $_POST['product_id'];

          $product_array = array(
                            'product_id' => $product_id,  //removed $_POST[]
                            'product_name' => $_POST['product_name'],
                            'product_price' => $_POST['product_price'],
                            'product_image' => $_POST['product_image'],
                            'product_quantity' => $_POST['product_quantity']
          );

          $_SESSION['cart'][$product_id] = $product_array;


      //produc has already been added
    }else{

        echo '<script>alert("Product was already added to cart");</script>';
        //echo '<script>window.location="index.php";</script>';

    }




    //if this is the first product
    }else{

      $product_id = $_POST['product_id'];
      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $product_image = $_POST['product_image'];
      $product_quantity = $_POST['product_quantity'];

      $product_array = array(
                        'product_id' => $product_id,
                        'product_name' => $product_name,
                        'product_price' => $product_price,
                        'product_image' => $product_image,
                        'product_quantity' => $product_quantity
      );

      $_SESSION['cart'][$product_id] = $product_array;
      
    }

    //calculate total
    calculateTotalCart();






//remove product from cart
}else if(isset($_POST['remove_product'])){

  $product_id = (int)$_POST['product_id']; //added force int (int)
  unset($_SESSION['cart'][$product_id]);

  //calculate total
  calculateTotalCart();


}else if( isset($_POST['edit_quantity'])){

  // we get id and quantity from the form
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

  // get the product array from the session
    $product_array = $_SESSION['cart'][$product_id];

  //update product quantity
    $product_array['product_quantity'] = $product_quantity;

  //return array back to its place
    $_SESSION['cart'][$product_id] = $product_array;

  //calculate total
  calculateTotalCart();


}else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    header('location: index.php');
}


  function calculateTotalCart(){

    $total_price = 0;
    $total_quantity = 0;

    foreach($_SESSION['cart'] as $key => $value){

        $product = $_SESSION['cart'][$key];

        $price = $product['product_price'];
        $quantity = $product['product_quantity'];

        $total_price = $total_price + ($price * $quantity);
        $total_quantity = $total_quantity + $quantity;

    }

    $_SESSION['total'] = $total_price;
    $_SESSION['quantity'] = $total_quantity;


  }


?>





<!--Cart-->
<section class="cart container my-5 py-5" style="padding-bottom: 100px;">
    <div class="mt-5 pt-5">
        <h2 class="fw-bold">Your Cart</h2>
        <hr>
    </div>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col" style="width: 120px;">Quantity</th>
                        <th scope="col" style="width: 120px;">Subtotal</th>
                        <th scope="col" style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $key => $value): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="assets/imgs/<?php echo htmlspecialchars($value['product_image']); ?>" alt="" 
                                     class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                <div class="ms-3">
                                    <p class="mb-1 fw-semibold"><?php echo htmlspecialchars($value['product_name']); ?></p>
                                    <small class="text-muted">R<?php echo number_format($value['product_price'], 2); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method="POST" action="cart.php" class="d-flex align-items-center">
                                <input type="hidden" name="product_id" value="<?php echo (int)$value['product_id']; ?>">
                                <input type="number" 
                                       name="product_quantity" 
                                       value="<?php echo (int)$value['product_quantity']; ?>" 
                                       min="1" max="99" 
                                       class="form-control form-control-sm me-2" 
                                       style="width: 70px;">
                                <button type="submit" name="edit_quantity" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </td>
                        <td>
                            <span class="fw-bold">R<?php echo number_format($value['product_quantity'] * $value['product_price'], 2); ?></span>
                        </td>
                        <td>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="product_id" value="<?php echo (int)$value['product_id']; ?>">
                                <button type="submit" name="remove_product" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <h5>Total: <span class="text-primary">R<?php echo number_format($_SESSION['total'], 2); ?></span></h5>
        </div>

        <div class="checkout-container d-flex justify-content-end mt-3">
            <form method="POST" action="checkout.php">
                <button type="submit" class="btn btn-success btn-lg">Checkout</button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-center fs-5 mt-5">Your cart is empty.</p>
    <?php endif; ?>
</section>

<?php include('layouts/footer.php'); ?>