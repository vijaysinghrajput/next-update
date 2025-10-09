<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

// Redirect if already logged in as admin
if (session('user_id') && session('is_admin')) {
    redirect('/admin');
}

$errors = [];
$success = '';

// Handle login form submission
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    } else {
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                $errors[] = 'Your account has been deactivated. Please contact support.';
            } elseif (!$user['is_admin']) {
                $errors[] = 'Access denied. Admin privileges required.';
            } else {
                // Login successful
                session('user_id', $user['id']);
                session('username', $user['username']);
                session('full_name', $user['full_name']);
                session('email', $user['email']);
                session('points', $user['points']);
                session('is_admin', $user['is_admin']);
                session('referral_code', $user['referral_code']);
                session('is_verified', $user['is_verified']);
                
                // Update last login
                $userModel->db->query("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
                
                redirect('/admin');
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

$page_title = "Admin Login";
include APP_PATH . '/views/layouts/admin-header.php';
?>

<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h2 class="fw-bold">Admin Login</h2>
                        <p class="text-muted">Access the admin panel</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Admin Panel
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted">
                            <a href="<?php echo base_url('/'); ?>" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Website
                            </a>
                        </p>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Default Admin Credentials:</strong><br>
                            Email: admin@gmail.com<br>
                            Password: 123456
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>
