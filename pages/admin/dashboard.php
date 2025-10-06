<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;
use App\Models\News;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "Dashboard";
include APP_PATH . '/views/layouts/admin-header.php';

$userModel = new User();
$newsModel = new News();

// Get admin statistics with error handling
try {
    $totalUsers = $userModel->db->fetch("SELECT COUNT(*) as count FROM users WHERE is_active = 1")['count'] ?? 0;
} catch (Exception $e) {
    $totalUsers = 0;
}

try {
    $totalNews = $newsModel->db->fetch("SELECT COUNT(*) as count FROM news_articles WHERE is_active = 1")['count'] ?? 0;
} catch (Exception $e) {
    $totalNews = 0;
}

try {
    $pendingKYC = $userModel->db->fetch("SELECT COUNT(*) as count FROM kyc_verifications WHERE status = 'pending'")['count'] ?? 0;
} catch (Exception $e) {
    $pendingKYC = 0;
}

try {
    $totalAds = $userModel->db->fetch("SELECT COUNT(*) as count FROM user_ads WHERE is_active = 1")['count'] ?? 0;
} catch (Exception $e) {
    $totalAds = 0;
}

// Get recent users with error handling
try {
    $recentUsers = $userModel->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5") ?? [];
} catch (Exception $e) {
    $recentUsers = [];
}

// Get recent news with error handling
try {
    $recentNews = $newsModel->db->fetchAll("SELECT n.*, u.full_name as author_name FROM news_articles n LEFT JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC LIMIT 5") ?? [];
} catch (Exception $e) {
    $recentNews = [];
}

// Get pending KYC verifications with error handling
try {
    $pendingKYCs = $userModel->db->fetchAll("SELECT k.*, u.full_name, u.email FROM kyc_verifications k JOIN users u ON k.user_id = u.id WHERE k.status = 'pending' ORDER BY k.created_at DESC LIMIT 5") ?? [];
} catch (Exception $e) {
    $pendingKYCs = [];
}
?>

<!-- Debug Info -->
<div class="alert alert-info">
    <strong>Debug Info:</strong> 
    Users: <?php echo $totalUsers; ?> | 
    News: <?php echo $totalNews; ?> | 
    KYC: <?php echo $pendingKYC; ?> | 
    Ads: <?php echo $totalAds; ?>
</div>

<!-- Welcome Section -->
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
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-users text-primary"></i>
            <h3><?php echo $totalUsers; ?></h3>
            <p class="text-muted">Total Users</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-newspaper text-success"></i>
            <h3><?php echo $totalNews; ?></h3>
            <p class="text-muted">Total News</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-id-card text-warning"></i>
            <h3><?php echo $pendingKYC; ?></h3>
            <p class="text-muted">Pending KYC</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-bullhorn text-info"></i>
            <h3><?php echo $totalAds; ?></h3>
            <p class="text-muted">Total Ads</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
            <!-- Quick Actions -->
            <div class="dashboard-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('admin/news'); ?>" class="btn btn-outline-primary w-100">
                            <i class="fas fa-newspaper me-2"></i>Manage News
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-outline-success w-100">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('admin/kyc'); ?>" class="btn btn-outline-warning w-100">
                            <i class="fas fa-id-card me-2"></i>KYC Reviews
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('admin/settings'); ?>" class="btn btn-outline-info w-100">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent News -->
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-newspaper me-2"></i>Recent News
                    </h5>
                    <a href="<?php echo base_url('admin/news'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($recentNews)): ?>
                    <p class="text-muted text-center">No news articles found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentNews as $news): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($news['title'], 0, 30)) . '...'; ?></td>
                                    <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $news['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $news['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($news['created_at'])); ?></td>
                                    <td>
                                        <a href="/admin/news/<?php echo $news['id']; ?>/edit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Pending KYC -->
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Pending KYC
                    </h5>
                    <a href="/admin/kyc" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($pendingKYCs)): ?>
                    <p class="text-muted text-center">No pending KYC verifications.</p>
                <?php else: ?>
                    <?php foreach ($pendingKYCs as $kyc): ?>
                    <div class="d-flex align-items-start mb-2 p-2 border rounded">
                        <div class="flex-grow-1">
                            <strong><?php echo htmlspecialchars($kyc['full_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($kyc['email']); ?></small>
                            <br>
                            <small class="text-muted"><?php echo ucfirst($kyc['document_type']); ?></small>
                        </div>
                        <div class="btn-group-vertical btn-group-sm">
                            <a href="/admin/kyc/<?php echo $kyc['id']; ?>/approve" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i>
                            </a>
                            <a href="/admin/kyc/<?php echo $kyc['id']; ?>/reject" class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Recent Users -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Recent Users
                    </h5>
                    <a href="<?php echo base_url('admin/users'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($recentUsers)): ?>
                    <p class="text-muted text-center">No users found.</p>
                <?php else: ?>
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="d-flex align-items-center mb-2 p-2 border rounded">
                        <div class="flex-grow-1">
                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                            <br>
                            <small class="text-muted">
                                <span class="badge bg-<?php echo $user['is_verified'] ? 'success' : 'warning'; ?>">
                                    <?php echo $user['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                </span>
                            </small>
                        </div>
                        <div>
                            <a href="<?php echo base_url('admin/users/' . $user['id']); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>
