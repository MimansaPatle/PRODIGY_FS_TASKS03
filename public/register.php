<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/models/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'address' => $address
        ]);
        
        if ($result['success']) {
            $success = 'Account created successfully! You can now login.';
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-indigo-50 to-pink-50 min-h-screen font-['Inter'] flex items-center justify-center p-6">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900"><?= SITE_NAME ?></h1>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
        <p class="text-gray-600">Join us and start shopping</p>
    </div>

    <!-- Register Form -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <p class="text-red-700 text-sm"><?= htmlspecialchars($error) ?></p>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <p class="text-green-700 text-sm"><?= htmlspecialchars($success) ?></p>
            <a href="login.php" class="text-green-700 font-semibold underline mt-2 inline-block">Go to Login →</a>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                       placeholder="John Doe">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                <input type="email" name="email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                       placeholder="you@example.com">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                <input type="tel" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                       placeholder="+91 98765 43210">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                       placeholder="••••••••">
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                <input type="password" name="confirm_password" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                       placeholder="••••••••">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition"
                          placeholder="Your delivery address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl transition shadow-lg hover:shadow-xl">
                Create Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Already have an account? 
                <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                    Sign In
                </a>
            </p>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <a href="index.php" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                ← Back to Home
            </a>
        </div>
    </div>
</div>

</body>
</html>
