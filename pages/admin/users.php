<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$page_title = "Manage Users";
include APP_PATH . '/views/layouts/admin-header.php';

$userModel = new User();

// Handle form submissions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $isActive = $_POST['is_active'] === 'true' ? 1 : 0;
        
        if ($userId > 0) {
            try {
                $userModel->db->query("UPDATE users SET is_active = ? WHERE id = ?", [$isActive, $userId]);
                $success = "User status updated successfully!";
            } catch (Exception $e) {
                $errors[] = "Failed to update user status: " . $e->getMessage();
            }
        }
    } elseif ($action === 'add_points') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        
        if ($userId > 0 && $points > 0 && !empty($reason)) {
            try {
                // Add points to user
                $userModel->addPoints($userId, $points, true);
                
                // Record transaction
                $userModel->db->query("
                    INSERT INTO user_transactions (user_id, transaction_type, points, description, reference_type, reference_id) 
                    VALUES (?, 'bonus', ?, ?, 'admin', ?)
                ", [$userId, $points, "Admin bonus: " . $reason, session('user_id')]);
                
                $success = "Points added successfully!";
            } catch (Exception $e) {
                $errors[] = "Failed to add points: " . $e->getMessage();
            }
        } else {
            $errors[] = "Invalid data provided.";
        }
    }
    
    // Redirect to prevent resubmission
    if (isset($success)) {
        redirect('/admin/users?success=' . urlencode($success));
    }
}

// Get success message from URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Get all users with error handling
try {
    $users = $userModel->getDb()->fetchAll("SELECT * FROM users ORDER BY created_at DESC") ?? [];
} catch (Exception $e) {
    $users = [];
}

// Get user statistics
try {
    $totalUsers = $userModel->getDb()->fetch("SELECT COUNT(*) as count FROM users")['count'] ?? 0;
    $activeUsers = $userModel->getDb()->fetch("SELECT COUNT(*) as count FROM users WHERE is_active = 1")['count'] ?? 0;
    $adminUsers = $userModel->getDb()->fetch("SELECT COUNT(*) as count FROM users WHERE is_admin = 1")['count'] ?? 0;
    $verifiedUsers = $userModel->getDb()->fetch("SELECT COUNT(*) as count FROM users WHERE is_verified = 1")['count'] ?? 0;
} catch (Exception $e) {
    $totalUsers = $activeUsers = $adminUsers = $verifiedUsers = 0;
}
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users me-2"></i>Manage Users</h2>
    <div class="text-muted">
        <small>Total: <?php echo $totalUsers; ?> users</small>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-persistent">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-users text-primary"></i>
            <h3><?php echo $totalUsers; ?></h3>
            <p class="text-muted">Total Users</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-user-check text-success"></i>
            <h3><?php echo $activeUsers; ?></h3>
            <p class="text-muted">Active Users</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-crown text-warning"></i>
            <h3><?php echo $adminUsers; ?></h3>
            <p class="text-muted">Admin Users</p>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-shield-alt text-info"></i>
            <h3><?php echo $verifiedUsers; ?></h3>
            <p class="text-muted">Verified Users</p>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="dashboard-card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>All Users
        </h5>
        <span class="badge bg-info"><?php echo count($users); ?> total users</span>
    </div>
    
    <!-- DataTables Info -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Advanced Table Features:</strong> Use the search box below to filter users instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
    </div>
    
    <?php if (empty($users)): ?>
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <p class="text-muted">No users found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover table-striped display">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">ID</th>
                        <th>User Info</th>
                        <th class="text-center">Points</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">KYC</th>
                        <th class="text-center">Created</th>
                        <th class="text-center">Last Login</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary">#<?php echo $user['id']; ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if ($user['profile_image']): ?>
                                        <img src="<?php echo base_url('public/' . $user['profile_image']); ?>" 
                                             class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                        <div class="small text-muted">@<?php echo htmlspecialchars($user['username']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                                        <?php if ($user['phone']): ?>
                                            <div class="small text-muted">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($user['phone']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6"><?php echo number_format($user['points']); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($user['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-warning">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $kycStatus = $user['kyc_status'] ?? 'pending';
                                $kycClass = match($kycStatus) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'pending' => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?php echo $kycClass; ?>">
                                    <?php echo ucfirst($kycStatus); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <small><?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                            </td>
                            <td class="text-center">
                                <?php if ($user['last_login']): ?>
                                    <small><?php echo date('M j, Y H:i', strtotime($user['last_login'])); ?></small>
                                <?php else: ?>
                                    <small class="text-muted">Never</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?php echo base_url('admin/users/' . $user['id']); ?>" 
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" 
                                            onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 'false' : 'true'; ?>)" 
                                            title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?> User">
                                        <i class="fas fa-<?php echo $user['is_active'] ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                    <?php if (!$user['is_admin']): ?>
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="addPoints(<?php echo $user['id']; ?>)" 
                                                title="Add Points">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add Points Modal -->
<div class="modal fade" id="addPointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Points to User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="addPointsForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_points">
                    <input type="hidden" name="user_id" id="add_points_user_id">
                    <div class="mb-3">
                        <label for="points_amount" class="form-label">Points Amount</label>
                        <input type="number" class="form-control" id="points_amount" name="points" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="points_reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="points_reason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Points</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    console.log('=== Users DataTables Debug ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('DataTable function:', typeof $.fn.DataTable);
    console.log('Table element:', $('#usersTable').length);
    
    // Initialize DataTables for users
    if ($('#usersTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing users DataTable...');
        $('#usersTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[6, 'desc']], // Sort by creation date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5, 6, 7, 8],
                    className: 'text-center'
                },
                {
                    targets: [8],
                    orderable: false,
                    searchable: false
                }
            ],
            language: {
                search: "Search users:",
                lengthMenu: "Show _MENU_ users per page",
                info: "Showing _START_ to _END_ of _TOTAL_ users",
                infoEmpty: "No users available",
                infoFiltered: "(filtered from _MAX_ total users)",
                emptyTable: "No users found"
            }
        });
        console.log('Users DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize users DataTable');
        console.error('Table found:', $('#usersTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
});

// Toggle user status
function toggleUserStatus(userId, newStatus) {
    if (confirm('Are you sure you want to ' + (newStatus ? 'activate' : 'deactivate') + ' this user?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="user_id" value="${userId}">
            <input type="hidden" name="is_active" value="${newStatus}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Add points to user
function addPoints(userId) {
    document.getElementById('add_points_user_id').value = userId;
    new bootstrap.Modal(document.getElementById('addPointsModal')).show();
}
</script>
