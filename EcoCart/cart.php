<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $action = $_POST['action'];

    if ($product_id && isset($_SESSION['cart'][$product_id])) {
        // Update Quantity
        if ($action === 'update' && isset($_POST['quantity'])) {
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
            $quantity = max(1, (int)$quantity); // Ensure at least 1
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        } 
        // Remove Item
        elseif ($action === 'remove') {
            unset($_SESSION['cart'][$product_id]);
        }
        
        // Refresh page to prevent resubmission
        header('Location: cart.php');
        exit();
    }
    
    // Clear Cart Action
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit();
    }
}

// --- 3. CALCULATE TOTALS ---
$subtotal = 0;
$cart_count = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
    $cart_count += $item['quantity'];
}

$tax_rate = 0.05; // 5% Tax
$shipping_cost = (count($_SESSION['cart']) > 0) ? 5.00 : 0.00;
$tax = $subtotal * $tax_rate;
$grand_total = $subtotal + $tax + $shipping_cost;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Shopping Cart</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Custom Tailwind Configuration for EcoCart Theme
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-green': '#344F1F', // Dark Green
                        'eco-light': '#8DA973', // Light Green
                        'eco-bg': '#F2EAD3',    // Cream/Light Background
                        'eco-neutral': '#8D6E63', // Earthy Brown
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Heroicons -->
    <script src="https://cdn.jsdelivr.net/npm/@heroicons/vue@1.0.6/dist/heroicons-vue.min.js"></script>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <!-- Header & Navigation -->
    <header class="bg-white shadow-lg sticky top-0 z-20">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-4 sm:p-6">
            <!-- Logo -->
            <a href="index.php">
                <img src="imej/logo ecocart.png" alt="EcoCart Logo" class="w-[157px] h-[98px] object-contain rounded" onerror="this.src='https://placehold.co/157x98/344F1F/FFFFFF?text=EcoCart+Logo'">
            </a>
            
            <!-- Nav -->
            <nav class="hidden md:flex space-x-8">
                <a href="index.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">HOME</a>
                <a href="products.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">PRODUCTS</a>
                <a href="about.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">ABOUT US</a>
            </nav>

            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <div class="relative cursor-pointer">
                    <a href="cart.php" class="text-eco-light hover:text-eco-green transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            <?php echo $cart_count; ?>
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
        <h1 class="text-3xl md:text-4xl font-extrabold text-center text-eco-green mb-8">
            Your Sustainable Shopping Cart
        </h1>

        <!-- EMPTY CART STATE -->
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center bg-white p-12 rounded-xl shadow-lg border border-eco-light/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-eco-light mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-eco-green mb-2">Your Cart is Empty</h2>
                <p class="text-gray-600 mb-8">Looks like you haven't added any sustainable goodies yet.</p>
                <a href="products.php" class="inline-block px-8 py-3 bg-eco-green text-white font-bold rounded-lg hover:bg-eco-light transition duration-300">
                    Browse Catalog
                </a>
            </div>
        <?php else: ?>

        <!-- ACTIVE CART GRID -->
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- LEFT COLUMN: CART ITEMS -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="p-4 sm:p-6 bg-eco-green/10 border-b border-eco-green/10 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-eco-green">Items in Cart</h2>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline font-semibold">Clear All</button>
                        </form>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($_SESSION['cart'] as $item_id => $item): ?>
                            <?php 
                                $image_url = isset($item['image_url']) ? $item['image_url'] : 'https://placehold.co/80x80/9e9e9e/ffffff?text=Product';
                            ?>
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-center gap-4 hover:bg-gray-50 transition duration-150">
                                
                                <!-- Product Image -->
                                <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                    <img src="<?php echo htmlspecialchars($image_url); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="w-full h-full object-cover"
                                         onerror="this.src='https://placehold.co/100x100/F2EAD3/344F1F?text=EcoCart'">
                                </div>

                                <!-- Product Details -->
                                <div class="flex-grow text-center sm:text-left">
                                    <h3 class="font-bold text-eco-green text-lg"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="text-sm text-gray-500">Unit Price: RM <?php echo number_format($item['price'], 2); ?></p>
                                </div>

                                <!-- Quantity Form -->
                                <form method="POST" action="cart.php" class="flex items-center border border-gray-300 rounded-lg bg-white">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item_id); ?>">
                                    
                                    <!-- Decrease Button -->
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 hover:text-eco-green rounded-l-lg transition">-</button>
                                    
                                    <!-- Display Quantity -->
                                    <span class="px-2 py-1 text-sm font-bold text-gray-700 w-8 text-center border-l border-r border-gray-200"><?php echo $item['quantity']; ?></span>
                                    
                                    <!-- Increase Button -->
                                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="px-3 py-1 text-gray-600 hover:bg-gray-100 hover:text-eco-green rounded-r-lg transition">+</button>
                                </form>

                                <!-- Total Price & Remove -->
                                <div class="w-full sm:w-auto flex sm:flex-col justify-between sm:items-end items-center px-4 sm:px-0">
                                    <div class="font-extrabold text-gray-800 mb-0 sm:mb-2 text-lg">
                                        RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                    
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item_id); ?>">
                                        <button type="submit" class="flex items-center text-sm text-red-400 hover:text-red-600 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Continue Shopping Link -->
                <div class="mt-6">
                    <a href="products.php" class="inline-flex items-center text-eco-green hover:text-eco-light font-semibold transition duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- RIGHT COLUMN: ORDER SUMMARY -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 sticky top-28">
                    <h2 class="text-xl font-bold text-eco-green mb-6 border-b border-gray-100 pb-4">Order Summary</h2>
                    
                    <div class="space-y-3 text-gray-600 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-800">RM <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping</span>
                            <span class="font-semibold text-gray-800">RM <?php echo number_format($shipping_cost, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tax (5%)</span>
                            <span class="font-semibold text-gray-800">RM <?php echo number_format($tax, 2); ?></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center border-t-2 border-dashed border-gray-200 pt-4 mb-6">
                        <span class="text-lg font-bold text-eco-green">Grand Total</span>
                        <span class="text-2xl font-extrabold text-eco-green">RM <?php echo number_format($grand_total, 2); ?></span>
                    </div>

                    <!-- CHECKOUT BUTTON LOGIC -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="checkout_process.php" method="POST">
                            <button type="submit" class="w-full py-4 bg-eco-green text-white font-bold rounded-lg shadow-lg hover:bg-eco-light hover:shadow-xl transition duration-300 transform active:scale-[0.98] flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Proceed to Checkout
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="block text-center w-full py-4 bg-gray-800 text-white font-bold rounded-lg shadow-lg hover:bg-gray-700 transition duration-300">
                            Login to Checkout
                        </a>
                    <?php endif; ?>

                    <div class="mt-4 flex items-center justify-center text-xs text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Secure 256-bit SSL Checkout
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-eco-green mt-auto py-8">
        <div class="max-w-7xl mx-auto text-center text-white px-4">
            <p class="font-semibold text-lg mb-2">EcoCart</p>
            <p class="text-sm opacity-80">&copy; <?php echo date("Y"); ?> EcoCart. All rights reserved. Sustainable shopping for a better planet.</p>
        </div>
    </footer>

</body>
</html>