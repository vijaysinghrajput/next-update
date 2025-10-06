<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#007bff">
    <meta name="description" content="<?php echo config('app_description'); ?>">
    <meta name="author" content="<?php echo config('admin_channel_name'); ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . config('app_name') : config('app_name') . ' - ' . config('app_tagline'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo asset('css/style.css'); ?>" rel="stylesheet">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .news-card {
            border-left: 4px solid #007bff;
        }
        .admin-badge {
            background: linear-gradient(45deg, #ffc107, #ff8c00);
        }
        .points-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 3rem 0 1rem;
        }
        .social-links a {
            color: #6c757d;
            font-size: 1.5rem;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }
        .social-links a:hover {
            color: #007bff;
        }
        .mobile-optimized {
            font-size: 14px;
        }
        
        /* Mobile Drawer Styles */
        .mobile-drawer {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            z-index: 1060;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            visibility: hidden;
        }
        
        .mobile-drawer.show {
            transform: translateX(0);
            visibility: visible;
        }
        
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: none;
            visibility: hidden;
        }
        
        .drawer-overlay.show {
            display: block;
            visibility: visible;
        }
        
        .drawer-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .drawer-nav {
            padding: 1rem 0;
        }
        
        .drawer-nav .nav-link {
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .drawer-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .drawer-nav .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .drawer-user-info {
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            margin: 1rem;
            border-radius: 8px;
            color: white;
        }
        
        .drawer-user-info .points-badge {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        /* Mobile App-like Header */
        .mobile-app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            z-index: 1030;
            padding: 0.75rem 1rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .mobile-app-header .header-left {
            display: flex;
            align-items: center;
        }
        
        .mobile-app-header .header-center {
            flex: 1;
            text-align: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .mobile-app-header .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .notification-badge {
            position: relative;
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .notification-badge:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .notification-badge .badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.3rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Bottom Tab Navigation */
        .bottom-tabs {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            z-index: 1020;
            display: none;
            padding: 0.5rem 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        .bottom-tabs .tab-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }
        
        .bottom-tabs .tab-item.active {
            color: #007bff;
        }
        
        .bottom-tabs .tab-item i {
            font-size: 1.2rem;
        }
        
        .bottom-tabs .tab-item span {
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .bottom-tabs .tab-item .badge {
            position: absolute;
            top: 0.25rem;
            right: 0.5rem;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Body padding for mobile app layout */
        body.mobile-app-layout {
            padding-top: 60px;
            padding-bottom: 70px;
        }
        
        /* Hide regular footer on mobile */
        .footer {
            display: block;
        }
        
        @media (max-width: 768px) {
            .mobile-optimized {
                font-size: 12px;
            }
            .hero-section {
                padding: 2rem 0;
            }
            
            /* Hide default navbar on mobile */
            .navbar {
                display: none !important;
            }
            
            /* Show mobile app header */
            .mobile-app-header {
                display: flex !important;
            }
            
            /* Show bottom tabs */
            .bottom-tabs {
                display: flex !important;
            }
            
            /* Hide regular footer on mobile */
            .footer {
                display: none !important;
            }
            
            /* Add mobile app layout */
            body {
                padding-top: 60px;
                padding-bottom: 70px;
            }
            
            /* Adjust content spacing */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-drawer-toggle {
                display: none !important;
            }
            
            /* Show regular footer on desktop */
            .footer {
                display: block !important;
            }
            
            /* Hide mobile app elements on desktop */
            .mobile-app-header {
                display: none !important;
            }
            
            .bottom-tabs {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile App Header -->
    <div class="mobile-app-header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleMobileDrawer()" id="menuToggleBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="header-center">
            <span id="pageTitle"><?php echo isset($page_title) ? $page_title : config('app_name'); ?></span>
        </div>
        <div class="header-right">
            <?php if (session('user_id')): ?>
                <button class="notification-badge" onclick="showNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="badge" id="notificationCount">3</span>
                </button>
                <div class="dropdown">
                    <button class="notification-badge" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo session('is_admin') ? base_url('admin') : base_url('dashboard'); ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo base_url('profile'); ?>">
                            <i class="fas fa-user-edit me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?php echo base_url('login'); ?>" class="notification-badge">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mobile Drawer Overlay -->
    <div class="drawer-overlay" id="drawerOverlay"></div>
    
    <!-- Mobile Drawer -->
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="drawer-header">
            <h4 class="mb-0">
                <i class="fas fa-newspaper me-2"></i><?php echo config('app_name'); ?>
            </h4>
            <p class="mb-0 small"><?php echo config('app_tagline'); ?></p>
        </div>
        
        <?php if (session('user_id')): ?>
        <div class="drawer-user-info">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle me-2"></i>
                <div>
                    <div class="fw-bold"><?php echo session('full_name'); ?></div>
                    <div class="points-badge">
                        <i class="fas fa-coins me-1"></i><?php echo session('points'); ?> points
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <nav class="drawer-nav">
            <a class="nav-link" href="<?php echo base_url(); ?>">
                <i class="fas fa-home"></i>Home
            </a>
            <a class="nav-link" href="<?php echo base_url('about'); ?>">
                <i class="fas fa-info-circle"></i>About
            </a>
            <a class="nav-link" href="<?php echo base_url('contact'); ?>">
                <i class="fas fa-envelope"></i>Contact
            </a>
            
            <?php if (session('user_id')): ?>
                <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
                <a class="nav-link" href="<?php echo session('is_admin') ? base_url('admin') : base_url('dashboard'); ?>">
                    <i class="fas fa-tachometer-alt"></i><?php echo session('is_admin') ? 'Admin Dashboard' : 'Dashboard'; ?>
                </a>
                <a class="nav-link" href="<?php echo base_url('profile'); ?>">
                    <i class="fas fa-user-edit"></i>Profile
                </a>
                <a class="nav-link" href="<?php echo base_url('my-news'); ?>">
                    <i class="fas fa-newspaper"></i>My News
                </a>
                <a class="nav-link" href="<?php echo base_url('post-news'); ?>">
                    <i class="fas fa-plus-circle"></i>Post News
                </a>
                <a class="nav-link" href="<?php echo base_url('kyc-verification'); ?>">
                    <i class="fas fa-shield-alt"></i>KYC Verification
                </a>
                <a class="nav-link" href="<?php echo base_url('buy-points'); ?>">
                    <i class="fas fa-coins"></i>Buy Points
                </a>
                <?php if (session('is_admin')): ?>
                    <a class="nav-link" href="<?php echo base_url('admin'); ?>">
                        <i class="fas fa-cog"></i>Admin Panel
                    </a>
                <?php endif; ?>
                <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
                <a class="nav-link" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </a>
            <?php else: ?>
                <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
                <a class="nav-link" href="<?php echo base_url('login'); ?>">
                    <i class="fas fa-sign-in-alt"></i>Login
                </a>
                <a class="nav-link" href="<?php echo base_url('signup'); ?>">
                    <i class="fas fa-user-plus"></i>Sign Up
                </a>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo base_url(); ?>">
                <i class="fas fa-newspaper me-2"></i><?php echo config('app_name'); ?>
            </a>
            
            <!-- Mobile Drawer Toggle -->
            <button class="navbar-toggler mobile-drawer-toggle" type="button" onclick="toggleMobileDrawer()">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Desktop Navigation Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url(); ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('about'); ?>">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('contact'); ?>">
                            <i class="fas fa-envelope me-1"></i>Contact
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (session('user_id')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo session('full_name'); ?>
                                <span class="badge bg-warning text-dark ms-1"><?php echo session('points'); ?> pts</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo session('is_admin') ? base_url('admin') : base_url('dashboard'); ?>">
                                    <i class="fas fa-tachometer-alt me-2"></i><?php echo session('is_admin') ? 'Admin Dashboard' : 'Dashboard'; ?>
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('profile'); ?>">
                                    <i class="fas fa-user-edit me-2"></i>Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('my-news'); ?>">
                                    <i class="fas fa-newspaper me-2"></i>My News
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('kyc-verification'); ?>">
                                    <i class="fas fa-shield-alt me-2"></i>KYC Verification
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                        <?php if (session('is_admin')): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo base_url('admin'); ?>">
                                    <i class="fas fa-cog me-1"></i>Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('login'); ?>">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('signup'); ?>">
                                <i class="fas fa-user-plus me-1"></i>Sign Up
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
