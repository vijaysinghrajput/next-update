<?php
require_once __DIR__ . '/../../bootstrap-fixed.php';

use App\Models\User;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$page_title = "Transactions";
include APP_PATH . '/views/layouts/user-header.php';

$userModel = new User();
$userId = session('user_id');
$user = $userModel->findById($userId);

// Get all transactions
$transactions = $userModel->getTransactions($userId, 50);

// Calculate totals
$totalEarned = 0;
$totalSpent = 0;
foreach ($transactions as $transaction) {
    if ($transaction['transaction_type'] === 'earned') {
        $totalEarned += $transaction['points'];
    } else {
        $totalSpent += $transaction['points'];
    }
}
?>

<!-- Transaction Overview -->
<div class="dashboard-card mb-4">
    <h2 class="mb-4">
        <i class="fas fa-history me-2"></i>Transaction History
    </h2>
    
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <i class="fas fa-coins text-success"></i>
                <h4><?php echo $totalEarned; ?></h4>
                <p>Total Earned</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <i class="fas fa-shopping-cart text-danger"></i>
                <h4><?php echo $totalSpent; ?></h4>
                <p>Total Spent</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <i class="fas fa-wallet text-primary"></i>
                <h4><?php echo $user['points']; ?></h4>
                <p>Current Balance</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card">
                <i class="fas fa-list text-info"></i>
                <h4><?php echo count($transactions); ?></h4>
                <p>Total Transactions</p>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Filters -->
<div class="dashboard-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-filter me-2"></i>Filter Transactions
    </h5>
    
    <div class="row">
        <div class="col-md-3 mb-2">
            <select class="form-select" id="typeFilter" onchange="filterTransactions()">
                <option value="">All Types</option>
                <option value="earned">Earned</option>
                <option value="spent">Spent</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" id="referenceFilter" onchange="filterTransactions()">
                <option value="">All Sources</option>
                <option value="signup">Signup Bonus</option>
                <option value="referral">Referral Bonus</option>
                <option value="kyc">KYC Verification</option>
                <option value="ad">Advertisement</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" class="form-control" id="dateFrom" onchange="filterTransactions()" placeholder="From Date">
        </div>
        <div class="col-md-3 mb-2">
            <input type="date" class="form-control" id="dateTo" onchange="filterTransactions()" placeholder="To Date">
        </div>
    </div>
</div>

<!-- Transaction List -->
<div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>All Transactions
        </h5>
        <span class="badge bg-primary" id="transactionCount"><?php echo count($transactions); ?> Transactions</span>
    </div>
    
    <?php if (empty($transactions)): ?>
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No transactions yet</h6>
            <p class="text-muted">Your transaction history will appear here.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover" id="transactionsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Points</th>
                        <th>Source</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr class="transaction-row" 
                            data-type="<?php echo $transaction['transaction_type']; ?>"
                            data-reference="<?php echo $transaction['reference_type']; ?>"
                            data-date="<?php echo date('Y-m-d', strtotime($transaction['created_at'])); ?>">
                            <td>
                                <i class="fas fa-calendar me-2"></i>
                                <?php echo date('M j, Y', strtotime($transaction['created_at'])); ?>
                                <br>
                                <small class="text-muted"><?php echo date('g:i A', strtotime($transaction['created_at'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $transaction['transaction_type'] === 'earned' ? 'success' : 'danger'; ?>">
                                    <i class="fas fa-<?php echo $transaction['transaction_type'] === 'earned' ? 'plus' : 'minus'; ?> me-1"></i>
                                    <?php echo ucfirst($transaction['transaction_type']); ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($transaction['description']); ?></strong>
                            </td>
                            <td>
                                <span class="fw-bold text-<?php echo $transaction['transaction_type'] === 'earned' ? 'success' : 'danger'; ?>">
                                    <?php echo $transaction['transaction_type'] === 'earned' ? '+' : '-'; ?><?php echo $transaction['points']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($transaction['reference_type']): ?>
                                    <span class="badge bg-info">
                                        <?php echo ucfirst($transaction['reference_type']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Completed
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function filterTransactions() {
    const typeFilter = document.getElementById('typeFilter').value;
    const referenceFilter = document.getElementById('referenceFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const rows = document.querySelectorAll('.transaction-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let show = true;
        
        // Filter by type
        if (typeFilter && row.dataset.type !== typeFilter) {
            show = false;
        }
        
        // Filter by reference
        if (referenceFilter && row.dataset.reference !== referenceFilter) {
            show = false;
        }
        
        // Filter by date range
        if (dateFrom && row.dataset.date < dateFrom) {
            show = false;
        }
        
        if (dateTo && row.dataset.date > dateTo) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    document.getElementById('transactionCount').textContent = visibleCount + ' Transactions';
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
