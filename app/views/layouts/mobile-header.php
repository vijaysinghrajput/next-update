<?php
// Mobile App Header Layout
$page_title = $page_title ?? 'Next Update';
$show_back_button = $show_back_button ?? false;
$show_search = $show_search ?? false;
$show_notifications = $show_notifications ?? false;
$show_profile = $show_profile ?? false;
$header_actions = $header_actions ?? [];
?>

<!DOCTYPE html>
<html lang="en" class="mobile-app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Next Update">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#667eea">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' 'unsafe-eval' data: blob: <?php echo config('app_url'); ?> https:;">
    <meta name="referrer" content="no-referrer">
    
    <title><?php echo htmlspecialchars($page_title); ?> - Next Update</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('public/assets/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo base_url('public/assets/images/apple-touch-icon.png'); ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="<?php echo base_url('public/assets/css/mobile-app.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('public/assets/css/style.css'); ?>" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo base_url('public/manifest.json'); ?>">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="<?php echo base_url('public/assets/js/mobile-app.js'); ?>" as="script">
    <link rel="preload" href="<?php echo base_url('public/assets/css/mobile-app.css'); ?>" as="style">
    
    <!-- Custom styles for this page -->
    <style>
        /* Force WebView CSS fixes */
        * {
            box-sizing: border-box !important;
            max-width: 100% !important;
        }
        
        body, html {
            overflow-x: hidden !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
        }
        
        .mobile-card, .mobile-list-item, .ad-content, .news-content {
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }
        
        .mobile-card h1, .mobile-card h2, .mobile-card h3, .mobile-card h4, 
        .mobile-card h5, .mobile-card h6, .mobile-card p, .mobile-card span, 
        .mobile-card div {
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }
        
        .ad-content h4, .news-content h3 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
        }
        
        .ad-content p, .news-content p {
            font-size: 0.9rem !important;
            line-height: 1.4 !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
        }
        
        .image-container {
            position: relative !important;
            width: 100% !important;
            height: auto !important;
            overflow: hidden !important;
            border-radius: 8px !important;
            margin-bottom: 1rem !important;
        }
        
        .image-container img {
            width: 100% !important;
            height: auto !important;
            max-height: 200px !important;
            object-fit: cover !important;
            display: block !important;
        }
        
        .image-placeholder {
            width: 100% !important;
            height: 150px !important;
            background: #f8f9fa !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #6c757d !important;
            font-size: 0.9rem !important;
            border-radius: 8px !important;
            border: 2px dashed #dee2e6 !important;
            margin-bottom: 1rem !important;
        }
        
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #56ab2f;
            --warning-color: #f093fb;
            --danger-color: #ff416c;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #2d3748;
            --border-radius: 0.75rem;
            --box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .mobile-app {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        /* Status bar spacing for iOS */
        .mobile-safe-top {
            padding-top: env(safe-area-inset-top);
        }
        
        /* Navigation bar spacing for Android */
        .mobile-safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Hide scrollbars but keep functionality */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
        
        /* Custom scrollbar for webkit browsers */
        .mobile-scrollable::-webkit-scrollbar {
            width: 3px;
        }
        
        .mobile-scrollable::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .mobile-scrollable::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 3px;
        }
        
        .mobile-scrollable::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
    </style>
    
    <!-- Force WebView fixes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Force CSS fixes for WebView
            const style = document.createElement('style');
            style.textContent = `
                * { box-sizing: border-box !important; max-width: 100% !important; }
                body, html { overflow-x: hidden !important; word-wrap: break-word !important; }
                .mobile-card, .mobile-list-item, .ad-content, .news-content { 
                    word-wrap: break-word !important; overflow: hidden !important; 
                }
                .mobile-card h1, .mobile-card h2, .mobile-card h3, .mobile-card h4, 
                .mobile-card h5, .mobile-card h6, .mobile-card p, .mobile-card span, 
                .mobile-card div { 
                    word-wrap: break-word !important; max-width: 100% !important; 
                    overflow: hidden !important; 
                }
                .ad-content h4, .news-content h3 { 
                    font-size: 1rem !important; word-wrap: break-word !important; 
                }
                .ad-content p, .news-content p { 
                    font-size: 0.9rem !important; word-wrap: break-word !important; 
                }
                .image-container { 
                    position: relative !important; width: 100% !important; 
                    overflow: hidden !important; 
                }
                .image-container img { 
                    width: 100% !important; height: auto !important; 
                    max-height: 200px !important; object-fit: cover !important; 
                }
        .image-placeholder { 
            width: 100% !important; height: 150px !important; 
            background: #f8f9fa !important; display: flex !important; 
            align-items: center !important; justify-content: center !important; 
            color: #6c757d !important; border: 2px dashed #dee2e6 !important; 
        }
        
        /* Mobile Navigation Fixes */
        .mobile-nav {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 1000 !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: white !important;
            border-top: 1px solid #e9ecef !important;
            padding: 0.5rem 0 !important;
        }
        
        .mobile-nav .nav-items {
            display: flex !important;
            justify-content: space-around !important;
            align-items: center !important;
            width: 100% !important;
        }
        
        .mobile-nav .nav-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-decoration: none !important;
            color: #6c757d !important;
            padding: 0.5rem !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }
        
        .mobile-nav .nav-item.active {
            color: #667eea !important;
            background: rgba(102, 126, 234, 0.1) !important;
        }
        
        .mobile-nav .nav-item i {
            font-size: 1.2rem !important;
            margin-bottom: 0.25rem !important;
        }
        
        .mobile-nav .nav-item span {
            font-size: 0.75rem !important;
            font-weight: 500 !important;
        }
            `;
            document.head.appendChild(style);
            
            // Force image loading
            function forceImageLoad() {
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
            }
            
            // Run immediately and after delays
            forceImageLoad();
            setTimeout(forceImageLoad, 1000);
            setTimeout(forceImageLoad, 3000);
            
            // Fix text overflow
            function fixTextOverflow() {
                const elements = document.querySelectorAll('.mobile-card, .mobile-list-item, .ad-content, .news-content');
                elements.forEach(el => {
                    el.style.wordWrap = 'break-word';
                    el.style.wordBreak = 'break-word';
                    el.style.overflowWrap = 'break-word';
                    el.style.maxWidth = '100%';
                    el.style.overflow = 'hidden';
                });
            }
            
            fixTextOverflow();
            setTimeout(fixTextOverflow, 500);
            
            // Ensure mobile navigation is visible
            function ensureNavigationVisible() {
                const mobileNav = document.querySelector('.mobile-nav');
                if (mobileNav) {
                    mobileNav.style.display = 'flex';
                    mobileNav.style.visibility = 'visible';
                    mobileNav.style.opacity = '1';
                    mobileNav.style.zIndex = '1000';
                    mobileNav.style.position = 'fixed';
                    mobileNav.style.bottom = '0';
                    mobileNav.style.left = '0';
                    mobileNav.style.right = '0';
                    mobileNav.style.background = 'white';
                    mobileNav.style.borderTop = '1px solid #e9ecef';
                    mobileNav.style.padding = '0.5rem 0';
                }
            }
            
            ensureNavigationVisible();
            setTimeout(ensureNavigationVisible, 1000);
        });
    </script>
