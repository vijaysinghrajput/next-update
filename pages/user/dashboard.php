<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;
use App\Models\News;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$page_title = "Dashboard";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$newsModel = new News();

$userId = session('user_id');

// Get user stats (fresh from database)
$userStats = $userModel->getStats($userId);

// Update session with fresh points data
if ($userStats) {
    session('points', $userStats['points']);
    session('total_earned_points', $userStats['total_earned_points']);
    session('total_spent_points', $userStats['total_spent_points']);
}

// Get recent transactions
$recentTransactions = $userModel->getTransactions($userId, 5);

// Get recent referrals
$recentReferrals = $userModel->getReferrals($userId);

// Get notifications
$notifications = $userModel->getNotifications($userId, 5);

// Get KYC status
$kycStatus = $userModel->getKYCStatus($userId);

// Get user's news
$userNews = $newsModel->getByUser($userId, 5);
?>

<!-- Welcome Section -->
<div class="dashboard-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview
            </h2>
            <p class="mb-0 text-muted">Welcome back, <?php echo htmlspecialchars(session('full_name')); ?>! Here's your account summary.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="h4 mb-0 text-success">
                <i class="fas fa-coins me-2"></i><?php echo $userStats['points']; ?> Points
            </div>
            <small class="text-muted">Available Balance</small>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-coins text-success"></i>
            <h4><?php echo $userStats['total_earned_points']; ?></h4>
            <p>Total Earned</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-users text-primary"></i>
            <h4><?php echo $userStats['referral_count']; ?></h4>
            <p>Referrals</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-bullhorn text-warning"></i>
            <h4><?php echo $userStats['ad_count']; ?></h4>
            <p>Ads Posted</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-shield-alt <?php echo $userStats['is_verified'] ? 'text-success' : 'text-muted'; ?>"></i>
            <h4><?php echo $userStats['is_verified'] ? 'Verified' : 'Unverified'; ?></h4>
            <p>Account Status</p>
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
                        <a href="<?php echo base_url('post-news'); ?>" class="btn btn-outline-primary w-100 quick-action-btn">
                            <i class="fas fa-plus me-2"></i>Post News
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('buy-points'); ?>" class="btn btn-outline-success w-100 quick-action-btn">
                            <i class="fas fa-coins me-2"></i>Buy Points
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('kyc-verification'); ?>" class="btn btn-outline-warning w-100 quick-action-btn">
                            <i class="fas fa-id-card me-2"></i>KYC Verify
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?php echo base_url('referrals'); ?>" class="btn btn-outline-info w-100 quick-action-btn">
                            <i class="fas fa-share me-2"></i>Share Referral
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Recent Transactions
                    </h5>
                    <a href="<?php echo base_url('transactions'); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($recentTransactions)): ?>
                    <p class="text-muted text-center">No transactions yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?php echo $transaction['transaction_type'] === 'earned' ? 'success' : ($transaction['transaction_type'] === 'spent' ? 'danger' : 'info'); ?>">
                                            <?php echo ucfirst($transaction['transaction_type']); ?>
                                        </span>
                                    </td>
                                    <td class="<?php echo $transaction['transaction_type'] === 'earned' ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $transaction['transaction_type'] === 'earned' ? '+' : '-'; ?><?php echo $transaction['points']; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($transaction['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- My News -->
            <div class="dashboard-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-newspaper me-2"></i>My Recent News
                    </h5>
                    <a href="/my-news" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($userNews)): ?>
                    <p class="text-muted text-center">No news posted yet. <a href="/post-news">Post your first news!</a></p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($userNews as $news): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h6>
                                    <p class="card-text small text-muted">
                                        <?php echo substr(strip_tags($news['content']), 0, 100); ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i><?php echo $news['views']; ?> views
                                        </small>
                                        <small class="text-muted">
                                            <?php echo date('M j', strtotime($news['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- KYC Status -->
            <div class="dashboard-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-shield-alt me-2"></i>Verification Status
                </h5>
                <?php if ($userStats['is_verified']): ?>
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="text-success">Account Verified</h6>
                        <p class="text-muted small">Your account is verified and you can access all features.</p>
                    </div>
                <?php elseif ($kycStatus && $kycStatus['status'] === 'pending'): ?>
                    <div class="text-center">
                        <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                        <h6 class="text-warning">Verification Pending</h6>
                        <p class="text-muted small">Your KYC verification is under review.</p>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                        <h6>Not Verified</h6>
                        <p class="text-muted small">Verify your account to access premium features.</p>
                        <a href="<?php echo base_url('kyc-verification'); ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-id-card me-2"></i>Verify Now (50 pts)
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Referral Code -->
            <div class="dashboard-card mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-share me-2"></i>Your Referral Code
                </h5>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="referralCode" value="<?php echo session('referral_code'); ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyReferralCode()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Share this code to earn 10 points for each successful referral!
                </p>
            </div>

            <!-- Notifications -->
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </h5>
                    <a href="/notifications" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($notifications)): ?>
                    <p class="text-muted text-center">No notifications.</p>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                    <div class="d-flex align-items-start mb-2 p-2 border rounded">
                        <i class="fas fa-<?php echo $notification['type'] === 'success' ? 'check-circle text-success' : ($notification['type'] === 'warning' ? 'exclamation-triangle text-warning' : 'info-circle text-info'); ?> me-2 mt-1"></i>
                        <div class="flex-grow-1">
                            <strong><?php echo htmlspecialchars($notification['title']); ?></strong>
                            <br>
                            <small class="text-muted"><?php echo htmlspecialchars($notification['message']); ?></small>
                            <br>
                            <small class="text-muted"><?php echo date('M j, Y', strtotime($notification['created_at'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
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
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
