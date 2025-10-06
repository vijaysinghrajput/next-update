<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

$page_title = "Sign Up";
include APP_PATH . '/views/layouts/header.php';

$userModel = new User();

// Get cities for dropdown
$cities = $userModel->getCities();

// Handle signup form submission
if ($_POST) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = $_POST['city'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $referral_code = trim($_POST['referral_code'] ?? '');
    
    $errors = [];
    
    // Validation
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($city)) $errors[] = "City is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters long.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    
    // Validate referral code if provided
    if (!empty($referral_code)) {
        $referrer = $userModel->findByReferralCode($referral_code);
        if (!$referrer) {
            $errors[] = "Invalid referral code.";
        }
    }
    
    if (empty($errors)) {
        // Check if email already exists
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser) {
            $errors[] = "An account with this email already exists.";
        } else {
            try {
                // Auto-generate username from full name
                $username = $userModel->generateUsername($full_name);
                
                // Create user
                $userData = [
                    'full_name' => $full_name,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'city' => $city,
                    'password' => $password,
                    'referred_by' => $referral_code
                ];
                
                $userId = $userModel->create($userData);
                
                // Get the created user data
                $user = $userModel->findById($userId);
                
                // Record welcome bonus transaction
                $welcomePoints = config('welcome_points', 10);
                $userModel->recordTransaction($userId, 'earned', $welcomePoints, 'Welcome bonus', 'signup', $userId);
                
                // Handle referral if exists (after user creation to avoid transaction conflicts)
                if (!empty($referral_code)) {
                    $referrer = $userModel->findByReferralCode($referral_code);
                    if ($referrer) {
                        // Add referral bonus to new user first
                        $referralBonus = config('referral_points', 10);
                        $userModel->addPoints($userId, $referralBonus);
                        $userModel->recordTransaction($userId, 'earned', $referralBonus, 'Referral bonus', 'referral', $referrer['id']);
                        
                        // Process referral (gives points to referrer) - separate transaction
                        $userModel->processReferral($referrer['id'], $userId);
                        
                        // Update success message
                        $success = "Account created successfully! Welcome to " . config('app_name') . ". You earned {$welcomePoints} welcome points plus {$referralBonus} referral bonus points!";
                    }
                } else {
                    $success = "Account created successfully! Welcome to " . config('app_name') . ". You earned {$welcomePoints} welcome points!";
                }
                
                // Send welcome email
                $userModel->sendWelcomeEmail($email, $full_name, $user['referral_code']);
                
            } catch (Exception $e) {
                $errors[] = "Failed to create account: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
                        <h3 class="card-title">Join <?php echo config('app_name'); ?></h3>
                        <p class="text-muted">Create your account and start earning points!</p>
                    </div>
                    
                    <?php if (isset($success)): ?>
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
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
                            </div>
                            <div class="form-text">Your username will be generated automatically from your name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo old('email'); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo old('phone'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <select class="form-select" id="city" name="city" required>
                                            <option value="">Select City</option>
                                            <?php foreach ($cities as $city): ?>
                                                <option value="<?php echo $city['id']; ?>" <?php echo old('city') == $city['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($city['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="referral_code" class="form-label">Referral Code (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-gift"></i></span>
                                <input type="text" class="form-control" id="referral_code" name="referral_code" value="<?php echo old('referral_code'); ?>" placeholder="Enter referral code to earn bonus points">
                            </div>
                            <div class="form-text">Get 10 bonus points for both you and your referrer!</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="<?php echo base_url('terms'); ?>" target="_blank">Terms of Service</a> and <a href="<?php echo base_url('privacy'); ?>" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">
                            Already have an account? 
                            <a href="<?php echo base_url('login'); ?>" class="text-decoration-none fw-bold">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/footer.php'; ?>
