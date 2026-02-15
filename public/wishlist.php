<?php
ob_start();

require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/cart.php';
require_once __DIR__ . '/../src/models/Wishlist.php';
require_once __DIR__ . '/../src/image-upload.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser();
$wishlist = new Wishlist();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'remove':
                $productId = $_POST['product_id'] ?? null;
                if ($productId) {
                    $result = $wishlist->removeFromWishlist($currentUser['id'], $productId);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product ID required']);
                }
                exit;
                
            case 'move_to_cart':
                $productId = $_POST['product_id'] ?? null;
                if ($productId) {
                    $result = $wishlist->moveToCart($currentUser['id'], $productId, 1);
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product ID required']);
                }
                exit;
                
            case 'clear_all':
                $result = $wishlist->clearWishlist($currentUser['id']);
                echo json_encode($result);
                exit;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
        exit;
    }
}

ob_end_clean();

$cartCount = getCartCount();
$currentPage = 'wishlist';
$wishlistItems = $wishlist->getUserWishlist($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - <?= SITE_NAME ?></title>
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
        
        <a href="my-orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/80 hover:bg-white/10 hover:text-white mb-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span class="font-medium">My Orders</span>
        </a>
        
        <a href="wishlist.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span class="font-medium">Wishlist</span>
            <?php if (count($wishlistItems) > 0): ?>
            <span class="ml-auto bg-white/30 px-2 py-1 rounded-full text-xs"><?= count($wishlistItems) ?></span>
            <?php endif; ?>
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
    <div class="bg-gradient-to-r from-pink-600 to-rose-600 rounded-2xl p-12 mb-6 text-white">
        <h1 class="text-4xl font-bold mb-4">My Wishlist</h1>
        <p class="text-xl text-pink-100">Save your favorite items for later</p>
    </div>

    <?php if (empty($wishlistItems)): ?>
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Your wishlist is empty</h2>
        <p class="text-gray-500 mb-6">Add items you love to your wishlist</p>
        <a href="index.php" class="inline-block px-6 py-3 bg-pink-600 text-white font-semibold rounded-xl hover:bg-pink-700 transition">
            Start Shopping
        </a>
    </div>
    <?php else: ?>
    <!-- Wishlist Items -->
    <div class="flex justify-between items-center mb-6">
        <p class="text-gray-600"><?= count($wishlistItems) ?> item(s) in your wishlist</p>
        <button onclick="clearWishlist()" class="text-red-500 hover:text-red-700 font-semibold">
            Clear All
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($wishlistItems as $item): ?>
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">
            <div class="aspect-square overflow-hidden bg-gray-100 relative">
                <img src="<?= ImageUpload::getImageUrl($item['image'] ?? '', true) ?>" 
                     alt="<?= htmlspecialchars($item['name']) ?>"
                     onerror="this.src='assets/images/placeholder.jpg'"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <button onclick="removeFromWishlist(<?= $item['product_id'] ?>)" 
                        class="absolute top-3 right-3 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 transition">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div class="text-xs font-semibold text-indigo-600 uppercase mb-2"><?= htmlspecialchars($item['category_name'] ?? 'Product') ?></div>
                <h3 class="font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($item['name']) ?></h3>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl font-bold text-pink-600">₹<?= number_format($item['price'], 2) ?></span>
                </div>
                <button onclick="moveToCart(<?= $item['product_id'] ?>)" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                    Move to Cart
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<script>
function removeFromWishlist(productId) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=remove&product_id=${productId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast.success('Item removed from wishlist');
            setTimeout(() => location.reload(), 800);
        } else {
            toast.error(data.message || 'Failed to remove item');
        }
    })
    .catch(err => toast.error('Failed to remove item'));
}

function moveToCart(productId) {
    fetch('wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=move_to_cart&product_id=${productId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast.success('Item moved to cart!');
            setTimeout(() => location.reload(), 1000);
        } else {
            toast.error(data.message || 'Failed to move item');
        }
    })
    .catch(err => toast.error('Failed to move item'));
}

function clearWishlist() {
    if (!confirm('Remove all items from wishlist?')) return;
    
    fetch('wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=clear_all'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            toast.success('Wishlist cleared');
            setTimeout(() => location.reload(), 800);
        } else {
            toast.error(data.message || 'Failed to clear wishlist');
        }
    })
    .catch(err => toast.error('Failed to clear wishlist'));
}
</script>

</body>
</html>
