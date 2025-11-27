<?php
session_start();
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$order_id = htmlspecialchars($_GET['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | EcoCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { 'eco-green': '#344F1F', 'eco-light': '#8DA973', 'eco-bg': '#F2EAD3' } }
            }
        }
    </script>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex items-center justify-center">

    <div class="bg-white p-10 rounded-2xl shadow-2xl text-center max-w-md w-full border-t-8 border-eco-green">
        <div class="flex justify-center mb-6">
            <div class="bg-green-100 p-4 rounded-full">
                <svg class="w-16 h-16 text-eco-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
        
        <h1 class="text-3xl font-bold text-eco-green mb-2">Order Confirmed!</h1>
        <p class="text-gray-600 mb-6">Thank you for shopping sustainably. Your order has been placed successfully.</p>
        
        <div class="bg-gray-50 p-4 rounded-lg mb-8">
            <p class="text-sm text-gray-500 uppercase tracking-wide">Order ID</p>
            <p class="text-2xl font-mono font-bold text-gray-800">#<?php echo $order_id; ?></p>
        </div>

        <div class="space-y-3">
            <a href="index.php" class="block w-full py-3 bg-eco-green text-white font-bold rounded-lg hover:bg-eco-light transition shadow-lg">
                Back to Home
            </a>
            <a href="products.php" class="block w-full py-3 bg-white border-2 border-eco-green text-eco-green font-bold rounded-lg hover:bg-gray-50 transition">
                Continue Shopping
            </a>
        </div>
    </div>

</body>
</html>