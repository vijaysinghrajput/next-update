<?php
// Fix Ads Positions
echo "🔧 Fixing Ads Positions\n";
echo "======================\n\n";

require_once __DIR__ . '/bootstrap-fixed.php';

use App\Models\Ad;

$adModel = new Ad();

try {
    // Get all ads without positions
    $ads = $adModel->getDb()->fetchAll("SELECT * FROM ads WHERE ad_position_id IS NULL OR ad_position_id = '' ORDER BY id");
    
    echo "📋 Found " . count($ads) . " ads without positions\n\n";
    
    $positions = ['top_banner', 'bottom_banner', 'between_news', 'popup_modal'];
    
    foreach ($ads as $index => $ad) {
        // Assign positions in round-robin fashion
        $position = $positions[$index % count($positions)];
        
        $result = $adModel->getDb()->query(
            "UPDATE ads SET ad_position_id = ? WHERE id = ?", 
            [$position, $ad['id']]
        );
        
        if ($result) {
            echo "✅ Ad ID {$ad['id']} ('{$ad['heading']}') → {$position}\n";
        } else {
            echo "❌ Failed to update Ad ID {$ad['id']}\n";
        }
    }
    
    echo "\n📋 Updated positions:\n";
    $updatedAds = $adModel->getDb()->fetchAll("SELECT id, heading, ad_position_id FROM ads ORDER BY id");
    foreach ($updatedAds as $ad) {
        echo "ID: {$ad['id']} | Position: {$ad['ad_position_id']} | Title: {$ad['heading']}\n";
    }
    
    echo "\n🎉 All ads now have positions!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