</head>
<body class="mobile-app">
    <!-- Mobile App Container -->
    <div class="mobile-app-container">
        
        <!-- Mobile Header -->
        <header class="mobile-header mobile-safe-top">
            <div class="header-content">
                <!-- Back Button -->
                <?php if ($show_back_button): ?>
                    <button class="header-btn" onclick="history.back()" aria-label="Go back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                <?php else: ?>
                    <div class="header-btn-placeholder"></div>
                <?php endif; ?>
                
                <!-- App Title -->
                <h1 class="app-title">
                    <i class="fas fa-newspaper"></i>
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <?php if ($show_search): ?>
                        <button class="header-btn" onclick="toggleSearch()" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($show_notifications): ?>
                        <button class="header-btn" onclick="showNotifications()" aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                    <?php if (session('user_id')): ?>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                    <?php endif; ?>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($show_profile): ?>
                        <button class="header-btn" onclick="showProfile()" aria-label="Profile">
                            <i class="fas fa-user"></i>
                        </button>
                    <?php endif; ?>
                    
                    <!-- Custom Header Actions -->
                    <?php foreach ($header_actions as $action): ?>
                        <button class="header-btn" onclick="<?php echo $action['onclick']; ?>" aria-label="<?php echo $action['label']; ?>">
                            <i class="<?php echo $action['icon']; ?>"></i>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Search Bar (Hidden by default) -->
            <?php if ($show_search): ?>
                <div class="search-container" id="searchContainer" style="display: none;">
                    <div class="search-input-group">
                        <input type="text" class="search-input" placeholder="Search news, categories, cities..." id="searchInput">
                        <button class="search-btn" onclick="performSearch()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </header>
        
        <!-- Main Content Area -->
        <main class="main-content mobile-scrollable" id="mainContent">
            <!-- Content will be inserted here -->
            
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                <div class="mobile-loading">
                    <div class="mobile-spinner"></div>
                    <div class="mobile-loading-text">Loading...</div>
                </div>
            </div>
            
            <!-- Pull to Refresh Indicator -->
            <div class="mobile-pull-refresh">
                <div class="mobile-pull-refresh-indicator">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
            
            <!-- Content Container -->
            <div class="content-container" id="contentContainer">
                <!-- Page content goes here -->
