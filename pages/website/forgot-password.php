<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

$page_title = "Forgot Password";

// Include mobile header
include_once __DIR__ . '/../../app/views/layouts/mobile-header.php';

$userModel = new User();

// Handle forgot password form submission
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    $success = false;
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Check if user exists
        $user = $userModel->findByEmail($email);
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store reset token (in a real app, you'd save this to database)
            // For now, we'll just show success message
            $success = true;
            
            // In a real application, you would:
            // 1. Save the token to database with expiration
            // 2. Send email with reset link
            // 3. Redirect to success page
            
        } else {
            // Don't reveal if email exists or not for security
            $success = true;
        }
    }
}
?>

<style>
    .auth-header {
        background: linear-gradient(135deg, #FF9500 0%, #FF3B30 100%);
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
        border-color: #FF9500;
        background: white;
        box-shadow: 0 0 0 4px rgba(255, 149, 0, 0.1);
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
        color: #FF9500;
    }

    .submit-button {
        width: 100%;
        padding: 1.2rem;
        background: linear-gradient(135deg, #FF9500, #FF3B30);
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

    .submit-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 149, 0, 0.3);
    }

    .submit-button:active {
        transform: translateY(0);
    }

    .submit-button.loading {
        pointer-events: none;
    }

    .submit-button .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 0.5rem;
    }

    .submit-button.loading .spinner {
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

    .success-alert {
        background: rgba(52, 199, 89, 0.1);
        border: 1px solid rgba(52, 199, 89, 0.3);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        color: #34C759;
        font-size: 0.9rem;
        text-align: center;
    }

    .info-text {
        background: rgba(0, 122, 255, 0.1);
        border: 1px solid rgba(0, 122, 255, 0.3);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        color: #007AFF;
        font-size: 0.9rem;
        text-align: center;
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

    .back-link {
        text-align: center;
        color: #6B7280;
        font-size: 0.95rem;
        padding-bottom: 2rem;
    }

    .back-link a {
        color: #007AFF;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link a:hover {
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
            <i class="fas fa-key"></i>
        </div>
        <h1>Reset Password</h1>
        <p>Enter your email to reset your password</p>
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

        <?php if ($success): ?>
            <div class="success-alert">
                <i class="fas fa-check-circle"></i>
                <strong>Reset link sent!</strong><br>
                If an account with that email exists, we've sent you a password reset link.
            </div>
            
            <div class="info-text">
                <i class="fas fa-info-circle"></i>
                Check your email (including spam folder) for the reset link. The link will expire in 1 hour.
            </div>
        <?php else: ?>
            <form method="POST" action="" id="forgotForm">
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
                               autocapitalize="none"
                               placeholder="Enter your email address">
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="submit-button" id="submitButton">
                    <div class="spinner"></div>
                    <span>Send Reset Link</span>
                </button>
            </form>

            <div class="info-text">
                <i class="fas fa-shield-alt"></i>
                We'll send you a secure link to reset your password. The link will expire in 1 hour for your security.
            </div>
        <?php endif; ?>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="back-link">
            Remember your password? <a href="/login">Sign in here</a><br>
            <a href="/signup">Don't have an account? Sign up</a>
        </div>
    </div>
</div>

<script>
    // Form submission with loading state
    document.getElementById('forgotForm')?.addEventListener('submit', function() {
        const button = document.getElementById('submitButton');
        button.classList.add('loading');
        button.querySelector('span').textContent = 'Sending...';
    });

    // Auto-focus email input
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.focus();
        }
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

    // Real-time email validation
    document.getElementById('email')?.addEventListener('blur', function() {
        const email = this.value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            this.style.borderColor = '#FF3B30';
        } else {
            this.style.borderColor = '#E5E7EB';
        }
    });

    // Auto-dismiss success message after some time
    if (document.querySelector('.success-alert')) {
        setTimeout(function() {
            const alert = document.querySelector('.success-alert');
            if (alert) {
                alert.style.opacity = '0.7';
            }
        }, 10000);
    }
</script>

<?php
// Include mobile footer
include_once __DIR__ . '/../../app/views/layouts/mobile-footer.php';
?>
