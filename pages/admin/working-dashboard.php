<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

// Dashboard content
ob_start();
?>

<!-- Working Dashboard Content -->
<div class="alert alert-success">
    <strong>Success!</strong> Admin dashboard is working!
</div>

<div class="dashboard-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
            </h2>
            <p class="mb-0 text-muted">Welcome back, <?php echo htmlspecialchars(session('full_name')); ?>! Here's what's happening on your platform.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="h4 mb-0 text-primary">
                <i class="fas fa-shield-alt me-2"></i>Admin Panel
            </div>
            <small class="text-muted">Full Access</small>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="icon text-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="number">25</div>
            <div class="label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="icon text-success">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="number">150</div>
            <div class="label">News Articles</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="icon text-warning">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="number">5</div>
            <div class="label">Pending KYC</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="icon text-info">
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="number">12</div>
            <div class="label">Active Ads</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-bolt me-2"></i>Quick Actions
    </h5>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="<?php echo base_url('admin/news'); ?>" class="btn btn-primary w-100">
                <i class="fas fa-newspaper me-2"></i>Manage News
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-success w-100">
                <i class="fas fa-users me-2"></i>Manage Users
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?php echo base_url('admin/payments'); ?>" class="btn btn-warning w-100">
                <i class="fas fa-credit-card me-2"></i>Manage Payments
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
render_admin_page($content, "Dashboard");
?>
