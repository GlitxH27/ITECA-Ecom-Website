<?php include('header.php'); ?>


<?php 
   
    if(!isset($_SESSION['admin_logged_in'])){
        header('location: login.php');
        exit();

    }
    
?>


<?php


    //1. determine page number
      if(isset($_GET['page_no']) && $_GET['page_no'] != ""){
        //if user has already entered page then page number is the on that they selected
        $page_no = $_GET['page_no'];
      }else{
        //if user just entered the page then default page is 1
        $page_no = 1;
      }

      //2. return number of products
      $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products");
      $stmt1->execute();
      $stmt1->bind_result($total_records);
      $stmt1->store_result();
      $stmt1->fetch();

      //3. products per page
      $total_records_per_page = 8;
      $offset = ($page_no-1) * $total_records_per_page;
      $previous_page = $page_no - 1;
      $next_page = $page_no +1 ;
      $adjacents ="2";
      $total_no_of_pages = ceil($total_records/$total_records_per_page);

      //4. get all products

      $stmt2 =$conn->prepare("SELECT * FROM products LIMIT $offset,$total_records_per_page");
      $stmt2->execute();
      $products = $stmt2->get_result();

     

?>


  <div class="container-fluid">
    <div class="row">
      
    <?php include('sidemenu.php'); ?>

      <!-- Main content -->
      <main class="col-md-10 ms-sm-auto px-4 py-4">
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['message']; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
      <?php endif; ?>
      
        <h2>Welcome, Seller</h2>

        <!-- Dashboard Cards -->
        <div class="row my-4">
          <div class="col-md-4">
            <div class="bg-white p-4 dashboard-card">
              <h5>Total Products</h5>
              <p class="fs-4">12</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="bg-white p-4 dashboard-card">
              <h5>Total Sales</h5>
              <p class="fs-4">R 2,340</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="bg-white p-4 dashboard-card">
              <h5>Pending Orders</h5>
              <p class="fs-4">3</p>
            </div>
          </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white p-4 dashboard-card">
          <h4>Products</h4>
          <table class="table mt-3">
            <thead>
              <tr>
                <th>Product ID</th>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Product Price</th>
                <th>Product Offer</th>
                <th>Product Category</th>
                <th>Product Color</th>
                <th>Edit</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach($products as $product)   {?>
              <tr>
                <td><?php echo $product['product_id'];?></td>
                <td><img src="<?php echo "../assets/imgs/". $product['product_image'];?>" alt="" style="width: 70px; height:70px "></td>
                <td><?php echo $product['product_name'];?></td>
                <td><?php echo "R".$product['product_price'];?></td>
                <td><?php echo $product['product_special_offer'] ."%";?></td>
                <td><?php echo $product['product_category'];?></td>
                <td><?php echo $product['product_color'];?></td>

                <td><a class="btn btn-sm btn-primary" href="edit_product.php?product_id=<?php echo $product['product_id'];?>">Edit</a></td>
                <td><a class="btn btn-sm btn-danger" href="delete_product.php?product_id=<?php echo $product['product_id'];?>">Delete</a></td>
                
              </tr>

              <?php }?>
              <!-- Repeat rows as needed -->
            </tbody>
          </table>


              <nav aria-label="Page navigation example">
                <ul class="pagination mt-5">


                <li class="page-item <?php if($page_no<=1){echo 'disabled';} ?> ">
                    <a class="page-link" href="<?php if($page_no <= 1){echo '#';}else{echo "?page_no=".($page_no-1);} ?>">Previous</a>
                </li>




                <li class="page-item"><a class="page-link" href="?page_no=1">1</a></li>
                <li class="page-item"><a class="page-link" href="?page_no=2">2</a></li>

                <?php if( $page_no >=3) {?>
                  <li class="page-item"><a class="page-link" href="#">...</a></li>
                  <li class="page-item"><a class="page-link" href="<?php echo "?page_no=".$page_no; ?>"><?php echo $page_no; ?></a></li>
                <?php } ?>

                
                <li class="page-item <?php if($page_no >= $total_no_of_pages){echo 'disabled';} ?>">
                  <a class="page-link" href="<?php if($page_no >= $total_no_of_pages){echo '#';}else{echo "?page_no=".($page_no+1);} ?>">Next</a>
                </li>

                </ul>
            </nav>


        </div>
      </main>

    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>