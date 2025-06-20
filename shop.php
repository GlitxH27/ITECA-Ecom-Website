<?php include('layouts/header.php'); ?>

<?php

include('server/connection.php');

$price = isset($_GET['price']) ? $_GET['price'] : 1000;
  //uses the search section
if(isset($_POST['search'])){

    if(isset($_GET['page_no']) && $_GET['page_no'] != ""){
    //if user has already entered page then page number is the on that they selected
    $page_no = $_GET['page_no'];
    }else{
    //if user just entered the page then default page is 1
    $page_no = 1;
    }

     $category = $_POST['category'];
     $price = $_POST['price'];



    //2. return number of products
    $stmt1 = $conn->prepare("SELECT COUNT(*) As total_records FROM products WHERE product_category=? AND product_price<=?");
    $stmt1->bind_param('si',$category,$price);
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
    $stmt2 =$conn->prepare("SELECT * FROM products WHERE product_category=? AND product_price<=? LIMIT $offset,$total_records_per_page");
    $stmt2->bind_param("si",$category,$price);
    $stmt2->execute();
    $products = $stmt2->get_result();



  
}else{
  //return all the products -> for small businesses
 /* $stmt = $conn->prepare("SELECT * FROM products");

  $stmt->execute();

  $products = $stmt->get_result(); */


 // 1. Get page number
$page_no = isset($_GET['page_no']) ? $_GET['page_no'] : 1;

// 2. Pagination setup
$total_records_per_page = 8;
$offset = ($page_no - 1) * $total_records_per_page;

// 3. Build query based on input
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

$sql_where = "WHERE 1";
$params = [];
$types = "";

// Add category filter if not "all"
if ($category !== "all") {
    $sql_where .= " AND product_category = ?";
    $types .= "s";
    $params[] = $category;
}

// Add search filter if not empty
if (!empty($search_query)) {
    $sql_where .= " AND product_name LIKE ?";
    $types .= "s";
    $params[] = "%" . $search_query . "%";
}

// Count total products
$stmt1 = $conn->prepare("SELECT COUNT(*) as total_records FROM products $sql_where");
if (!empty($params)) {
    $stmt1->bind_param($types, ...$params);
}
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();

// Total pages
$total_no_of_pages = ceil($total_records / $total_records_per_page);

// Get products with limit
$stmt2 = $conn->prepare("SELECT * FROM products $sql_where LIMIT ?, ?");
$types .= "ii";
$params[] = $offset;
$params[] = $total_records_per_page;
$stmt2->bind_param($types, ...$params);
$stmt2->execute();
$products = $stmt2->get_result();
}





?>


<!-- Main content with filter and products -->
  <div class="container-fluid mt-5 pt-5">
    <div class="row">
      <!-- Search Sidebar -->
      <!--<div class="col-lg-2 col-md-3 col-sm-12 " id="search-sidebar">
        <section id="search" class="mt-5 py-3">
          <h5>Search Products</h5>
          <hr />

          <form action="shop.php" method="POST">
            <p>Category</p>
            <div class="form-check">
              <input class="form-check-input" value="shoes" type="radio" name="category" id="category_one" <?php if(isset($category) && $category=='shoes'){echo 'checked';} ?>/>
              <label class="form-check-label" for="flexRadioDefault1">Shoes</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="coats" type="radio" name="category" id="category_two" <?php if(isset($category) && $category=='coats'){echo 'checked';} ?>/>
              <label class="form-check-label" for="flexRadioDefault2">Coats</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="watches" type="radio" name="category" id="category_three" <?php if(isset($category) && $category=='watches'){echo 'checked';} ?>/>
              <label class="form-check-label" for="flexRadioDefault2">Watches</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" value="bags" type="radio" name="category" id="category_four" <?php if(isset($category) && $category=='bags'){echo 'checked';} ?>/>
              <label class="form-check-label" for="flexRadioDefault2">Bags</label>
            </div>

             <div class="form-check">
              <input class="form-check-input" value="featured" type="radio" name="category" id="category_five" <?php if(isset($category) && $category=='featured'){echo 'checked';} ?>/>
              <label class="form-check-label" for="flexRadioDefault2">Featured</label>
            </div>

            <p class="mt-4">Price</p>
            <input type="range" class="form-range w-75" name="price" value="<?php if(isset($price)){echo $price;}else{echo "100";} ?>" min="1" max="1000" id="customRange2" />
            <div class="w-75 d-flex justify-content-between">
              <span>1</span>
              <span>1000</span>
            </div>

            <div class="form-group my-3">
              <input type="submit" name="search" value="Search" class="btn btn-primary" />
            </div>
          </form>
        </section>
      </div> -->

      <!-- Shop Section -->
      <div class="col-12">



