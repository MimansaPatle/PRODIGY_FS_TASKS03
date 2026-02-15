<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/cart.php';
require_once __DIR__ . '/../src/models/Order.php';
require_once __DIR__ . '/../src/image-upload.php';

$cartCount = getCartCount();
$currentPage = 'track-order';
$currentUser = getCurrentUser();

$order = null;
$orderItems = [];
$error = '';

// Handle order tracking
if (isset($_GET['order']) && !empty($_GET['order'])) {
    $orderNumber = trim($_GET['order']);
    
    try {
        $order = Order::getByOrderNumber($orderNumber);
        
        if ($order) {
            // If user is logged in, verify they own this order
            if ($currentUser && $order['user_id'] != $currentUser['id']) {
                $error = 'This order does not belong to your account.';
                $order = null;
            } else {
                $orderItems = Order::getItems($order['id']);
            }
        } else {
            $error = 'Order not found. Please check your order number and try again.';
        }
    } catch (Exception $e) {
        $error = 'An error occurred while tracking your order. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-['Inter']">

<!-- Sidebar (same as other pages) -->
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
            <?php if ($cartCount > 0): ?>
            <span class="ml-auto bg-white/30 px-2 py-1 rounded-full text-xs"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
        
        <?php if ($currentUser): ?>
        <a href="my-orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
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
        <?php endif; ?>
        
        <a href="track-order.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
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
    
    <?php if ($currentUser): ?>
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
    <?php else: ?>
    <div class="absolute bottom-0 left-0 right-0 p-6">
        <a href="login.php" class="block w-full text-center px-4 py-3 bg-white/20 hover:bg-white/30 rounded-xl font-medium transition">
            Login / Register
        </a>
    </div>
    <?php endif; ?>
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
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-12 mb-6 text-white">
        <h1 class="text-4xl font-bold mb-4">Track Your Order</h1>
        <p class="text-xl text-indigo-100">Enter your order number to see the latest status</p>
    </div>

    <!-- Track Order Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
            <form method="GET" action="" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Order Number</label>
                    <input type="text" name="order" required
                           value="<?= htmlspecialchars($_GET['order'] ?? '') ?>"
                           class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition text-lg"
                           placeholder="e.g., ORD-123456">
                    <p class="text-sm text-gray-500 mt-2">You can find your order number in the confirmation email</p>
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-4 rounded-xl transition shadow-lg hover:shadow-xl">
                    Track Order
                </button>
            </form>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-50 border-2 border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium"><?= htmlspecialchars($error) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Order Tracking Results -->
        <?php if ($order): ?>
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-6">
            <div class="flex items-center justify-between mb-6 pb-6 border-b-2 border-gray-100">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Order #<?= htmlspecialchars($order['order_number']) ?></h2>
                    <p class="text-gray-500">Placed on <?= date('M j, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="text-right">
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
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold <?= $statusColor ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">Order Status</h3>
                <div class="relative">
                    <?php
                    $statuses = [
                        'pending' => ['label' => 'Order Placed', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'processing' => ['label' => 'Processing', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'shipped' => ['label' => 'Shipped', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'],
                        'delivered' => ['label' => 'Delivered', 'icon' => 'M5 13l4 4L19 7']
                    ];
                    
                    $currentStatusIndex = array_search($order['status'], array_keys($statuses));
                    $isCancelled = $order['status'] === 'cancelled';
                    ?>
                    
                    <?php if ($isCancelled): ?>
                    <div class="flex items-center gap-4 p-4 bg-red-50 rounded-xl">
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-red-800">Order Cancelled</p>
                            <p class="text-sm text-red-600">This order has been cancelled</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="space-y-6">
                        <?php foreach ($statuses as $key => $status): 
                            $index = array_search($key, array_keys($statuses));
                            $isCompleted = $index <= $currentStatusIndex;
                            $isCurrent = $key === $order['status'];
                        ?>
                        <div class="flex items-start gap-4">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center <?= $isCompleted ? 'bg-indigo-600' : 'bg-gray-200' ?>">
                                    <svg class="w-6 h-6 <?= $isCompleted ? 'text-white' : 'text-gray-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $status['icon'] ?>"></path>
                                    </svg>
                                </div>
                                <?php if ($index < count($statuses) - 1): ?>
                                <div class="absolute left-6 top-12 w-0.5 h-6 <?= $isCompleted ? 'bg-indigo-600' : 'bg-gray-200' ?>"></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 pt-2">
                                <p class="font-semibold <?= $isCurrent ? 'text-indigo-600' : ($isCompleted ? 'text-gray-800' : 'text-gray-400') ?>">
                                    <?= $status['label'] ?>
                                </p>
                                <?php if ($isCurrent): ?>
                                <p class="text-sm text-gray-500 mt-1">Your order is currently being <?= strtolower($status['label']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border-t-2 border-gray-100 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
                <div class="space-y-4">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                        <img src="<?= ImageUpload::getImageUrl($item['product_image'], true) ?>" 
                             alt="<?= htmlspecialchars($item['product_name']) ?>"
                             onerror="this.src='assets/images/placeholder.jpg'"
                             class="w-20 h-20 object-cover rounded-xl">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h4>
                            <p class="text-sm text-gray-500 mt-1">Quantity: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-800">₹<?= number_format($item['subtotal'], 2) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-6 pt-6 border-t-2 border-gray-100 flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-800">Total Amount</span>
                    <span class="text-2xl font-bold text-indigo-600">₹<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="border-t-2 border-gray-100 mt-6 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Shipping Address</h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-700"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if ($currentUser): ?>
            <a href="my-orders.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 transition">
                        <svg class="w-6 h-6 text-indigo-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">My Orders</h3>
                        <p class="text-sm text-gray-500">View all your orders</p>
                    </div>
                </div>
            </a>
            <?php endif; ?>
            
            <a href="support.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center group-hover:bg-pink-600 transition">
                        <svg class="w-6 h-6 text-pink-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold">Need Help?</h3>
                        <p class="text-sm text-gray-500">Contact support</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</main>

</body>
</html>
