<?php
session_start();
require_once 'db_connect.php';

// 1. Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error_message = '';
$email_input = '';

// 2. Handle Login Form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = trim($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? '';

    if (empty($email_input) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        try {
            // Query DB for user
            $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, password_hash, role FROM Users WHERE email = ?");
            $stmt->execute([$email_input]);
            $user = $stmt->fetch();

            // Verify Password
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // Set Session Variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['role'] = $user['role']; 
                
                // --- CRITICAL: ROLE BASED REDIRECT ---
                if ($user['role'] === 'Admin') {
                    header("Location: dashboard.php"); // Admin -> Dashboard
                } else {
                    header("Location: index.php"); // Customer -> Home
                }
                exit();

            } else {
                $error_message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error_message = "System Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Login</title>
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
        body { background-color: #F2EAD3; }
        .eco-card {
            background-color: #FFFFFF;
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(52, 79, 31, 0.1);
        }
        .eco-input {
            background-color: #FAFAF5;
            border: 2px solid #D1D1C4;
            border-radius: 9999px;
            transition: all 0.3s ease;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }
        .eco-input:focus {
            outline: none;
            border-color: #8DA973;
            box-shadow: 0 0 8px rgba(141, 169, 115, 0.7);
            background-color: #FFFFFF;
        }
        .eco-button {
            transition: all 0.3s ease;
            background-color: #344F1F;
            color: #FFFFFF;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .eco-button:hover {
            background-color: #8DA973;
            color: #344F1F;
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-eco-bg font-sans min-h-screen flex flex-col">

    <main class="flex-grow flex items-center justify-center p-4 sm:p-6">
        
        <div class="w-full max-w-sm eco-card mx-auto">
            
            <!-- Back Link -->
            <div class="mb-4 text-center">
                 <a href="index.php" class="text-eco-green hover:underline font-semibold text-sm">&larr; Back to Home</a>
            </div>
            
            <div class="text-center mb-6">
                <img src="imej/logo ecocart.png" width="150" height="90" alt="EcoCart Logo" class="mx-auto rounded-lg shadow-md" onerror="this.src='https://placehold.co/150x90/344F1F/FFFFFF?text=EcoCart'">
            </div>

            <h1 class="text-3xl font-extrabold text-center text-eco-green mb-2">
                Welcome Back!
            </h1>
            <p class="text-center text-gray-500 mb-8">
                Sign in to your account.
            </p>

            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-100 border border-red-200 text-red-700 text-center text-sm font-semibold">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-6">
                
                <div>
                    <label class="block text-sm font-medium text-eco-green ml-4 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        class="w-full py-3 eco-input"
                        placeholder="you@ecocart.com"
                        value="<?php echo htmlspecialchars($email_input); ?>"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-eco-green ml-4 mb-1">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full py-3 eco-input"
                        placeholder="Enter your password"
                    >
                </div>

                <button 
                    type="submit" 
                    class="eco-button w-full py-3 px-4 text-lg shadow-xl focus:outline-none focus:ring-4 focus:ring-eco-light/50"
                >
                    Log In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Don't have an account? 
                <a href="register.php" class="font-bold text-eco-green hover:text-eco-light transition duration-150">
                    Create an account
                </a>
            </div>

        </div>

    </main>
</body>
</html>