
<?php

session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
//include('../server/connection.php');

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!--<link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">-->
    <script src="https://kit.fontawesome.com/47f904657d.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="./assets/css/style.css">

    <style>
    .nav-buttons{
    margin-left: 0%;
}


.navbar{
    font-size: 16px;
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
    z-index: 1000;
    background-color: white
}
    </style>

</head>
<body>
    
<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 fixed-top"> <!--bg-body-tertiary?-->
  <div class="container-fluid">


    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent"> <!--nav-buttons class-->
      <!-- Logo -->
     <a class="navbar-brand me-4" href="index.php">
      <img src="assets/imgs/logo_nobg.png" alt="Logo" height="70">
    </a>
    <ul class="navbar-nav mb-2 mb-lg-0 gap-3" >
       
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="shop.php">Shop</a>
        </li>

         <li class="nav-item">
         <a class="nav-link" href="marketplace.php">Market Place</a>
        </li>

         <!--<?php if (
              isset($_SESSION['logged_in']) &&
              !empty($_SESSION['is_seller']) &&
              $currentPage == 'marketplace.php'
          ): ?>
              <li class="nav-item">
                  <a class="nav-link" href="upload_product.php">Sell Your Products</a>
              </li>
          <?php endif; ?>-->

         <li class="nav-item">
          <a class="nav-link" href="contact.php">Contact us</a>
        </li>

      </ul>



      <!-- User + Cart Section (slightly shifted right) -->
      <div class="d-flex align-items-center ms-5 nav-button">

        <?php if (isset($_SESSION['logged_in'])): ?>
          <span class="me-3">Hello, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
          <a href="account.php?logout=1" class="btn btn-outline-danger me-2">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <?php endif; ?>

        <!-- Cart -->
        <a href="cart.php" class="position-relative me-2">
        <i class="bi bi-cart fs-5 text-dark"></i>
        <?php if (isset($_SESSION['logged_in']) && isset($_SESSION['quantity']) && $_SESSION['quantity'] != 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $_SESSION['quantity'] ?>
            </span>
        <?php endif; ?>
        </a>


          <!-- Account Icon -->
          <a href="account.php" class="text-dark">
            <i class="fas fa-user fs-5"></i>
          </a>

      </div>
    </div>
  </div>
</nav>