<?php
session_start();
include "../auth.php";
include("../style/nav_bar.php");

// Form preprocessing
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF validation
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $_SESSION['error_message'] = 'Invalid form token. Please try again.';
        header("Location: ../inventory.php");
        exit();
    }

    $item_name = trim($_POST["name"]);
    $category_id = (int)$_POST["category_id"];
    $subcategory = !empty($_POST['subcategory']) ? $_POST['subcategory'] : null;
    $fit_id = (int)$_POST["fit_id"];
    $price = (float)$_POST["price"];
    $size_id = (int)$_POST['size_id'];
    $quantity = (int)$_POST["quantity"];

    // Basic validation
    $errors = [];
    if ($item_name === '') $errors[] = 'Name required';
    if ($category_id <= 0) $errors[] = 'Category required';
    if ($size_id <= 0) $errors[] = 'Size required';
    if ($quantity < 0) $errors[] = 'Quantity invalid';
    if ($price < 0) $errors[] = 'Price invalid';

    // If no validation errors, proceed with database operations
    if (empty($errors)) {
        try {
            // First, check if product already exists
            $product_check = $conn->prepare("SELECT id FROM products WHERE name = ? AND category_id = ?");
            $product_check->bind_param("si", $item_name, $category_id);
            $product_check->execute();
            $product_result = $product_check->get_result();
            
            if ($product_result->num_rows > 0) {
                // Product exists, get its ID
                $product_row = $product_result->fetch_assoc();
                $product_id = $product_row['id'];
            } else {
                // Product doesn't exist, create new product
                $product_insert = $conn->prepare("INSERT INTO products (name, category_id, price) VALUES (?, ?, ?)");
                $product_insert->bind_param("sid", $item_name, $category_id, $price);
                
                if ($product_insert->execute()) {
                    $product_id = $conn->insert_id;
                } else {
                    throw new Exception("Error creating product: " . $conn->error);
                }
            }

            // Check if this product with this size already exists in inventory
            $inventory_check = $conn->prepare("SELECT id, quantity FROM inventory WHERE product_id = ? AND size_id = ?");
            $inventory_check->bind_param("ii", $product_id, $size_id);
            $inventory_check->execute();
            $inventory_result = $inventory_check->get_result();

            if ($inventory_result->num_rows > 0) {
                // Item exists in inventory, update quantity
                $inventory_row = $inventory_result->fetch_assoc();
                $new_quantity = $inventory_row['quantity'] + $quantity;
                
                $update_inventory = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
                $update_inventory->bind_param("ii", $new_quantity, $inventory_row['id']);
                
                if ($update_inventory->execute()) {
                    $_SESSION['success_message'] = "Inventory updated successfully! Quantity increased by $quantity.";
                } else {
                    throw new Exception("Error updating inventory: " . $conn->error);
                }
            } else {
                // New inventory item, insert new record
                $insert_inventory = $conn->prepare("INSERT INTO inventory (product_id, size_id, quantity, fit_id) VALUES (?, ?, ?, ?)");
                $insert_inventory->bind_param("iiii", $product_id, $size_id, $quantity, $fit_id);
                
                if ($insert_inventory->execute()) {
                    $_SESSION['success_message'] = "New item added to inventory successfully!";
                } else {
                    throw new Exception("Error adding to inventory: " . $conn->error);
                }
            }

            // Redirect back to inventory page
            header("Location: ../inventory.php");
            exit();

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: ../inventory.php");
            exit();
        }
    } else {
        // Validation errors exist
        $_SESSION['error_message'] = implode(", ", $errors);
        header("Location: ../inventory.php");
        exit();
    }
}
?>
