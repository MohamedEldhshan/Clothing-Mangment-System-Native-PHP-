<?php
session_start();
include "../auth.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // CSRF validation
    $csrf_token = isset($_GET['csrf_token']) ? $_GET['csrf_token'] : '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $_SESSION['error_message'] = 'Invalid security token. Please try again.';
        header("Location: ../inventory.php");
        exit();
    }

    $item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Validation
    if ($item_id <= 0) {
        $_SESSION['error_message'] = 'Invalid item ID.';
        header("Location: ../inventory.php");
        exit();
    }

    try {
        // First, delete from inventory table
        $stmt = $conn->prepare("DELETE FROM inventory WHERE product_id = ?");
        $stmt->bind_param("i", $item_id);
        
        if ($stmt->execute()) {
            // Then delete from products table
            $stmt2 = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt2->bind_param("i", $item_id);
            $stmt2->execute();
            $stmt2->close();
            
            $_SESSION['success_message'] = "Item deleted successfully!";
        } else {
            throw new Exception("Error deleting item: " . $conn->error);
        }
        
        $stmt->close();
        header("Location: ../inventory.php");
        exit();

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: ../inventory.php");
        exit();
    }
} else {
    header("Location: ../inventory.php");
    exit();
}
?>
