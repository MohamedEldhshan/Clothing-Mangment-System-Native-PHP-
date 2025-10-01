<?php
session_start();
include "auth.php";
include("style/nav_bar.php");
//include("controller/inventory_save.php");

//fetch inventory data
$sql = "SELECT products.id,products.name, categories.name, products.price , sizes.size_name , inventory.quantity
 FROM inventory 
 JOIN products ON inventory.product_id = products.id 
 JOIN sizes ON inventory.size_id= sizes.id
 JOIN categories ON products.category_id = categories.id";

$result = mysqli_query($conn, $sql);

// fetch categories
$cat_sql = "SELECT id, name  FROM categories ORDER BY name";
$cat_res = mysqli_query($conn, $cat_sql);

// fetch sizes
$size_sql = "SELECT id, size_name FROM sizes ORDER BY id";
$size_res = mysqli_query($conn, $size_sql);

// fetch fittings
$fit_sql = "SELECT id, fit_type FROM fittings ORDER BY fit_type";
$fit_res = mysqli_query($conn, $fit_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { padding-top: 76px; }
    .card { border-radius: 12px; }
    .card-header { font-weight: bold; }
  </style>
</head>
<body>

<div class ="container inventory-table" style="margin-top: 30px;">
        <!-- creating table -->
        <h2 class="mb-3 text-center p-3">Inventory Stock</h2>
        <table class="table table-striped table-hover table-bordered text-center">
            <thead class = "table-light">
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
            <?php while ($row = mysqli_fetch_assoc($result)): ?>    
                <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['size_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
            </tr>
         <?php endwhile; ?>
         </tbody>     
        </table>


<div class="container my-4">
  <div class="card shadow-sm">
    <div class="card-header bg-dark text-white">Add to Inventory</div>
    <div class="card-body">
      <form method="POST" class="row g-3">

            <!-- Name -->
        <div class="col-md-6">
          <label for="name" class="form-label">Name</label>
          <input type="text" id="name" name="name" class="form-control" required>
        </div>


        <!-- Category -->
        <div class="col-md-6">
          <label for="category_id" class="form-label">Category</label>
          <select id="category_id" name="category_id" class="form-select" required>
            <option value="">-- Select Category --</option>
            <?php while ($cat = mysqli_fetch_assoc($cat_res)): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Subcategory (hidden by default) -->
        <div class="col-md-6" id="subcategory-container" style="display:none;">
          <label for="subcategory" class="form-label">Subcategory</label>
          <select id="subcategory" name="subcategory" class="form-select"></select>
        </div>

        <!-- Size -->
        <div class="col-md-6">
          <label for="size_id" class="form-label">Size</label>
          <select id="size_id" name="size_id" class="form-select" required>
            <?php while ($size = mysqli_fetch_assoc($size_res)): ?>
              <option value="<?= (int)$size['id'] ?>"><?= htmlspecialchars($size['size_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Fit -->
        <div class="col-md-6">
          <label for="fit_id" class="form-label">Fitting</label>
          <select id="fit_id" name="fit_id" class="form-select">
            <?php while ($fit = mysqli_fetch_assoc($fit_res)): ?>
              <option value="<?= (int)$fit['id'] ?>"><?= htmlspecialchars($fit['fit_type']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Price -->
        <div class="col-md-6">
          <label for="price" class="form-label">Price</label>
          <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" min="0" id="price" name="price" class="form-control" required>
          </div>
        </div>

        <!-- Quantity -->
        <div class="col-md-6">
          <label for="quantity" class="form-label">Quantity</label>
          <input type="number" min="0" id="quantity" name="quantity" class="form-control" required>
        </div>

        <!-- Submit -->
        <div class="col-12">
          <button type="submit" class="btn btn-dark">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// DOM Elements
const categorySelect   = document.getElementById("category_id");
const subCategoryDiv   = document.getElementById("subcategory-container");
const subCategorySelect = document.getElementById("subcategory");

// Subcategories mapping
const subcategories = {
  "1": ["T-shirts", "Shirts", "Hoodies"],  // Topwear
  "2": ["Jeans", "Shorts", "Trousers"],    // Legwear
  "3": ["Boxers", "Socks"],                // Underwear
  "4": ["Sneakers", "Sandals", "Boots"],   // Shoes
  "5": ["Hats", "Belts", "Bags"]           // Accessories
};

// Event listener
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
</body>
</html>
