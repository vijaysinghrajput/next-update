<?php
// Popup Modal Ads Component
if (empty($popup_modal_ads)) return;
?>

<div class="modal fade" id="popupAdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ad-modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Advertisement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="popupAdContent">
                <!-- Popup ad content will be loaded here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Store popup ads data for JavaScript -->
<script>
    window.popupAds = <?php 
        $popupAdsWithPaths = [];
        foreach ($popup_modal_ads as $ad) {
            $ad['image'] = $ad['image'] ? base_url('public/' . $ad['image']) : null;
            $popupAdsWithPaths[] = $ad;
        }
        echo json_encode($popupAdsWithPaths); 
    ?>;
</script>
