            </div>
            
            <!-- Infinite Scroll Trigger (removed old markup; handled in page) -->
            
            <!-- Bottom Spacing for Navigation -->
            <div class="bottom-spacing mobile-safe-bottom"></div>
        </main>
        
        <!-- Mobile Navigation -->
        <nav class="mobile-nav mobile-safe-bottom">
            <div class="nav-items">
                <?php if (session('user_id')): ?>
                    <?php if (session('is_admin')): ?>
                        <!-- Admin user navigation -->
                        
                        <!-- Home -->
                        <a href="<?php echo base_url('/'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/') !== false && $_SERVER['REQUEST_URI'] === '/') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                        
                        <!-- Admin Dashboard -->
                        <a href="<?php echo base_url('/admin/dashboard'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Admin</span>
                        </a>
                        
                        <!-- Users -->
                        <a href="<?php echo base_url('/admin/users'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                        
                        <!-- News -->
                        <a href="<?php echo base_url('/admin/news'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/news') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-newspaper"></i>
                            <span>News</span>
                        </a>
                        
                        <!-- Ads -->
                        <a href="<?php echo base_url('/admin/ads'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/ads') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-bullhorn"></i>
                            <span>Ads</span>
                        </a>
                        
                    <?php else: ?>
                        <!-- Regular user navigation -->
                        
                        <!-- Home -->
                        <a href="<?php echo base_url('/'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/') !== false && $_SERVER['REQUEST_URI'] === '/') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                        
                        
                        <!-- Dashboard -->
                        <a href="<?php echo base_url('/dashboard'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        
                        <!-- Post Ads -->
                        <a href="<?php echo base_url('/post-ad'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/post-ad') !== false) ? 'active' : ''; ?>">
                            <i class="fas fa-bullhorn"></i>
                            <span>Post Ads</span>
                        </a>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- Guest user navigation -->
                    
                    <!-- Home -->
                    <a href="<?php echo base_url('/'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/') !== false && $_SERVER['REQUEST_URI'] === '/') ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    
                    
                    <!-- Login -->
                    <a href="<?php echo base_url('/login'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/login') !== false) ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    
                    <!-- Signup -->
                    <a href="<?php echo base_url('/signup'); ?>" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], '/signup') !== false) ? 'active' : ''; ?>">
                        <i class="fas fa-user-plus"></i>
                        <span>Signup</span>
                    </a>
                    
                <?php endif; ?>
            </div>
        </nav>
        
        <!-- Floating Action Button -->
        <?php if (session('user_id')): ?>
            <div class="fab-container">
                <button class="fab" onclick="showFABMenu()" aria-label="Quick Actions">
                    <i class="fas fa-plus"></i>
                </button>
                
                <!-- FAB Menu -->
                <div class="fab-menu" id="fabMenu">
                    <a href="<?php echo base_url('/post-news'); ?>" class="fab-item" data-action="post-news">
                        <i class="fas fa-newspaper"></i>
                        <span>Post News</span>
                    </a>
                    <a href="<?php echo base_url('/post-ad'); ?>" class="fab-item" data-action="post-ad">
                        <i class="fas fa-bullhorn"></i>
                        <span>Post Ad</span>
                    </a>
                    <a href="<?php echo base_url('/buy-points'); ?>" class="fab-item" data-action="buy-points">
                        <i class="fas fa-coins"></i>
                        <span>Buy Points</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Toast Container -->
        <div class="toast-container" id="toastContainer"></div>
        
        <!-- Modal Container -->
        <div class="modal-container" id="modalContainer"></div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo config('app_url'); ?>/public/assets/js/mobile-app.js"></script>
    
    <!-- Mobile App JavaScript -->
    <script>
        // Mobile App Initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize mobile app features
            initializeMobileApp();
        });
        
        function initializeMobileApp() {
            // Setup event listeners
            setupEventListeners();
            
            // Setup pull to refresh
            setupPullToRefresh();
            
            // Setup infinite scroll
            setupInfiniteScroll();
            
            // Setup native features
            setupNativeFeatures();
            
            // Setup FAB menu
            setupFABMenu();
            
            // Setup search functionality
            setupSearch();
            
            // Setup notifications
            setupNotifications();
            
            // Setup profile menu
            setupProfile();
        }
        
        function setupEventListeners() {
            // Handle mobile app events
            document.addEventListener('mobileAppReady', function() {
                console.log('Mobile app is ready');
                hideLoading();
            });
            
            document.addEventListener('pullToRefresh', function() {
                refreshContent();
            });
            
            document.addEventListener('infiniteScroll', function() {
                loadMoreContent();
            });
            
            document.addEventListener('swipeLeft', function(e) {
                console.log('Swipe left detected', e.detail);
            });
            
            document.addEventListener('swipeRight', function(e) {
                console.log('Swipe right detected', e.detail);
            });
            
            document.addEventListener('keyboardOpen', function(e) {
                console.log('Keyboard opened', e.detail);
                adjustForKeyboard(true);
            });
            
            document.addEventListener('keyboardClose', function(e) {
                console.log('Keyboard closed', e.detail);
                adjustForKeyboard(false);
            });
            
            document.addEventListener('networkOnline', function() {
                showToast('Connection restored', 'success');
            });
            
            document.addEventListener('networkOffline', function() {
                showToast('No internet connection', 'warning');
            });
        }
        
        function setupPullToRefresh() {
            // Pull to refresh is handled by mobile-app.js
            // Override the refresh function for this page
            window.refreshContent = function() {
                showLoading('Refreshing...');
                
                // Simulate refresh
                setTimeout(() => {
                    hideLoading();
                    showToast('Content refreshed', 'success');
                    window.MobileApp.resetPullToRefresh();
                }, 1500);
            };
        }
        
        function setupInfiniteScroll() {
            // Infinite scroll is handled by mobile-app.js
            // Override the load more function for this page
            window.loadMoreContent = function() {
                showLoading('Loading more...');
                
                // Simulate loading more content
                setTimeout(() => {
                    hideLoading();
                    showToast('More content loaded', 'success');
                    window.MobileApp.resetInfiniteScroll();
                }, 1500);
            };
        }
        
        function setupNativeFeatures() {
            // Setup native app communication
            if (window.MobileApp.isWebView) {
                console.log('Running in WebView');
                
                // Get device info
                window.MobileApp.nativeGetDeviceInfo().then(deviceInfo => {
                    console.log('Device info:', deviceInfo);
                    
                    // Set status bar color based on theme
                    if (deviceInfo.platform === 'android') {
                        window.MobileApp.nativeSetStatusBarColor('#667eea');
                        window.MobileApp.nativeSetNavigationBarColor('#667eea');
                    }
                });
            }
        }
        
        function setupFABMenu() {
            const fab = document.querySelector('.fab');
            const fabMenu = document.getElementById('fabMenu');
            
            if (fab && fabMenu) {
                fab.addEventListener('click', function(e) {
                    e.preventDefault();
                    showFABMenu();
                });
                
                // Close FAB menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!fab.contains(e.target) && !fabMenu.contains(e.target)) {
                        hideFABMenu();
                    }
                });
            }
        }
        
        function showFABMenu() {
            const fabMenu = document.getElementById('fabMenu');
            if (fabMenu) {
                fabMenu.classList.add('show');
                window.MobileApp.hapticFeedback('light');
            }
        }
        
        function hideFABMenu() {
            const fabMenu = document.getElementById('fabMenu');
            if (fabMenu) {
                fabMenu.classList.remove('show');
            }
        }
        
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (query.length > 2) {
                        performSearch(query);
                    }
                });
                
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        performSearch(this.value.trim());
                    }
                });
            }
        }
        
        function toggleSearch() {
            const searchContainer = document.getElementById('searchContainer');
            const searchInput = document.getElementById('searchInput');
            
            if (searchContainer) {
                if (searchContainer.style.display === 'none') {
                    searchContainer.style.display = 'block';
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                } else {
                    searchContainer.style.display = 'none';
                }
            }
        }
        
        function performSearch(query = null) {
            const searchInput = document.getElementById('searchInput');
            const searchQuery = query || (searchInput ? searchInput.value.trim() : '');
            
            if (searchQuery) {
                window.location.href = `<?php echo base_url('/search'); ?>?q=${encodeURIComponent(searchQuery)}`;
            }
        }
        
        function setupNotifications() {
            // Check for new notifications periodically
            if (<?php echo session('user_id') ? 'true' : 'false'; ?>) {
                checkNotifications();
                setInterval(checkNotifications, 30000); // Check every 30 seconds
            }
        }
        
        function checkNotifications() {
            // Fetch notifications via AJAX
            fetch('<?php echo base_url('/api/notifications'); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        updateNotificationBadge(data.count);
                    }
                })
                .catch(error => console.error('Error checking notifications:', error));
        }
        
        function updateNotificationBadge(count) {
            const notificationBtn = document.querySelector('.header-btn[aria-label="Notifications"]');
            if (notificationBtn) {
                let badge = notificationBtn.querySelector('.notification-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'notification-badge';
                    notificationBtn.appendChild(badge);
                }
                badge.textContent = count;
            }
        }
        
        function showNotifications() {
            // Show notifications modal or navigate to notifications page
            window.location.href = '<?php echo base_url('/notifications'); ?>';
        }
        
        function setupProfile() {
            // Setup profile menu functionality
        }
        
        function showProfile() {
            if (<?php echo session('user_id') ? 'true' : 'false'; ?>) {
                window.location.href = '<?php echo base_url('/profile'); ?>';
            } else {
                window.location.href = '<?php echo base_url('/login'); ?>';
            }
        }
        
        function showLoading(message = 'Loading...') {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                const loadingText = loadingOverlay.querySelector('.mobile-loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
                loadingOverlay.style.display = 'flex';
            }
        }
        
        function hideLoading() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }
        
        function showToast(message, type = 'info', duration = 3000) {
            window.MobileApp.showToast(message);
        }
        
        function adjustForKeyboard(isOpen) {
            const mainContent = document.getElementById('mainContent');
            if (mainContent) {
                if (isOpen) {
                    mainContent.style.paddingBottom = '200px';
                } else {
                    mainContent.style.paddingBottom = '';
                }
            }
        }
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden
                console.log('Page hidden');
            } else {
                // Page is visible
                console.log('Page visible');
                // Refresh data if needed
                if (<?php echo session('user_id') ? 'true' : 'false'; ?>) {
                    checkNotifications();
                }
            }
        });
        
        // Handle app state changes
        window.addEventListener('beforeunload', function() {
            // Save any pending data
            console.log('Page unloading');
        });
        
        // Handle online/offline status
        window.addEventListener('online', function() {
            showToast('Connection restored', 'success');
        });
        
        window.addEventListener('offline', function() {
            showToast('No internet connection', 'warning');
        });
        
        // Handle orientation changes
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                // Adjust layout for new orientation
                console.log('Orientation changed');
            }, 100);
        });
        
        // Handle back button (Android)
        document.addEventListener('backbutton', function() {
            if (window.history.length > 1) {
                history.back();
            } else {
                // Exit app or show exit confirmation
                if (window.MobileApp.isWebView) {
                    window.MobileApp.nativeShowAlert('Exit App', 'Are you sure you want to exit?');
                }
            }
        });
        
        // Handle menu button (Android)
        document.addEventListener('menubutton', function() {
            showFABMenu();
        });
        
        // Handle search button (Android)
        document.addEventListener('searchbutton', function() {
            toggleSearch();
        });
    </script>
    
    <!-- Additional CSS for mobile components -->
    <style>
        .header-btn-placeholder {
            width: 40px;
            height: 40px;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff416c;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .search-container {
            margin-top: 1rem;
            animation: slideDown 0.3s ease;
        }
        
        .search-input-group {
            display: flex;
            background: rgba(255,255,255,0.2);
            border-radius: 25px;
            padding: 0.5rem;
            backdrop-filter: blur(10px);
        }
        
        .search-input {
            flex: 1;
            background: none;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
        
        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }
        
        .search-input:focus {
            outline: none;
        }
        
        .search-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .search-btn:active {
            background: rgba(255,255,255,0.3);
            transform: scale(0.95);
        }
        
        .fab-container {
            position: fixed;
            bottom: 100px;
            right: 20px;
            z-index: 1000;
        }
        
        .fab {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .fab:active {
            transform: scale(0.95);
        }
        
        .fab-menu {
            position: absolute;
            bottom: 70px;
            right: 0;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        
        .fab-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .fab-item {
            display: flex;
            align-items: center;
            background: white;
            color: #2d3748;
            padding: 0.75rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .fab-item i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .fab-item:active {
            transform: scale(0.95);
        }
        
        .bottom-spacing {
            height: 20px;
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10000;
            pointer-events: none;
        }
        
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2000;
            pointer-events: none;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Landscape optimizations */
        @media (orientation: landscape) and (max-height: 500px) {
            .fab-container {
                bottom: 80px;
            }
            
            .mobile-nav {
                padding: 0.25rem 0;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .fab {
                box-shadow: 0 2px 10px rgba(102, 126, 234, 0.4);
            }
        }
    </style>
    
    <!-- Mobile App JavaScript -->
    <script>
        // Additional WebView fixes
        document.addEventListener('DOMContentLoaded', function() {
            // Force image loading
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                if (img.src && !img.complete) {
                    img.onload = function() {
                        this.style.opacity = '1';
                    };
                }
            });
            
            // Fix text overflow
            const textElements = document.querySelectorAll('.news-title, .news-content, .ad-title, .ad-description');
            textElements.forEach(el => {
                el.style.wordWrap = 'break-word';
                el.style.wordBreak = 'break-word';
                el.style.overflowWrap = 'break-word';
                el.style.hyphens = 'auto';
            });
        });
    </script>
    <script>
        // Force image loading on page load
        window.addEventListener('load', function() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                if (img.src) {
                    // Show the image by default
                    img.style.display = 'block';
                    
                    // Hide placeholder if it exists
                    const placeholder = img.nextElementSibling;
                    if (placeholder && placeholder.classList.contains('image-placeholder')) {
                        placeholder.style.display = 'none';
                    }
                    
                    // Only handle error case
                    img.onerror = function() {
                        console.log('Image failed to load:', this.src);
                        this.style.display = 'none';
                        const placeholder = this.nextElementSibling;
                        if (placeholder && placeholder.classList.contains('image-placeholder')) {
                            placeholder.style.display = 'flex';
                        }
                    };
                    
                    // Force reload if not complete
                    if (!img.complete) {
                        const newImg = new Image();
                        newImg.onload = function() {
                            img.src = this.src;
                            img.style.display = 'block';
                            const placeholder = img.nextElementSibling;
                            if (placeholder && placeholder.classList.contains('image-placeholder')) {
                                placeholder.style.display = 'none';
                            }
                        };
                        newImg.onerror = function() {
                            img.style.display = 'none';
                            const placeholder = img.nextElementSibling;
                            if (placeholder && placeholder.classList.contains('image-placeholder')) {
                                placeholder.style.display = 'flex';
                            }
                        };
                        newImg.src = img.src;
                    }
                }
            });
        });
        
        // Fix text overflow on dynamic content
        function fixDynamicContent() {
            const elements = document.querySelectorAll('.mobile-card, .mobile-list-item, .ad-content, .news-content');
            elements.forEach(el => {
                el.style.wordWrap = 'break-word';
                el.style.wordBreak = 'break-word';
                el.style.overflowWrap = 'break-word';
                el.style.maxWidth = '100%';
                el.style.overflow = 'hidden';
            });
        }
        
        // Run fixes periodically
        setInterval(fixDynamicContent, 2000);
        
        // Fix for dynamically loaded content
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            fixDynamicContent();
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    </script>
</body>
</html>
