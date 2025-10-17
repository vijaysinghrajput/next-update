<?php
// Include mobile header
$page_title = "Login";
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';

require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;
use App\Services\Session;

$userModel = new User();

// Handle login form submission
if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $token = $_POST['_token'] ?? '';
    
    $errors = [];
    
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";
    
    if (empty($errors)) {
        $user = $userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active']) {
                $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
                
                // Use Session service to handle login with remember option
                if (class_exists('Session') && method_exists(new Session(), 'login')) {
                    $session = new Session();
                    $session->login($user, $remember);
                } else {
                    // Fallback to regular sessions
                    session('user_id', $user['id']);
                    session('username', $user['username']);
                    session('full_name', $user['full_name']);
                    session('email', $user['email']);
                    session('points', $user['points']);
                    session('is_admin', $user['is_admin']);
                    session('referral_code', $user['referral_code']);
                    session('is_verified', $user['is_verified']);
                }
                
                // Redirect based on user role
                if ($user['is_admin']) {
                    redirect('/admin/dashboard');
                } else {
                    redirect('/user/dashboard');
                }
            } else {
                $errors[] = "Your account is not active. Please contact support.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<style>
    .auth-header {
        background: linear-gradient(135deg, #007AFF 0%, #5856D6 100%);
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
    }

    .form-group {
        margin-bottom: 1.5rem;
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

    .form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #E5E7EB;
        border-radius: 15px;
        font-size: 1rem;
        background: #F9FAFB;
        color: #333;
        transition: all 0.3s ease;
    }

    .form-input:focus {
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

    .form-input:focus + .input-icon {
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

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .custom-checkbox {
        width: 20px;
        height: 20px;
        border: 2px solid #E5E7EB;
        border-radius: 6px;
        background: white;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-checkbox.checked {
        background: #007AFF;
        border-color: #007AFF;
    }

    .custom-checkbox.checked::after {
        content: '✓';
        position: absolute;
        color: white;
        font-size: 12px;
        font-weight: bold;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .checkbox-label {
        font-size: 0.9rem;
        color: #6B7280;
        cursor: pointer;
    }

    .forgot-link {
        color: #007AFF;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .forgot-link:hover {
        opacity: 0.7;
    }

    .login-button {
        width: 100%;
        padding: 1.2rem;
        background: linear-gradient(135deg, #007AFF, #5856D6);
        border: none;
        border-radius: 15px;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 122, 255, 0.3);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .login-button.loading {
        pointer-events: none;
    }

    .login-button .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }

    .login-button.loading .spinner {
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

    .signup-link {
        text-align: center;
        color: #6B7280;
        font-size: 0.95rem;
        padding-bottom: 2rem;
    }

    .signup-link a {
        color: #007AFF;
        text-decoration: none;
        font-weight: 600;
    }

    .signup-link a:hover {
        opacity: 0.7;
    }

    /* Mobile responsiveness */
    @media (max-width: 480px) {
        .auth-container {
            padding: 1.5rem 1rem;
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
            <i class="fas fa-sign-in-alt"></i>
        </div>
        <h1>Welcome Back</h1>
        <p>Sign in to your NextUpdate account</p>
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

        <form method="POST" action="" id="loginForm">
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

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-wrapper">
                    <input type="password" 
                           class="form-input" 
                           id="password" 
                           name="password" 
                           required
                           autocomplete="current-password">
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <div class="checkbox-wrapper">
                    <div class="custom-checkbox" onclick="toggleCheckbox()" id="rememberCheckbox"></div>
                    <input type="checkbox" id="remember" name="remember" value="1" style="display: none;">
                    <label for="remember" class="checkbox-label" onclick="toggleCheckbox()">Remember me</label>
                </div>
                <a href="/forgot-password" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="login-button" id="loginButton">
                <div class="spinner"></div>
                <span>Sign In</span>
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="signup-link">
            Don't have an account? <a href="/signup">Sign up here</a>
        </div>
    </div>
</div>

<script>
    // Password toggle functionality
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }

    // Custom checkbox functionality
    function toggleCheckbox() {
        const checkbox = document.getElementById('rememberCheckbox');
        const hiddenInput = document.getElementById('remember');
        
        if (checkbox.classList.contains('checked')) {
            checkbox.classList.remove('checked');
            hiddenInput.checked = false;
        } else {
            checkbox.classList.add('checked');
            hiddenInput.checked = true;
        }
    }

    // Form submission with loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const button = document.getElementById('loginButton');
        button.classList.add('loading');
        button.querySelector('span').textContent = 'Signing In...';
    });

    // Auto-focus first input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email').focus();
    });

    // Add haptic feedback for mobile devices
    function vibrate() {
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
    }

    // Add vibration on button clicks
    document.querySelectorAll('button, .custom-checkbox').forEach(element => {
        element.addEventListener('click', vibrate);
    });
</script>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>