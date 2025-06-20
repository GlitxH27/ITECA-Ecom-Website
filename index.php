<?php include('layouts/header.php'); ?>

<!--Home-->
<section id="banner" class="mt-5">
  <div class="container">
    <h5 style="color:white">NEW ARRIVALS</h5>
    <h1 style="color:white"><span>Best Prices</span> This Season</h1>
    <p style="color:white">Afri Mart offers the best products for the most affordable prices </p>
    <a href="shop.php"><button>Shop Now</button></a>
    <a href="seller_dashboard.php"><button style="color: amber; margin-left:8px;">List Products</button></a>
  </div>
</section>




<!--Featured-->
<section id="featured" class="my-5 pb-5">
  <div class="container text-center mt-5 py-5">
    <h3>Our Featured </h3>
    <hr class="mx-auto">
    <p>Here you can check out our new featured products</p>
  </div>
  <div class="row mx-auto container-fluid">

    <?php include('server/get_featured_products.php'); ?>

    <?php while($row= $featured_products->fetch_assoc()) { ?>

    <!--Featured 1-->
    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
      <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>

      <div class="star">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
      </div>
      <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
      <h4 class="p-price">R <?php echo $row['product_price']; ?></h4>

      <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now </button></a>
    </div>

    <?php } ?>
  </div>
</section>

<!--Banner-->
<section id="banner" class="my-5 py-5">
  <div class="container">
    <h4>Mid Season Sale</h4>
    <h1>Autumn Collection <br> UP to 30% OFF</h1>
    <a href="shop.php"><button class="text-uppercase">shop now</button></a>
  </div>
</section>

<!--Clothes-->
<section id="featured" class="my-5 py-5">
  <div class="container text-center mt-5 ">
    <h3>Dresses & Coats</h3>
    <hr class="mx-auto">
    <p>Here you can check out our new clothing products</p>
  </div>

  <div class="row mx-auto container-fluid">

    <?php include('server/get_coats.php'); ?>

    <?php while($row= $coats_products->fetch_assoc()) { ?>


    <!--clothes 1-->
    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
      <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>" alt="">

      <div class="star">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
      </div>
      <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
      <h4 class="p-price">R<?php echo $row['product_price']; ?></h4>
      <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now </button></a>
    </div>
  <?php } ?>

  </div>
</section>


<!--Watches-->
<section id="featured" class="my-5">
  <div class="container text-center mt-5 py-5">
    <h3>Watches</h3>
    <hr class="mx-auto">
    <p>Here you can check out our latest watch products</p>
  </div>

  <div class="row mx-auto container-fluid">

    <?php include('server/get_watches.php'); ?>

    <?php while($row= $watches->fetch_assoc()) { ?>

    <!--Watch 1-->
    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
      <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>" alt="">

      <div class="star">
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
        <i class="fas fa-star"></i>
      </div>
      <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
      <h4 class="p-price">R<?php echo $row['product_price']; ?></h4>
      <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now </button></a>
    </div>

      <?php } ?>

  </div>
</section>





<?php include('layouts/footer.php'); ?>

