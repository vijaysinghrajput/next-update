<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

$page_title = "Login";
include APP_PATH . '/views/layouts/header.php';

$userModel = new User();

// Handle login form submission
if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $token = $_POST['_token'] ?? '';
    
    $errors = [];
    
    
        // Skip CSRF verification for now
        // if (!verify_csrf_token($token)) {
        //     $errors[] = "Invalid security token. Please try again.";
        // }
    
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";
    
    if (empty($errors)) {
        $user = $userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active']) {
                // Set session
                session('user_id', $user['id']);
                session('username', $user['username']);
                session('full_name', $user['full_name']);
                session('email', $user['email']);
                session('points', $user['points']);
                session('is_admin', $user['is_admin']);
                session('referral_code', $user['referral_code']);
                session('is_verified', $user['is_verified']);
                
                // Redirect based on user type
                if ($user['is_admin']) {
                    echo '<script>window.location.href = "' . base_url('admin') . '";</script>';
                    exit;
                } else {
                    echo '<script>window.location.href = "' . base_url('dashboard') . '";</script>';
                    exit;
                }
            } else {
                $errors[] = "Your account is inactive. Please contact support.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt fa-3x text-primary mb-3"></i>
                        <h3 class="card-title">Welcome Back</h3>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="mb-2">
                            <a href="<?php echo base_url('forgot-password'); ?>" class="text-decoration-none">
                                Forgot your password?
                            </a>
                        </p>
                        <p class="mb-0">
                            Don't have an account? 
                            <a href="<?php echo base_url('signup'); ?>" class="text-decoration-none fw-bold">
                                Sign up here
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/footer.php'; ?>
