<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "Payment Management";
include APP_PATH . '/views/layouts/admin-header.php';

$userModel = new User();
$adminId = session('user_id');

$errors = [];
$success = '';

// Handle payment actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $paymentId = (int)($_POST['payment_id'] ?? 0);
    
    if ($action === 'approve' && $paymentId > 0) {
        try {
            $userModel->approvePayment($paymentId);
            $success = "Payment approved successfully! Points have been added to user's account.";
        } catch (Exception $e) {
            $errors[] = "Failed to approve payment: " . $e->getMessage();
        }
    } elseif ($action === 'reject' && $paymentId > 0) {
        $reason = trim($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            $errors[] = "Please provide a reason for rejection.";
        } else {
            try {
                $userModel->rejectPayment($paymentId, $reason);
                $success = "Payment rejected successfully.";
            } catch (Exception $e) {
                $errors[] = "Failed to reject payment: " . $e->getMessage();
            }
        }
    }
}

// Get all pending payments (for the pending section)
$pendingPayments = $userModel->getAllPendingPayments();

// Get all payments for DataTables (client-side filtering)
$allPayments = $userModel->getAllPayments();
?>

<!-- Payment Management -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-credit-card me-2"></i>Payment Management
        </h2>
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
    
    <!-- Pending Payments -->
    <div class="mb-5">
        <h4 class="mb-3">
            <i class="fas fa-clock me-2"></i>Pending Payments (<?php echo count($pendingPayments); ?>)
        </h4>
        
        <?php if (empty($pendingPayments)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No pending payments at the moment.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Points</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Screenshot</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingPayments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['id']; ?></td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($payment['user_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($payment['user_email']); ?></small>
                                </div>
                            </td>
                            <td><?php echo $payment['points']; ?></td>
                            <td>₹<?php echo $payment['amount']; ?></td>
                            <td><?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewScreenshot('<?php echo base_url($payment['payment_screenshot']); ?>')">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success" onclick="approvePayment(<?php echo $payment['id']; ?>)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="rejectPayment(<?php echo $payment['id']; ?>)">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Payment History -->
    <div class="dashboard-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                <i class="fas fa-history me-2"></i>Payment History
            </h4>
            <span class="badge bg-info"><?php echo count($allPayments); ?> payments</span>
        </div>
        
        <!-- DataTables Info -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Advanced Table Features:</strong> Use the search box below to filter payments instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
        </div>
        
        <!-- Payment Table with DataTables -->
        <div class="table-responsive">
            <table id="paymentsTable" class="table table-hover table-striped display">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">ID</th>
                        <th>User Details</th>
                        <th class="text-center">Points</th>
                        <th class="text-center">Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allPayments)): ?>
                        <?php foreach ($allPayments as $payment): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">#<?php echo $payment['id']; ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($payment['user_name']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($payment['user_email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6"><?php echo number_format($payment['points']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-success">₹<?php echo number_format($payment['amount']); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($payment['status'] === 'approved'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                <?php elseif ($payment['status'] === 'pending'): ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Rejected
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="text-nowrap">
                                    <div><?php echo date('M j, Y', strtotime($payment['created_at'])); ?></div>
                                    <small class="text-muted"><?php echo date('H:i', strtotime($payment['created_at'])); ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewScreenshot('<?php echo base_url($payment['payment_screenshot']); ?>')" title="View Screenshot">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="approvePayment(<?php echo $payment['id']; ?>)" title="Approve Payment">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectPayment(<?php echo $payment['id']; ?>)" title="Reject Payment">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No payments found</h5>
                                <p class="text-muted">No payment history available.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check me-2"></i>Approve Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this payment? Points will be added to the user's account.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="payment_id" id="approve_payment_id">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times me-2"></i>Reject Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejection_reason" class="form-label">Reason for Rejection *</label>
                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                              placeholder="Please provide a reason for rejecting this payment..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="payment_id" id="reject_payment_id">
                    <input type="hidden" name="rejection_reason" id="reject_reason_input">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Reject Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
let currentScreenshotUrl = '';

function approvePayment(paymentId) {
    // Use AJAX for dynamic table or modal for pending payments
    if (typeof ajaxApprovePayment === 'function') {
        ajaxApprovePayment(paymentId);
    } else {
        document.getElementById('approve_payment_id').value = paymentId;
        new bootstrap.Modal(document.getElementById('approveModal')).show();
    }
}

function rejectPayment(paymentId) {
    // Use AJAX for dynamic table or modal for pending payments
    if (typeof ajaxRejectPayment === 'function') {
        ajaxRejectPayment(paymentId);
    } else {
        document.getElementById('reject_payment_id').value = paymentId;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
}

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

// Handle rejection form submission
document.getElementById('rejectModal').addEventListener('show.bs.modal', function() {
    document.getElementById('rejection_reason').value = '';
});

document.querySelector('#rejectModal form').addEventListener('submit', function(e) {
    const reason = document.getElementById('rejection_reason').value.trim();
    if (!reason) {
        e.preventDefault();
        alert('Please provide a reason for rejection.');
        return;
    }
    document.getElementById('reject_reason_input').value = reason;
});

// Mobile webview optimizations for modal
document.addEventListener('DOMContentLoaded', function() {
    // Prevent modal from opening in new tab on mobile
    const screenshotModal = document.getElementById('screenshotModal');
    if (screenshotModal) {
        screenshotModal.addEventListener('shown.bs.modal', function() {
            // Ensure image loads properly in webview
            const img = document.getElementById('screenshotImage');
            if (img) {
                img.onerror = function() {
                    this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
                };
            }
        });
    }
});

// Simple functions for payment management
</script>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    console.log('=== DataTables Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable function:', typeof $.fn.DataTable);
    console.log('Table element:', $('#paymentsTable').length);
    console.log('Table HTML:', $('#paymentsTable')[0]);
    
    // Try direct initialization
    if ($('#paymentsTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing DataTable directly...');
        $('#paymentsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[5, 'desc']],
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5, 6],
                    className: 'text-center'
                },
                {
                    targets: [6],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search payments:",
                lengthMenu: "Show _MENU_ payments per page",
                info: "Showing _START_ to _END_ of _TOTAL_ payments",
                infoEmpty: "No payments available",
                infoFiltered: "(filtered from _MAX_ total payments)",
                emptyTable: "No payment data available"
            }
        });
        console.log('DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize DataTable');
        console.error('Table found:', $('#paymentsTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
});
</script>
