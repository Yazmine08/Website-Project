<?php
session_start();
require_once 'db_connect.php';

// --- 1. ADD TO CART LOGIC ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $id = $_POST['product_id'];
    $name = $_POST['product_name']; 
    $price = floatval($_POST['price']);
    $image = $_POST['image'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $_SESSION['cart'][$id] = [
            'product_id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'image_url' => $image
        ];
    }
    $msg = "Item added to cart!";
}

// --- 2. FETCH PRODUCTS FROM DATABASE ---
try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#344F1F',
                        'eco-light': '#8DA973',
                        'eco-bg': '#F2EAD3',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@heroicons/vue@1.0.6/dist/heroicons-vue.min.js"></script>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-20">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-4 sm:p-6">
            <a href="index.php">
                <img src="imej/logo ecocart.png" alt="EcoCart Logo" class="w-[157px] h-[98px] object-contain rounded" onerror="this.src='https://placehold.co/157x98/344F1F/FFFFFF?text=EcoCart+Logo'">
            </a>
            
            <nav class="hidden md:flex space-x-8">
                <a href="index.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">HOME</a>
                <a href="products.php" class="text-eco-light font-bold border-b-2 border-eco-light pb-1">PRODUCTS</a>
                <a href="aboutus.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">ABOUT US</a>
            </nav>

            <div class="flex items-center space-x-4">
                <div class="relative cursor-pointer">
                    <a href="cart.php" class="text-eco-green hover:text-eco-light transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                    <?php 
                        $count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
                        if ($count > 0): 
                    ?>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            <?php echo $count; ?>
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="hidden sm:flex items-center space-x-2">
                        <span class="text-sm font-medium text-eco-green">Hi, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?></span>
                        <a href="logout.php" class="text-xs text-red-500 border border-red-200 px-2 py-1 rounded hover:bg-red-50">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php">
                        <img src="login2.png" alt="Login Icon" class="w-[52px] h-[40px] object-contain rounded" onerror="this.src='https://placehold.co/52x40/344F1F/FFFFFF?text=Login'">
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 flex-grow w-full">
        
        <h1 class="text-3xl font-extrabold text-center text-eco-green my-8">The Full Eco-Friendly Catalog</h1>

        <?php if ($msg): ?>
            <div id="toast" class="fixed bottom-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl transition-opacity duration-500 z-50">
                <?php echo $msg; ?>
            </div>
            <script>setTimeout(() => document.getElementById('toast').style.opacity = '0', 3000);</script>
        <?php endif; ?>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4 p-6 bg-white rounded-xl shadow-lg h-fit sticky top-24">
                <h3 class="text-xl font-bold text-eco-green mb-4 border-b pb-2 border-eco-light/50">Filter Options</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Search</label>
                        <input type="text" id="search" onkeyup="filterProducts()" placeholder="e.g., Bamboo" class="w-full p-2 border border-gray-300 rounded-lg focus:border-eco-light outline-none">
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Category</label>
                        <select id="category" onchange="filterProducts()" class="w-full p-2 border border-gray-300 rounded-lg focus:border-eco-light outline-none">
                            <option value="all">All</option>
                            <option value="Personal Care">Personal Care</option>
                            <option value="Home">Home & Kitchen</option>
                            <option value="Accessories">Accessories</option>
                            <option value="Kitchenware">Kitchenware</option>
                            <option value="Bath Accessories">Bath Accessories</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-2">Max Price: RM <span id="priceValue">50</span></label>
                        <input type="range" id="priceRange" min="0" max="50" value="50" oninput="filterProducts()" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-eco-green">
                    </div>
                    <button onclick="resetFilters()" class="w-full mt-2 p-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition">
                        Reset Filters
                    </button>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="w-full lg:w-3/4">
                <div id="productGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    
                    <?php foreach ($products as $product): 
                        $isOutOfStock = (isset($product['stock_quantity']) && $product['stock_quantity'] <= 0);
                    ?>
                    <div class="product-card bg-white p-4 rounded-xl shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 border border-transparent hover:border-eco-light <?php echo $isOutOfStock ? 'opacity-75' : ''; ?>"
                         data-name="<?php echo strtolower($product['product_name']); ?>"
                         data-category="<?php echo $product['category']; ?>"
                         data-price="<?php echo $product['price']; ?>">
                        
                        <div class="relative">
                            <img src="<?php echo $product['image_url']; ?>" 
                                 alt="<?php echo $product['product_name']; ?>" 
                                 class="w-full h-48 object-cover rounded-lg mb-4 border border-eco-light/30 <?php echo $isOutOfStock ? 'grayscale' : ''; ?>"
                                 onerror="this.src='https://placehold.co/400x300/F2EAD3/344F1F?text=Product'">
                            
                            <!-- OUT OF STOCK OVERLAY -->
                            <?php if ($isOutOfStock): ?>
                                <div class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-40 rounded-lg mb-4">
                                    <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold shadow-md transform -rotate-12">OUT OF STOCK</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-eco-green truncate"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-gray-500 text-sm capitalize"><?php echo htmlspecialchars($product['category']); ?></p>
                            <?php if(!$isOutOfStock && $product['stock_quantity'] < 10): ?>
                                <span class="text-xs text-red-500 font-bold">Low Stock: <?php echo $product['stock_quantity']; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-2xl font-extrabold text-black mb-4">RM <?php echo number_format($product['price'], 2); ?></p>
                        
                        <form method="POST">
                            <input type="hidden" name="add_to_cart" value="1">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                            <input type="hidden" name="image" value="<?php echo $product['image_url']; ?>">
                            
                            <button type="submit" <?php echo $isOutOfStock ? 'disabled' : ''; ?> 
                                    class="w-full py-2 <?php echo $isOutOfStock ? 'bg-gray-400 cursor-not-allowed' : 'bg-eco-light hover:bg-eco-green'; ?> text-white font-bold rounded-lg transition duration-300 flex items-center justify-center gap-2">
                                <?php echo $isOutOfStock ? 'Unavailable' : 'Add to Cart'; ?>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>

                    <div id="noResults" class="hidden col-span-full text-center py-10 text-gray-500 text-lg">
                        No products match your filters.
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer class="bg-eco-green mt-12 py-8 text-center text-white">
        <p class="text-sm">&copy; 2023 EcoCart. All rights reserved.</p>
    </footer>

    <!-- Filter Logic Script (Same as before) -->
    <script>
        function filterProducts() {
            const search = document.getElementById('search').value.toLowerCase();
            const category = document.getElementById('category').value;
            const maxPrice = parseFloat(document.getElementById('priceRange').value);
            document.getElementById('priceValue').innerText = maxPrice;
            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                const cat = card.getAttribute('data-category');
                const price = parseFloat(card.getAttribute('data-price'));
                let isVisible = true;
                if (search && !name.includes(search)) isVisible = false;
                if (category !== 'all' && cat !== category) isVisible = false;
                if (price > maxPrice) isVisible = false;
                if (isVisible) {
                    card.style.display = "block";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });
            const noResults = document.getElementById('noResults');
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }
        function resetFilters() {
            document.getElementById('search').value = '';
            document.getElementById('category').value = 'all';
            document.getElementById('priceRange').value = 50;
            filterProducts();
        }
    </script>
</body>
</html>