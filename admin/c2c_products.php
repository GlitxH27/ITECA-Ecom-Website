<?php include('header.php'); ?>
<?php 
include('../server/connection.php');

if(!isset($_SESSION['admin_logged_in'])){
    header('location: login.php');
    exit();
}

// 1. determine page number
if(isset($_GET['page_no']) && $_GET['page_no'] != ""){
    $page_no = intval($_GET['page_no']);
}else{
    $page_no = 1;
}

// 2. get total records count
$stmt1 = $conn->prepare("SELECT COUNT(*) FROM marketplace_products");
$stmt1->execute();
$stmt1->bind_result($total_records);
$stmt1->store_result();
$stmt1->fetch();

// 3. products per page & calculate pagination vars
$total_records_per_page = 8;
$offset = ($page_no - 1) * $total_records_per_page;
$total_no_of_pages = ceil($total_records / $total_records_per_page);
$previous_page = $page_no - 1;
$next_page = $page_no + 1;

// 4. get products with seller name
$sql = "SELECT mp.id, mp.title, mp.price, mp.image, mp.approved, u.user_name
        FROM marketplace_products mp
        JOIN users u ON mp.seller_id = u.user_id
        ORDER BY mp.id DESC
        LIMIT ?, ?";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("ii", $offset, $total_records_per_page);
$stmt2->execute();
$products = $stmt2->get_result();

// Count pending products
$stmt3 = $conn->prepare("SELECT COUNT(*) FROM marketplace_products WHERE approved=0");
$stmt3->execute();
$stmt3->bind_result($pending_count);
$stmt3->fetch();
$stmt3->close();

// Count denied products (approved=0 and denied_reason not null/empty)
$stmt4 = $conn->prepare("SELECT COUNT(*) FROM marketplace_products WHERE approved=0 AND denied_reason IS NOT NULL AND denied_reason != ''");
$stmt4->execute();
$stmt4->bind_result($denied_count);
$stmt4->fetch();
$stmt4->close();

?>

<div class="container-fluid">
  <div class="row">

    <?php include('sidemenu.php'); ?>

    <main class="col-md-10 ms-sm-auto px-4 py-4">

      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
          <?php echo $_SESSION['message']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
      <?php endif; ?>

      <h2>Marketplace Products</h2>

      <!-- Dashboard Cards -->
      <div class="row my-4">
        <div class="col-md-4">
          <div class="bg-white p-4 dashboard-card">
            <h5>Total Products</h5>
            <p class="fs-4"><?= $total_records ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="bg-white p-4 dashboard-card">
            <h5>Pending Approval</h5>
            <p class="fs-4"><?= $pending_count ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="bg-white p-4 dashboard-card">
            <h5>Approved Products</h5>
            <p class="fs-4"><?= $total_records - $pending_count ?></p>
          </div>
        </div>

        <!-- Denied Products card -->
        <div class="col-md-4 mt-4">
          <div class="bg-white p-4 dashboard-card">
            <h5>Denied Products</h5>
            <p class="fs-4"><?= $denied_count ?></p>
          </div>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white p-4 dashboard-card">
        <table class="table table-hover mt-3 align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Title</th>
              <th>Price (ZAR)</th>
              <th>Approved</th>
              <th>Seller</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($product = $products->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><img src="../uploads/marketplace_products/<?= htmlspecialchars($product['image']) ?>" alt="Image" style="width:70px; height:70px; object-fit:cover;"></td>
                <td><?= htmlspecialchars($product['title']) ?></td>
                <td>R<?= number_format($product['price'], 2) ?></td>
                <td><?= $product['approved'] ? 'Yes' : 'Pending' ?></td>
                <td><?= htmlspecialchars($product['user_name']) ?></td>
                <td>
                  <?php if (!$product['approved']): ?>
                    <a href="c2c_approve_product.php?id=<?= $product['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                    <!-- Deny button triggers modal -->
                    <button 
                      class="btn btn-warning btn-sm deny-btn" 
                      data-bs-toggle="modal" 
                      data-bs-target="#denyModal" 
                      data-product-id="<?= $product['id'] ?>"
                      data-product-title="<?= htmlspecialchars($product['title'], ENT_QUOTES) ?>"
                    >Deny</button>
                  <?php endif; ?>
                  <a href="c2c_delete_product.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?');">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation example">
          <ul class="pagination mt-4">

            <li class="page-item <?= ($page_no <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= ($page_no <= 1) ? '#' : '?page_no=' . ($page_no - 1) ?>">Previous</a>
            </li>

            <?php for($i = 1; $i <= $total_no_of_pages; $i++): ?>
              <li class="page-item <?= ($page_no == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page_no=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <li class="page-item <?= ($page_no >= $total_no_of_pages) ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= ($page_no >= $total_no_of_pages) ? '#' : '?page_no=' . ($page_no + 1) ?>">Next</a>
            </li>

          </ul>
        </nav>
      </div>

      <!-- Deny Product Modal -->
      <div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <form action="c2c_deny_product.php" method="POST" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="denyModalLabel">Deny Product</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id" id="denyProductId" value="">
              <p>Provide a reason for denying the product: <strong id="denyProductTitle"></strong></p>
              <div class="mb-3">
                <textarea name="denied_reason" class="form-control" rows="4" placeholder="Enter denial reason" required></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-warning">Deny Product</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>

    </main>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // When the modal opens, set product ID and title in the modal form
  var denyModal = document.getElementById('denyModal');
  denyModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var productId = button.getAttribute('data-product-id');
    var productTitle = button.getAttribute('data-product-title');

    var modalProductIdInput = denyModal.querySelector('#denyProductId');
    var modalProductTitle = denyModal.querySelector('#denyProductTitle');

    modalProductIdInput.value = productId;
    modalProductTitle.textContent = productTitle;
  });
</script>