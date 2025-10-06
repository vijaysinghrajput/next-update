<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$page_title = "KYC Verification";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');

$errors = [];
$success = '';

// Get user's current KYC status
$kycStatus = $userModel->getKYCStatus($userId);
$user = $userModel->findById($userId);

// Get recent KYC-related transactions
$kycTransactions = $userModel->getUserTransactions($userId, 'kyc');

// Handle KYC submission
if ($_POST) {
    // Check if user already has a pending KYC submission
    if ($kycStatus && $kycStatus['status'] === 'pending') {
        $errors[] = "You already have a pending KYC verification. Please wait for it to be reviewed.";
    } else {
        $documentType = trim($_POST['document_type'] ?? '');
        $documentNumber = trim($_POST['document_number'] ?? '');
        
        // Validate inputs
        if (empty($documentType)) {
            $errors[] = "Please select a document type.";
        }
        if (empty($documentNumber)) {
            $errors[] = "Please enter your document number.";
        }
    
    // Handle document image upload
    $documentImage = null;
    if (isset($_FILES['document_image']) && $_FILES['document_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/kyc/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['document_image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Invalid file type. Please upload JPG, PNG, or PDF files only.";
        } elseif ($_FILES['document_image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "File size too large. Please upload files smaller than 5MB.";
        } else {
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['document_image']['tmp_name'], $filePath)) {
                $documentImage = 'uploads/kyc/' . $fileName;
            } else {
                $errors[] = "Failed to upload document image.";
            }
        }
    } else {
        $errors[] = "Please upload a document image.";
    }
    
    if (empty($errors)) {
        try {
            // Check if user has enough points for KYC verification
            $verificationCost = config('kyc_verification_cost', 50);
            
            if ($user['points'] < $verificationCost) {
                $errors[] = "You need {$verificationCost} points to submit KYC verification. You currently have {$user['points']} points.";
            } else {
                // Submit KYC verification
                $userModel->submitKYC($userId, $documentType, $documentNumber, $documentImage);
                $success = "KYC verification submitted successfully! Your verification is under review. You spent {$verificationCost} points. This transaction has been recorded in your transaction history.";
            }
        } catch (Exception $e) {
            $errors[] = "Failed to submit KYC verification: " . $e->getMessage();
        }
    }
    
    // Redirect after successful submission to prevent form resubmission
    if ($success) {
        redirect('/kyc-verification');
    }
    }
}
?>

