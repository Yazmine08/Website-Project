<?php
session_start();
require_once 'db_connect.php';

// Check Admin Role (Uncomment for production)
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin')) {
    // header("Location: login.php"); 
}

$msg = "";
$msg_type = ""; 

// --- HANDLE 1: UPDATE STOCK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = $_POST['product_id'];
    $new_stock = intval($_POST['stock']);
    try {
        $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE product_id = ?");
        if ($stmt->execute([$new_stock, $id])) {
            $msg = "Stock updated!";
            $msg_type = "success";
        }
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
        $msg_type = "error";
    }
}

// --- HANDLE 2: DELETE PRODUCT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = $_POST['product_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        if ($stmt->execute([$id])) {
            $msg = "Product deleted!";
            $msg_type = "success";
        }
    } catch (PDOException $e) {
        $msg = "Cannot delete product (it may be in an order history).";
        $msg_type = "error";
    }
}

// --- HANDLE 3: ADD PRODUCT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = $_POST['category'];
    $stock = intval($_POST['stock']); // Set to 0 to make "Not Available"
    $recyclability = intval($_POST['recyclability']);
    $material = trim($_POST['material']);
    $certification = trim($_POST['certification']);
    $image_path = 'imej/placeholder.png'; 

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $target_dir = "imej/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $name) . "." . $file_extension;
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_dir . $new_filename);
        $image_path = $target_dir . $new_filename;
    }

    if(!empty($name)) {
        try {
            $sql = "INSERT INTO products (product_name, price, category, stock_quantity, recyclability_percent, material_source, certification_body, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $price, $category, $stock, $recyclability, $material, $certification, $image_path])) {
                $msg = "New product added!";
                $msg_type = "success";
            }
        } catch (PDOException $e) {
            $msg = "Error: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoCart | Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { 'eco-green': '#344F1F', 'eco-light': '#8DA973', 'eco-bg': '#F2EAD3' } } }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@heroicons/vue@1.0.6/dist/heroicons-vue.min.js"></script>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-eco-green transition">&larr; Back</a>
            <h1 class="text-xl font-bold text-eco-green">Inventory Management</h1>
        </div>
        <div class="text-sm text-gray-500">Total Products: <?php echo count($products); ?></div>
    </header>

    <main class="max-w-7xl mx-auto p-6 w-full space-y-8">
        
        <?php if ($msg): ?>
            <div class="p-4 rounded-lg font-bold text-center <?php echo $msg_type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- ADD PRODUCT -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-eco-light/30">
            <h2 class="text-lg font-bold text-eco-green mb-4 border-b pb-2">Add New Product</h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2"><label class="text-xs text-gray-500 uppercase">Name</label><input type="text" name="name" required class="w-full p-2 border rounded outline-none focus:border-eco-light"></div>
                <div><label class="text-xs text-gray-500 uppercase">Category</label><select name="category" class="w-full p-2 border rounded"><option>Personal Care</option><option>Home</option><option>Accessories</option><option>Kitchenware</option><option>Bath Accessories</option></select></div>
                <div><label class="text-xs text-gray-500 uppercase">Price (RM)</label><input type="number" step="0.01" name="price" required class="w-full p-2 border rounded"></div>
                <div><label class="text-xs text-gray-500 uppercase">Initial Stock</label><input type="number" name="stock" placeholder="0 = Not Available" required class="w-full p-2 border rounded"></div>
                <div><label class="text-xs text-gray-500 uppercase">Recyclability %</label><input type="number" name="recyclability" value="100" class="w-full p-2 border rounded"></div>
                <div class="md:col-span-2"><label class="text-xs text-gray-500 uppercase">Image</label><input type="file" name="product_image" accept="image/*" class="w-full p-1 border rounded bg-gray-50 text-sm"></div>
                <div><label class="text-xs text-gray-500 uppercase">Certification</label><input type="text" name="certification" value="None" class="w-full p-2 border rounded"></div>
                <div class="md:col-span-3"><label class="text-xs text-gray-500 uppercase">Material Source</label><input type="text" name="material" value="Natural" class="w-full p-2 border rounded"></div>
                <div class="md:col-span-4 text-right"><button type="submit" name="add_product" class="px-6 py-2 bg-eco-green text-white font-bold rounded hover:bg-eco-light transition">+ Add Product</button></div>
            </form>
        </div>

        <!-- INVENTORY LIST -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 bg-gray-50 border-b"><h2 class="text-lg font-bold text-gray-700">Current Inventory</h2></div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-eco-green text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Img</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Stock</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4"><img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="h-10 w-10 rounded border object-cover" onerror="this.src='https://placehold.co/40x40?text=Img'"></td>
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($p['product_name']); ?></td>
                            <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><?php echo htmlspecialchars($p['category']); ?></span></td>
                            <td class="px-6 py-4 text-gray-500">RM <?php echo $p['price']; ?></td>
                            <td class="px-6 py-4">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                    <!-- Highlight 0 stock in Red -->
                                    <input type="number" name="stock" value="<?php echo $p['stock_quantity']; ?>" 
                                           class="w-16 p-1 text-center border rounded border-gray-300 outline-none font-mono font-bold <?php echo $p['stock_quantity'] == 0 ? 'text-red-600 bg-red-50 border-red-300' : ''; ?>">
                                    <button type="submit" name="update_stock" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase">Save</button>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form method="POST" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                    <input type="hidden" name="delete_product" value="1">
                                    <button type="submit" class="text-gray-400 hover:text-red-600"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>