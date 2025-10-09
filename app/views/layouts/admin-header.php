<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#667eea">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin Panel' : 'Admin Panel - ' . config('app_name'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css">
    
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
        
        html, body {
            overflow-x: hidden;
            width: 100%;
        }
        
        .admin-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s ease;
            transform: translateX(0); /* Show by default on desktop */
        }
        
        .admin-sidebar.show {
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
        
        .admin-sidebar .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .sidebar-header h4 {
            color: white;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .admin-main {
            margin-left: 250px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .admin-topbar {
            background: white;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .admin-content {
            padding: 1.5rem;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card .number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-item .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .activity-item .activity-content {
            flex: 1;
        }
        
        .activity-item .activity-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .activity-item .activity-time {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .points-badge {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 280px;
                transform: translateX(-100%); /* Hide by default on mobile */
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .admin-topbar {
                position: sticky;
                top: 0;
                z-index: 100;
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
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
            
            .nav-link {
                padding: 1rem 1.5rem;
                font-size: 16px;
                min-height: 44px;
            }
            
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="adminSidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <h4>
                <i class="fas fa-crown me-2"></i>
                Admin Panel
            </h4>
            <small class="text-white-50"><?php echo config('app_name'); ?></small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link <?php echo (basename($_SERVER['REQUEST_URI']) == 'admin' || strpos($_SERVER['REQUEST_URI'], '/admin') !== false && strpos($_SERVER['REQUEST_URI'], '/admin/') === false) ? 'active' : ''; ?>" href="<?php echo base_url('admin'); ?>">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/news') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/news'); ?>">
                <i class="fas fa-newspaper"></i>
                News Management
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/users'); ?>">
                <i class="fas fa-users"></i>
                User Management
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/categories'); ?>">
                <i class="fas fa-tags"></i>
                Categories
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/cities') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/cities'); ?>">
                <i class="fas fa-map-marker-alt"></i>
                Cities
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/kyc') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/kyc'); ?>">
                <i class="fas fa-id-card"></i>
                KYC Management
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/ads') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/ads'); ?>">
                <i class="fas fa-bullhorn"></i>
                Advertisements
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/payments') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/payments'); ?>">
                <i class="fas fa-credit-card"></i>
                Payment Management
            </a>
            
            <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : ''; ?>" href="<?php echo base_url('admin/settings'); ?>">
                <i class="fas fa-cog"></i>
                Settings
            </a>
            
            <hr style="border-color: rgba(255,255,255,0.2); margin: 1rem 0;">
            
            <a class="nav-link" href="<?php echo base_url('logout'); ?>" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="admin-main">
        <!-- Top Bar -->
        <div class="admin-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-secondary d-md-none" type="button" onclick="toggleAdminSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 d-none d-md-inline"><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?></h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="me-3 d-none d-md-block">
                        <span class="text-muted">Welcome,</span>
                        <strong><?php echo htmlspecialchars(session('full_name')); ?></strong>
                        <span class="badge bg-warning text-dark ms-2">Admin</span>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo base_url('profile'); ?>">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url(); ?>">
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
        <div class="admin-content">