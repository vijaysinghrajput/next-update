<?php
// Include mobile header
$page_title = "Sign Up";
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';

require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

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
                // Create new user
                $userData = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'city_id' => $city,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'referral_code' => strtoupper(uniqid('REF')),
                    'points' => 10, // Welcome bonus
                    'is_active' => 1,
                    'is_verified' => 0
                ];
                
                $userId = $userModel->create($userData);
                
                if ($userId) {
                    // Award referral bonus if applicable
                    if (!empty($referral_code) && $referrer) {
                        // Note: awardPoints method needs to be implemented
                        // $userModel->awardPoints($referrer['id'], 50, 'Referral bonus for ' . $full_name);
                    }
                    
                    // Auto login after successful registration
                    // Note: find method needs to be implemented
                    // $newUser = $userModel->find($userId);
                    $newUser = $userModel->findByEmail($email);
                    session('user_id', $newUser['id']);
                    session('username', $newUser['username']);
                    session('full_name', $newUser['full_name']);
                    session('email', $newUser['email']);
                    session('points', $newUser['points']);
                    session('is_admin', $newUser['is_admin']);
                    session('referral_code', $newUser['referral_code']);
                    session('is_verified', $newUser['is_verified']);
                    
                    redirect('/user/dashboard');
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            } catch (Exception $e) {
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>

<style>
    .auth-header {
        background: linear-gradient(135deg, #34C759 0%, #007AFF 100%);
        padding: 2rem 0;
        text-align: center;
        color: white;
        margin-bottom: 2rem;
    }

    .auth-header .logo-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .auth-header .logo-icon i {
        font-size: 2rem;
        color: white;
    }

    .auth-header h1 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .auth-header p {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }

    .auth-container {
        background: white;
        border-radius: 25px 25px 0 0;
        min-height: calc(100vh - 200px);
        padding: 2rem 1.5rem;
        margin-top: -20px;
        position: relative;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1);
        padding-bottom: 6rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .input-wrapper {
        position: relative;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #E5E7EB;
        border-radius: 15px;
        font-size: 1rem;
        background: #F9FAFB;
        color: #333;
        transition: all 0.3s ease;
        -webkit-appearance: none;
        appearance: none;
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 3rem;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #007AFF;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .form-input:focus + .input-icon,
    .form-select:focus + .input-icon {
        color: #007AFF;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9CA3AF;
        cursor: pointer;
        padding: 0.5rem;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #007AFF;
    }

    .password-strength {
        margin-top: 0.5rem;
        height: 4px;
        background: #E5E7EB;
        border-radius: 2px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        background: #FF3B30;
        width: 0%;
        transition: all 0.3s ease;
    }

    .password-strength-bar.weak {
        background: #FF3B30;
        width: 33%;
    }

    .password-strength-bar.medium {
        background: #FF9500;
        width: 66%;
    }

    .password-strength-bar.strong {
        background: #34C759;
        width: 100%;
    }

    .signup-button {
        width: 100%;
        padding: 1.2rem;
        background: linear-gradient(135deg, #34C759, #007AFF);
        border: none;
        border-radius: 15px;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 1.5rem 0;
        position: relative;
    }

    .signup-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(52, 199, 89, 0.3);
    }

    .signup-button:active {
        transform: translateY(0);
    }

    .signup-button.loading {
        pointer-events: none;
    }

    .signup-button .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }

    .signup-button.loading .spinner {
        display: inline-block;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .error-alert {
        background: rgba(255, 59, 48, 0.1);
        border: 1px solid rgba(255, 59, 48, 0.3);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        color: #FF3B30;
        font-size: 0.9rem;
    }

    .error-alert ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .error-alert li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .error-alert li::before {
        content: '⚠️';
        font-size: 0.8rem;
    }

    .divider {
        text-align: center;
        margin: 2rem 0;
        position: relative;
        color: #9CA3AF;
        font-size: 0.9rem;
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #E5E7EB;
        z-index: 1;
    }

    .divider span {
        background: white;
        padding: 0 1rem;
        position: relative;
        z-index: 2;
    }

    .login-link {
        text-align: center;
        color: #6B7280;
        font-size: 0.95rem;
        padding-bottom: 2rem;
    }

    .login-link a {
        color: #007AFF;
        text-decoration: none;
        font-weight: 600;
    }

    .login-link a:hover {
        opacity: 0.7;
    }

    .referral-info {
        background: rgba(0, 122, 255, 0.1);
        border: 1px solid rgba(0, 122, 255, 0.3);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
        color: #007AFF;
    }

    /* Mobile responsiveness */
    @media (max-width: 480px) {
        .auth-container {
            padding: 1.5rem 1rem;
        }
        
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
        
        .auth-header .logo-icon {
            width: 70px;
            height: 70px;
        }
        
        .auth-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="mobile-app-container">
    <div class="auth-header">
        <div class="logo-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h1>Join NextUpdate</h1>
        <p>Create your account to get started</p>
    </div>

    <div class="auth-container">
        <?php if (!empty($errors)): ?>
            <div class="error-alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <div class="input-wrapper">
                    <input type="text" 
                           class="form-input" 
                           id="full_name" 
                           name="full_name" 
                           value="<?php echo htmlspecialchars($full_name ?? ''); ?>" 
                           required 
                           autocomplete="name">
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" 
                           class="form-input" 
                           id="email" 
                           name="email" 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                           required 
                           autocomplete="email"
                           autocapitalize="none">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-wrapper">
                        <input type="tel" 
                               class="form-input" 
                               id="phone" 
                               name="phone" 
                               value="<?php echo htmlspecialchars($phone ?? ''); ?>" 
                               required 
                               autocomplete="tel">
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <div class="input-wrapper">
                        <select class="form-select" id="city" name="city" required>
                            <option value="">Select City</option>
                            <?php foreach ($cities as $cityOption): ?>
                                <option value="<?php echo $cityOption['id']; ?>" 
                                        <?php echo (isset($city) && $city == $cityOption['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cityOption['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-map-marker-alt input-icon"></i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           class="form-input" 
                           id="password" 
                           name="password" 
                           required
                           autocomplete="new-password"
                           minlength="6">
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           class="form-input" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required
                           autocomplete="new-password">
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye" id="confirmPasswordToggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="referral_code" class="form-label">Referral Code (Optional)</label>
                <div class="input-wrapper">
                    <input type="text" 
                           class="form-input" 
                           id="referral_code" 
                           name="referral_code" 
                           value="<?php echo htmlspecialchars($referral_code ?? ''); ?>" 
                           placeholder="Enter referral code">
                    <i class="fas fa-gift input-icon"></i>
                </div>
                <div class="referral-info">
                    <i class="fas fa-info-circle"></i> Use a referral code to earn bonus points!
                </div>
            </div>

            <button type="submit" class="signup-button" id="signupButton">
                <div class="spinner"></div>
                <span>Create Account</span>
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="login-link">
            Already have an account? <a href="/login">Sign in here</a>
        </div>
    </div>
</div>

<script>
    // Password toggle functionality
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrengthBar');
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        strengthBar.className = 'password-strength-bar';
        
        if (strength === 1) {
            strengthBar.classList.add('weak');
        } else if (strength === 2 || strength === 3) {
            strengthBar.classList.add('medium');
        } else if (strength >= 4) {
            strengthBar.classList.add('strong');
        }
    }

    // Password confirmation check
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const confirmInput = document.getElementById('confirm_password');
        
        if (confirmPassword && password !== confirmPassword) {
            confirmInput.style.borderColor = '#FF3B30';
        } else {
            confirmInput.style.borderColor = '#E5E7EB';
        }
    }

    // Event listeners
    document.getElementById('password').addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });

    document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

    // Form submission with loading state
    document.getElementById('signupForm').addEventListener('submit', function() {
        const button = document.getElementById('signupButton');
        button.classList.add('loading');
        button.querySelector('span').textContent = 'Creating Account...';
    });

    // Auto-focus first input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('full_name').focus();
    });

    // Add haptic feedback for mobile devices
    function vibrate() {
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
    }

    // Add vibration on button clicks
    document.querySelectorAll('button').forEach(element => {
        element.addEventListener('click', vibrate);
    });

    // Real-time validation feedback
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            this.style.borderColor = '#FF3B30';
        } else {
            this.style.borderColor = '#E5E7EB';
        }
    });

    document.getElementById('phone').addEventListener('input', function() {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^\d+\-\s()]/g, '');
    });

    // Referral code validation
    document.getElementById('referral_code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
</script>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>