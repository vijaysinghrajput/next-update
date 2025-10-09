<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$page_title = "Referrals";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');
$user = $userModel->findById($userId);

// Get referrals
$referrals = $userModel->getReferrals($userId);
?>

<!-- Referral Overview -->
<div class="dashboard-card mb-4">
    <h2 class="mb-4">
        <i class="fas fa-share-alt me-2"></i>Referral Program
    </h2>
    
    <div class="row">
        <div class="col-md-8">
            <h5>Earn Points by Referring Friends!</h5>
            <p class="text-muted">Share your referral code with friends and earn <strong><?php echo config('referral_points', 10); ?> points</strong> for each successful referral. They also get <strong><?php echo config('referral_points', 10); ?> bonus points</strong> when they sign up!</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>How it works:</strong> Share your referral code with friends. When they sign up using your code, both of you earn bonus points!
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="stats-card">
                <i class="fas fa-users text-primary"></i>
                <h4><?php echo count($referrals); ?></h4>
                <p>Total Referrals</p>
            </div>
        </div>
    </div>
</div>

<!-- Referral Code -->
<div class="dashboard-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-gift me-2"></i>Your Referral Code
    </h5>
    
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" id="referralCode" 
                       value="<?php echo htmlspecialchars($user['referral_code']); ?>" readonly>
                <button class="btn btn-primary" type="button" onclick="copyReferralCode()">
                    <i class="fas fa-copy me-2"></i>Copy Code
                </button>
            </div>
            <div class="form-text mt-2">
                <i class="fas fa-share-alt me-1"></i>
                Share this code with your friends: <strong><?php echo htmlspecialchars($user['referral_code']); ?></strong>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="stats-card">
                <i class="fas fa-coins text-success"></i>
                <h4><?php echo config('referral_points', 10); ?></h4>
                <p>Points per Referral</p>
            </div>
        </div>
    </div>
</div>

<!-- Share Options -->
<div class="dashboard-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-share me-2"></i>Share Your Referral Code
    </h5>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <i class="fas fa-link fa-2x text-primary mb-3"></i>
                    <h6>Share Link</h6>
                    <p class="text-muted small">Share this link with your friends</p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="referralLink" 
                               value="<?php echo base_url('signup?ref=' . $user['referral_code']); ?>" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <div class="card border">
                <div class="card-body text-center">
                    <i class="fas fa-qrcode fa-2x text-success mb-3"></i>
                    <h6>QR Code</h6>
                    <p class="text-muted small">Generate QR code for easy sharing</p>
                    <button class="btn btn-outline-success" onclick="generateQRCode()">
                        <i class="fas fa-qrcode me-2"></i>Generate QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Referral History -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>Referral History
        </h5>
        <span class="badge bg-primary"><?php echo count($referrals); ?> Referrals</span>
    </div>
    
    <?php if (empty($referrals)): ?>
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No referrals yet</h6>
            <p class="text-muted">Start sharing your referral code to earn points!</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined Date</th>
                        <th>Points Earned</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($referrals as $referral): ?>
                        <tr>
                            <td>
                                <i class="fas fa-user me-2"></i>
                                <?php echo htmlspecialchars($referral['referred_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($referral['referred_email']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($referral['joined_date'])); ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-coins me-1"></i><?php echo $referral['points_earned']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function copyReferralCode() {
    const referralCode = document.getElementById('referralCode');
    referralCode.select();
    referralCode.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
    button.classList.remove('btn-primary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-primary');
    }, 2000);
}

function copyReferralLink() {
    const referralLink = document.getElementById('referralLink');
    referralLink.select();
    referralLink.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

function generateQRCode() {
    const referralLink = document.getElementById('referralLink').value;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(referralLink)}`;
    
    // Open QR code in new window
    window.open(qrUrl, '_blank', 'width=300,height=300');
}
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
