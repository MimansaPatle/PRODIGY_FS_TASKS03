<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/models/Order.php';
require_once __DIR__ . '/../src/image-upload.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser();
$orderId = $_GET['id'] ?? 0;

if (empty($orderId)) {
    header('Location: my-orders.php');
    exit;
}

// Get order details
$order = Order::getById($orderId);
if (!$order || $order['user_id'] != $currentUser['id']) {
    header('Location: my-orders.php');
    exit;
}

$orderItems = Order::getItems($order['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#ec4899',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-['Inter']">

<!-- Sidebar -->
<aside class="fixed left-0 top-0 w-72 h-screen bg-gradient-to-b from-indigo-600 to-indigo-700 text-white z-50 overflow-y-auto transition-transform duration-300 lg:translate-x-0 -translate-x-full" id="sidebar">
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <h1 class="text-xl font-bold"><?= SITE_NAME ?></h1>
        </div>
    </div>
    
    <nav class="p-4">
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="font-medium">Home</span>
        </a>
        
        <a href="cart.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-medium">Cart</span>
        </a>
        
        <a href="my-orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="font-medium">My Orders</span>
        </a>
        
        <a href="wishlist.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span class="font-medium">Wishlist</span>
        </a>
        
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">Profile</span>
        </a>
        
        <a href="track-order.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span class="font-medium">Track Order</span>
        </a>
        
        <a href="support.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Support</span>
        </a>
        
        <?php if (isAdmin()): ?>
        <div class="mt-6 pt-6 border-t border-white/10">
            <a href="admin/" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 text-white mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">Admin Panel</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    
    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-white/10 bg-black/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold">
                <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm truncate"><?= htmlspecialchars($currentUser['name']) ?></div>
                <a href="logout.php" class="text-xs text-white/70 hover:text-white">Logout</a>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Toggle -->
<button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" 
        class="lg:hidden fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg flex items-center justify-center z-40">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Main Content -->
<main class="lg:ml-72 min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Order Details</h1>
            <a href="my-orders.php" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Orders
            </a>
        </div>

        <!-- Order Info Card -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <span class="text-sm text-gray-500">Order Number</span>
                    <p class="font-bold text-lg text-gray-800"><?= htmlspecialchars($order['order_number']) ?></p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Order Date</span>
                    <p class="font-semibold text-gray-800"><?= date('M j, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Status</span>
                    <p>
                        <?php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'shipped' => 'bg-purple-100 text-purple-800',
                            'delivered' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $statusColor ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Total Amount</span>
                    <p class="font-bold text-xl text-indigo-600">₹<?= number_format($order['total_amount'], 2) ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Items</h2>
                    <div class="space-y-4">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="flex gap-4 p-4 border-2 border-gray-100 rounded-xl">
                            <img src="<?= ImageUpload::getImageUrl($item['product_image'], true) ?>" 
                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'"
                                 class="w-20 h-20 object-cover rounded-xl">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h3>
                                <p class="text-sm text-gray-500 mt-1">₹<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-800">₹<?= number_format($item['subtotal'], 2) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Order Total -->
                    <div class="border-t-2 border-gray-200 mt-6 pt-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total</span>
                            <span class="text-2xl font-bold text-indigo-600">₹<?= number_format($order['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Shipping Address</h2>
                    <div class="text-gray-600 space-y-1">
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                        <?php if ($currentUser['phone']): ?>
                        <p><?= htmlspecialchars($currentUser['phone']) ?></p>
                        <?php endif; ?>
                        <div class="mt-2 text-sm">
                            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm text-gray-500">Payment Method</span>
                            <p class="font-semibold text-gray-800">
                                <?php
                                $paymentMethods = [
                                    'COD' => 'Cash on Delivery',
                                    'razorpay' => 'Online Payment',
                                    'BANK_TRANSFER' => 'Bank Transfer',
                                    'WHATSAPP_PAY' => 'WhatsApp Payment'
                                ];
                                echo $paymentMethods[$order['payment_method']] ?? $order['payment_method'];
                                ?>
                            </p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Payment Status</span>
                            <p>
                                <?php
                                $paymentStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800'
                                ];
                                $paymentColor = $paymentStatusColors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $paymentColor ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <a href="track-order.php?order=<?= htmlspecialchars($order['order_number']) ?>" 
                       class="block w-full text-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
                        Track Order
                    </a>
                    <a href="support.php" 
                       class="block w-full text-center px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>
