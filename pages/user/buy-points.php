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

$page_title = "Buy Points";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');

$errors = [];
$success = '';

// Get user's current points
$user = $userModel->findById($userId);

// Handle points purchase submission
if ($_POST) {
    $points = (int)($_POST['points'] ?? 0);
    $amount = (int)($_POST['amount'] ?? 0);
    
    // Validate inputs
    if ($points <= 0) {
        $errors[] = "Please enter a valid number of points.";
    }
    if ($amount <= 0) {
        $errors[] = "Please enter a valid amount.";
    }
    if ($points !== $amount) {
        $errors[] = "Points and amount must be equal (1 Rs = 1 Point).";
    }
    
    // Handle payment screenshot upload
    $paymentScreenshot = null;
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../public/uploads/payments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['payment_screenshot']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Invalid file type. Please upload JPG or PNG files only.";
        } elseif ($_FILES['payment_screenshot']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "File size too large. Please upload files smaller than 5MB.";
        } else {
            $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $filePath)) {
                $paymentScreenshot = 'uploads/payments/' . $fileName;
            } else {
                $errors[] = "Failed to upload payment screenshot.";
            }
        }
    } else {
        $errors[] = "Please upload a payment screenshot.";
    }
    
    if (empty($errors)) {
        try {
            // Create payment record
            $paymentId = $userModel->createPaymentRequest($userId, $points, $amount, $paymentScreenshot);
            $success = "Payment request submitted successfully! Your payment is under review. You will receive your points once the admin verifies your payment.";
        } catch (Exception $e) {
            $errors[] = "Failed to submit payment request: " . $e->getMessage();
        }
    }
}

// Get user's payment history
$paymentHistory = $userModel->getPaymentHistory($userId);
?>

<!-- Buy Points Form -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-shopping-cart me-2"></i>Buy Points
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
    
    <!-- Current Points -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-coins me-2"></i>Current Balance
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="h3 text-success me-2"><?php echo $user['points']; ?></span>
                        <span class="text-muted">Points</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Exchange Rate
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="h4 text-primary me-2"><?php echo config('payment.exchange_rate', 1); ?> Rs = 1 Point</span>
                        <span class="text-muted">Fixed Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="points" class="form-label">Number of Points *</label>
                    <input type="number" class="form-control" id="points" name="points" 
                           value="<?php echo htmlspecialchars($_POST['points'] ?? ''); ?>" 
                           placeholder="Enter number of points" min="1" required>
                    <div class="form-text">Minimum 1 point</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (Rs) *</label>
                    <input type="number" class="form-control" id="amount" name="amount" 
                           value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" 
                           placeholder="Enter amount in rupees" min="1" required>
                    <div class="form-text"><?php echo config('payment.exchange_rate', 1); ?> Rs = 1 Point</div>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="payment_screenshot" class="form-label">Payment Screenshot *</label>
            <input type="file" class="form-control" id="payment_screenshot" name="payment_screenshot" 
                   accept=".jpg,.jpeg,.png" required>
            <div class="form-text">
                Upload a screenshot of your UPI payment (JPG or PNG, max 5MB)
            </div>
        </div>
        
        
        <div class="d-flex justify-content-between">
            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Submit Payment Request
            </button>
        </div>
    </form>
</div>

