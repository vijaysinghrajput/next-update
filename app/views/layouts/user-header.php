<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#28a745">
    <title><?php echo isset($page_title) ? $page_title . ' - User Dashboard' : 'User Dashboard - ' . config('app_name'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 14px;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Mobile webview optimizations */
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        input, textarea, select {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
        
        /* Prevent zoom on input focus */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], 
        input[type="tel"], input[type="url"], textarea, select {
            font-size: 16px !important;
        }
        
        /* Mobile touch optimizations */
        .btn, .nav-link, .form-control, .form-select {
            min-height: 44px;
            touch-action: manipulation;
        }
        
        /* Prevent horizontal scroll */
        html, body {
            overflow-x: hidden;
            width: 100%;
        }
        
        .user-sidebar {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
            transform: translateX(0); /* Show by default on desktop */
        }
        
        .user-sidebar.show {
            transform: translateX(0);
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        .user-sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .user-sidebar .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .user-sidebar .sidebar-header .user-info {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .user-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .user-sidebar .nav-link:hover,
        .user-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .user-sidebar .nav-link i {
            width: 18px;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        
        .user-main {
            margin-left: 250px;
            min-height: 100vh;
        }
        
        .user-topbar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .user-content {
            padding: 1.5rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 1rem;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
        }
        
        .stats-card i {
            font-size: 2rem;
            margin-bottom: 0.75rem;
        }
        
        .stats-card h4 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stats-card p {
            font-size: 0.85rem;
            margin: 0;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .points-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .quick-action-btn {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .user-sidebar {
                width: 280px;
                transform: translateX(-100%);
            }
            
            .user-sidebar.show {
                transform: translateX(0);
            }
            
            .user-main {
                margin-left: 0;
            }
            
            .user-content {
                padding: 1rem;
            }
            
            .user-topbar {
                padding: 1rem;
                position: sticky;
                top: 0;
                z-index: 100;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .dashboard-card {
                padding: 1rem;
                margin-bottom: 1rem;
                border-radius: 8px;
            }
            
            body {
                font-size: 14px;
            }
            
            /* Mobile-specific optimizations */
            .btn {
                padding: 0.75rem 1rem;
                font-size: 16px;
                min-height: 44px;
            }
            
            .form-control, .form-select {
                padding: 0.75rem;
                font-size: 16px;
                min-height: 44px;
            }
            
            /* Better touch targets */
            .nav-link {
                padding: 1rem 1.5rem;
                font-size: 16px;
                min-height: 44px;
            }
            
            /* Mobile table responsiveness */
            .table-responsive {
                border-radius: 8px;
                overflow: hidden;
            }
            
            /* Mobile modal optimizations */
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
        }
        
        @media (max-width: 576px) {
            .stats-card h4 {
                font-size: 1.25rem;
            }
            
            .stats-card i {
                font-size: 1.5rem;
            }
            
            .quick-action-btn {
                padding: 0.5rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <!-- User Sidebar -->
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="user-sidebar" id="userSidebar">
        <div class="sidebar-header">
            <h4>
                <i class="fas fa-user-circle me-2"></i>
                My Dashboard
            </h4>
            <div class="user-info">
                <div><?php echo htmlspecialchars(session('full_name')); ?></div>
                <div class="points-badge">
                    <i class="fas fa-coins me-1"></i><?php echo session('points'); ?> pts
                </div>
            </div>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link <?php echo (basename($_SERVER['REQUEST_URI']) == 'dashboard' || strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false && strpos($_SERVER['REQUEST_URI'], '/dashboard/') === false) ? 'active' : ''; ?>" href="<?php echo base_url('dashboard'); ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/profile') !== false ? 'active' : ''; ?>" href="<?php echo base_url('profile'); ?>">
                <i class="fas fa-user-edit"></i>
                Profile
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/my-news') !== false ? 'active' : ''; ?>" href="<?php echo base_url('my-news'); ?>">
                <i class="fas fa-newspaper"></i>
                My News
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/post-news') !== false ? 'active' : ''; ?>" href="<?php echo base_url('post-news'); ?>">
                <i class="fas fa-plus-circle"></i>
                Post News
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/post-ad') !== false ? 'active' : ''; ?>" href="<?php echo base_url('post-ad'); ?>">
                <i class="fas fa-bullhorn"></i>
                Post Advertisement
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/referrals') !== false ? 'active' : ''; ?>" href="<?php echo base_url('referrals'); ?>">
                <i class="fas fa-share-alt"></i>
                Referrals
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/transactions') !== false ? 'active' : ''; ?>" href="<?php echo base_url('transactions'); ?>">
                <i class="fas fa-history"></i>
                Transactions
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/kyc-verification') !== false ? 'active' : ''; ?>" href="<?php echo base_url('kyc-verification'); ?>">
                <i class="fas fa-shield-alt"></i>
                KYC Verification
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/notifications') !== false ? 'active' : ''; ?>" href="<?php echo base_url('notifications'); ?>">
                <i class="fas fa-bell"></i>
                Notifications
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/buy-points') !== false ? 'active' : ''; ?>" href="<?php echo base_url('buy-points'); ?>">
                <i class="fas fa-coins"></i>
                Buy Points
            </a>
            
            <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
            
            <a class="nav-link" href="<?php echo base_url(); ?>" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                View Website
            </a>
            
            <a class="nav-link" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="user-main">
        <!-- Top Bar -->
        <div class="user-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-secondary d-md-none" type="button" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 d-none d-md-inline"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="me-3 d-none d-md-block">
                        <span class="text-muted">Welcome,</span>
                        <strong><?php echo htmlspecialchars(session('full_name')); ?></strong>
                        <span class="points-badge ms-2">
                            <i class="fas fa-coins me-1"></i><?php echo session('points'); ?>
                        </span>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo base_url('profile'); ?>">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('notifications'); ?>">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>View Website
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="user-content">
