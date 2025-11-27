<?php
session_start();
require_once 'db_connect.php';

// Check Admin Role
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin')) {
    // header("Location: login.php"); 
}

// --- FETCH LIVE METRICS FROM DATABASE ---
try {
    // 1. Total Stock Count
    $stmtStock = $pdo->query("SELECT SUM(stock_quantity) FROM products");
    $total_stock = $stmtStock->fetchColumn() ?: 0;

    // 2. Total Inventory Value
    $stmtValue = $pdo->query("SELECT SUM(price * stock_quantity) FROM products");
    $total_value = $stmtValue->fetchColumn() ?: 0;

    // 3. Low Stock Count
    $stmtLow = $pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 20");
    $low_stock_count = $stmtLow->fetchColumn() ?: 0;

    // 4. PENDING ORDERS COUNT (New!)
    $stmtPending = $pdo->query("SELECT COUNT(*) FROM Orders WHERE status = 'Pending'");
    $pending_count = $stmtPending->fetchColumn() ?: 0;

    // 5. Fetch Top Low Stock Items
    $stmtLowItems = $pdo->query("SELECT product_name, stock_quantity FROM products WHERE stock_quantity < 20 ORDER BY stock_quantity ASC LIMIT 3");
    $low_stock_items = $stmtLowItems->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error loading metrics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#344F1F',
                        'eco-light': '#8DA973',
                        'eco-bg': '#F2EAD3',
                        'eco-accent': '#C0D9B5',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { background-color: #F2EAD3; }
        .kpi-card {
            border-radius: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(52, 79, 31, 0.1);
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(52, 79, 31, 0.15); }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="flex items-center justify-between p-4 bg-white shadow-md sticky top-0 z-10">
            <h1 class="text-xl md:text-2xl font-bold text-eco-green">EcoCart Admin</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600 hidden sm:block">Welcome, Admin</span>
                <div class="w-10 h-10 bg-eco-light rounded-full flex items-center justify-center text-white font-bold"><img src="imej/save the earth2.jpg" width="221" height="221" alt=""/></div>
                <a href="logout.php" class="text-xs font-bold text-red-500 hover:text-red-700 ml-2">LOGOUT</a>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full"> 
            
            <!-- KPI Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Total Inventory -->
                <div class="kpi-card bg-white p-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">ASSETS</span>
                        <span class="text-xl">💰</span>
                    </div>
                    <p class="text-3xl font-extrabold text-eco-green mt-2">RM <?php echo number_format($total_value, 2); ?></p>
                </div>

                <!-- Total Units -->
                <div class="kpi-card bg-white p-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">UNITS</span>
                        <span class="text-xl">📦</span>
                    </div>
                    <p class="text-3xl font-extrabold text-eco-green mt-2"><?php echo number_format($total_stock); ?></p>
                </div>

                <!-- Pending Orders (Linked to Management Page) -->
                <a href="manage_orders.php" class="kpi-card bg-white p-6 border-l-4 <?php echo $pending_count > 0 ? 'border-yellow-500' : 'border-gray-200'; ?>">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">PENDING ORDERS</span>
                        <span class="text-xl">🛒</span>
                    </div>
                    <p class="text-3xl font-extrabold text-eco-green mt-2">
                        <?php echo $pending_count; ?>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Needs processing</p>
                </a>

                <!-- Low Stock -->
                <a href="inventory.php" class="kpi-card bg-white p-6 border-l-4 <?php echo $low_stock_count > 0 ? 'border-red-500' : 'border-green-500'; ?>">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">LOW STOCK</span>
                        <span class="text-xl">⚠️</span>
                    </div>
                    <p class="text-3xl font-extrabold <?php echo $low_stock_count > 0 ? 'text-red-600' : 'text-green-600'; ?> mt-2">
                        <?php echo $low_stock_count; ?>
                    </p>
                </a>
            </div>
            
            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Actions -->
                <div class="lg:col-span-2 bg-white p-6 kpi-card">
                    <h2 class="text-xl font-bold text-eco-green mb-4">Management Console</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="inventory.php" class="flex items-center justify-center gap-2 w-full p-4 rounded-xl bg-eco-light text-white font-bold hover:bg-eco-green transition shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                            Manage Inventory
                        </a>
                        <!-- New Link for Orders -->
                        <a href="manage_orders.php" class="flex items-center justify-center gap-2 w-full p-4 rounded-xl bg-gray-800 text-white font-bold hover:bg-gray-700 transition shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Manage Orders
                        </a>
                    </div>
                </div>

                <!-- Low Stock Feed -->
                <div class="bg-white p-6 kpi-card">
                    <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Urgent Restock</h2>
                    <?php if (count($low_stock_items) > 0): ?>
                        <ul class="space-y-3">
                            <?php foreach ($low_stock_items as $item): ?>
                                <li class="flex justify-between items-center">
                                    <span class="text-gray-600"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-full">
                                        <?php echo $item['stock_quantity']; ?> Left
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-4 pt-2 border-t text-center">
                            <a href="inventory.php" class="text-sm text-eco-light hover:text-eco-green font-semibold">View Full Inventory &rarr;</a>
                        </div>
                    <?php else: ?>
                        <p class="text-green-600 text-sm">All stock levels are healthy!</p>
                    <?php endif; ?>
                </div>

            </div>
            <footer class="mt-8 text-center text-xs text-gray-500 p-4">&copy; 2023 EcoCart Admin.</footer>
        </main>
    </div>
</body>
</html>