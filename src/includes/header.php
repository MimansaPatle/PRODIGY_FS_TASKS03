<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../cart.php';
require_once __DIR__ . '/../utils.php';

$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/custom.css">
    <style>
        /* Critical CSS - Inline to ensure it loads */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            background: #f8fafc !important;
            color: #1e293b !important;
        }
        .sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            width: 280px !important;
            height: 100vh !important;
            background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%) !important;
            padding: 2rem 0 !important;
            z-index: 1000 !important;
            overflow-y: auto !important;
        }
        .main-content {
            margin-left: 280px !important;
            min-height: 100vh !important;
            padding: 2rem !important;
        }
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%) !important; }
            .sidebar.active { transform: translateX(0) !important; }
            .main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h1>
            <div class="sidebar-logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <?= SITE_NAME ?>
        </h1>
    </div>
    
    <nav class="sidebar-nav">
        <a href="<?= SITE_URL ?>" class="<?= $currentPage == 'index' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Home</span>
        </a>
        
        <a href="<?= SITE_URL ?>cart.php" class="<?= $currentPage == 'cart' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span>Cart</span>
            <?php if ($cartCount > 0): ?>
                <span style="margin-left: auto; background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
        
        <?php if (isLoggedIn()): ?>
        <a href="<?= SITE_URL ?>my-orders.php" class="<?= $currentPage == 'my-orders' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span>My Orders</span>
        </a>
        
        <a href="<?= SITE_URL ?>wishlist.php" class="<?= $currentPage == 'wishlist' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span>Wishlist</span>
        </a>
        
        <a href="<?= SITE_URL ?>profile.php" class="<?= $currentPage == 'profile' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Profile</span>
        </a>
        <?php endif; ?>
        
        <a href="<?= SITE_URL ?>track-order.php" class="<?= $currentPage == 'track-order' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <span>Track Order</span>
        </a>
        
        <a href="<?= SITE_URL ?>support.php" class="<?= $currentPage == 'support' ? 'active' : '' ?>">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>Support</span>
        </a>
        
        <?php if (isAdmin()): ?>
        <div style="margin: 1.5rem 0; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.5rem;">
            <a href="<?= ADMIN_URL ?>" style="background: rgba(255,255,255,0.15);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Admin Panel</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    
    <?php if (isLoggedIn()): ?>
    <div class="sidebar-user">
        <div class="sidebar-user-info">
            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($_SESSION['user_name']) ?>
                </div>
                <a href="<?= SITE_URL ?>logout.php" style="font-size: 0.8rem; color: rgba(255,255,255,0.7); text-decoration: none;">
                    Logout
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="sidebar-user">
        <a href="<?= SITE_URL ?>login.php" class="btn btn-outline" style="width: 100%; justify-content: center; color: white; border-color: rgba(255,255,255,0.3);">
            Login / Register
        </a>
    </div>
    <?php endif; ?>
</aside>

<!-- Mobile Sidebar Toggle -->
<div class="sidebar-toggle" id="sidebarToggle">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</div>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="search-bar">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <form action="<?= SITE_URL ?>" method="GET" style="width: 100%;">
                <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </form>
        </div>
        
        <div class="top-bar-actions">
            <a href="<?= SITE_URL ?>cart.php" class="icon-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <?php if ($cartCount > 0): ?>
                    <span class="badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (hasFlashMessage('success')): ?>
        <div class="flash-message flash-success">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?= getFlashMessage('success') ?></span>
        </div>
    <?php endif; ?>

    <?php if (hasFlashMessage('error')): ?>
        <div class="flash-message flash-error">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?= getFlashMessage('error') ?></span>
        </div>
    <?php endif; ?>

    <?php if (hasFlashMessage('info')): ?>
        <div class="flash-message flash-info">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?= getFlashMessage('info') ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Content -->
    <div class="page-content"><?php