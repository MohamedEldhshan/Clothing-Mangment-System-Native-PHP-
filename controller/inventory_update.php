<?php
session_start();
include "../auth.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF validation
    $csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $_SESSION['error_message'] = 'Invalid form token. Please try again.';
        header("Location: ../inventory.php");
        exit();
    }

    $item_id = (int)$_POST["item_id"];
    $quantity = (int)$_POST["quantity"];

    // Validation
    if ($item_id <= 0) {
        $_SESSION['error_message'] = 'Invalid item ID.';
        header("Location: ../inventory.php");
        exit();
    }

    if ($quantity < 0) {
        $_SESSION['error_message'] = 'Quantity cannot be negative.';
        header("Location: ../inventory.php");
        exit();
    }

    try {
        // Update inventory quantity
        $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $quantity, $item_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Item updated successfully!";
        } else {
            throw new Exception("Error updating item: " . $conn->error);
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
