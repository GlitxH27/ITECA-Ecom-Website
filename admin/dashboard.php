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
      $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM orders");
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

      $stmt2 =$conn->prepare("SELECT * FROM orders LIMIT $offset,$total_records_per_page");
      $stmt2->execute();
      $orders = $stmt2->get_result();

     

?>










  <div class="container-fluid">
    <div class="row">
      
    <?php include('sidemenu.php'); ?>

      <!-- Main content -->
      <main class="col-md-10 ms-sm-auto px-4 py-4">
        <h2>Welcome, Admin</h2>

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
            <?php if (isset($_SESSION['message'])): ?>
              <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>

          <h4>Orders</h4>
          <table class="table mt-3">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Order Status</th>
                <th>User ID</th>
                <th>Order Date</th>
                <th>User Phone</th>
                <th>User Address</th>
                <th>Edit</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody>

              <?php foreach($orders as $order)   {?>
              <tr>
                <td><?php echo $order['order_id'];?></td>
                <td><?php echo $order['order_status'];?></td>
                <td><?php echo $order['user_id'];?></td>
                <td><?php echo $order['order_date'];?></td>
                <td><?php echo $order['user_phone'];?></td>
                <td><?php echo $order['user_address'];?></td>

                <td><a class="btn btn-sm btn-primary" href="edit_order.php?order_id=<?php echo $order['order_id'];?>">Edit</a></td>
                <td><a class="btn btn-sm btn-danger"  href="delete_order.php?order_id=<?php echo $order['order_id']; ?>"
                              onclick="return confirm('Are you sure you want to delete this order?');">Delete</a></td>
                
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

