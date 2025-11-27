<?php
session_start();
require_once 'db_connect.php';

// Check Admin Role
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin')) {
    // header("Location: login.php");
}

// --- HANDLE STATUS UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE Orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$new_status, $order_id]);
}

// --- FETCH ORDERS ---
// Gets Order details + Customer Name (Joined with Users table)
$sql = "SELECT o.*, u.first_name, u.last_name 
        FROM Orders o 
        JOIN Users u ON o.user_id = u.user_id 
        ORDER BY o.order_date DESC";
$orders = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoCart | Manage Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { 'eco-green': '#344F1F', 'eco-light': '#8DA973', 'eco-bg': '#F2EAD3' } } }
        }
    </script>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-eco-green transition font-bold">← Back</a>
            <h1 class="text-xl font-bold text-eco-green">Order Management</h1>
        </div>
        <div class="text-sm text-gray-500">Total Orders: <?php echo count($orders); ?></div>
    </header>

    <main class="max-w-7xl mx-auto p-6 w-full">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-eco-green text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($orders as $order): ?>
                            <?php
                                // Fetch items for this specific order
                                $stmtItems = $pdo->prepare("
                                    SELECT oi.quantity, p.product_name 
                                    FROM Order_Items oi 
                                    JOIN products p ON oi.product_id = p.product_id 
                                    WHERE oi.order_id = ?
                                ");
                                $stmtItems->execute([$order['order_id']]);
                                $items = $stmtItems->fetchAll();
                            ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono text-sm">#<?php echo $order['order_id']; ?></td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                <div class="text-xs text-gray-400 truncate w-32" title="<?php echo htmlspecialchars($order['shipping_address']); ?>">
                                    <?php echo htmlspecialchars($order['shipping_address']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <ul class="list-disc list-inside">
                                    <?php foreach ($items as $item): ?>
                                        <li><?php echo $item['quantity'] . 'x ' . htmlspecialchars($item['product_name']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                            <td class="px-6 py-4 font-bold text-eco-green">
                                RM <?php echo $order['total_amount']; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php 
                                    $statusColor = 'bg-yellow-100 text-yellow-800';
                                    if($order['status'] == 'Completed') $statusColor = 'bg-green-100 text-green-800';
                                    if($order['status'] == 'Shipped') $statusColor = 'bg-blue-100 text-blue-800';
                                    if($order['status'] == 'Cancelled') $statusColor = 'bg-red-100 text-red-800';
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusColor; ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 focus:border-eco-green outline-none cursor-pointer">
                                        <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($orders) == 0): ?>
                <div class="p-10 text-center text-gray-500">No orders found.</div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>