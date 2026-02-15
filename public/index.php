<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/cart.php';
require_once __DIR__ . '/../src/models/Product.php';
require_once __DIR__ . '/../src/models/Category.php';
require_once __DIR__ . '/../src/models/Wishlist.php';
require_once __DIR__ . '/../src/image-upload.php';

$cartCount = getCartCount();
$currentPage = 'index';
$currentUser = getCurrentUser();

// Get user's wishlist items if logged in
$wishlistItems = [];
if ($currentUser) {
    $wishlist = new Wishlist();
    $userWishlist = $wishlist->getUserWishlist($currentUser['id']);
    $wishlistItems = array_column($userWishlist, 'product_id');
}

// Get filters from URL
$filters = [
    'category' => $_GET['category'] ?? '',
    'search' => $_GET['search'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest',
];

// Get products and categories
$products = Product::getAll($filters);
$categories = Category::getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Home</title>
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
        <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
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
    <!-- Top Bar -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1 max-w-xl">
                <form action="" method="GET" class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" placeholder="Search products..." 
                           value="<?= htmlspecialchars($filters['search']) ?>"
                           class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                </form>
            </div>
            <a href="cart.php" class="relative w-12 h-12 bg-gray-100 hover:bg-indigo-600 hover:text-white rounded-xl flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <?php if ($cartCount > 0): ?>
                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Hero -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-2xl p-12 mb-6 text-white">
        <h1 class="text-5xl font-bold mb-4">Welcome to <?= SITE_NAME ?></h1>
        <p class="text-xl text-indigo-100">Fresh, local products delivered to your door</p>
    </div>

    <!-- Categories -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Categories</h3>
        <div class="flex flex-wrap gap-2">
            <a href="?" class="px-4 py-2 rounded-xl <?= empty($filters['category']) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> transition">
                All Products
            </a>
            <?php foreach ($categories as $category): ?>
            <a href="?category=<?= $category['id'] ?>" 
               class="px-4 py-2 rounded-xl <?= $filters['category'] == $category['id'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> transition">
                <?= htmlspecialchars($category['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($products as $product): ?>
        <?php $isInWishlist = in_array($product['id'], $wishlistItems); ?>
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">
            <div class="aspect-square overflow-hidden bg-gray-100 relative">
                <img src="<?= ImageUpload::getImageUrl($product['image'] ?? '', true) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     onerror="this.src='assets/images/placeholder.jpg'"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                
                <!-- Wishlist Heart Icon -->
                <?php if ($currentUser): ?>
                <button onclick="toggleWishlist(<?= $product['id'] ?>, this)" 
                        data-in-wishlist="<?= $isInWishlist ? 'true' : 'false' ?>"
                        class="absolute top-3 right-3 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 <?= $isInWishlist ? 'bg-pink-500 text-white' : 'bg-white/90 text-gray-600 hover:bg-pink-500 hover:text-white' ?> shadow-lg">
                    <svg class="w-5 h-5 <?= $isInWishlist ? 'fill-current' : '' ?>" fill="<?= $isInWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
            <div class="p-4">
                <div class="text-xs font-semibold text-indigo-600 uppercase mb-2"><?= htmlspecialchars($product['category_name'] ?? 'Product') ?></div>
                <h3 class="font-bold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($product['name']) ?></h3>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl font-bold text-indigo-600">₹<?= number_format($product['price'], 2) ?></span>
                    <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                    <span class="text-sm text-gray-400 line-through">₹<?= number_format($product['original_price'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <button onclick="addToCart(<?= $product['id'] ?>)" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                    Add to Cart
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">No products found</h2>
        <p class="text-gray-500 mb-6">Try adjusting your filters or browse all products</p>
        <a href="?" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
            View All Products
        </a>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-white rounded-2xl shadow-sm mt-12 p-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="font-bold mb-4"><?= SITE_NAME ?></h4>
                <p class="text-gray-600 text-sm">Fresh, local products delivered to your door.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="?" class="text-gray-600 hover:text-indigo-600">All Products</a></li>
                    <li><a href="?category=5" class="text-gray-600 hover:text-indigo-600">Fresh Produce</a></li>
                    <li><a href="?category=6" class="text-gray-600 hover:text-indigo-600">Bakery</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="track-order.php" class="text-gray-600 hover:text-indigo-600">Track Order</a></li>
                    <li><a href="support.php" class="text-gray-600 hover:text-indigo-600">Help Center</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Account</h4>
                <ul class="space-y-2 text-sm">
                    <?php if ($currentUser): ?>
                    <li><a href="profile.php" class="text-gray-600 hover:text-indigo-600">My Profile</a></li>
                    <li><a href="my-orders.php" class="text-gray-600 hover:text-indigo-600">My Orders</a></li>
                    <?php else: ?>
                    <li><a href="login.php" class="text-gray-600 hover:text-indigo-600">Login</a></li>
                    <li><a href="register.php" class="text-gray-600 hover:text-indigo-600">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="border-t pt-6 text-center text-sm text-gray-500">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.
        </div>
    </footer>
</main>

<script>
function addToCart(productId) {
    fetch('cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=add&product_id=${productId}&quantity=1`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.toast.success('Product added to cart!');
            setTimeout(() => location.reload(), 1000);
        } else {
            window.toast.error(data.message || 'Failed to add product');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        window.toast.error('Failed to add product to cart');
    });
}

function toggleWishlist(productId, button) {
    const isInWishlist = button.getAttribute('data-in-wishlist') === 'true';
    
    fetch('wishlist-api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle&product_id=${productId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update button state
            const newState = data.action === 'added';
            button.setAttribute('data-in-wishlist', newState ? 'true' : 'false');
            
            // Update button appearance
            const svg = button.querySelector('svg');
            if (data.action === 'removed') {
                // Removed from wishlist
                button.className = 'absolute top-3 right-3 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 bg-white/90 text-gray-600 hover:bg-pink-500 hover:text-white shadow-lg';
                svg.setAttribute('fill', 'none');
                svg.classList.remove('fill-current');
                window.toast.success('Removed from wishlist');
            } else {
                // Added to wishlist
                button.className = 'absolute top-3 right-3 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-200 bg-pink-500 text-white shadow-lg';
                svg.setAttribute('fill', 'currentColor');
                svg.classList.add('fill-current');
                window.toast.success('Added to wishlist!');
            }
        } else {
            window.toast.error(data.message || 'Failed to update wishlist');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        window.toast.error('Failed to update wishlist');
    });
}
</script>

<script>
// Inline Toast Notification System
(function() {
    class Toast {
        constructor() {
            this.container = null;
            this.createContainer();
        }

        createContainer() {
            if (!document.getElementById('toast-container')) {
                this.container = document.createElement('div');
                this.container.id = 'toast-container';
                this.container.style.cssText = 'position: fixed; top: 24px; right: 24px; z-index: 9999;';
                document.body.appendChild(this.container);
            } else {
                this.container = document.getElementById('toast-container');
            }
        }

        show(message, type = 'success', duration = 3000) {
            if (!this.container) this.createContainer();

            const toast = document.createElement('div');
            toast.style.cssText = 'transform: translateX(400px); opacity: 0; transition: all 0.3s ease-in-out; margin-bottom: 12px;';
            
            const colors = {
                success: '#10b981',
                error: '#ef4444',
                warning: '#f59e0b',
                info: '#3b82f6'
            };

            const icons = {
                success: '<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
                error: '<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
                warning: '<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg style="width: 24px; height: 24px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            const bgColor = colors[type] || colors.success;
            const icon = icons[type] || icons.success;

            toast.innerHTML = `
                <div style="background-color: ${bgColor}; color: white; padding: 16px 24px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); display: flex; align-items: center; gap: 12px; min-width: 300px; max-width: 400px;">
                    ${icon}
                    <p style="flex: 1; font-weight: 500; margin: 0; font-family: Inter, sans-serif;">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" style="flex-shrink: 0; background: rgba(255,255,255,0.2); border: none; border-radius: 8px; padding: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            `;

            this.container.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 10);

            if (duration > 0) {
                setTimeout(() => {
                    toast.style.transform = 'translateX(400px)';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }

            return toast;
        }

        success(message, duration = 3000) { return this.show(message, 'success', duration); }
        error(message, duration = 4000) { return this.show(message, 'error', duration); }
        warning(message, duration = 3500) { return this.show(message, 'warning', duration); }
        info(message, duration = 3000) { return this.show(message, 'info', duration); }
    }

    window.toast = new Toast();
})();
</script>

</body>
</html>
