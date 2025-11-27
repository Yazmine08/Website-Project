<?php
session_start();
require_once 'db_connect.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

try {
    // START TRANSACTION (Ensures all database changes happen together or not at all)
    $pdo->beginTransaction();

    $user_id = $_SESSION['user_id'];
    
    // Calculate Totals
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $shipping = ($subtotal > 0) ? 5.00 : 0.00;
    $total = $subtotal + ($subtotal * 0.05) + $shipping;

    // Get user address (In a real app, you'd ask via a form, for now we take from DB)
    $stmtUser = $pdo->prepare("SELECT address FROM Users WHERE user_id = ?");
    $stmtUser->execute([$user_id]);
    $userAddress = $stmtUser->fetchColumn() ?: 'Default Address';

    // 3. INSERT INTO Orders Table
    $stmtOrder = $pdo->prepare("INSERT INTO Orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
    $stmtOrder->execute([$user_id, $total, $userAddress]);
    
    // Get the ID of the order we just created
    $order_id = $pdo->lastInsertId();

    // 4. INSERT ITEMS & UPDATE STOCK
    $stmtItem = $pdo->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
    $stmtStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");

    foreach ($_SESSION['cart'] as $item) {
        // Add to Order_Items
        $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        
        // Subtract Stock
        $stmtStock->execute([$item['quantity'], $item['product_id']]);
    }

    // COMMIT THE TRANSACTION
    $pdo->commit();

    // 5. Clear Cart and Redirect
    unset($_SESSION['cart']);
    header("Location: order_success.php?id=" . $order_id);
    exit();

} catch (Exception $e) {
    // If anything goes wrong, undo changes
    $pdo->rollBack();
    die("Order Failed: " . $e->getMessage());
}
?>