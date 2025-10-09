<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "Settings";
include APP_PATH . '/views/layouts/admin-header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = "Settings updated successfully!";
    // TODO: Implement settings update functionality
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog me-2"></i>Settings</h2>
</div>

<!-- Success Message -->
<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Settings Form -->
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-cog me-2"></i>Application Settings
            </h5>
            
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="app_name" class="form-label">Application Name</label>
                        <input type="text" class="form-control" id="app_name" name="app_name" 
                               value="<?php echo config('app_name'); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="app_tagline" class="form-label">Application Tagline</label>
                        <input type="text" class="form-control" id="app_tagline" name="app_tagline" 
                               value="<?php echo config('app_tagline'); ?>" readonly>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="app_description" class="form-label">Application Description</label>
                    <textarea class="form-control" id="app_description" name="app_description" rows="3" readonly><?php echo config('app_description'); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="admin_channel_name" class="form-label">Admin Channel Name</label>
                        <input type="text" class="form-control" id="admin_channel_name" name="admin_channel_name" 
                               value="<?php echo config('admin_channel_name'); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" 
                               value="<?php echo config('admin_email'); ?>" readonly>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" disabled>
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Points System Settings -->
        <div class="dashboard-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-coins me-2"></i>Points System
            </h5>
            
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Welcome Points</span>
                    <span class="badge bg-primary"><?php echo config('welcome_points'); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Referral Points</span>
                    <span class="badge bg-primary"><?php echo config('referral_points'); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>News Post Points</span>
                    <span class="badge bg-primary"><?php echo config('news_post_points'); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>KYC Verification Cost</span>
                    <span class="badge bg-warning"><?php echo config('kyc_verification_cost'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Settings -->
        <div class="dashboard-card">
            <h5 class="mb-3">
                <i class="fas fa-credit-card me-2"></i>Payment Settings
            </h5>
            
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <strong>UPI ID:</strong><br>
                    <code><?php echo config('payment.upi_id'); ?></code>
                </div>
                <div class="list-group-item">
                    <strong>Exchange Rate:</strong><br>
                    <span class="badge bg-success"><?php echo config('payment.exchange_rate'); ?> Rs = 1 Point</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="dashboard-card mt-4">
    <h5 class="mb-3">
        <i class="fas fa-info-circle me-2"></i>System Information
    </h5>
    
    <div class="row">
        <div class="col-md-6">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Application Version</span>
                    <span class="badge bg-info"><?php echo config('app_version'); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Debug Mode</span>
                    <span class="badge bg-<?php echo config('debug') ? 'warning' : 'success'; ?>">
                        <?php echo config('debug') ? 'Enabled' : 'Disabled'; ?>
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Timezone</span>
                    <span class="badge bg-secondary"><?php echo config('timezone'); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>PHP Version</span>
                    <span class="badge bg-info"><?php echo PHP_VERSION; ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Server Time</span>
                    <span class="badge bg-secondary"><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Memory Usage</span>
                    <span class="badge bg-info"><?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>
