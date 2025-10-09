<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

$page_title = "About Us";
include APP_PATH . '/views/layouts/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary">About <?php echo config('app_name'); ?></h1>
                <p class="lead">Your trusted local news platform</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                            <h5>Local News</h5>
                            <p>Stay updated with the latest news from your community and surrounding areas.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x text-success mb-3"></i>
                            <h5>Community</h5>
                            <p>Connect with your neighbors and share important local information.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                            <h5>Earn Points</h5>
                            <p>Earn points by sharing news and referring friends to the platform.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                            <h5>Verified Content</h5>
                            <p>All content is verified to ensure accuracy and reliability.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h3>Our Mission</h3>
                <p class="lead">
                    <?php echo config('app_description'); ?> We believe in the power of local communities 
                    and the importance of staying connected with what's happening around you.
                </p>
                
                <h3>Why Choose Us?</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Real-time local news updates</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Community-driven content</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Mobile-optimized platform</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Reward system for active users</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Verified and trusted information</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/footer.php'; ?>
