<?php
use App\Models\User;
use App\Models\Ad;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect('/login');
}

$userModel = new User();
$adModel = new Ad();
$userId = $_SESSION['user_id'];
$user = $userModel->findById($userId);

$errors = [];
$success = '';

// Get ad positions
$adPositions = $adModel->getAdPositions();

// Handle form submission
if ($_POST) {
    $position = $_POST['position'] ?? '';
    $heading = trim($_POST['heading'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $whatsappNumber = trim($_POST['whatsapp_number'] ?? '');
    $callNumber = trim($_POST['call_number'] ?? '');
    
    // Auto-add +91 for Indian mobile numbers (10 digits)
    if (!empty($whatsappNumber) && preg_match('/^[6-9]\d{9}$/', $whatsappNumber)) {
        $whatsappNumber = '+91' . $whatsappNumber;
    }
    if (!empty($callNumber) && preg_match('/^[6-9]\d{9}$/', $callNumber)) {
        $callNumber = '+91' . $callNumber;
    }
    $websiteUrl = trim($_POST['website_url'] ?? '');
    $totalDays = (int)($_POST['total_days'] ?? 1);
    
    // Validation
    if (empty($position)) {
        $errors[] = "Please select an ad position.";
    }
    
    if (empty($heading) || strlen($heading) > 200) {
        $errors[] = "Heading is required and must be 200 characters or less.";
    }
    
    if (empty($description) || strlen($description) > 500) {
        $errors[] = "Description is required and must be 500 characters or less.";
    }
    
    if ($totalDays < 1 || $totalDays > 30) {
        $errors[] = "Total days must be between 1 and 30.";
    }
    
    // Validate action values (only if provided)
    if (!empty($whatsappNumber) && !preg_match('/^[0-9+\-\s\(\)]{10,15}$/', $whatsappNumber)) {
        $errors[] = "Please enter a valid phone number for WhatsApp.";
    }
    
    if (!empty($callNumber) && !preg_match('/^[0-9+\-\s\(\)]{10,15}$/', $callNumber)) {
        $errors[] = "Please enter a valid phone number for call.";
    }
    
    if (!empty($websiteUrl) && !filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "Please enter a valid website URL.";
    }
    
    // Check image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = APP_PATH . '/../public/uploads/ads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Only JPG, JPEG, and PNG images are allowed.";
        }
        
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB
            $errors[] = "Image size must be less than 5MB.";
        }
        
        if (empty($errors)) {
            $imageName = uniqid() . '_' . time() . '.' . $fileExtension;
            $imagePath = $uploadDir . $imageName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $image = 'uploads/ads/' . $imageName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }
    
    // Calculate cost
    $costPerDay = $adModel->getPositionCost($position);
    $totalCost = $costPerDay * $totalDays;
    
    // Check if user has enough points
    if ($user['points'] < $totalCost) {
        $errors[] = "You don't have enough points. Required: {$totalCost}, Available: {$user['points']}";
    }
    
    // Create ad if no errors
    if (empty($errors)) {
        try {
            $adId = $adModel->createAd($userId, $position, $heading, $description, $image, $whatsappNumber, $callNumber, $websiteUrl, $totalDays);
            
            if ($adId) {
                $success = "Ad posted successfully! Your ad is under review. Total cost: {$totalCost} points.";
                // Clear form data
                $position = $heading = $description = $whatsappNumber = $callNumber = $websiteUrl = '';
                $totalDays = 1;
            } else {
                $errors[] = "Failed to post ad. Please try again.";
            }
        } catch (Exception $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}

// Get user's ads
$userAds = $adModel->getUserAds($userId);

include APP_PATH . '/views/layouts/user-header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-bullhorn me-2"></i>Post Advertisement
                    </h4>
                </div>
                <div class="card-body">
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
                    
                    <form method="POST" enctype="multipart/form-data" id="adForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position" class="form-label">Ad Position <span class="text-danger">*</span></label>
                                    <select class="form-select" id="position" name="position" required>
                                        <option value="">Select Position</option>
                                        <?php foreach ($adPositions as $pos): ?>
                                            <option value="<?php echo $pos['position']; ?>" 
                                                    data-cost="<?php echo $pos['cost_per_day']; ?>"
                                                    <?php echo (isset($position) && $position === $pos['position']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($pos['name']); ?> - 
                                                <?php echo $pos['cost_per_day']; ?> points/day
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Choose where your ad will be displayed</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="heading" name="heading" 
                                           maxlength="200" required
                                           value="<?php echo htmlspecialchars($heading ?? ''); ?>">
                                    <div class="form-text">
                                        <span id="headingCount">0</span>/200 characters
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" maxlength="500" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                    <div class="form-text">
                                        <span id="descriptionCount">0</span>/500 characters
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Ad Image</label>
                                    <input type="file" class="form-control" id="image" name="image" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif">
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <div class="border rounded p-2 bg-light">
                                            <div class="d-flex align-items-center">
                                                <img id="previewImg" src="" style="max-width: 100px; max-height: 100px; object-fit: contain;" class="me-3">
                                                <div>
                                                    <div class="fw-bold" id="previewFileName"></div>
                                                    <div class="small text-muted" id="previewFileSize"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-text">
                                        <div>Optional: JPG, PNG, GIF (Max 5MB)</div>
                                        <div id="imageSizeRequirement" class="text-info mt-1">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Select an ad position to see recommended image size
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Action Buttons</label>
                                    <div class="form-text mb-3">Optional: Add call-to-action buttons to your ad</div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="whatsapp_number" class="form-label">
                                                <i class="fab fa-whatsapp text-success me-1"></i>WhatsApp
                                            </label>
                                            <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" 
                                                   placeholder="9876543210" value="<?php echo htmlspecialchars($whatsappNumber ?? ''); ?>">
                                            <div class="form-text">Enter 10-digit mobile number (auto-adds +91)</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="call_number" class="form-label">
                                                <i class="fas fa-phone text-primary me-1"></i>Phone Call
                                            </label>
                                            <input type="text" class="form-control" id="call_number" name="call_number" 
                                                   placeholder="9876543210" value="<?php echo htmlspecialchars($callNumber ?? ''); ?>">
                                            <div class="form-text">Enter 10-digit mobile number (auto-adds +91)</div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-3">
                                            <label for="website_url" class="form-label">
                                                <i class="fas fa-globe text-info me-1"></i>Website
                                            </label>
                                            <input type="url" class="form-control" id="website_url" name="website_url" 
                                                   placeholder="https://example.com" value="<?php echo htmlspecialchars($websiteUrl ?? ''); ?>">
                                            <div class="form-text">Website URL</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="total_days" class="form-label">Duration (Days) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="total_days" name="total_days" 
                                           min="1" max="30" value="<?php echo $totalDays ?? 1; ?>" required>
                                    <div class="form-text">Minimum 1 day, Maximum 30 days</div>
                                    <div id="durationError" class="text-danger small" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ad Rates & Cost Summary -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-chart-line me-2"></i>Ad Rates
                                        </h6>
                                        <div id="adRatesDisplay">
                                            <div class="text-muted">Select a position to see rates</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-calculator me-2"></i>Cost Summary
                                        </h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Cost per day:</small>
                                                <div id="costPerDay">-</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Total days:</small>
                                                <div id="totalDaysDisplay">-</div>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Total cost:</small>
                                            <div class="fw-bold text-primary" id="totalCost">-</div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Your points: <span class="fw-bold"><?php echo $user['points']; ?></span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ad Preview -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-2"></i>Ad Preview
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="adPreview" class="text-center text-muted">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p>Fill in the form above to see your ad preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="testJavaScript()">
                                <i class="fas fa-bug me-2"></i>Test JavaScript
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Post Advertisement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- User's Ads -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>My Advertisements
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($userAds)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <p class="text-muted">You haven't posted any ads yet.</p>
                        </div>
                    <?php else: ?>
                        <!-- DataTables Info -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Advanced Table Features:</strong> Use the search box below to filter your ads instantly. Click column headers to sort. The table is fully responsive and mobile-friendly.
                        </div>
                        
                        <div class="table-responsive">
                            <table id="userAdsTable" class="table table-hover table-striped display">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Position</th>
                                        <th>Heading</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                        <th>Cost</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userAds as $ad): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($ad['position_name']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($ad['heading']); ?></td>
                                            <td>
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
                                            <td><?php echo $ad['total_days']; ?> days</td>
                                            <td><?php echo $ad['total_cost']; ?> points</td>
                                            <td><?php echo date('M j, Y', strtotime($ad['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('jQuery loaded and document ready');
    
    // Character counters
    function updateCharacterCounts() {
        const headingLength = $('#heading').val().length;
        const descriptionLength = $('#description').val().length;
        $('#headingCount').text(headingLength);
        $('#descriptionCount').text(descriptionLength);
        console.log('Character counts updated:', headingLength, descriptionLength);
    }
    
    $('#heading').on('input keyup paste', function() {
        setTimeout(updateCharacterCounts, 10);
    });
    
    $('#description').on('input keyup paste', function() {
        setTimeout(updateCharacterCounts, 10);
    });
    
    // Initialize counters
    updateCharacterCounts();
    
    // Initialize all functions on page load
    console.log('Initializing functions...');
    updateAdRates();
    updateAdPreview();
    calculateCost();
    console.log('All functions initialized');
    
    // Initialize DataTables for user ads
    if ($('#userAdsTable').length && typeof $.fn.DataTable !== 'undefined') {
        console.log('Initializing user ads DataTable...');
        $('#userAdsTable').DataTable({
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            order: [[5, 'desc']], // Sort by creation date
            columnDefs: [
                {
                    targets: [0, 2, 3, 4, 5],
                    className: 'text-center'
                }
            ],
            language: {
                search: "Search your ads:",
                lengthMenu: "Show _MENU_ ads per page",
                info: "Showing _START_ to _END_ of _TOTAL_ ads",
                infoEmpty: "No ads available",
                infoFiltered: "(filtered from _MAX_ total ads)",
                emptyTable: "No advertisements found"
            }
        });
        console.log('User ads DataTable initialized successfully!');
    } else {
        console.error('Failed to initialize user ads DataTable');
        console.error('Table found:', $('#userAdsTable').length);
        console.error('DataTable function available:', typeof $.fn.DataTable);
    }
    
    // Test function
    window.testJavaScript = function() {
        alert('jQuery is working!');
        console.log('Test button clicked');
        updateCharacterCounts();
        updateAdRates();
        updateAdPreview();
        calculateCost();
    };
    
    // Phone number formatting for WhatsApp and Call
    $('#whatsapp_number, #call_number').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        
        // If it's a 10-digit Indian mobile number, show +91 prefix
        if (value.length === 10 && /^[6-9]/.test(value)) {
            $(this).val('+91' + value);
        } else if (value.length > 0 && value.length <= 10) {
            // Show just the digits for 10-digit numbers
            $(this).val(value);
        } else if (value.length > 10) {
            // For longer numbers, format as international
            if (value.startsWith('91') && value.length === 12) {
                $(this).val('+91' + value.substring(2));
            } else {
                $(this).val('+' + value);
            }
        }
    });
    
    // Image size requirements and ad rates based on position
    $('#position').on('change', function() {
        const position = $(this).val();
        const sizeRequirements = {
            'top_banner': {
                sizes: '728x90px (Desktop) or 320x50px (Mobile)',
                description: 'Banner ads at the top of the page',
                format: 'JPG, PNG, GIF'
            },
            'bottom_banner': {
                sizes: '728x90px (Desktop) or 320x50px (Mobile)',
                description: 'Banner ads at the bottom of the page',
                format: 'JPG, PNG, GIF'
            },
            'between_news': {
                sizes: '300x250px (Medium Rectangle) or 336x280px (Large Rectangle)',
                description: 'Display ads between news articles',
                format: 'JPG, PNG, GIF'
            },
            'popup_modal': {
                sizes: '600x400px (Square) or 800x600px (Landscape)',
                description: 'Popup ads that appear after 3 seconds',
                format: 'JPG, PNG, GIF'
            }
        };
        
        const requirementDiv = $('#imageSizeRequirement');
        if (position && sizeRequirements[position]) {
            const req = sizeRequirements[position];
            requirementDiv.html(`
                <div class="alert alert-info p-2 mb-0">
                    <div class="fw-bold"><i class="fas fa-info-circle me-1"></i>${req.description}</div>
                    <div class="small mt-1">
                        <strong>Recommended Size:</strong> ${req.sizes}<br>
                        <strong>Format:</strong> ${req.format}
                    </div>
                </div>
            `);
        } else {
            requirementDiv.html('<i class="fas fa-info-circle me-1"></i>Select an ad position to see recommended image size');
        }
        
        // Update ad rates
        updateAdRates();
        // Update preview
        updateAdPreview();
        // Update cost calculation
        calculateCost();
    });
    
    // Update ad rates display
    function updateAdRates() {
        const position = $('#position option:selected');
        const ratesDisplay = $('#adRatesDisplay');
        console.log('updateAdRates called, position:', position.val());
        
        if (position.length && position.val()) {
            const costPerDay = parseInt(position.data('cost')) || 0;
            const positionName = position.text().split(' - ')[0];
            
            ratesDisplay.html(`
                <div class="mb-2">
                    <strong>${positionName}</strong>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="fw-bold text-primary">${costPerDay}</div>
                        <small class="text-muted">Points/Day</small>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold text-success">₹${costPerDay}</div>
                        <small class="text-muted">Rupees/Day</small>
                    </div>
                </div>
                <div class="mt-2 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    1 Point = ₹1
                </div>
            `);
        } else {
            ratesDisplay.html('<div class="text-muted">Select a position to see rates</div>');
        }
    }
    
    // Update ad preview
    function updateAdPreview() {
        const position = $('#position').val();
        const heading = $('#heading').val();
        const description = $('#description').val();
        const whatsappNumber = $('#whatsapp_number').val();
        const callNumber = $('#call_number').val();
        const websiteUrl = $('#website_url').val();
        const imageFile = $('#image')[0].files[0];
        
        const previewDiv = $('#adPreview');
        
        if (!position) {
            previewDiv.html(`
                <i class="fas fa-image fa-3x mb-3"></i>
                <p>Select an ad position to see preview</p>
            `);
            return;
        }
        
        let previewHtml = '<div class="ad-preview-container">';
        
        // Get image preview HTML
        let imageHtml = '';
        if (imageFile) {
            try {
                const imageUrl = URL.createObjectURL(imageFile);
                imageHtml = `<img src="${imageUrl}" style="max-height: 80px; max-width: 200px; object-fit: contain;" class="mb-1">`;
                console.log('Image preview created for:', imageFile.name);
            } catch (e) {
                console.log('Error creating image preview:', e);
                imageHtml = '<i class="fas fa-image fa-2x text-muted mb-2"></i>';
            }
        } else {
            imageHtml = '<i class="fas fa-image fa-2x text-muted mb-2"></i>';
        }
        
        // Position-specific preview
        if (position === 'top_banner' || position === 'bottom_banner') {
            previewHtml += `
                <div class="ad-preview-banner mb-3">
                    <div class="banner-ad" style="width: 100%; max-width: 728px; height: 90px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; background: #f8f9fa; margin: 0 auto;">
                        <div class="text-center">
                            ${imageHtml}
                            ${heading ? '<div class="fw-bold">' + heading + '</div>' : '<div class="text-muted">Ad Heading</div>'}
                        </div>
                    </div>
                </div>
            `;
        } else if (position === 'between_news') {
            const rectangleImageHtml = imageFile ? 
                `<img src="${URL.createObjectURL(imageFile)}" style="max-height: 120px; max-width: 250px; object-fit: contain;" class="mb-2">` : 
                '<i class="fas fa-image fa-2x text-muted mb-2"></i>';
            
            previewHtml += `
                <div class="ad-preview-rectangle mb-3">
                    <div class="rectangle-ad" style="width: 300px; height: 250px; border: 2px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fa; margin: 0 auto;">
                        <div class="text-center p-3">
                            ${rectangleImageHtml}
                            ${heading ? '<div class="fw-bold mb-1">' + heading + '</div>' : '<div class="text-muted mb-1">Ad Heading</div>'}
                            ${description ? '<div class="small">' + description.substring(0, 50) + (description.length > 50 ? '...' : '') + '</div>' : '<div class="small text-muted">Ad Description</div>'}
                        </div>
                    </div>
                </div>
            `;
        } else if (position === 'popup_modal') {
            const popupImageHtml = imageFile ? 
                `<img src="${URL.createObjectURL(imageFile)}" style="max-height: 150px; max-width: 300px; object-fit: contain;" class="mb-2">` : 
                '<i class="fas fa-image fa-3x text-muted mb-2"></i>';
            
            previewHtml += `
                <div class="ad-preview-popup mb-3">
                    <div class="popup-ad" style="width: 400px; height: 300px; border: 2px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f9fa; margin: 0 auto; position: relative;">
                        <div class="text-center p-3">
                            <div class="position-absolute top-0 end-0 p-2">
                                <i class="fas fa-times text-muted"></i>
                            </div>
                            ${popupImageHtml}
                            ${heading ? '<div class="fw-bold mb-1">' + heading + '</div>' : '<div class="text-muted mb-1">Ad Heading</div>'}
                            ${description ? '<div class="small mb-2">' + description.substring(0, 80) + (description.length > 80 ? '...' : '') + '</div>' : '<div class="small text-muted mb-2">Ad Description</div>'}
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Action buttons preview
        if (whatsappNumber || callNumber || websiteUrl) {
            previewHtml += '<div class="action-buttons-preview mt-3">';
            previewHtml += '<div class="small text-muted mb-2">Action Buttons:</div>';
            previewHtml += '<div class="d-flex gap-2 justify-content-center flex-wrap">';
            
            if (whatsappNumber) {
                previewHtml += `<button class="btn btn-success btn-sm"><i class="fab fa-whatsapp me-1"></i>WhatsApp</button>`;
            }
            if (callNumber) {
                previewHtml += `<button class="btn btn-primary btn-sm"><i class="fas fa-phone me-1"></i>Call</button>`;
            }
            if (websiteUrl) {
                previewHtml += `<button class="btn btn-info btn-sm"><i class="fas fa-globe me-1"></i>Visit</button>`;
            }
            
            previewHtml += '</div></div>';
        }
        
        previewHtml += '</div>';
        previewDiv.html(previewHtml);
    }
    
    // Duration validation
    function validateDuration() {
        const totalDays = parseInt($('#total_days').val()) || 0;
        const durationError = $('#durationError');
        
        if (totalDays < 1 || totalDays > 30) {
            durationError.text('Duration must be between 1 and 30 days').show();
            $('#total_days').addClass('is-invalid');
            return false;
        } else {
            durationError.hide();
            $('#total_days').removeClass('is-invalid');
            return true;
        }
    }
    
    // Cost calculation
    function calculateCost() {
        const position = $('#position option:selected');
        const totalDays = parseInt($('#total_days').val()) || 0;
        console.log('calculateCost called, position:', position.val(), 'totalDays:', totalDays);
        
        // Validate duration first
        const isValidDuration = validateDuration();
        
        if (position.length && totalDays > 0 && isValidDuration) {
            const costPerDay = parseInt(position.data('cost')) || 0;
            const totalCost = costPerDay * totalDays;
            
            $('#costPerDay').text(costPerDay + ' points');
            $('#totalDaysDisplay').text(totalDays + ' days');
            $('#totalCost').text(totalCost + ' points');
            
            // Check if user has enough points
            const userPoints = <?php echo $user['points']; ?>;
            if (totalCost > userPoints) {
                $('#totalCost').addClass('text-danger').removeClass('text-primary');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#totalCost').addClass('text-primary').removeClass('text-danger');
                $('#submitBtn').prop('disabled', false);
            }
        } else {
            $('#costPerDay').text('-');
            $('#totalDaysDisplay').text('-');
            $('#totalCost').text('-');
            $('#submitBtn').prop('disabled', true);
        }
    }
    
    $('#position, #total_days').on('change input', function() {
        calculateCost();
        updateAdPreview();
    });
    
    // Update preview when form fields change
    $('#heading, #description, #whatsapp_number, #call_number, #website_url').on('input change', updateAdPreview);
    
    // Handle image file selection
    $('#image').on('change', function() {
        const file = this.files[0];
        const previewDiv = $('#imagePreview');
        const previewImg = $('#previewImg');
        const previewFileName = $('#previewFileName');
        const previewFileSize = $('#previewFileSize');
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF)');
                this.value = '';
                previewDiv.hide();
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must be less than 5MB');
                this.value = '';
                previewDiv.hide();
                return;
            }
            
            // Show image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.attr('src', e.target.result);
                previewFileName.text(file.name);
                previewFileSize.text((file.size / 1024 / 1024).toFixed(2) + ' MB');
                previewDiv.show();
                updateAdPreview();
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.hide();
            updateAdPreview();
        }
    });
    
    // Form submission protection
    $('#adForm').on('submit', function() {
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Posting...');
    });
});
</script>

<?php include APP_PATH . '/views/layouts/user-footer.php'; ?>
