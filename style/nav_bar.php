<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>X-SOLUTIONS</title>
</head>
<body>

<nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg fixed top-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex justify-between items-center h-16">
      <!-- Logo -->
      <a href="/united/dashboard.php" class="flex items-center space-x-2 text-white font-bold text-xl hover:text-blue-400 transition duration-200">
        <i class="fas fa-cube text-2xl"></i>
        <span>X-SOLUTIONS</span>
      </a>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center space-x-1">
        <a href="dashboard.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
        <a href="inventory.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-boxes mr-2"></i>Inventory
        </a>
        <a href="invoice.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-file-invoice mr-2"></i>Invoice
        </a>
        <a href="report.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
          <i class="fas fa-chart-bar mr-2"></i>Report
        </a>
        
        <!-- Account Dropdown (Click-based) -->
        <div class="relative" id="accountDropdown">
          <button id="accountDropdownBtn" class="text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-user-circle mr-2"></i>Account
            <i class="fas fa-chevron-down ml-2 text-xs"></i>
          </button>
          <div id="accountDropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl hidden z-50">
            <a href="Registration.php" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-t-lg transition duration-200">
              <i class="fas fa-user-plus mr-2"></i>Registration
            </a>
            <a href="logout.php" class="block px-4 py-3 text-red-600 hover:bg-red-50 rounded-b-lg transition duration-200">
              <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
          </div>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <button id="mobileMenuBtn" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="hidden md:hidden bg-gray-800 border-t border-gray-700">
    <div class="px-4 py-3 space-y-1">
      <a href="dashboard.php" class="block text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-home mr-2"></i>Dashboard
      </a>
      <a href="inventory.php" class="block text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-boxes mr-2"></i>Inventory
      </a>
      <a href="invoice.php" class="block text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-file-invoice mr-2"></i>Invoice
      </a>
      <a href="report.php" class="block text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-chart-bar mr-2"></i>Report
      </a>
      <div class="border-t border-gray-700 my-2"></div>
      <a href="Registration.php" class="block text-gray-300 hover:bg-gray-700 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-user-plus mr-2"></i>Registration
      </a>
      <a href="logout.php" class="block text-red-400 hover:bg-red-900 hover:text-white px-4 py-3 rounded-lg transition duration-200">
        <i class="fas fa-sign-out-alt mr-2"></i>Logout
      </a>
    </div>
  </div>
</nav>

<script>
  // Mobile menu toggle
  const mobileBtn = document.getElementById('mobileMenuBtn');
  if (mobileBtn) {
    mobileBtn.addEventListener('click', function() {
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileMenu) mobileMenu.classList.toggle('hidden');
    });
  }

  // Account dropdown toggle (click-based)
  const accountBtn = document.getElementById('accountDropdownBtn');
  const accountMenu = document.getElementById('accountDropdownMenu');
  const accountWrap = document.getElementById('accountDropdown');

  function closeAccountMenu() {
    if (accountMenu && !accountMenu.classList.contains('hidden')) {
      accountMenu.classList.add('hidden');
    }
  }

  if (accountBtn && accountMenu) {
    accountBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      accountMenu.classList.toggle('hidden');
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
      if (!accountWrap || accountWrap.contains(e.target)) return;
      closeAccountMenu();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeAccountMenu();
    });
  }
</script>

</body>
</html>