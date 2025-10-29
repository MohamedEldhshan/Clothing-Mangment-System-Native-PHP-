<?php
session_start();
include "auth.php";
// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// fetch data only for authenticated users (assumes auth.php guards access)
//include("controller/inventory_save.php");

//fetch inventory data
$sql = "SELECT products.id, products.name, categories.name as category_name, products.price, sizes.size_name, inventory.quantity
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
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { padding-top: 76px; }
  </style>
</head>
<body class="bg-gray-50">

<?php include("style/nav_bar.php"); ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
            <span><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between">
            <span><i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($_SESSION['error_message']) ?></span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white text-center"><i class="fas fa-boxes mr-2"></i>Inventory Stock</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Serial</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Size</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>    
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int)$row['id'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['category_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">$<?= htmlspecialchars($row['price']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['size_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold"><?= (int)$row['quantity'] ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <button onclick="editItem(<?= (int)$row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', <?= (int)$row['quantity'] ?>)" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg mr-2 transition duration-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteItem(<?= (int)$row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition duration-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>     
                </table>
            </div>
        </div>
    </div>


<div class="max-w-7xl mx-auto px-4 py-8">
  <div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
      <h3 class="text-xl font-bold text-white"><i class="fas fa-plus-circle mr-2"></i>Add to Inventory</h3>
    </div>
    <div class="p-6">
      <form method="POST" action="controller/inventory_save.php" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
          <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" required>
        </div>


        <!-- Category -->
        <div>
          <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
          <select id="category_id" name="category_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" required>
            <option value="">-- Select Category --</option>
            <?php while ($cat = mysqli_fetch_assoc($cat_res)): ?>
              <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Subcategory (hidden by default) -->
        <div id="subcategory-container" style="display:none;">
          <label for="subcategory" class="block text-sm font-semibold text-gray-700 mb-2">Subcategory</label>
          <select id="subcategory" name="subcategory" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"></select>
        </div>

        <!-- Size -->
        <div>
          <label for="size_id" class="block text-sm font-semibold text-gray-700 mb-2">Size</label>
          <select id="size_id" name="size_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" required>
            <?php while ($size = mysqli_fetch_assoc($size_res)): ?>
              <option value="<?= (int)$size['id'] ?>"><?= htmlspecialchars($size['size_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Fit -->
        <div>
          <label for="fit_id" class="block text-sm font-semibold text-gray-700 mb-2">Fitting</label>
          <select id="fit_id" name="fit_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
            <?php while ($fit = mysqli_fetch_assoc($fit_res)): ?>
              <option value="<?= (int)$fit['id'] ?>"><?= htmlspecialchars($fit['fit_type']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Price -->
        <div>
          <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">$</span>
            <input type="number" step="0.01" min="0" id="price" name="price" class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" required>
          </div>
        </div>

        <!-- Quantity -->
        <div>
          <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
          <input type="number" min="0" id="quantity" name="quantity" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" required>
        </div>

        <!-- Submit -->
        <div class="md:col-span-2">
          <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200">
            <i class="fas fa-save mr-2"></i>Save to Inventory
          </button>
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

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
    <h3 class="text-2xl font-bold text-gray-800 mb-6"><i class="fas fa-edit mr-2 text-yellow-500"></i>Edit Item</h3>
    <form id="editForm" method="POST" action="controller/inventory_update.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
      <input type="hidden" name="item_id" id="edit_item_id">
      
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Item Name</label>
        <input type="text" id="edit_name" readonly class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg">
      </div>
      
      <div class="mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
        <input type="number" name="quantity" id="edit_quantity" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent" required>
      </div>
      
      <div class="flex gap-3">
        <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-lg transition duration-200">
          <i class="fas fa-check mr-2"></i>Update
        </button>
        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 rounded-lg transition duration-200">
          <i class="fas fa-times mr-2"></i>Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function editItem(id, name, quantity) {
  document.getElementById('edit_item_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_quantity').value = quantity;
  document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
}

function deleteItem(id, name) {
  if (confirm('Are you sure you want to delete "' + name + '"?')) {
    window.location.href = 'controller/inventory_delete.php?id=' + id + '&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>';
  }
}
</script>
</body>
</html>
