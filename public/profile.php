<?php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Address.php';

// Require login
if (!isLoggedIn()) {
    header('Location: login.php?redirect=profile.php');
    exit;
}

$currentUser = getCurrentUser();
$addresses = [];
$error = '';
$success = '';

// Try to get addresses, handle if table doesn't exist
try {
    $addresses = Address::getByUserId($currentUser['id']);
} catch (Exception $e) {
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        $error = 'Address book not set up yet. <a href="create-address-table.php" class="underline text-emerald-600">Click here to set it up</a>.';
    } else {
        $error = 'Error loading addresses: ' . $e->getMessage();
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($name)) {
            $error = 'Name is required';
        } else {
            // Update profile data
            $result = User::updateProfile($currentUser['id'], [
                'name' => $name,
                'phone' => $phone,
                'address' => $currentUser['address'] // Keep existing address
            ]);
            
            if ($result) {
                $success = 'Profile updated successfully!';
                $currentUser = getCurrentUser(); // Refresh data
            } else {
                $error = 'Failed to update profile';
            }
        }
    }
    
    elseif ($_POST['action'] === 'add_address') {
        $addressData = [
            'user_id' => $currentUser['id'],
            'label' => trim($_POST['label'] ?? ''),
            'name' => trim($_POST['address_name'] ?? ''),
            'phone' => trim($_POST['address_phone'] ?? ''),
            'address_line_1' => trim($_POST['address_line_1'] ?? ''),
            'address_line_2' => trim($_POST['address_line_2'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'state' => trim($_POST['state'] ?? ''),
            'postal_code' => trim($_POST['postal_code'] ?? ''),
            'country' => trim($_POST['country'] ?? 'India'),
            'is_default' => isset($_POST['is_default'])
        ];
        
        if (empty($addressData['label']) || empty($addressData['name']) || empty($addressData['phone']) || 
            empty($addressData['address_line_1']) || empty($addressData['city']) || 
            empty($addressData['state']) || empty($addressData['postal_code'])) {
            $error = 'Please fill in all required address fields';
        } else {
            $result = Address::create($addressData);
            if ($result['success']) {
                $success = 'Address added successfully!';
                $addresses = Address::getByUserId($currentUser['id']); // Refresh addresses
            } else {
                $error = $result['error'];
            }
        }
    }
    
    elseif ($_POST['action'] === 'delete_address') {
        $addressId = $_POST['address_id'] ?? 0;
        if (Address::delete($addressId)) {
            $success = 'Address deleted successfully!';
            $addresses = Address::getByUserId($currentUser['id']); // Refresh addresses
        } else {
            $error = 'Failed to delete address';
        }
    }
    
    elseif ($_POST['action'] === 'set_default') {
        $addressId = $_POST['address_id'] ?? 0;
        if (Address::setDefault($addressId, $currentUser['id'])) {
            $success = 'Default address updated!';
            $addresses = Address::getByUserId($currentUser['id']); // Refresh addresses
        } else {
            $error = 'Failed to update default address';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?= SITE_NAME ?></title>
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
        
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/15 text-white mb-2">
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
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Profile</h1>

    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-6">
        <?= $error ?>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6">
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Personal Information</h2>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="name" required
                                       value="<?= htmlspecialchars($currentUser['name']) ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <!-- Email (read-only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" value="<?= htmlspecialchars($currentUser['email']) ?>" 
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50" 
                                       readonly>
                                <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                            </div>
                            
                            <!-- Phone -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone"
                                       value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none"
                                       placeholder="+91 98765 43210">
                            </div>
                        </div>
                            
                        <!-- Password Change Section -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-800">Password</h3>
                                <a href="#" onclick="sendPasswordReset()" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Forgot Password
                                </a>
                            </div>
                            <p class="text-gray-600 text-sm">To change your password, click "Forgot Password" above. You'll receive an email with instructions to set a new password.</p>
                            
                            <!-- Password Reset Status -->
                            <div id="passwordResetStatus" class="hidden mt-4"></div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition">
                                Update Profile
                            </button>
                        </div>
                        </form>
                    </div>

            <!-- Address Book -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Address Book</h2>
                    <button onclick="toggleAddressForm()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-xl transition">
                        Add New Address
                    </button>
                </div>
                        
                <!-- Add Address Form (Hidden by default) -->
                <div id="addAddressForm" class="hidden mb-6 p-4 bg-gray-50 rounded-xl">
                    <form method="POST">
                        <input type="hidden" name="action" value="add_address">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Label *</label>
                                <select name="label" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                                    <option value="">Select Label</option>
                                    <option value="Home">Home</option>
                                    <option value="Office">Office</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name *</label>
                                <input type="text" name="address_name" required
                                       value="<?= htmlspecialchars($currentUser['name']) ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                <input type="tel" name="address_phone" required
                                       value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                <input type="text" name="address_line_1" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none"
                                       placeholder="Street address">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                <input type="text" name="address_line_2"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none"
                                       placeholder="Apartment, suite, etc.">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" name="city" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                <input type="text" name="state" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                                <input type="text" name="postal_code" required
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="country" value="India"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_default" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Set as default address</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-xl transition">
                                Save Address
                            </button>
                            <button type="button" onclick="toggleAddressForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-xl transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
                        
                <!-- Existing Addresses -->
                <div class="space-y-4">
                    <?php if (empty($addresses)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p>No addresses saved yet</p>
                        <p class="text-sm">Add your first address to make checkout faster</p>
                    </div>
                    <?php else: ?>
                    <?php foreach ($addresses as $address): ?>
                    <div class="border-2 border-gray-200 rounded-xl p-4 <?= $address['is_default'] ? 'ring-2 ring-indigo-500 bg-indigo-50' : '' ?>">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $address['is_default'] ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' ?>">
                                        <?= htmlspecialchars($address['label']) ?>
                                    </span>
                                    <?php if ($address['is_default']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                        Default
                                    </span>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($address['name']) ?></p>
                                <p class="text-gray-600"><?= htmlspecialchars($address['phone']) ?></p>
                                <p class="text-gray-600 mt-1"><?= htmlspecialchars(Address::getFormattedAddress($address)) ?></p>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <?php if (!$address['is_default']): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="set_default">
                                    <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                        Set Default
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
                                    <input type="hidden" name="action" value="delete_address">
                                    <input type="hidden" name="address_id" value="<?= $address['id'] ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Profile Summary Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Summary</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($currentUser['name']) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($currentUser['email']) ?></p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Saved Addresses</span>
                            <span class="text-sm font-medium text-gray-800"><?= count($addresses) ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Account Type</span>
                            <span class="text-sm font-medium text-gray-800 capitalize"><?= htmlspecialchars($currentUser['role']) ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-medium text-gray-800"><?= date('M Y', strtotime($currentUser['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    <a href="my-orders.php" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-xl text-center transition">
                        View My Orders
                    </a>
                    <a href="index.php" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-xl text-center transition">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

    <script>
        function toggleAddressForm() {
            const form = document.getElementById('addAddressForm');
            form.classList.toggle('hidden');
        }
        
        function sendPasswordReset() {
            const statusDiv = document.getElementById('passwordResetStatus');
            const userEmail = '<?= htmlspecialchars($currentUser['email']) ?>';
            
            // Show loading state
            statusDiv.className = 'mt-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm';
            statusDiv.innerHTML = 'Sending password reset email...';
            statusDiv.classList.remove('hidden');
            
            // Send AJAX request to forgot password endpoint
            fetch('forgot-password.php?email=' + encodeURIComponent(userEmail))
                .then(response => response.text())
                .then(data => {
                    // Check if the response contains success message
                    if (data.includes('Password reset instructions have been sent')) {
                        statusDiv.className = 'mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm';
                        statusDiv.innerHTML = 'Password reset instructions have been sent to ' + userEmail + '. Check your email inbox.';
                    } else {
                        statusDiv.className = 'mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm';
                        statusDiv.innerHTML = 'Failed to send password reset email. Please try again or contact support.';
                    }
                })
                .catch(error => {
                    statusDiv.className = 'mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm';
                    statusDiv.innerHTML = 'An error occurred. Please try again.';
                });
        }
    </script>
</body>
</html>