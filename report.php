<?php
session_start();
include "auth.php";

// Fetch inventory data
$sql = "SELECT products.id, products.name, categories.name as category_name, products.price, sizes.size_name, inventory.quantity
 FROM inventory 
 JOIN products ON inventory.product_id = products.id 
 JOIN sizes ON inventory.size_id= sizes.id
 JOIN categories ON products.category_id = categories.id
 ORDER BY products.name";

$result = mysqli_query($conn, $sql);

// Calculate totals
$total_items = 0;
$total_value = 0;
$data_rows = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data_rows[] = $row;
    $total_items += $row['quantity'];
    $total_value += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Report</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- SheetJS for Excel Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <!-- jsPDF for PDF Export -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <style>
    body { padding-top: 76px; }
    @media print {
      .no-print { display: none; }
      body { padding-top: 0; }
    }
  </style>
</head>
<body class="bg-gray-50">

<?php include("style/nav_bar.php"); ?>

<div class="max-w-7xl mx-auto px-4 py-8">
  <!-- Header Section -->
  <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
          <i class="fas fa-chart-bar mr-2 text-indigo-600"></i>Inventory Report
        </h1>
        <p class="text-gray-600">Generated on <?= date('F d, Y - h:i A') ?></p>
      </div>
      
      <!-- Export Buttons -->
      <div class="flex gap-3 mt-4 md:mt-0 no-print">
        <button onclick="exportToExcel()" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-file-excel mr-2"></i>Export Excel
        </button>
        <button onclick="exportToPDF()" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-file-pdf mr-2"></i>Export PDF
        </button>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-print mr-2"></i>Print
        </button>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-blue-100 text-sm font-semibold uppercase">Total Products</p>
          <h3 class="text-3xl font-bold mt-2"><?= count($data_rows) ?></h3>
        </div>
        <div class="bg-white bg-opacity-20 rounded-full p-4">
          <i class="fas fa-boxes text-3xl"></i>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-green-100 text-sm font-semibold uppercase">Total Items</p>
          <h3 class="text-3xl font-bold mt-2"><?= number_format($total_items) ?></h3>
        </div>
        <div class="bg-white bg-opacity-20 rounded-full p-4">
          <i class="fas fa-cubes text-3xl"></i>
        </div>
      </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-purple-100 text-sm font-semibold uppercase">Total Value</p>
          <h3 class="text-3xl font-bold mt-2">$<?= number_format($total_value, 2) ?></h3>
        </div>
        <div class="bg-white bg-opacity-20 rounded-full p-4">
          <i class="fas fa-dollar-sign text-3xl"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Inventory Table -->
  <div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
      <h2 class="text-xl font-bold text-white"><i class="fas fa-table mr-2"></i>Detailed Inventory</h2>
    </div>
    <div class="overflow-x-auto">
      <table id="inventoryTable" class="w-full">
        <thead class="bg-gray-100 border-b-2 border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Serial</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Product Name</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Price</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Size</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Value</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php foreach ($data_rows as $row): 
            $row_total = $row['price'] * $row['quantity'];
        ?>    
          <tr class="hover:bg-gray-50 transition duration-150">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= (int)$row['id'] ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['category_name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">$<?= number_format($row['price'], 2) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($row['size_name']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold"><?= (int)$row['quantity'] ?></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">$<?= number_format($row_total, 2) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
          <tr>
            <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase">Total:</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
              <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full"><?= number_format($total_items) ?></span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$<?= number_format($total_value, 2) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<script>
// Export to Excel
function exportToExcel() {
  const table = document.getElementById('inventoryTable');
  const wb = XLSX.utils.table_to_book(table, {sheet: "Inventory Report"});
  const fileName = 'Inventory_Report_' + new Date().toISOString().split('T')[0] + '.xlsx';
  XLSX.writeFile(wb, fileName);
}

// Export to PDF
function exportToPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('l', 'mm', 'a4');
  
  // Add title
  doc.setFontSize(18);
  doc.setTextColor(40);
  doc.text('Inventory Report', 14, 20);
  
  // Add date
  doc.setFontSize(10);
  doc.setTextColor(100);
  doc.text('Generated on: ' + new Date().toLocaleString(), 14, 28);
  
  // Add statistics
  doc.setFontSize(11);
  doc.setTextColor(40);
  doc.text('Total Products: <?= count($data_rows) ?>', 14, 38);
  doc.text('Total Items: <?= number_format($total_items) ?>', 80, 38);
  doc.text('Total Value: $<?= number_format($total_value, 2) ?>', 150, 38);
  
  // Prepare table data
  const tableData = [];
  <?php foreach ($data_rows as $row): 
      $row_total = $row['price'] * $row['quantity'];
  ?>
  tableData.push([
    '<?= (int)$row['id'] ?>',
    '<?= addslashes(htmlspecialchars($row['name'])) ?>',
    '<?= addslashes(htmlspecialchars($row['category_name'])) ?>',
    '$<?= number_format($row['price'], 2) ?>',
    '<?= htmlspecialchars($row['size_name']) ?>',
    '<?= (int)$row['quantity'] ?>',
    '$<?= number_format($row_total, 2) ?>'
  ]);
  <?php endforeach; ?>
  
  // Add table
  doc.autoTable({
    startY: 45,
    head: [['Serial', 'Product Name', 'Category', 'Price', 'Size', 'Quantity', 'Total Value']],
    body: tableData,
    foot: [['', '', '', '', 'Total:', '<?= number_format($total_items) ?>', '$<?= number_format($total_value, 2) ?>']],
    theme: 'grid',
    headStyles: { fillColor: [79, 70, 229], textColor: 255, fontStyle: 'bold' },
    footStyles: { fillColor: [243, 244, 246], textColor: 40, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [249, 250, 251] },
    margin: { top: 45 }
  });
  
  // Save PDF
  const fileName = 'Inventory_Report_' + new Date().toISOString().split('T')[0] + '.pdf';
  doc.save(fileName);
}
</script>

</body>
</html>