<!-- KYC Verification Form -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-id-card me-2"></i>KYC Verification
        </h2>
        <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
    
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
    
    <!-- Current Status -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Current Status
                    </h5>
                    <?php if ($kycStatus): ?>
                        <div class="d-flex align-items-center">
                            <?php if ($kycStatus['status'] === 'approved'): ?>
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check me-1"></i>Verified
                                </span>
                                <span>Your account is verified!</span>
                            <?php elseif ($kycStatus['status'] === 'pending'): ?>
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-clock me-1"></i>Pending Review
                                </span>
                                <span>Your verification is under review.</span>
                            <?php else: ?>
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-times me-1"></i>Rejected
                                </span>
                                <span>Your verification was rejected. Please try again.</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted">
                            Submitted: <?php echo date('M j, Y', strtotime($kycStatus['created_at'])); ?>
                        </small>
                    <?php else: ?>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-question me-1"></i>Not Verified
                            </span>
                            <span>Your account is not verified yet.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-coins me-2"></i>Verification Cost
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="h4 text-warning me-2"><?php echo config('kyc_verification_cost', 50); ?> Points</span>
                        <span class="text-muted">Required for verification</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-muted">Your current points: </span>
                        <strong class="<?php echo $user['points'] >= config('kyc_verification_cost', 50) ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $user['points']; ?> points
                        </strong>
                    </div>
                    <?php if ($user['points'] < config('kyc_verification_cost', 50)): ?>
                        <div class="mt-3">
                            <a href="<?php echo base_url('buy-points'); ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-shopping-cart me-2"></i>Buy Points
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!$kycStatus || $kycStatus['status'] === 'rejected'): ?>
        <?php if ($user['points'] >= config('kyc_verification_cost', 50)): ?>
            <form method="POST" enctype="multipart/form-data" id="kycForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="document_type" class="form-label">Document Type *</label>
                            <select class="form-select" id="document_type" name="document_type" required>
                                <option value="">Select Document Type</option>
                                <option value="aadhar" <?php echo (isset($_POST['document_type']) && $_POST['document_type'] === 'aadhar') ? 'selected' : ''; ?>>Aadhar Card</option>
                                <option value="pan" <?php echo (isset($_POST['document_type']) && $_POST['document_type'] === 'pan') ? 'selected' : ''; ?>>PAN Card</option>
                                <option value="passport" <?php echo (isset($_POST['document_type']) && $_POST['document_type'] === 'passport') ? 'selected' : ''; ?>>Passport</option>
                                <option value="driving_license" <?php echo (isset($_POST['document_type']) && $_POST['document_type'] === 'driving_license') ? 'selected' : ''; ?>>Driving License</option>
                                <option value="voter_id" <?php echo (isset($_POST['document_type']) && $_POST['document_type'] === 'voter_id') ? 'selected' : ''; ?>>Voter ID</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="document_number" class="form-label">Document Number *</label>
                            <input type="text" class="form-control" id="document_number" name="document_number" 
                                   value="<?php echo htmlspecialchars($_POST['document_number'] ?? ''); ?>" 
                                   placeholder="Enter your document number" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="document_image" class="form-label">Document Image *</label>
                    <input type="file" class="form-control" id="document_image" name="document_image" 
                           accept=".jpg,.jpeg,.png,.pdf" required>
                    <div class="form-text">
                        Upload a clear image of your document (JPG, PNG, or PDF, max 5MB)
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Important:</strong> Make sure your document image is clear and all details are visible. 
                    Blurry or unclear images will be rejected.
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>Submit Verification
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Insufficient Points:</strong> You need <?php echo config('kyc_verification_cost', 50); ?> points to submit KYC verification. 
                You currently have <?php echo $user['points']; ?> points.
                <div class="mt-3">
                    <a href="<?php echo base_url('buy-points'); ?>" class="btn btn-warning">
                        <i class="fas fa-shopping-cart me-2"></i>Buy Points Now
                    </a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Verification Guidelines -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-info-circle me-2"></i>Verification Guidelines
    </h5>
    
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-success">✅ Do's</h6>
            <ul class="list-unstyled">
                <li><i class="fas fa-check text-success me-2"></i>Use a clear, high-quality image</li>
                <li><i class="fas fa-check text-success me-2"></i>Ensure all text is readable</li>
                <li><i class="fas fa-check text-success me-2"></i>Upload the front side of the document</li>
                <li><i class="fas fa-check text-success me-2"></i>Use good lighting when taking photos</li>
                <li><i class="fas fa-check text-success me-2"></i>Keep the document flat and straight</li>
            </ul>
        </div>
        <div class="col-md-6">
            <h6 class="text-danger">❌ Don'ts</h6>
            <ul class="list-unstyled">
                <li><i class="fas fa-times text-danger me-2"></i>Don't upload blurry or unclear images</li>
                <li><i class="fas fa-times text-danger me-2"></i>Don't upload edited or modified documents</li>
                <li><i class="fas fa-times text-danger me-2"></i>Don't upload screenshots of documents</li>
                <li><i class="fas fa-times text-danger me-2"></i>Don't upload expired documents</li>
                <li><i class="fas fa-times text-danger me-2"></i>Don't upload documents of other people</li>
            </ul>
        </div>
    </div>
</div>

<!-- KYC Transaction History -->
<?php if (!empty($kycTransactions)): ?>
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-history me-2"></i>KYC Transaction History
    </h5>
    
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th class="text-end">Points</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kycTransactions as $transaction): ?>
                <tr>
                    <td>
                        <div class="text-nowrap">
                            <div><?php echo date('M j, Y', strtotime($transaction['created_at'])); ?></div>
                            <small class="text-muted"><?php echo date('H:i', strtotime($transaction['created_at'])); ?></small>
                        </div>
                    </td>
                    <td>
                        <?php if ($transaction['type'] === 'spent'): ?>
                            <span class="badge bg-danger">Spent</span>
                        <?php else: ?>
                            <span class="badge bg-success">Earned</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                    <td class="text-end">
                        <span class="<?php echo $transaction['type'] === 'spent' ? 'text-danger' : 'text-success'; ?>">
                            <?php echo $transaction['type'] === 'spent' ? '-' : '+'; ?><?php echo number_format($transaction['points']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="text-center mt-3">
        <a href="<?php echo base_url('transactions'); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-list me-1"></i>View All Transactions
        </a>
    </div>
</div>
<?php endif; ?>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
