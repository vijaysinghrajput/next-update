<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;
use App\Models\Ad;

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    redirect('/login');
}

$userModel = new User();
$adModel = new Ad();
$adminId = $_SESSION['user_id'];

$errors = [];
$success = '';

// Handle admin actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $adId = (int)($_POST['ad_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    if ($action === 'approve' && $adId > 0) {
        try {
            $ad = $adModel->getAdById($adId);
            if ($ad && $ad['status'] === 'pending') {
                // Approve the ad
                if ($adModel->approveAd($adId, $adminId, $notes)) {
                    // Deduct points from user
                    $adModel->deductAdPoints($ad['user_id'], $ad['total_cost'], $adId);
                    $success = "Ad approved successfully! Points deducted from user account.";
                } else {
                    $errors[] = "Failed to approve ad.";
                }
            } else {
                $errors[] = "Ad not found or not pending.";
            }
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    } elseif ($action === 'reject' && $adId > 0) {
        try {
            $ad = $adModel->getAdById($adId);
            if ($ad && $ad['status'] === 'pending') {
                // Reject the ad
                if ($adModel->rejectAd($adId, $adminId, $notes)) {
                    $success = "Ad rejected successfully.";
                } else {
                    $errors[] = "Failed to reject ad.";
                }
            } else {
                $errors[] = "Ad not found or not pending.";
            }
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
    
    // Redirect to prevent resubmission
    if ($success) {
        redirect('/admin/ads?success=' . urlencode($success));
    }
}

// Get success message from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Get ads data
try {
    $allAds = $adModel->getAllAds();
    $pendingAds = $adModel->getPendingAds();
    $adStats = $adModel->getAdStats();
} catch (Exception $e) {
    $errors[] = "Failed to load ads data: " . $e->getMessage();
    $allAds = [];
    $pendingAds = [];
    $adStats = [];
}

include APP_PATH . '/views/layouts/admin-header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Total Ads</h6>
                                    <h3><?php echo ($adStats['pending'] ?? 0) + ($adStats['approved'] ?? 0) + ($adStats['active'] ?? 0) + ($adStats['completed'] ?? 0) + ($adStats['rejected'] ?? 0); ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-bullhorn fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Pending</h6>
                                    <h3><?php echo $adStats['pending'] ?? 0; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Active</h6>
                                    <h3><?php echo $adStats['active'] ?? 0; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-play fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title">Revenue</h6>
                                    <h3><?php echo $adStats['total_revenue'] ?? 0; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-coins fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="alert alert-success alert-persistent">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Pending Ads -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Pending Advertisements
                    </h4>
                    <span class="badge bg-warning"><?php echo count($pendingAds); ?> pending</span>
                </div>
                
                <!-- DataTables Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Advanced Table Features:</strong> Use the search box below to filter pending ads instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
                </div>
                
                <div class="card-body">
                    <?php if (empty($pendingAds)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">No pending advertisements to review.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="pendingAdsTable" class="table table-hover table-striped display">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>Ad Details</th>
                                        <th class="text-center">Position</th>
                                        <th class="text-center">Duration</th>
                                        <th class="text-center">Cost</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Created</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingAds as $ad): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">#<?php echo $ad['id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <?php if ($ad['image']): ?>
                                                        <img src="<?php echo base_url('public/' . $ad['image']); ?>" 
                                                             class="rounded me-3" style="width: 60px; height: 40px; object-fit: cover;"
                                                             onclick="viewAdImage('<?php echo base_url('public/' . $ad['image']); ?>')">
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($ad['heading']); ?></h6>
                                                        <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($ad['description'], 0, 100)); ?>...</p>
                                                        <div class="small">
                                                            <?php if ($ad['whatsapp_number']): ?>
                                                                <div class="text-success">
                                                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp: <?php echo htmlspecialchars($ad['whatsapp_number']); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($ad['call_number']): ?>
                                                                <div class="text-primary">
                                                                    <i class="fas fa-phone me-1"></i>Call: <?php echo htmlspecialchars($ad['call_number']); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if ($ad['website_url']): ?>
                                                                <div class="text-info">
                                                                    <i class="fas fa-globe me-1"></i>Website: <?php echo htmlspecialchars($ad['website_url']); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?php echo htmlspecialchars($ad['position_name']); ?></span>
                                            </td>
                                            <td class="text-center"><?php echo $ad['total_days']; ?> days</td>
                                            <td class="text-center">
                                                <strong><?php echo $ad['total_cost']; ?> points</strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="small">
                                                    <div><?php echo htmlspecialchars($ad['user_name']); ?></div>
                                                    <div class="text-muted"><?php echo htmlspecialchars($ad['user_email']); ?></div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <small><?php echo date('M j, Y H:i', strtotime($ad['created_at'])); ?></small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-success me-1" onclick="approveAd(<?php echo $ad['id']; ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="rejectAd(<?php echo $ad['id']; ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- All Ads -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2"></i>All Advertisements
                    </h4>
                    <span class="badge bg-info"><?php echo count($allAds); ?> total ads</span>
                </div>
                
                <!-- DataTables Info -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Advanced Table Features:</strong> Use the search box below to filter all ads instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="allAdsTable" class="table table-hover table-striped display">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Ad Details</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">Cost</th>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allAds as $ad): ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">#<?php echo $ad['id']; ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <?php if ($ad['image']): ?>
                                                    <img src="<?php echo base_url('public/' . $ad['image']); ?>"
                                                         class="rounded me-3" style="width: 60px; height: 40px; object-fit: cover;"
                                                         onclick="viewAdImage('<?php echo base_url('public/' . $ad['image']); ?>')">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($ad['heading']); ?></h6>
                                                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($ad['description'], 0, 100)); ?>...</p>
                                                    <small class="text-info">
                                                        <i class="fas fa-<?php echo $ad['action_type'] === 'whatsapp' ? 'whatsapp' : ($ad['action_type'] === 'call' ? 'phone' : 'globe'); ?> me-1"></i>
                                                        <?php echo ucfirst($ad['action_type']); ?>: <?php echo htmlspecialchars($ad['action_value']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?php echo htmlspecialchars($ad['position_name']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $statusClass = match($ad['status']) {
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'active' => 'bg-primary',
                                                'rejected' => 'bg-danger',
                                                'completed' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <?php echo ucfirst($ad['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo $ad['total_days']; ?> days</td>
                                        <td class="text-center">
                                            <strong><?php echo $ad['total_cost']; ?> points</strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="small">
                                                <div><?php echo htmlspecialchars($ad['user_name']); ?></div>
                                                <div class="text-muted"><?php echo htmlspecialchars($ad['user_email']); ?></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <small><?php echo date('M j, Y H:i', strtotime($ad['created_at'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ad Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="adImage" src="" class="img-fluid" alt="Ad Image">
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="ad_id" id="approve_ad_id">
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="ad_id" id="reject_ad_id">
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== Ads DataTables Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable function:', typeof $.fn.DataTable);
    console.log('Pending table element:', $('#pendingAdsTable').length);
    console.log('All table element:', $('#allAdsTable').length);
    
    // Initialize DataTables for pending ads
    if ($('#pendingAdsTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing pending ads DataTable...');
        $('#pendingAdsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[6, 'asc']], // Sort by creation date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5, 6, 7],
                    className: 'text-center'
                },
                {
                    targets: [7],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search pending ads:",
                lengthMenu: "Show _MENU_ ads per page",
                info: "Showing _START_ to _END_ of _TOTAL_ pending ads",
                infoEmpty: "No pending ads available",
                infoFiltered: "(filtered from _MAX_ total pending ads)",
                emptyTable: "No pending advertisements"
            }
        });
        console.log('Pending ads DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize pending ads DataTable');
        console.error('Table found:', $('#pendingAdsTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
    
    // Initialize DataTables for all ads
    if ($('#allAdsTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing all ads DataTable...');
        $('#allAdsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[7, 'desc']], // Sort by creation date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5, 6, 7],
                    className: 'text-center'
                }
            ],
            language: {
                search: "Search all ads:",
                lengthMenu: "Show _MENU_ ads per page",
                info: "Showing _START_ to _END_ of _TOTAL_ ads",
                infoEmpty: "No ads available",
                infoFiltered: "(filtered from _MAX_ total ads)",
                emptyTable: "No advertisements found"
            }
        });
        console.log('All ads DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize all ads DataTable');
        console.error('Table found:', $('#allAdsTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
});

// View ad image
function viewAdImage(imageUrl) {
    document.getElementById('adImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Approve ad
function approveAd(adId) {
    document.getElementById('approve_ad_id').value = adId;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

// Reject ad
function rejectAd(adId) {
    document.getElementById('reject_ad_id').value = adId;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>