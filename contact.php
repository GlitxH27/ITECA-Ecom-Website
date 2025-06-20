<?php include('layouts/header.php'); ?>
<?php

include('server/connection.php'); // Adjust path as needed

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if ($name && $email && $subject && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!-- Centered Contact Form -->
<section id="contact" class="container my-5 py-5">
<div class="container d-flex justify-content-center align-items-center">
    <div class="col-md-8 col-lg-6 shadow p-4 rounded bg-white">
      <h3 class="text-center mt-5">Contact Us</h3>
      <hr class=mx-auto>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="post" action="contact.php">
        <div class="mb-3">
          <label for="name" class="form-label">Your Name</label>
          <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Your Email</label>
          <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="mb-3">
          <label for="subject" class="form-label">Subject</label>
          <input type="text" name="subject" class="form-control" id="subject" required>
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" class="form-control" id="message" rows="5" required></textarea>
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-success px-4">Send Message</button>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include('layouts/footer.php'); ?>