<!-- Payment Instructions - Always Visible -->
<div class="dashboard-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-credit-card me-2"></i>Payment Instructions
        </h4>
        <span class="badge bg-info">Important</span>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="alert alert-info alert-persistent mb-3">
                <h6><i class="fas fa-info-circle me-2"></i>How to Pay</h6>
                <ol class="mb-0">
                    <li><strong>Send Payment:</strong> Transfer the exact amount to our UPI ID: <strong class="text-primary"><?php echo config('payment.upi_id', '8052553000@ybl'); ?></strong></li>
                    <li><strong>Take Screenshot:</strong> Capture a clear screenshot of the payment confirmation</li>
                    <li><strong>Upload Screenshot:</strong> Upload the screenshot using the form above</li>
                    <li><strong>Wait for Verification:</strong> Your points will be added after admin verification (usually within 24 hours)</li>
                </ol>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check me-2"></i>Do's</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Send exact amount as specified</li>
                                <li><i class="fas fa-check text-success me-2"></i>Take clear screenshot of payment</li>
                                <li><i class="fas fa-check text-success me-2"></i>Include transaction ID in screenshot</li>
                                <li><i class="fas fa-check text-success me-2"></i>Upload screenshot immediately after payment</li>
                                <li><i class="fas fa-check text-success me-2"></i>Keep payment receipt for your records</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0"><i class="fas fa-times me-2"></i>Don'ts</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-times text-danger me-2"></i>Don't send partial amounts</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Don't upload blurry screenshots</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Don't edit or modify screenshots</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Don't send to wrong UPI ID</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Don't submit multiple requests for same payment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="text-center">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-qrcode me-2"></i>Quick Pay</h6>
                    </div>
                    <div class="card-body">
                        <div class="qr-code-container">
                            <img src="<?php echo base_url(config('payment.qr_code_image', 'uploads/payment.png')); ?>" 
                                 alt="UPI QR Code" class="img-fluid" style="max-width: 180px; min-width: 150px;">
                            <p class="small text-muted mt-2">Scan to Pay</p>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyUPI()">
                                <i class="fas fa-copy me-1"></i>Copy UPI ID
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-warning alert-persistent">
                        <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Important Notes</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><i class="fas fa-clock me-1"></i>Verification takes 24-48 hours</li>
                            <li><i class="fas fa-shield-alt me-1"></i>Only genuine payments are approved</li>
                            <li><i class="fas fa-envelope me-1"></i>You'll receive email confirmation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
<?php if (!empty($paymentHistory)): ?>
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-history me-2"></i>Payment History
    </h5>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Points</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentHistory as $payment): ?>
                <tr>
                    <td><?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></td>
                    <td><?php echo $payment['points']; ?></td>
                    <td>₹<?php echo $payment['amount']; ?></td>
                    <td>
                        <?php if ($payment['status'] === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($payment['status'] === 'pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($payment['payment_screenshot']): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewScreenshot('<?php echo base_url($payment['payment_screenshot']); ?>')">
                                <i class="fas fa-eye"></i> View Screenshot
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<!-- Screenshot View Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-image me-2"></i>Payment Screenshot
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="screenshotImage" src="" alt="Payment Screenshot" class="img-fluid" style="max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="downloadScreenshot()">
                        <i class="fas fa-download me-2"></i>Download
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="openInNewTab()">
                        <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate amount when points change
document.getElementById('points').addEventListener('input', function() {
    const points = parseInt(this.value) || 0;
    document.getElementById('amount').value = points;
});

// Auto-calculate points when amount changes
document.getElementById('amount').addEventListener('input', function() {
    const amount = parseInt(this.value) || 0;
    document.getElementById('points').value = amount;
});

// Copy UPI ID to clipboard
function copyUPI() {
    const upiId = '<?php echo config('payment.upi_id', '8052553000@ybl'); ?>';
    
    if (navigator.clipboard && window.isSecureContext) {
        // Use modern clipboard API
        navigator.clipboard.writeText(upiId).then(function() {
            showCopySuccess();
        }).catch(function() {
            fallbackCopy(upiId);
        });
    } else {
        // Fallback for older browsers
        fallbackCopy(upiId);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showCopySuccess();
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess() {
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
    button.classList.remove('btn-outline-primary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-primary');
    }, 2000);
}

// Screenshot viewing functions
let currentScreenshotUrl = '';

function viewScreenshot(imageUrl) {
    currentScreenshotUrl = imageUrl;
    document.getElementById('screenshotImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('screenshotModal')).show();
}

function downloadScreenshot() {
    if (currentScreenshotUrl) {
        const link = document.createElement('a');
        link.href = currentScreenshotUrl;
        link.download = 'payment-screenshot-' + Date.now() + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function openInNewTab() {
    if (currentScreenshotUrl) {
        window.open(currentScreenshotUrl, '_blank');
    }
}
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
