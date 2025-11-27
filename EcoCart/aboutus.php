<?php
session_start();

// Optional: Handle Contact Form Submission
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, you would send an email here using mail() or PHPMailer
    // $to = "support@ecocart.com";
    // $subject = $_POST['subject'];
    // ...
    $msg = "Thank you for contacting us! We will get back to you shortly.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | About Us</title>
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
                <a href="products.php" class="text-eco-green hover:text-eco-light font-medium transition duration-300">PRODUCTS</a>
                <a href="about.php" class="text-eco-light font-bold border-b-2 border-eco-light pb-1">ABOUT US</a>
            </nav>

            <div class="flex items-center space-x-4">
                <!-- Cart Icon -->
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

                <!-- User Logic -->
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
    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 flex-grow">
        
        <h1 class="text-4xl font-extrabold text-center text-eco-green my-8 md:my-12">
            Our Mission: Sustainable Living Made Easy
        </h1>

        <!-- About Section -->
        <section class="bg-white p-6 sm:p-10 rounded-xl shadow-2xl mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <!-- Image -->
                <div class="order-2 lg:order-1">
                    <img src="imej/logo ecocart.png" onerror="this.src='https://placehold.co/600x400/8DA973/344F1F?text=EcoCart+Team'" alt="EcoCart Team" class="w-full h-auto rounded-xl shadow-xl border-4 border-eco-light">
                </div>
                
                <!-- Text -->
                <div class="order-1 lg:order-2 space-y-4 text-gray-700">
                    <h2 class="text-3xl font-bold text-eco-green border-b border-eco-light pb-2">Why EcoCart?</h2>
                    <p>We founded EcoCart on a simple but powerful belief: that choosing a sustainable lifestyle shouldn't be difficult or expensive.</p>
                    <p>Our mission is to curate the best zero-waste, organic, and ethically-sourced products from around the globe and bring them directly to you.</p>
                    <p class="font-semibold text-eco-green p-3 bg-eco-bg rounded-lg">Join us in our journey toward a greener planet—one shopping cart at a time!</p>
                </div>
            </div>
        </section>
        
        <!-- Values Section -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-center text-eco-green mb-8">Our Core Values</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-eco-light hover:shadow-xl transition">
                    <div class="text-4xl text-eco-light mb-3">♻️</div>
                    <h3 class="text-xl font-semibold text-eco-green mb-2">Zero-Waste</h3>
                    <p class="text-sm text-gray-600">We prioritize products with minimal packaging and recyclable materials.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-eco-light hover:shadow-xl transition">
                    <div class="text-4xl text-eco-light mb-3">🤝</div>
                    <h3 class="text-xl font-semibold text-eco-green mb-2">Ethical Sourcing</h3>
                    <p class="text-sm text-gray-600">Fair trade practices are non-negotiable standards for all our suppliers.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-eco-light hover:shadow-xl transition">
                    <div class="text-4xl text-eco-light mb-3">🌿</div>
                    <h3 class="text-xl font-semibold text-eco-green mb-2">Natural Quality</h3>
                    <p class="text-sm text-gray-600">Every product is effective, non-toxic, and made from natural ingredients.</p>
                </div>
            </div>
        </section>

        <!-- Contact Form -->
        <section class="max-w-3xl mx-auto w-full">
            <h2 class="text-3xl font-bold text-center text-eco-green mb-8">Get In Touch</h2>
            
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl">
                
                <?php if ($msg): ?>
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-center font-semibold">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:border-eco-light outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:border-eco-light outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:border-eco-light outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Your Message</label>
                        <textarea name="message" rows="4" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:border-eco-light outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-eco-green text-white font-bold rounded-lg shadow-lg hover:bg-eco-light transition transform hover:scale-[1.01]">
                        Send Message
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>Email: <a href="mailto:support@ecocart.com" class="text-eco-green hover:underline font-semibold">support@ecocart.com</a></p>
                    <p class="mt-1">Phone: 014-9826091 | Address: Kg Tok Raja, Jerteh, 22000, Terengganu</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-eco-green mt-12 py-8 text-center text-white">
        <p class="text-sm">&copy; 2023 EcoCart. All rights reserved. Sustainable shopping for a better planet.</p>
    </footer>

</body>
</html>