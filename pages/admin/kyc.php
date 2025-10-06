<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "KYC Management";
include APP_PATH . '/views/layouts/admin-header.php';

$userModel = new User();
$adminId = session('user_id');

$errors = [];
$success = '';

// Handle KYC actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $kycId = (int)($_POST['kyc_id'] ?? 0);
    
    if ($action === 'approve' && $kycId > 0) {
        try {
            $userModel->approveKYC($kycId, $adminId);
            $success = "KYC verification approved successfully! User account is now verified.";
        } catch (Exception $e) {
            $errors[] = "Failed to approve KYC: " . $e->getMessage();
        }
    } elseif ($action === 'reject' && $kycId > 0) {
        $reason = trim($_POST['reason'] ?? '');
        if (empty($reason)) {
            $errors[] = "Please provide a reason for rejection.";
        } else {
            try {
                $userModel->rejectKYC($kycId, $adminId, $reason);
                $success = "KYC verification rejected successfully. User's points have been returned.";
            } catch (Exception $e) {
                $errors[] = "Failed to reject KYC: " . $e->getMessage();
            }
        }
    }
    
    // Redirect to prevent form resubmission
    if ($success) {
        redirect('/admin/kyc?success=' . urlencode($success));
    }
}

// Get success message from URL parameter
if (isset($_GET['success'])) {
    $success = urldecode($_GET['success']);
}

// Get all KYC verifications with error handling
try {
    $allKYC = $userModel->getAllKYCVerifications();
    $pendingKYC = $userModel->getPendingKYCVerifications();
} catch (Exception $e) {
    $errors[] = "Failed to load KYC data: " . $e->getMessage();
    $allKYC = [];
    $pendingKYC = [];
}
?>

<!-- KYC Management -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-id-card me-2"></i>KYC Verification Management
        </h2>
        <div class="d-flex gap-2">
            <span class="badge bg-warning"><?php echo count($pendingKYC); ?> Pending</span>
            <span class="badge bg-info"><?php echo count($allKYC); ?> Total</span>
        </div>
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
</div>

<!-- Pending KYC Verifications -->
<?php if (!empty($pendingKYC)): ?>
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-clock me-2"></i>Pending Verifications
        </h4>
        <span class="badge bg-warning"><?php echo count($pendingKYC); ?> pending</span>
    </div>
    
    <!-- DataTables Info -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Advanced Table Features:</strong> Use the search box below to filter KYC verifications instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
    </div>
    
    <div class="table-responsive">
        <table id="pendingKycTable" class="table table-hover table-striped display">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>User Details</th>
                    <th class="text-center">Document Type</th>
                    <th class="text-center">Document Number</th>
                    <th class="text-center">Submitted</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingKYC as $kyc): ?>
                <tr>
                    <td class="text-center">
                        <span class="badge bg-secondary">#<?php echo $kyc['id']; ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($kyc['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($kyc['user_email']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $kyc['document_type'])); ?></span>
                    </td>
                    <td class="text-center">
                        <code><?php echo htmlspecialchars($kyc['document_number']); ?></code>
                    </td>
                    <td class="text-center">
                        <div class="text-nowrap">
                            <div><?php echo date('M j, Y', strtotime($kyc['created_at'])); ?></div>
                            <small class="text-muted"><?php echo date('H:i', strtotime($kyc['created_at'])); ?></small>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument('<?php echo base_url($kyc['document_image']); ?>')" title="View Document">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="approveKYC(<?php echo $kyc['id']; ?>)" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectKYC(<?php echo $kyc['id']; ?>)" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- All KYC Verifications -->
<div class="dashboard-card">
    <h4 class="mb-4">
        <i class="fas fa-history me-2"></i>All KYC Verifications
    </h4>
    
    <div class="table-responsive">
        <table id="allKycTable" class="table table-hover table-striped display">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ID</th>
                    <th>User Details</th>
                    <th class="text-center">Document Type</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Submitted</th>
                    <th class="text-center">Verified</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allKYC as $kyc): ?>
                <tr>
                    <td class="text-center">
                        <span class="badge bg-secondary">#<?php echo $kyc['id']; ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold"><?php echo htmlspecialchars($kyc['user_name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($kyc['user_email']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $kyc['document_type'])); ?></span>
                    </td>
                    <td class="text-center">
                        <?php if ($kyc['status'] === 'approved'): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>Approved
                            </span>
                        <?php elseif ($kyc['status'] === 'pending'): ?>
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
                            <div><?php echo date('M j, Y', strtotime($kyc['created_at'])); ?></div>
                            <small class="text-muted"><?php echo date('H:i', strtotime($kyc['created_at'])); ?></small>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php if ($kyc['verified_at']): ?>
                            <div class="text-nowrap">
                                <div><?php echo date('M j, Y', strtotime($kyc['verified_at'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($kyc['verified_at'])); ?></small>
                            </div>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewDocument('<?php echo base_url($kyc['document_image']); ?>')" title="View Document">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if ($kyc['status'] === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="approveKYC(<?php echo $kyc['id']; ?>)" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="rejectKYC(<?php echo $kyc['id']; ?>)" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Document View Modal -->
<div class="modal fade" id="documentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="documentImage" src="" alt="Document" class="img-fluid" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject KYC Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="kyc_id" id="reject_kyc_id">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Rejection *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required 
                                  placeholder="Please provide a reason for rejecting this KYC verification..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject KYC</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// View document
function viewDocument(imageUrl) {
    document.getElementById('documentImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('documentModal')).show();
}

// Approve KYC
function approveKYC(kycId) {
    if (confirm('Are you sure you want to approve this KYC verification?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.href;
        form.innerHTML = `
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="kyc_id" value="${kycId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Reject KYC
function rejectKYC(kycId) {
    document.getElementById('reject_kyc_id').value = kycId;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    console.log('=== KYC DataTables Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable function:', typeof $.fn.DataTable);
    console.log('Pending table element:', $('#pendingKycTable').length);
    console.log('All table element:', $('#allKycTable').length);
    
    // Initialize pending KYC table
    if ($('#pendingKycTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing pending KYC DataTable...');
        $('#pendingKycTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[4, 'asc']], // Sort by submission date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5],
                    className: 'text-center'
                },
                {
                    targets: [5],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search pending KYC:",
                lengthMenu: "Show _MENU_ pending verifications per page",
                info: "Showing _START_ to _END_ of _TOTAL_ pending verifications",
                infoEmpty: "No pending verifications available",
                infoFiltered: "(filtered from _MAX_ total pending verifications)",
                emptyTable: "No pending KYC verifications"
            }
        });
        console.log('Pending KYC DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize pending KYC DataTable');
    }
    
    // Initialize all KYC table
    if ($('#allKycTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing all KYC DataTable...');
        $('#allKycTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[4, 'desc']], // Sort by submission date
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
                search: "Search all KYC:",
                lengthMenu: "Show _MENU_ verifications per page",
                info: "Showing _START_ to _END_ of _TOTAL_ verifications",
                infoEmpty: "No verifications available",
                infoFiltered: "(filtered from _MAX_ total verifications)",
                emptyTable: "No KYC verifications found"
            }
        });
        console.log('All KYC DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize all KYC DataTable');
    }
});
</script>