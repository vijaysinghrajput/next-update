<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\User;
use App\Models\News;

// Check if user is logged in
if (!session('user_id')) {
    redirect('/login');
}

// Redirect admin users to admin dashboard
if (session('is_admin')) {
    redirect('/admin');
}

$userModel = new User();
$newsModel = new News();
$userId = session('user_id');

// Get the news ID from the URL parameter
$newsId = $id ?? null;


if (!$newsId) {
    redirect('/my-news');
}

try {
    // Get the news article to check if it exists and belongs to user
    $newsToDelete = $newsModel->getById($newsId);
    if (!$newsToDelete || $newsToDelete['user_id'] != $userId) {
        redirect('/my-news?error=not_found');
    }
    
    // Check if user has enough points to deduct
    $pointsToDeduct = config('news_post_points', 10);
    $user = $userModel->findById($userId);
    
    if ($user['points'] >= $pointsToDeduct) {
        // Deduct points earned from posting this news
        $userModel->spendPoints($userId, $pointsToDeduct);
        $userModel->recordTransaction($userId, 'spent', $pointsToDeduct, 'News article deletion - points refunded', 'news', $newsId);
        $pointsMessage = "{$pointsToDeduct} points have been deducted from your account.";
    } else {
        // User doesn't have enough points, just record the transaction as 0
        $userModel->recordTransaction($userId, 'spent', 0, 'News article deletion - no points to deduct', 'news', $newsId);
        $pointsMessage = "No points were deducted as you don't have enough points.";
    }
    
    // Delete the news article
    $newsModel->delete($newsId);
    
    // Redirect to my-news page with success message
    redirect('/my-news?deleted=1&message=' . urlencode($pointsMessage));
    
} catch (Exception $e) {
    redirect('/my-news?error=' . urlencode($e->getMessage()));
}
?>
