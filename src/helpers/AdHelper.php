<?php
namespace App\Helpers;

use App\Models\Ad;

class AdHelper {
    private static $adModel = null;
    
    private static function getAdModel() {
        if (self::$adModel === null) {
            self::$adModel = new Ad();
        }
        return self::$adModel;
    }
    
    /**
     * Display top banner ad
     */
    public static function displayTopBanner() {
        $ads = self::getAdModel()->getActiveAds('top_banner');
        
        if (empty($ads)) {
            return '';
        }
        
        // Get random ad for variety
        $ad = $ads[array_rand($ads)];
        
        return self::renderAd($ad, 'top-banner');
    }
    
    /**
     * Display bottom banner ad
     */
    public static function displayBottomBanner() {
        $ads = self::getAdModel()->getActiveAds('bottom_banner');
        
        if (empty($ads)) {
            return '';
        }
        
        // Get random ad for variety
        $ad = $ads[array_rand($ads)];
        
        return self::renderAd($ad, 'bottom-banner');
    }
    
    /**
     * Display between news ad
     */
    public static function displayBetweenNews() {
        $ads = self::getAdModel()->getActiveAds('between_news');
        
        if (empty($ads)) {
            return '';
        }
        
        // Get random ad for variety
        $ad = $ads[array_rand($ads)];
        
        return self::renderAd($ad, 'between-news');
    }
    
    /**
     * Display popup modal ad
     */
    public static function displayPopupModal() {
        $ads = self::getAdModel()->getActiveAds('popup_modal');
        
        if (empty($ads)) {
            return '';
        }
        
        // Get random ad for variety
        $ad = $ads[array_rand($ads)];
        
        return self::renderAd($ad, 'popup-modal', true);
    }
    
    /**
     * Render ad HTML
     */
    private static function renderAd($ad, $position, $isModal = false) {
        $actionUrl = self::getActionUrl($ad['action_type'], $ad['action_value']);
        $imageUrl = $ad['image'] ? base_url($ad['image']) : '';
        
        if ($isModal) {
            return self::renderModalAd($ad, $actionUrl, $imageUrl);
        }
        
        $html = '<div class="ad-container ad-' . $position . ' mb-4">';
        $html .= '<div class="ad-content border rounded p-3 bg-light">';
        
        if ($imageUrl) {
            $html .= '<div class="ad-image mb-3">';
            $html .= '<img src="' . htmlspecialchars($imageUrl) . '" class="img-fluid rounded" alt="' . htmlspecialchars($ad['heading']) . '">';
            $html .= '</div>';
        }
        
        $html .= '<div class="ad-text">';
        $html .= '<h5 class="ad-heading mb-2">' . htmlspecialchars($ad['heading']) . '</h5>';
        $html .= '<p class="ad-description mb-3">' . htmlspecialchars($ad['description']) . '</p>';
        
        $html .= '<div class="ad-actions">';
        $html .= '<a href="' . htmlspecialchars($actionUrl) . '" class="btn btn-primary btn-sm" target="_blank">';
        $html .= '<i class="fas fa-' . self::getActionIcon($ad['action_type']) . ' me-1"></i>';
        $html .= ucfirst($ad['action_type']);
        $html .= '</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="ad-label text-center">';
        $html .= '<small class="text-muted">Advertisement</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render modal ad
     */
    private static function renderModalAd($ad, $actionUrl, $imageUrl) {
        $modalId = 'adModal_' . $ad['id'];
        
        $html = '<div class="modal fade" id="' . $modalId . '" tabindex="-1" data-bs-backdrop="static">';
        $html .= '<div class="modal-dialog modal-dialog-centered">';
        $html .= '<div class="modal-content">';
        $html .= '<div class="modal-header">';
        $html .= '<h5 class="modal-title">' . htmlspecialchars($ad['heading']) . '</h5>';
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
        $html .= '</div>';
        $html .= '<div class="modal-body text-center">';
        
        if ($imageUrl) {
            $html .= '<img src="' . htmlspecialchars($imageUrl) . '" class="img-fluid rounded mb-3" alt="' . htmlspecialchars($ad['heading']) . '">';
        }
        
        $html .= '<p>' . htmlspecialchars($ad['description']) . '</p>';
        $html .= '</div>';
        $html .= '<div class="modal-footer">';
        $html .= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        $html .= '<a href="' . htmlspecialchars($actionUrl) . '" class="btn btn-primary" target="_blank">';
        $html .= '<i class="fas fa-' . self::getActionIcon($ad['action_type']) . ' me-1"></i>';
        $html .= ucfirst($ad['action_type']);
        $html .= '</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Auto-show modal script
        $html .= '<script>';
        $html .= 'document.addEventListener("DOMContentLoaded", function() {';
        $html .= 'setTimeout(function() {';
        $html .= 'var modal = new bootstrap.Modal(document.getElementById("' . $modalId . '"));';
        $html .= 'modal.show();';
        $html .= '}, 3000);'; // Show after 3 seconds
        $html .= '});';
        $html .= '</script>';
        
        return $html;
    }
    
    /**
     * Get action URL based on type
     */
    private static function getActionUrl($type, $value) {
        switch ($type) {
            case 'whatsapp':
                $phone = preg_replace('/[^0-9+]/', '', $value);
                return 'https://wa.me/' . $phone;
            case 'call':
                return 'tel:' . $value;
            case 'website':
                return $value;
            default:
                return '#';
        }
    }
    
    /**
     * Get action icon
     */
    private static function getActionIcon($type) {
        switch ($type) {
            case 'whatsapp':
                return 'whatsapp';
            case 'call':
                return 'phone';
            case 'website':
                return 'globe';
            default:
                return 'external-link-alt';
        }
    }
    
    /**
     * Auto-activate approved ads
     */
    public static function autoActivateAds() {
        $adModel = self::getAdModel();
        
        // Get approved ads that should be active
        $query = "SELECT * FROM ads WHERE status = 'approved' 
                  AND start_date <= CURDATE() AND end_date >= CURDATE()";
        
        $approvedAds = $adModel->db->fetchAll($query);
        
        foreach ($approvedAds as $ad) {
            $adModel->activateAd($ad['id']);
        }
        
        // Complete expired ads
        $adModel->completeExpiredAds();
    }
}
