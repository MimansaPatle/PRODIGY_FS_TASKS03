    </div><!-- End page-content -->

<!-- Footer -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-section">
            <h4><?= SITE_NAME ?></h4>
            <p style="color: var(--gray); font-size: 0.9rem; line-height: 1.6;">
                Fresh, local products delivered to your door. Supporting local farmers and artisans.
            </p>
        </div>
        
        <div class="footer-section">
            <h4>Shop</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>">All Products</a></li>
                <li><a href="<?= SITE_URL ?>?category=5">Fresh Produce</a></li>
                <li><a href="<?= SITE_URL ?>?category=6">Bakery</a></li>
                <li><a href="<?= SITE_URL ?>?category=7">Dairy</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Support</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>track-order.php">Track Order</a></li>
                <li><a href="<?= SITE_URL ?>support.php">Help Center</a></li>
                <li><a href="#">Shipping Info</a></li>
                <li><a href="#">Returns</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Account</h4>
            <ul>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?= SITE_URL ?>profile.php">My Profile</a></li>
                    <li><a href="<?= SITE_URL ?>my-orders.php">My Orders</a></li>
                    <li><a href="<?= SITE_URL ?>wishlist.php">Wishlist</a></li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>login.php">Login</a></li>
                    <li><a href="<?= SITE_URL ?>register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
    </div>
</footer>

</main><!-- End main-content -->

<script>
// Sidebar toggle for mobile
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');

sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1024) {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});
</script>

<script src="<?= SITE_URL ?>assets/js/main.js"></script>
</body>
</html>