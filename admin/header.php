<?php session_start(); ?>

<?php include('../server/connection.php');  ?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seller Dashboard</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Optional Custom CSS -->
  <link href="dashboard.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
    }
    .sidebar a {
      color: #ffffff;
      text-decoration: none;
      display: block;
      padding: 15px;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .dashboard-card {
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table thead {
      background-color: #343a40;
      color: white;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">AfriMart</a>

  <button class="navbar-toggler position-absolute d-md-none collapsed" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#sidebarMenu" 
          aria-controls="sidebarMenu" 
          aria-expanded="false" 
          aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-nav">
    <div class="nav-item text-nowrap">
      <?php if (isset($_SESSION['admin_logged_in'])): ?>
        <a class="nav-link px-3" href="logout.php?logout=1">Sign out</a>
      <?php endif; ?>
    </div>
  </div>
</header>