<!-- Shop Search & Filter Form -->
<div class="container mt-5 pt-4">
  <div class="text-center mb-4">
    <h3>Featured Items</h3>
    <hr class="mx-auto"/>
  </div>

  <form method="GET" action="shop.php" id="shopFilterForm" class="mx-auto" style="max-width: 600px;" autocomplete="off">
    <!-- Search input and clear/search buttons -->
    <div class="d-flex mb-3">
      <input 
        type="text" 
        id="searchInput"
        name="search_query" 
        class="form-control" 
        placeholder="Search for products..." 
        value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>"
      />
      <button 
        type="button" 
        id="clearSearch" 
        class="btn btn-outline-secondary ms-2" 
        style="width: 40px;" 
        title="Clear search"
      >&times;</button>
      <button type="submit" class="btn btn-primary ms-2">Search</button>
    </div>

    <!-- Category dropdown and Filter/Reset buttons -->
    <div class="d-flex justify-content-center align-items-center mb-3">
      <select class="form-select form-select-sm w-auto" name="category" aria-label="Filter by category">
        <option value="all" <?php if(isset($_GET['category']) && $_GET['category']=='all') echo 'selected'; ?>>All Categories</option>
        <option value="shoes" <?php if(isset($_GET['category']) && $_GET['category']=='shoes') echo 'selected'; ?>>Shoes</option>
        <option value="coats" <?php if(isset($_GET['category']) && $_GET['category']=='coats') echo 'selected'; ?>>Coats</option>
        <option value="watches" <?php if(isset($_GET['category']) && $_GET['category']=='watches') echo 'selected'; ?>>Watches</option>
        <option value="bags" <?php if(isset($_GET['category']) && $_GET['category']=='bags') echo 'selected'; ?>>Bags</option>
        <option value="featured" <?php if(isset($_GET['category']) && $_GET['category']=='featured') echo 'selected'; ?>>Featured</option>
        <option value="electronics" <?php if(isset($_GET['category']) && $_GET['category']=='electronics') echo 'selected'; ?>>Electronics</option>
        <option value="fashion" <?php if(isset($_GET['category']) && $_GET['category']=='fashion') echo 'selected'; ?>>Fashion</option>
        <option value="home_and_garden" <?php if(isset($_GET['category']) && $_GET['category']=='home_and_garden') echo 'selected'; ?>>Home and Garden</option>
        <option value="books" <?php if(isset($_GET['category']) && $_GET['category']=='books') echo 'selected'; ?>>Books</option>
        <option value="toys" <?php if(isset($_GET['category']) && $_GET['category']=='toys') echo 'selected'; ?>>Toys</option>
        <option value="beauty" <?php if(isset($_GET['category']) && $_GET['category']=='beauty') echo 'selected'; ?>>Beauty</option>
      </select>

      <button type="submit" class="btn btn-primary btn-sm ms-2">Filter</button>
      <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm ms-2">Reset</button>
    </div>
  </form>

  <div class="text-center">
    <p class="text-muted">Showing <strong><?php echo $products->num_rows; ?></strong> item(s)</p>
  </div>
</div>
 <hr style="border-top: #dee2e6; width: 100%; height:0.5px;">

<!-- JS: Clear + Reset logic -->
<script>
  // Clear search text
  document.getElementById('clearSearch').addEventListener('click', function () {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInput').focus();
  });

  // Reset filters
  document.getElementById('resetFilters').addEventListener('click', function () {
    document.getElementById('searchInput').value = '';
    document.querySelector('select[name="category"]').value = 'all';
    document.getElementById('shopFilterForm').submit();
  });
</script>

          <div class="row mx-auto container-fluid">
            <!--Featured -->
            <style>
  .card {
    border-radius: 16px;
    transition: transform 0.2s ease;
  }

  .card:hover {
    transform: translateY(-5px);
  }

  .star i {
    font-size: 0.9rem;
    color: black;
  }

  .card-title {
    font-size: 1rem;
    font-weight: 600;
  }

  .card-text.price {
    color: #000;
    font-weight: bold;
    margin-bottom: 1rem;
  }

  @media (max-width: 576px) {
    .col-6 {
      padding-left: 8px;
      padding-right: 8px;
    }
  }
</style>

<?php while($row = $products->fetch_assoc()) { ?>
  <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-4">
    <div class="card h-100 border-0 shadow-sm">
      <img src="assets/imgs/<?php echo $row['product_image']; ?>" class="card-img-top" alt="<?php echo $row['product_name']; ?>">
      
      <div class="card-body text-center d-flex flex-column">
        <div class="mb-2">
          <div class="star mb-2">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
          </div>
          <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
          <p class="card-text price">R<?php echo $row['product_price']; ?></p>
        </div>
        <a href="single_product.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-outline-primary mt-auto">Buy Now</a>
      </div>
    </div>
  </div>
            
            <?php } ?>


            <!-- Add more products here -->


            <!---->
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
           
        </section>
      </div>
    </div>
  </div>

<?php include('layouts/footer.php'); ?>
