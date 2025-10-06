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

$page_title = "Notifications";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');

// Get notifications
$notifications = $userModel->getNotifications($userId, 50);

// Mark all notifications as read
if (!empty($notifications)) {
    // This would typically update the database to mark notifications as read
    // For now, we'll just show them
}
?>

<!-- Notifications Overview -->
<div class="dashboard-card mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">
                <i class="fas fa-bell me-2"></i>Notifications
            </h2>
            <p class="mb-0 text-muted">Stay updated with your account activity and important announcements.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double me-2"></i>Mark All Read
                </button>
                <button class="btn btn-outline-secondary" onclick="clearAll()">
                    <i class="fas fa-trash me-2"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-bell text-primary"></i>
            <h4><?php echo count($notifications); ?></h4>
            <p>Total Notifications</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-envelope text-warning"></i>
            <h4><?php echo count(array_filter($notifications, function($n) { return !$n['is_read']; })); ?></h4>
            <p>Unread</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-check-circle text-success"></i>
            <h4><?php echo count(array_filter($notifications, function($n) { return $n['is_read']; })); ?></h4>
            <p>Read</p>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <i class="fas fa-cog text-info"></i>
            <h4>Settings</h4>
            <p>Manage Preferences</p>
        </div>
    </div>
</div>

<!-- Notification Filters -->
<div class="dashboard-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-filter me-2"></i>Filter Notifications
    </h5>
    
    <div class="row">
        <div class="col-md-3 mb-2">
            <select class="form-select" id="typeFilter" onchange="filterNotifications()">
                <option value="">All Types</option>
                <option value="info">Info</option>
                <option value="success">Success</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" id="statusFilter" onchange="filterNotifications()">
                <option value="">All Status</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" class="form-control" id="dateFrom" onchange="filterNotifications()" placeholder="From Date">
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" class="form-control" id="dateTo" onchange="filterNotifications()" placeholder="To Date">
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>All Notifications
        </h5>
        <span class="badge bg-primary" id="notificationCount"><?php echo count($notifications); ?> Notifications</span>
    </div>
    
    <?php if (empty($notifications)): ?>
        <div class="text-center py-5">
            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No notifications yet</h6>
            <p class="text-muted">You'll receive notifications about your account activity here.</p>
        </div>
    <?php else: ?>
        <div class="notification-list">
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                     data-type="<?php echo $notification['type']; ?>"
                     data-status="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>"
                     data-date="<?php echo date('Y-m-d', strtotime($notification['created_at'])); ?>">
                    
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-3">
                            <?php
                            $iconClass = 'fas fa-info-circle';
                            $iconColor = 'text-info';
                            
                            switch ($notification['type']) {
                                case 'success':
                                    $iconClass = 'fas fa-check-circle';
                                    $iconColor = 'text-success';
                                    break;
                                case 'warning':
                                    $iconClass = 'fas fa-exclamation-triangle';
                                    $iconColor = 'text-warning';
                                    break;
                                case 'error':
                                    $iconClass = 'fas fa-times-circle';
                                    $iconColor = 'text-danger';
                                    break;
                            }
                            ?>
                            <i class="<?php echo $iconClass . ' ' . $iconColor; ?> fa-lg"></i>
                        </div>
                        
                        <div class="notification-content flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="notification-title mb-1">
                                    <?php echo htmlspecialchars($notification['title']); ?>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="badge bg-primary ms-2">New</span>
                                    <?php endif; ?>
                                </h6>
                                <small class="text-muted">
                                    <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                </small>
                            </div>
                            
                            <p class="notification-message mb-2">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </p>
                            
                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                    <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                        <i class="fas fa-check me-1"></i>Mark as Read
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.notification-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: all 0.3s;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.notification-item.read {
    background-color: white;
    opacity: 0.8;
}

.notification-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.notification-title {
    font-weight: 600;
}

.notification-message {
    color: #6c757d;
    line-height: 1.5;
}

.notification-actions {
    margin-top: 0.5rem;
}

.notification-actions .btn {
    margin-right: 0.5rem;
}
</style>

<script>
function filterNotifications() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const items = document.querySelectorAll('.notification-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        let show = true;
        
        // Filter by type
        if (typeFilter && item.dataset.type !== typeFilter) {
            show = false;
        }
        
        // Filter by status
        if (statusFilter && item.dataset.status !== statusFilter) {
            show = false;
        }
        
        // Filter by date range
        if (dateFrom && item.dataset.date < dateFrom) {
            show = false;
        }
        
        if (dateTo && item.dataset.date > dateTo) {
            show = false;
        }
        
        item.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    document.getElementById('notificationCount').textContent = visibleCount + ' Notifications';
}

function markAsRead(notificationId) {
    // This would typically make an AJAX call to mark the notification as read
    const item = event.target.closest('.notification-item');
    item.classList.remove('unread');
    item.classList.add('read');
    item.dataset.status = 'read';
    
    // Remove the "New" badge
    const badge = item.querySelector('.badge');
    if (badge) badge.remove();
    
    // Hide the "Mark as Read" button
    const markButton = item.querySelector('button[onclick*="markAsRead"]');
    if (markButton) markButton.remove();
    
    // Show success message
    showToast('Notification marked as read', 'success');
}

function markAllAsRead() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => {
        item.classList.remove('unread');
        item.classList.add('read');
        item.dataset.status = 'read';
        
        const badge = item.querySelector('.badge');
        if (badge) badge.remove();
        
        const markButton = item.querySelector('button[onclick*="markAsRead"]');
        if (markButton) markButton.remove();
    });
    
    showToast('All notifications marked as read', 'success');
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const item = event.target.closest('.notification-item');
        item.style.transition = 'opacity 0.3s';
        item.style.opacity = '0';
        
        setTimeout(() => {
            item.remove();
            updateNotificationCount();
        }, 300);
        
        showToast('Notification deleted', 'success');
    }
}

function clearAll() {
    if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
        const items = document.querySelectorAll('.notification-item');
        items.forEach(item => {
            item.style.transition = 'opacity 0.3s';
            item.style.opacity = '0';
        });
        
        setTimeout(() => {
            items.forEach(item => item.remove());
            updateNotificationCount();
        }, 300);
        
        showToast('All notifications cleared', 'success');
    }
}

function updateNotificationCount() {
    const remainingItems = document.querySelectorAll('.notification-item');
    document.getElementById('notificationCount').textContent = remainingItems.length + ' Notifications';
}

function showToast(message, type) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Set default date range (last 30 days)
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
    document.getElementById('dateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
});
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
