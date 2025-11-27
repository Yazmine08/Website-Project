<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Sustainable Shopping</title>
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
    <style>
        .hero-bg {
            background-color: #F2EAD3;
            background-image: url("data:image/svg+xml,%3Csvg width='42' height='44' viewBox='0 0 42 44' xmlns='http://www.w3.org/2000/svg'%3E%3Cg id='Page-1' fill='none' fill-rule='evenodd'%3E%3Cg id='brick-wall' fill='%238DA973' fill-opacity='0.2'%3E%3Cpath d='M0 0h42v44H0V0zm1 1h40v2H1V1zm0 4h40v2H1V5zm0 4h40v2H1V9zm0 4h40v2H1V13zm0 4h40v2H1V17zm0 4h40v2H1V21zm0 4h40v2H1V25zm0 4h40v2H1V29zm0 4h40v2H1V33zm0 4h40v2H1V37zm0 4h40v2H1V41z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            background-size: 80px;
        }
    </style>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <!-- Header & Navigation -->
    <header class="bg-white shadow-lg sticky top-0 z-10">
        <div class="max-w-7xl mx-auto flex justify-between items-center p-4 sm:p-6">
            <a href="index.php">
                <img src="imej/logo ecocart.png" alt="EcoCart Logo" class="w-[157px] h-[98px] object-contain rounded" onerror="this.src='https://placehold.co/157x98/344F1F/FFFFFF?text=EcoCart+Logo'">
            </a>
            
            <nav class="hidden md:flex space-x-8">
                <a href="index.php" class="text-eco-light font-bold border-b-2 border-eco-light pb-1">HOME</a>
                <a href="products.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">PRODUCTS</a>
                <a href="aboutus.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">ABOUT US</a>
            </nav>

            <div class="flex items-center space-x-4">
                <!-- Cart Icon -->
                <a href="cart.php" class="text-eco-green hover:text-eco-light transition duration-300" title="View Cart">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                   </svg>
                </a>

                <!-- Dynamic Login/User Section -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged In State -->
                    <div class="flex items-center space-x-3">
                        <span class="text-eco-green font-medium hidden sm:block">
                            Hi, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'User'); ?>
                        </span>
                        <a href="logout.php" class="text-sm text-red-500 hover:text-red-700 font-semibold border border-red-200 px-3 py-1 rounded-full hover:bg-red-50 transition">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Guest State -->
                    <div class="cursor-pointer" title="Login">
                        <a href="login.php">
                            <img src="login2.png" alt="Login Icon" class="w-[52px] h-[40px] object-contain rounded" onerror="this.src='https://placehold.co/52x40/344F1F/FFFFFF?text=Login'">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="hero-bg py-16 sm:py-24 border-b-8 border-eco-light/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-eco-green mb-4">
                    Shop Sustainably, Live Beautifully.
                </h2>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-700">
                    Your one-stop destination for ethical, eco-friendly, and high-quality goods that don't cost the Earth.
                </p>
                <div class="mt-10">
                    <a href="products.php" class="inline-block px-12 py-4 bg-eco-green text-white font-bold rounded-full shadow-xl text-lg uppercase tracking-wider hover:bg-eco-light transition duration-300 transform hover:scale-105">
                        Start Shopping Now
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h3 class="text-3xl font-bold text-gray-800">Our Commitment</h3>
                    <p class="mt-2 text-lg text-gray-500">Every product meets our strict standards for planet and people.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-6 bg-eco-bg rounded-xl shadow-lg border border-eco-light/30">
                        <div class="text-4xl text-eco-green mb-4">🌱</div>
                        <h4 class="text-xl font-semibold text-eco-green mb-2">Zero-Waste Focus</h4>
                        <p class="text-gray-600">Minimizing packaging and prioritizing compostable or reusable materials.</p>
                    </div>
                    <div class="text-center p-6 bg-eco-bg rounded-xl shadow-lg border border-eco-light/30">
                        <div class="text-4xl text-eco-green mb-4">🤝</div>
                        <h4 class="text-xl font-semibold text-eco-green mb-2">Ethically Sourced</h4>
                        <p class="text-gray-600">We partner exclusively with suppliers who ensure fair wages.</p>
                    </div>
                    <div class="text-center p-6 bg-eco-bg rounded-xl shadow-lg border border-eco-light/30">
                        <div class="text-4xl text-eco-green mb-4">☀️</div>
                        <h4 class="text-xl font-semibold text-eco-green mb-2">Renewable & Natural</h4>
                        <p class="text-gray-600">Materials like organic cotton and bamboo that are renewable.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Preview -->
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-3xl font-bold text-center text-eco-green mb-8">Popular Picks</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-4 rounded-xl shadow-xl border border-eco-light/50 text-center">
                    <img src="imej/wooden cup 2.png" onerror="this.src='https://placehold.co/400x300/F2EAD3/344F1F?text=Cup+Placeholder'" alt="Reusable Coffee Cup" class="w-full h-56 object-cover rounded-lg mb-4">
                    <h4 class="text-xl font-semibold text-eco-green">Reusable Cup</h4>
                    <p class="text-2xl font-extrabold text-black my-3">RM 20.00</p>
                    <a href="products.php" class="inline-block w-full py-2 bg-eco-light text-white font-bold rounded-lg hover:bg-eco-green transition duration-300">View Item</a>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-xl border border-eco-light/50 text-center">
                    <img src="imej/toothbrush.png" onerror="this.src='https://placehold.co/400x300/F2EAD3/344F1F?text=Brush+Placeholder'" alt="Bamboo Toothbrush Set" class="w-full h-56 object-cover rounded-lg mb-4">
                    <h4 class="text-xl font-semibold text-eco-green">Bamboo Toothbrush Set</h4>
                    <p class="text-2xl font-extrabold text-black my-3">RM 28.00</p>
                    <a href="products.php" class="inline-block w-full py-2 bg-eco-light text-white font-bold rounded-lg hover:bg-eco-green transition duration-300">View Item</a>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-xl border border-eco-light/50 text-center">
                    <img src="imej/tote bag2.png" onerror="this.src='https://placehold.co/400x300/F2EAD3/344F1F?text=Tote+Bag+Placeholder'" alt="Organic Cotton Tote Bag" class="w-full h-56 object-cover rounded-lg mb-4">
                    <h4 class="text-xl font-semibold text-eco-green">Organic Tote Bag</h4>
                    <p class="text-2xl font-extrabold text-black my-3">RM 18.00</p>
                    <a href="products.php" class="inline-block w-full py-2 bg-eco-light text-white font-bold rounded-lg hover:bg-eco-green transition duration-300">View Item</a>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-eco-green mt-12 py-8">
        <div class="max-w-7xl mx-auto text-center text-white px-4">
            <p class="mb-2 text-lg font-semibold">EcoCart</p>
            <p class="text-sm">&copy; 2023 EcoCart. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>