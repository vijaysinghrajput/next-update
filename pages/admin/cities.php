<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\News;

// Check if user is logged in and is admin
if (!session('user_id') || !session('is_admin')) {
    redirect('/login');
}

$newsModel = new News();

// Handle AJAX requests FIRST - before any HTML output
if (isset($_GET['action']) || (isset($_POST['action']))) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $id = (int)$_GET['id'];
            try {
                $city = $newsModel->getDb()->fetch("SELECT * FROM cities WHERE id = ?", [$id]);
                echo json_encode(['success' => true, 'data' => $city]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'update':
            $id = (int)$_POST['id'];
            $name = trim($_POST['name'] ?? '');
            $state = trim($_POST['state'] ?? '');
            $country = trim($_POST['country'] ?? '');
            
            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'City name is required.']);
                exit;
            }
            
            try {
                $newsModel->getDb()->query("UPDATE cities SET name = ?, state = ?, country = ?, updated_at = NOW() WHERE id = ?", 
                    [$name, $state, $country, $id]);
                echo json_encode(['success' => true, 'message' => 'City updated successfully!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error updating city: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete':
            $id = (int)$_POST['id'];
            try {
                // Check if city is being used by any news articles
                $usage = $newsModel->getDb()->fetch("SELECT COUNT(*) as count FROM news_articles WHERE city_id = ?", [$id]);
                if ($usage['count'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete city. It is being used by ' . $usage['count'] . ' news article(s).']);
                    exit;
                }
                
                // Check if city is being used by any users
                $userUsage = $newsModel->getDb()->fetch("SELECT COUNT(*) as count FROM users WHERE city_id = ?", [$id]);
                if ($userUsage['count'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete city. It is being used by ' . $userUsage['count'] . ' user(s).']);
                    exit;
                }
                
                $newsModel->getDb()->query("DELETE FROM cities WHERE id = ?", [$id]);
                echo json_encode(['success' => true, 'message' => 'City deleted successfully!']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error deleting city: ' . $e->getMessage()]);
            }
            exit;
    }
}

// Only include HTML if not an AJAX request
if (!isset($_GET['action']) && !isset($_POST['action'])) {
    $page_title = "Manage Cities";
    include APP_PATH . '/views/layouts/admin-header.php';

    // Handle form submission for adding new city
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $country = trim($_POST['country'] ?? '');
    
        if (!empty($name)) {
            try {
                $newsModel->getDb()->query("INSERT INTO cities (name, state, country, created_at) VALUES (?, ?, ?, NOW())", 
                    [$name, $state, $country]);
                $success = "City added successfully!";
            } catch (Exception $e) {
                $error = "Error adding city: " . $e->getMessage();
            }
        } else {
            $error = "City name is required.";
        }
    }

    // Get all cities with error handling
    try {
        $cities = $newsModel->getDb()->fetchAll("SELECT * FROM cities ORDER BY name ASC") ?? [];
    } catch (Exception $e) {
        $cities = [];
    }
    ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-map-marker-alt me-2"></i>Manage Cities</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
        <i class="fas fa-plus me-2"></i>Add City
    </button>
</div>

<!-- Success/Error Messages -->
<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Cities Table -->
<div class="dashboard-card">
    <h5 class="mb-3">
        <i class="fas fa-list me-2"></i>All Cities
    </h5>
    
    <?php if (empty($cities)): ?>
        <div class="text-center py-4">
            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">No cities found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cities as $city): ?>
                        <tr>
                            <td><?php echo $city['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($city['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($city['state']); ?></td>
                            <td><?php echo htmlspecialchars($city['country']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($city['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="editCity(<?php echo $city['id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteCity(<?php echo $city['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Add City Modal -->
<div class="modal fade" id="addCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New City
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">City Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state">
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="India">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit City Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit City
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCityForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">City Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_state" class="form-label">State</label>
                        <input type="text" class="form-control" id="edit_state" name="state">
                    </div>
                    <div class="mb-3">
                        <label for="edit_country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="edit_country" name="country">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCity(id) {
    // Fetch city data
    fetch('?action=get&id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                document.getElementById('edit_id').value = data.data.id;
                document.getElementById('edit_name').value = data.data.name;
                document.getElementById('edit_state').value = data.data.state || '';
                document.getElementById('edit_country').value = data.data.country || '';
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('editCityModal'));
                modal.show();
            } else {
                showMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error fetching city data', 'error');
        });
}

function deleteCity(id) {
    if (confirm('Are you sure you want to delete this city?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showMessage('Error: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error deleting city', 'error');
        });
    }
}

// Handle edit form submission
document.getElementById('editCityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update');
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating city', 'error');
    });
});

// Function to show messages
function showMessage(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Try to find container, fallback to body if not found
    let container = document.querySelector('.container-fluid') || 
                   document.querySelector('.container') || 
                   document.querySelector('main') || 
                   document.body;
    
    if (container) {
        container.insertAdjacentHTML('afterbegin', alertHtml);
    } else {
        // Fallback: create alert in body
        document.body.insertAdjacentHTML('afterbegin', alertHtml);
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php include APP_PATH . '/views/layouts/admin-footer.php'; ?>

<?php } // End of HTML section ?>