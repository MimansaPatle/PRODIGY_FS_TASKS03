<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/cart.php';
require_once __DIR__ . '/../src/image-upload.php';
require_once __DIR__ . '/../src/models/Product.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $productId = intval($_POST['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 1);
            
            if ($productId <= 0 || $quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
                exit;
            }
            
            $product = Product::getById($productId);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            if ($product['stock'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
                exit;
            }
            
            addToCart($productId, $quantity);
            echo json_encode([
                'success' => true, 
                'message' => 'Product added to cart',
                'cart_count' => getCartCount()
            ]);
            exit;
            
        case 'update':
            $productId = intval($_POST['product_id'] ?? 0);
            $quantity = intval($_POST['quantity'] ?? 0);
            
            updateCart($productId, $quantity);
            echo json_encode([
                'success' => true,
                'cart_count' => getCartCount(),
                'cart_total' => getCartTotal()
            ]);
            exit;
            
        case 'remove':
            $productId = intval($_POST['product_id'] ?? 0);
            removeFromCart($productId);
            echo json_encode([
                'success' => true,
                'cart_count' => getCartCount(),
                'cart_total' => getCartTotal()
            ]);
            exit;
            
        case 'get_items':
            $items = getCartItems();
            $total = getCartTotal();
            echo json_encode([
                'success' => true,
                'items' => $items,
                'total' => $total
            ]);
            exit;
            
        case 'clear':
            clearCart();
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            exit;
    }
}

$cartCount = getCartCount();
$currentPage = 'cart';
$currentUser = getCurrentUser();
$cartItems = getCartItems();
$cartTotal = getCartTotal();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        
        <a href="cart.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
                <p class="text-gray-500 mt-1">Review your items before checkout</p>
            </div>
            <a href="index.php" class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition">
                Continue Shopping
            </a>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
    <!-- Empty Cart -->
    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Your cart is empty</h2>
        <p class="text-gray-500 mb-6">Add some products to get started</p>
        <a href="index.php" class="inline-block px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
            Start Shopping
        </a>
    </div>
    <?php else: ?>
    <!-- Cart Items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <?php foreach ($cartItems as $item): ?>
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex gap-6">
                    <img src="<?= ImageUpload::getImageUrl($item['image'], true) ?>" 
                         alt="<?= htmlspecialchars($item['name']) ?>"
                         onerror="this.src='assets/images/placeholder.jpg'"
                         class="w-32 h-32 object-cover rounded-xl">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="text-gray-500 text-sm mb-4">₹<?= number_format($item['price'], 2) ?> each</p>
                        <div class="flex items-center gap-3">
                            <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['cart_quantity'] - 1 ?>)" 
                                    class="w-8 h-8 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="font-bold text-lg w-12 text-center"><?= $item['cart_quantity'] ?></span>
                            <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['cart_quantity'] + 1 ?>)" 
                                    class="w-8 h-8 flex items-center justify-center border-2 border-gray-300 rounded-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-indigo-600 mb-4">₹<?= number_format($item['subtotal'], 2) ?></p>
                        <button onclick="removeItem(<?= $item['id'] ?>)" 
                                class="text-red-500 hover:text-red-700 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h2 class="text-xl font-bold mb-6">Order Summary</h2>
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">₹<?= number_format($cartTotal, 2) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="font-semibold">Free</span>
                    </div>
                    <div class="border-t-2 pt-4 flex justify-between text-xl font-bold">
                        <span>Total</span>
                        <span class="text-indigo-600">₹<?= number_format($cartTotal, 2) ?></span>
                    </div>
                </div>
                <a href="checkout.php" class="block w-full text-center px-6 py-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition mb-3">
                    Proceed to Checkout
                </a>
                <button onclick="clearCart()" class="block w-full text-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:border-red-500 hover:text-red-500 transition">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeItem(productId);
        return;
    }
    
    fetch('cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
}

function removeItem(productId) {
    if (!confirm('Remove this item from cart?')) return;
    
    fetch('cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=remove&product_id=${productId}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
}

function clearCart() {
    if (!confirm('Clear all items from cart?')) return;
    
    fetch('cart.php?action=clear')
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
</script>

</body>
</html>
