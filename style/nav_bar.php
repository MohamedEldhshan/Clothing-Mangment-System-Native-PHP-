<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title> X-SOLUTIONS </title>
</head>
<body >

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="/united/dashboard.php">X-SOLUTIONS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="inventory.php">Inventory</a></li>
        <li class="nav-item"><a class="nav-link active" href="invoice.php">Invoice</a></li>
        <li class="nav-item"><a class="nav-link active" href="report.php">Report</a></li>
        <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
             data-bs-toggle="dropdown" aria-expanded="true"> Account </a>
          <div class = dropdownmenu>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="Registration.php">Registration</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
          </div>
        </li>
      </ul>
    </div>
   </ul>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>