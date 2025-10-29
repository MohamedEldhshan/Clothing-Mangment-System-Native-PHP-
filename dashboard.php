<?php
session_start();
include "auth.php";
include("style/nav_bar.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>
        body { padding-top: 76px; }
    </style>
</head>
<body class="bg-gray-50">

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg p-8 mb-8 text-white">
        <h1 class="text-4xl font-bold mb-2">
            <i class="fas fa-tachometer-alt mr-3"></i>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
        </h1>
        <p class="text-blue-100 text-lg">Manage your inventory system efficiently</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Inventory</p>
                    <h3 class="text-2xl font-bold text-gray-800">Manage Stock</h3>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <i class="fas fa-boxes text-3xl text-blue-600"></i>
                </div>
            </div>
            <a href="inventory.php" class="mt-4 inline-block text-blue-600 hover:text-blue-800 font-semibold">
                View Details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Reports</p>
                    <h3 class="text-2xl font-bold text-gray-800">View Reports</h3>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <i class="fas fa-chart-bar text-3xl text-purple-600"></i>
                </div>
            </div>
            <a href="report.php" class="mt-4 inline-block text-purple-600 hover:text-purple-800 font-semibold">
                View Details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Invoices</p>
                    <h3 class="text-2xl font-bold text-gray-800">Create Invoice</h3>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <i class="fas fa-file-invoice text-3xl text-green-600"></i>
                </div>
            </div>
            <a href="invoice.php" class="mt-4 inline-block text-green-600 hover:text-green-800 font-semibold">
                View Details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase mb-1">Account</p>
                    <h3 class="text-2xl font-bold text-gray-800">Settings</h3>
                </div>
                <div class="bg-orange-100 rounded-full p-4">
                    <i class="fas fa-user-cog text-3xl text-orange-600"></i>
                </div>
            </div>
            <a href="Registration.php" class="mt-4 inline-block text-orange-600 hover:text-orange-800 font-semibold">
                View Details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="inventory.php" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-200">
                <i class="fas fa-plus-circle text-2xl text-blue-600 mr-3"></i>
                <span class="font-semibold text-gray-800">Add New Item</span>
            </a>
            <a href="report.php" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition duration-200">
                <i class="fas fa-download text-2xl text-purple-600 mr-3"></i>
                <span class="font-semibold text-gray-800">Export Report</span>
            </a>
            <a href="invoice.php" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition duration-200">
                <i class="fas fa-receipt text-2xl text-green-600 mr-3"></i>
                <span class="font-semibold text-gray-800">New Invoice</span>
            </a>
        </div>
    </div>
</div>

</body>
</html>