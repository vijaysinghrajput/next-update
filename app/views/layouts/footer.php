    <!-- Bottom Tab Navigation -->
    <div class="bottom-tabs">
        <a href="<?php echo base_url(); ?>" class="tab-item <?php echo (basename($_SERVER['REQUEST_URI']) == '' || basename($_SERVER['REQUEST_URI']) == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?php echo base_url('about'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/about') !== false ? 'active' : ''; ?>">
            <i class="fas fa-info-circle"></i>
            <span>About</span>
        </a>
        <?php if (session('user_id')): ?>
            <a href="<?php echo session('is_admin') ? base_url('admin') : base_url('dashboard'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || strpos($_SERVER['REQUEST_URI'], '/admin') !== false ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo base_url('my-news'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/my-news') !== false ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i>
                <span>My News</span>
                <span class="badge">2</span>
            </a>
            <a href="<?php echo base_url('profile'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
        <?php else: ?>
            <a href="<?php echo base_url('login'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/login') !== false ? 'active' : ''; ?>">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
            <a href="<?php echo base_url('signup'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/signup') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i>
                <span>Sign Up</span>
            </a>
            <a href="<?php echo base_url('contact'); ?>" class="tab-item <?php echo strpos($_SERVER['REQUEST_URI'], '/contact') !== false ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>Contact</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><?php echo config('app_name'); ?></h5>
                    <p><?php echo config('app_description'); ?></p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo base_url(); ?>" class="text-light">Home</a></li>
                        <li><a href="<?php echo base_url('about'); ?>" class="text-light">About</a></li>
                        <li><a href="<?php echo base_url('contact'); ?>" class="text-light">Contact</a></li>
                        <li><a href="<?php echo base_url('privacy'); ?>" class="text-light">Privacy</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>User</h6>
                    <ul class="list-unstyled">
                        <?php if (session('user_id')): ?>
                            <li><a href="<?php echo base_url('dashboard'); ?>" class="text-light">Dashboard</a></li>
                            <li><a href="<?php echo base_url('my-news'); ?>" class="text-light">My News</a></li>
                            <li><a href="<?php echo base_url('kyc-verification'); ?>" class="text-light">KYC</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo base_url('login'); ?>" class="text-light">Login</a></li>
                            <li><a href="<?php echo base_url('signup'); ?>" class="text-light">Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6>Contact Info</h6>
                    <p class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i><?php echo config('contact_address'); ?>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-phone me-2"></i><?php echo config('contact_phone'); ?>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2"></i><?php echo config('contact_email'); ?>
                    </p>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo config('app_name'); ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <small>Powered by <?php echo config('admin_channel_name'); ?></small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // News filtering functionality
        function filterNews(type, value) {
            const newsCards = document.querySelectorAll('.news-card');
            newsCards.forEach(card => {
                if (type === 'all' || card.dataset[type] === value) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        // Copy referral code
        function copyReferralCode() {
            const referralCode = document.getElementById('referralCode');
            referralCode.select();
            referralCode.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-info')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
        
        // Mobile optimization
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-optimized');
        }
        
        // Mobile Drawer Functionality
        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            
            console.log('Toggle drawer clicked'); // Debug log
            console.log('Drawer element:', drawer); // Debug log
            console.log('Overlay element:', overlay); // Debug log
            
            if (drawer && overlay) {
                drawer.classList.toggle('show');
                overlay.classList.toggle('show');
                console.log('Drawer classes:', drawer.classList.toString()); // Debug log
            } else {
                console.error('Drawer or overlay element not found');
            }
        }
        
        // Close drawer when clicking overlay
        document.getElementById('drawerOverlay').addEventListener('click', function() {
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            
            drawer.classList.remove('show');
            overlay.classList.remove('show');
        });
        
        // Close drawer when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            const toggleBtn = event.target.closest('.mobile-drawer-toggle');
            
            if (window.innerWidth <= 768 && !drawer.contains(event.target) && !toggleBtn) {
                drawer.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
        
        // Close drawer when window is resized to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const drawer = document.getElementById('mobileDrawer');
                const overlay = document.getElementById('drawerOverlay');
                
                drawer.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
        
        // Add touch feedback for drawer links
        document.addEventListener('DOMContentLoaded', function() {
            const drawerLinks = document.querySelectorAll('.drawer-nav .nav-link');
            drawerLinks.forEach(link => {
                link.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                link.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Add touch feedback for bottom tabs
            const tabItems = document.querySelectorAll('.bottom-tabs .tab-item');
            tabItems.forEach(tab => {
                tab.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                });
                tab.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });
            });
            
            // Alternative drawer toggle method using event listeners
            const menuToggle = document.getElementById('menuToggleBtn');
            if (menuToggle) {
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Menu toggle clicked via event listener');
                    toggleMobileDrawer();
                });
                
                // Also add touch event for mobile
                menuToggle.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Menu toggle touched');
                    toggleMobileDrawer();
                });
            }
            
            // Debug: Check if elements exist
            console.log('Menu toggle element:', document.getElementById('menuToggleBtn'));
            console.log('Drawer element:', document.getElementById('mobileDrawer'));
            console.log('Overlay element:', document.getElementById('drawerOverlay'));
            
            // Test drawer functionality
            setTimeout(function() {
                const drawer = document.getElementById('mobileDrawer');
                const overlay = document.getElementById('drawerOverlay');
                if (drawer && overlay) {
                    console.log('Drawer elements found, testing...');
                    // Test if we can add classes
                    drawer.classList.add('test-class');
                    if (drawer.classList.contains('test-class')) {
                        console.log('Drawer class manipulation works');
                        drawer.classList.remove('test-class');
                    }
                } else {
                    console.error('Drawer elements not found');
                }
            }, 1000);
        });
        
        // Show notifications
        function showNotifications() {
            // This would typically show a notifications modal or dropdown
            alert('Notifications feature coming soon!');
        }
        
        // Update page title dynamically
        function updatePageTitle(title) {
            const pageTitleElement = document.getElementById('pageTitle');
            if (pageTitleElement) {
                pageTitleElement.textContent = title;
            }
        }
        
        // Update notification count
        function updateNotificationCount(count) {
            const notificationCount = document.getElementById('notificationCount');
            if (notificationCount) {
                if (count > 0) {
                    notificationCount.textContent = count;
                    notificationCount.style.display = 'flex';
                } else {
                    notificationCount.style.display = 'none';
                }
            }
        }
        
        // Simulate notification updates (for demo)
        setInterval(function() {
            const randomCount = Math.floor(Math.random() * 5);
            updateNotificationCount(randomCount);
        }, 10000);
        
        // Test drawer function (for debugging)
        function testDrawer() {
            console.log('Testing drawer...');
            const drawer = document.getElementById('mobileDrawer');
            const overlay = document.getElementById('drawerOverlay');
            
            if (drawer && overlay) {
                drawer.classList.add('show');
                overlay.classList.add('show');
                console.log('Drawer should be visible now');
                
                setTimeout(function() {
                    drawer.classList.remove('show');
                    overlay.classList.remove('show');
                    console.log('Drawer closed after 3 seconds');
                }, 3000);
            } else {
                console.error('Drawer elements not found for test');
            }
        }
        
        // Make test function globally available
        window.testDrawer = testDrawer;
    </script>
</body>
</html>
