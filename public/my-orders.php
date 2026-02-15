<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/cart.php';
require_once __DIR__ . '/../src/models/Order.php';
require_once __DIR__ . '/../src/image-upload.php';

// Require user to be logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$cartCount = getCartCount();
$currentPage = 'my-orders';
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Get user's orders
$userOrders = Order::getByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="assets/js/toast.js"></script>
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
            <?php if ($cartCount > 0): ?>
            <span class="ml-auto bg-white/30 px-2 py-1 rounded-full text-xs"><?= $cartCount ?></span>
            <?php endif; ?>
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
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-12 mb-6 text-white">
        <h1 class="text-4xl font-bold mb-4">My Orders</h1>
        <p class="text-xl text-indigo-100">Track and manage your orders</p>
    </div>

    <?php if (empty($userOrders)): ?>
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">No orders yet</h2>
        <p class="text-gray-500 mb-6">Start shopping to see your orders here</p>
        <a href="index.php" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
            Start Shopping
        </a>
    </div>
    <?php else: ?>
    <!-- Orders List -->
    <div class="space-y-4">
        <?php foreach ($userOrders as $order): ?>
        <div class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-lg transition">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h3 class="font-bold text-lg mb-1">Order #<?= htmlspecialchars($order['order_number']) ?></h3>
                    <p class="text-sm text-gray-500">Placed on <?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div class="mt-3 md:mt-0">
                    <?php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'shipped' => 'bg-purple-100 text-purple-700',
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700'
                    ];
                    $statusColor = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold <?= $statusColor ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-4 mt-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Amount</p>
                        <p class="text-2xl font-bold text-indigo-600">₹<?= number_format($order['total_amount'], 2) ?></p>
                    </div>
                    <div class="flex gap-2">
                        <a href="order-details.php?id=<?= $order['id'] ?>" 
                           class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                            View Details
                        </a>
                        <?php if ($order['status'] === 'pending'): ?>
                        <button onclick="cancelOrder(<?= $order['id'] ?>)" 
                                class="px-4 py-2 border-2 border-red-500 text-red-500 font-semibold rounded-xl hover:bg-red-50 transition">
                            Cancel
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<script>
function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) return;
    
    fetch('cancel-order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `order_id=${orderId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast.success('Order cancelled successfully');
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(data.message || 'Failed to cancel order');
        }
    })
    .catch(err => toast.error('Failed to cancel order'));
}
</script>

</body>
</html>
