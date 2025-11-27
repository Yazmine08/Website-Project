<?php
include 'db_connect.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || !isset($_POST['terms'])) {
        $message = "All fields are required.";
        $message_type = 'error';
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = 'error';
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $message = "Email already registered.";
                $message_type = 'error';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'Customer'; // Default role
                $created_at = date("Y-m-d H:i:s");

                // Added 'role' to the INSERT statement
                $sql = "INSERT INTO Users (first_name, last_name, email, password_hash, phone, address, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert = $pdo->prepare($sql);
                
                if ($stmt_insert->execute([$first_name, $last_name, $email, $hashed_password, $phone, $address, $role, $created_at])) {
                    header("Location: login.php?registration=success");
                    exit();
                } else {
                    $message = "Registration failed.";
                    $message_type = 'error';
                }
            }
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoCart | Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { 'eco-green': '#344F1F', 'eco-light': '#8DA973', 'eco-bg': '#F2EAD3', 'eco-text': '#4B5563', } }
            }
        }
    </script>
    <style>
        body { background-color: #F2EAD3; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; }
        .register-card { box-shadow: 0 10px 30px -5px rgba(52, 79, 31, 0.15); border: 1px solid rgba(141, 169, 115, 0.3); }
        .input-field:focus { border-color: #8DA973; box-shadow: 0 0 0 1px #8DA973; outline: none; }
        .btn-register:hover { background-color: #44682a; }
    </style>
</head>
<body>
    <div class="w-full max-w-lg my-10">
        <div class="register-card bg-white p-8 md:p-10 rounded-xl space-y-6">
            <h1 class="text-3xl font-extrabold text-eco-green text-center">Join EcoCart</h1>
            <?php if ($message): ?>
                <div class="p-3 rounded-lg font-medium text-sm border <?php echo $message_type === 'success' ? 'bg-green-100 text-eco-green border-eco-light' : 'bg-red-100 text-red-700 border-red-300'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm text-eco-text">First Name</label><input type="text" name="first_name" required class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block text-sm text-eco-text">Last Name</label><input type="text" name="last_name" required class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                </div>
                <div><label class="block text-sm text-eco-text">Email</label><input type="email" name="email" required class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-sm text-eco-text">Phone</label><input type="tel" name="phone" class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-sm text-eco-text">Address</label><textarea name="address" rows="2" class="input-field w-full p-3 border border-gray-300 rounded-lg"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm text-eco-text">Password</label><input type="password" name="password" required minlength="6" class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                    <div><label class="block text-sm text-eco-text">Confirm</label><input type="password" name="confirm-password" required class="input-field w-full p-3 border border-gray-300 rounded-lg"></div>
                </div>
                <div class="flex items-center pt-2"><input name="terms" type="checkbox" required class="mr-2 text-eco-green"><label class="text-sm text-eco-text">I agree to Terms</label></div>
                <button type="submit" class="btn-register w-full py-3 bg-eco-green text-white font-bold rounded-lg mt-4">Register Now</button>
            </form>
            <p class="text-center text-sm text-eco-text">Already registered? <a href="login.php" class="text-eco-light font-bold">Log in</a></p>
        </div>
    </div>
</body>
</html>