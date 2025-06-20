<?php
session_start();
include('header.php');
include('../server/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_messages.php?deleted=1");
    exit();
}

$result = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
?>

<!-- Page Layout -->
<div class="container-fluid">
  <div class="row">
    
    <!-- Sidebar -->
    <?php include('sidemenu.php'); ?>

    <!-- Main Panel -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-4">
      <h2 class="mb-4">Contact Form Messages</h2>

      <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Message deleted successfully.</div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Message</th>
              <th>Date Submitted</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['subject']) ?></td>
                  <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                  <td><?= date('Y-m-d H:i', strtotime($row['submitted_at'])) ?></td>
                  <td>
                    <a href="admin_messages.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted">No messages found.</td>
              </tr>
            <?php endif; ?>
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>