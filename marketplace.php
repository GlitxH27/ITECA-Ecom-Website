<?php include 'layouts/header.php'; ?>
<?php
include('server/connection.php');

// Handle search input safely
$search = '';
$categoryFilter = '';

if (isset($_GET['search'])) {
    $search = trim(mysqli_real_escape_string($conn, $_GET['search']));
}
if (isset($_GET['category'])) {
    $categoryFilter = trim(mysqli_real_escape_string($conn, $_GET['category']));
}
?>

<div class="container-fluid mt-5 pt-5">
    <h3 class="mt-5 text-center">Marketplace Products</h3>
    <hr class="mx-auto">

    <!-- Search Bar -->
    <div class="row justify-content-center mb-2">
        <div class="col-md-6">
            <form method="GET" action="marketplace.php" id="searchForm" class="input-group">
                <input 
                    type="text" 
                    id="searchInput"
                    name="search" 
                    class="form-control" 
                    placeholder="Search products..." 
                    value="<?php echo htmlspecialchars($search); ?>" 
                    aria-label="Search products"
                >
                <button type="button" id="clearSearch" class="btn btn-outline-secondary" style="width: 40px;">&times;</button>
                <button type="submit" class="btn btn-primary ms-2" name="search-bar">Search</button>
            </form>
        </div>
    </div>

    <!-- Dynamic Category Input -->
    <?php
    $categoryQuery = "SELECT id, name FROM categories";
    $categoryResult = mysqli_query($conn, $categoryQuery);
    $categories = [];
    while ($cat = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $cat;
    }
    ?>
    <div class="row justify-content-center mb-4">
        <div class="col-md-6 text-center">
            <form method="GET" action="marketplace.php" id="filterForm" class="d-inline-flex align-items-center justify-content-center">
                <input 
                    type="text" 
                    name="category" 
                    list="category-list" 
                    class="form-control form-control-sm" 
                    placeholder="Type or select a category" 
                    value="<?php echo htmlspecialchars($categoryFilter); ?>" 
                    style="max-width: 250px;"
                >
                <datalist id="category-list">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>"></option>
                    <?php endforeach; ?>
                </datalist>

                <button type="submit" class="btn btn-primary btn-sm ms-2">Filter</button>
                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="resetFilters">Reset</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchInput').focus();
        });

        document.getElementById('resetFilters').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.querySelector('input[name="category"]').value = '';
            document.getElementById('searchForm').submit();
        });
    </script>

    <!-- Product Count -->
    <?php
    $countQuery = "SELECT COUNT(*) as total FROM marketplace_products WHERE approved = 1";
    if ($search !== '') {
        $countQuery .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    }
    if ($categoryFilter !== '') {
        $countQuery .= " AND category_id IN (SELECT id FROM categories WHERE name LIKE '%$categoryFilter%')";
    }
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalProducts = $countRow['total'];
    ?>
    <div class="row justify-content-center mb-4">
        <div class="col-auto">
            <p class="fs-6 text-muted">Showing <strong><?php echo $totalProducts; ?></strong> product<?php echo ($totalProducts == 1) ? '' : 's'; ?></p>
        </div>
    </div>
    <hr style="border-top: #dee2e6; width: 100%; height:0.5px;">

    <!-- Products Grid -->
    <div class="row g-4">
        <?php
        $query = "SELECT mp.*, c.name as category_name FROM marketplace_products mp LEFT JOIN categories c ON mp.category_id = c.id WHERE mp.approved = 1";
        if ($search !== '') {
            $query .= " AND (mp.title LIKE '%$search%' OR mp.description LIKE '%$search%')";
        }
        if ($categoryFilter !== '') {
            $query .= " AND c.name LIKE '%$categoryFilter%'";
        }
        $query .= " ORDER BY mp.id DESC";

        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "<p class='text-center text-danger'>Error fetching products: " . mysqli_error($conn) . "</p>";
        } elseif (mysqli_num_rows($result) > 0) {
            while ($product = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm product-card">
                        <img src="uploads/marketplace_products/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>" style="height: 220px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <?php if (!empty($product['category_name'])): ?>
                                <span class="badge bg-info text-dark mb-2"><?php echo ucfirst(htmlspecialchars($product['category_name'])); ?></span>
                            <?php endif; ?>
                            <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="card-text flex-grow-1 text-truncate" title="<?php echo htmlspecialchars($product['description']); ?>"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text fw-bold">R<?php echo number_format($product['price'], 2); ?></p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="contact_seller.php?product_id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm w-50">Contact Seller</a>
                                <a href="marketplace_product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-dark">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center fs-5'>No products available in the marketplace right now.</p>";
        }
        ?>
    </div>
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

<?php include 'layouts/footer.php'; ?>
<style>
.product-card {
    border-radius: 16px;
    transition: transform 0.2s ease;
}
.product-card:hover {
    transform: translateY(-5px);
}
.product-card .card-title {
    font-size: 1rem;
    font-weight: 600;
}
.product-card .card-text.fw-bold {
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
