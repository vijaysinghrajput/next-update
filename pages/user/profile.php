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

$page_title = "Profile";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');
$user = $userModel->findById($userId);

$errors = [];
$success = '';

// Handle form submission
if ($_POST) {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = $_POST['city'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($city)) $errors[] = "City is required.";
    
    // Password change validation
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to change password.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
    }
    
    if (empty($errors)) {
        try {
            $updateData = [
                'full_name' => $full_name,
                'phone' => $phone,
                'city' => $city
            ];
            
            if (!empty($new_password)) {
                $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            $userModel->update($userId, $updateData);
            
            // Update session data
            session('full_name', $full_name);
            
            $success = "Profile updated successfully!";
            
            // Refresh user data
            $user = $userModel->findById($userId);
            
        } catch (Exception $e) {
            $errors[] = "Failed to update profile: " . $e->getMessage();
        }
    }
}

// Get cities for dropdown
$cities = $userModel->getCities();
?>

<!-- Profile Form -->
<div class="dashboard-card">
    <h2 class="mb-4">
        <i class="fas fa-user-edit me-2"></i>Edit Profile
    </h2>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <div class="form-text">Email cannot be changed. Contact support if needed.</div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="city" class="form-label">City *</label>
                    <select class="form-select" id="city" name="city" required>
                        <option value="">Select City</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo $city['id']; ?>" 
                                    <?php echo $user['city'] == $city['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Change Password (Optional)</h5>
        
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Profile
            </button>
        </div>
    </form>
</div>

<!-- Account Information -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-info-circle me-2"></i>Account Information
    </h5>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label fw-bold">Username</label>
                <p class="form-control-plaintext"><?php echo htmlspecialchars($user['username']); ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label fw-bold">Referral Code</label>
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['referral_code']); ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyReferralCode()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label fw-bold">Member Since</label>
                <p class="form-control-plaintext"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label fw-bold">Account Status</label>
                <p class="form-control-plaintext">
                    <span class="badge bg-<?php echo $user['is_verified'] ? 'success' : 'warning'; ?>">
                        <?php echo $user['is_verified'] ? 'Verified' : 'Unverified'; ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
