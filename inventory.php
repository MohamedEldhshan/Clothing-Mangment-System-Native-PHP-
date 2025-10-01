<?php
session_start();

// Database connection (consider extracting to a shared include if you already have one)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "todo_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = '';
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $subcategory = isset($_POST['subcategory']) && $_POST['subcategory'] !== '' ? trim($_POST['subcategory']) : null;
    $fit_id = isset($_POST['fit_id']) ? (int)$_POST['fit_id'] : null;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $size_id = isset($_POST['size_id']) ? (int)$_POST['size_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Validation
    if ($name === '') { $errors[] = 'Name required'; }
    if ($category_id <= 0) { $errors[] = 'Category required'; }
    if ($size_id <= 0) { $errors[] = 'Size required'; }
    if ($quantity < 0) { $errors[] = 'Quantity invalid'; }
    if ($price < 0) { $errors[] = 'Price invalid'; }

    if (empty($errors)) {
        // Ensure product exists or create it
        $product_id = null;
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND category_id = ? AND (? IS NULL OR fit_id = ?) LIMIT 1");
        $fit_id_param = $fit_id !== null ? $fit_id : null;
        $stmt->bind_param('siii', $name, $category_id, $fit_id_param, $fit_id_param);
        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $product_id = (int)$row['id'];
            }
        }
        $stmt->close();

        if ($product_id === null) {
            $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, fit_id, subcategory) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sidis', $name, $category_id, $price, $fit_id_param, $subcategory);
            if (!$stmt->execute()) {
                $errors[] = 'Failed to create product: ' . $stmt->error;
            } else {
                $product_id = (int)$stmt->insert_id;
            }
            $stmt->close();
        } else {
            // Optionally keep product price updated
            $stmt = $conn->prepare("UPDATE products SET price = ? WHERE id = ?");
            $stmt->bind_param('di', $price, $product_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($product_id !== null && empty($errors)) {
            // Upsert inventory quantity
            $stmt = $conn->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND size_id = ? LIMIT 1");
            $stmt->bind_param('ii', $product_id, $size_id);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $inv_id = (int)$row['id'];
                    $new_qty = (int)$row['quantity'] + $quantity;
                    $stmt->close();
                    $stmt2 = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
                    $stmt2->bind_param('ii', $new_qty, $inv_id);
                    if (!$stmt2->execute()) {
                        $errors[] = 'Failed to update inventory: ' . $stmt2->error;
                    }
                    $stmt2->close();
                } else {
                    $stmt->close();
                    $stmt2 = $conn->prepare("INSERT INTO inventory (product_id, size_id, quantity) VALUES (?, ?, ?)");
                    $stmt2->bind_param('iii', $product_id, $size_id, $quantity);
                    if (!$stmt2->execute()) {
                        $errors[] = 'Failed to add to inventory: ' . $stmt2->error;
                    }
                    $stmt2->close();
                }
            } else {
                $errors[] = 'Failed to query inventory: ' . $stmt->error;
                $stmt->close();
            }

            if (empty($errors)) {
                $success_message = 'Saved successfully';
            }
        }
    }
}

// Fetch inventory data for display
$sql = "SELECT 
            products.id AS id,
            products.name AS name,
            categories.name AS category,
            products.price AS price,
            sizes.size_name AS size_name,
            inventory.quantity AS quantity
        FROM inventory
        JOIN products ON inventory.product_id = products.id
        JOIN sizes ON inventory.size_id = sizes.id
        JOIN categories ON products.category_id = categories.id
        ORDER BY products.id DESC";
$result = $conn->query($sql);

// Fetch categories
$cat_res = $conn->query("SELECT id, name FROM categories ORDER BY name");

// Fetch sizes
$size_res = $conn->query("SELECT id, size_name FROM sizes ORDER BY id");

// Fetch fittings
$fit_res = $conn->query("SELECT id, fit_type FROM fittings ORDER BY fit_type");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { padding-top: 24px; }
        .card { border-radius: 12px; }
        .card-header { font-weight: bold; }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="/United/style.css">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">X-SOLUTIONS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="inventory.php">Inventory</a></li>
            <li class="nav-item"><a class="nav-link" href="invoice.php">Invoice</a></li>
            <li class="nav-item"><a class="nav-link" href="report.php">Report</a></li>
          </ul>
        </div>
      </div>
    </nav>
</head>
<body>

<div class="container inventory-table" style="margin-top: 30px;">
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($errors as $e) { echo htmlspecialchars($e) . '<br>'; } ?>
        </div>
    <?php elseif ($success_message !== ''): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-3 text-center p-3">Inventory Stock</h2>
    <table class="table table-striped table-hover table-bordered text-center">
        <thead class="table-light">
        <tr>
            <th>Serial</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Size</th>
            <th>Quantity</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result) : ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['size_name']) ?></td>
                    <td><?= (int)$row['quantity'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="container my-4">
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Add to Inventory</div>
    <div class="card-body">
      <form method="POST" class="row g-3">
        <div class="col-md-6">
          <label for="name" class="form-label">Name</label>
          <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label for="category_id" class="form-label">Category</label>
          <select id="category_id" name="category_id" class="form-select" required>
            <option value="">-- Select Category --</option>
            <?php if ($cat_res) { while ($cat = $cat_res->fetch_assoc()): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; } ?>
          </select>
        </div>

        <div class="col-md-6" id="subcategory-container" style="display:none;">
          <label for="subcategory" class="form-label">Subcategory</label>
          <select id="subcategory" name="subcategory" class="form-select"></select>
        </div>

        <div class="col-md-6">
          <label for="size_id" class="form-label">Size</label>
          <select id="size_id" name="size_id" class="form-select" required>
            <?php if ($size_res) { while ($size = $size_res->fetch_assoc()): ?>
              <option value="<?= (int)$size['id'] ?>"><?= htmlspecialchars($size['size_name']) ?></option>
            <?php endwhile; } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label for="fit_id" class="form-label">Fitting</label>
          <select id="fit_id" name="fit_id" class="form-select">
            <?php if ($fit_res) { while ($fit = $fit_res->fetch_assoc()): ?>
              <option value="<?= (int)$fit['id'] ?>"><?= htmlspecialchars($fit['fit_type']) ?></option>
            <?php endwhile; } ?>
          </select>
        </div>

        <div class="col-md-6">
          <label for="price" class="form-label">Price</label>
          <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
          </div>
        </div>

        <div class="col-md-6">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" min="0" id="quantity" name="quantity" class="form-control" required>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-dark">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const categorySelect   = document.getElementById("category_id");
const subCategoryDiv   = document.getElementById("subcategory-container");
const subCategorySelect = document.getElementById("subcategory");

const subcategories = {
  "1": ["T-shirts", "Shirts", "Hoodies"],
  "2": ["Jeans", "Shorts", "Trousers"],
  "3": ["Boxers", "Socks"],
  "4": ["Sneakers", "Sandals", "Boots"],
  "5": ["Hats", "Belts", "Bags"]
};

categorySelect.addEventListener("change", function() {
  const selectedCat = categorySelect.value;
  if (subcategories[selectedCat]) {
    subCategoryDiv.style.display = "block";
    subCategorySelect.innerHTML = subcategories[selectedCat]
      .map(item => `<option value="${item.toLowerCase()}">${item}</option>`)
      .join("");
  } else {
    subCategoryDiv.style.display = "none";
    subCategorySelect.innerHTML = "";
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

