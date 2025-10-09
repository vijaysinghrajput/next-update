<?php
// Homepage JavaScript Component
?>

<script>
$(document).ready(function() {
    // Smooth loader on internal link navigation
    $(document).on('click', 'a[href^="/"]', function(e) {
        const href = $(this).attr('href');
        // Ignore anchors, JS links, and external links
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (this.target && this.target.toLowerCase() === '_blank') return;
        // Only same-origin
        const isSameOrigin = this.hostname === window.location.hostname || href.startsWith('/');
        if (!isSameOrigin) return;
        // Show loader and proceed
        $('#loadingOverlay').stop(true, true).fadeIn(100);
    });

    let currentFilter = 'all';
    let currentPage = 1;
    let isLoading = false;
    let hasMoreNews = true;

    // Category slider functionality
    $('.category-item').on('click', function() {
        const filter = $(this).data('filter');
        currentFilter = filter;
        currentPage = 1;
        hasMoreNews = true;
        
        // Update active state
        $('.category-item').removeClass('active');
        $(this).addClass('active');
        
        // Smooth scroll to center the selected item
        const container = $('#categorySlider')[0];
        const item = this;
        const containerWidth = container.offsetWidth;
        const itemOffset = item.offsetLeft;
        const itemWidth = item.offsetWidth;
        const scrollLeft = itemOffset - (containerWidth / 2) + (itemWidth / 2);
        
        container.scrollTo({
            left: scrollLeft,
            behavior: 'smooth'
        });
        
        // Filter existing news
        filterNews(filter);
        
        // Load new news for this filter
        loadMoreNews();
    });
    
    function filterNews(filter) {
        $('.news-item, .mobile-list-item').each(function() {
            const $item = $(this);
            const category = $item.data('category');
            const isBansgaonsandesh = $item.data('is-bansgaonsandesh');
            let show = false;
            
            switch(filter) {
                case 'all':
                    show = true;
                    break;
                case 'featured':
                    show = $item.hasClass('featured') || $item.find('.item-icon i').hasClass('fa-star');
                    break;
                case 'bansgaonsandesh':
                    show = isBansgaonsandesh == 1;
                    break;
                default:
                    if (filter.startsWith('category-')) {
                        const categoryId = filter.split('-')[1];
                        show = category == categoryId;
                    }
                    break;
            }
            
            if (show) {
                $item.removeClass('hidden').show();
            } else {
                $item.addClass('hidden').hide();
            }
        });
    }

    // Infinite scroll
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!isLoading && hasMoreNews) {
                loadMoreNews();
            }
        }
    });

    function renderSkeleton(count = 4) {
        const $newsList = $('#newsList');
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
                <div class="skeleton-news-card">
                    <div class="skeleton-block skeleton-thumb"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-block skeleton-line long"></div>
                        <div class="skeleton-block skeleton-line medium"></div>
                        <div class="skeleton-block skeleton-line short" style="margin-top:14px;"></div>
                    </div>
                </div>
            `;
        }
        $newsList.append(html);
        return count;
    }

    function removeSkeleton(count = 4) {
        const $newsList = $('#newsList');
        $newsList.find('.skeleton-news-card').slice(-count).remove();
    }

    // Hide loading sections by default
    $('.loading-section').hide();

    function loadMoreNews() {
        if (isLoading) return;
        
        isLoading = true;
        const skeletonCount = renderSkeleton(4);
        // Show and update loading sections
        const $loadingSections = $('.loading-section');
        $loadingSections.stop(true, true).show().removeClass('success error').addClass('loading');
        $loadingSections.find('.loading-text').text('Loading more stories...');
        $loadingSections.find('.loading-subtitle').text('Discovering fresh content for you');
        
        $.ajax({
            url: '<?php echo base_url('api/news'); ?>',
            method: 'GET',
            data: {
                page: currentPage + 1,
                limit: 10,
                filter: currentFilter
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    currentPage++;
                    appendNews(response.data);
                } else {
                    hasMoreNews = false;
                    // Mark both loading sections as success when no more items
                    $loadingSections.removeClass('loading error').addClass('success');
                    $loadingSections.find('.loading-text').text('You are all caught up');
                    $loadingSections.find('.loading-subtitle').text('No more stories right now');
                    // Auto-hide after delay
                    setTimeout(function(){ $loadingSections.fadeOut(300); }, 1800);
                }
            },
            error: function() {
                console.log('Error loading more news');
                $loadingSections.removeClass('loading success').addClass('error');
                $loadingSections.find('.loading-text').text('Failed to load stories');
                $loadingSections.find('.loading-subtitle').text('Please check your connection and try again');
                // Auto-hide after delay
                setTimeout(function(){ $loadingSections.fadeOut(300); }, 2200);
            },
            complete: function() {
                isLoading = false;
                removeSkeleton(skeletonCount);
                // If still loading state (success path handled above), hide after short delay
                setTimeout(function(){ if (!$loadingSections.hasClass('success') && !$loadingSections.hasClass('error')) { $loadingSections.fadeOut(300); } }, 1500);
            }
        });
    }

    function appendNews(newsItems) {
        const $newsList = $('#newsList');
        
        newsItems.forEach(function(news, index) {
            const newsHtml = createNewsItemHtml(news);
            $newsList.append(newsHtml);
            
            // Inject between-news ads every 3 items
            if ((index + 1) % 3 === 0 && window.betweenNewsAds && window.betweenNewsAds.length > 0) {
                const adIndex = Math.floor((index + 1) / 3 - 1) % window.betweenNewsAds.length;
                const ad = window.betweenNewsAds[adIndex];
                const adHtml = createBetweenNewsAdHtml(ad);
                $newsList.append(adHtml);
            }
        });
    }

    function createNewsItemHtml(news) {
        return `
            <div class="mobile-list-item mobile-touch-feedback" data-category="${news.category_id}" data-is-bansgaonsandesh="${news.is_bansgaonsandesh}" onclick="trackNewsView(${news.id})">
                <div class="item-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    ${news.is_bansgaonsandesh ? '<i class="fas fa-crown"></i>' : (news.is_featured ? '<i class="fas fa-star"></i>' : '<i class="fas fa-newspaper"></i>')}
                </div>
                <div class="item-content">
                    ${news.featured_image ? `<img src="${news.featured_image}" alt="${news.title}" class="news-image">` : ''}
                    <div class="item-title">${news.title}</div>
                    <div class="item-subtitle">${news.excerpt || news.content.substring(0, 80) + '...'}</div>
                    <div class="news-meta">
                        <small class="text-muted">
                            <i class="fas fa-tag"></i> ${news.category_name} • 
                            <i class="fas fa-map-marker-alt"></i> ${news.city_name} • 
                            <i class="fas fa-eye"></i> ${news.views || 0} views
                        </small>
                    </div>
                </div>
                <div class="item-action">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        `;
    }

function createBetweenNewsAdHtml(ad) {
    return `
        <div class="between-news-ad">
            <div class="ad-header">
                <span class="ad-label">Advertisement</span>
            </div>
            <div class="ad-card-container">
                <div class="ad-card" onclick="trackAdClick(${ad.id})">
                    <div class="ad-tag">Advertisement</div>
                    ${ad.image ? `
                        <div class="ad-image-wrapper">
                            <img src="${ad.image}" alt="${ad.heading}" class="ad-image">
                        </div>
                    ` : ''}
                    <div class="ad-content">
                        <h3 class="ad-title">${ad.heading}</h3>
                        <p class="ad-description">${ad.description}</p>
                        <div class="ad-actions">
                            ${ad.whatsapp_number ? `<button class="action-btn whatsapp-btn" data-action="whatsapp" data-phone="${ad.whatsapp_number}"><span class="whatsapp-icon">💬</span><span>WhatsApp</span></button>` : ''}
                            ${ad.call_number ? `<button class="action-btn call-btn" data-action="call" data-phone="${ad.call_number}"><i class="fas fa-phone"></i><span>Call</span></button>` : ''}
                            ${ad.website_url ? `<a href="${ad.website_url}" class="action-btn visit-btn"><i class="fas fa-globe"></i><span>Visit</span></a>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

    // Ad click tracking
    window.trackAdClick = function(adId) {
        $.ajax({
            url: '<?php echo base_url('api/track-ad'); ?>',
            method: 'POST',
            data: { ad_id: adId },
            success: function(response) {
                console.log('Ad click tracked');
            }
        });
    };

    // News click tracking
    window.trackNewsView = function(newsId) {
        $.ajax({
            url: '<?php echo base_url('api/track-news-view'); ?>',
            method: 'POST',
            data: { news_id: newsId }
        });
    };

    // Popup ad system - show random popup ads every 2 minutes
    let popupAdInterval;
    let lastPopupTime = 0;
    
    function showRandomPopupAd() {
        console.log('Checking for popup ads...', window.popupAds);
        const popupModal = document.getElementById('popupAdModal');
        const popupContent = document.getElementById('popupAdContent');
        
        if (popupModal && popupContent && window.popupAds && window.popupAds.length > 0) {
            console.log('Popup ads found:', window.popupAds.length);
            const randomAd = window.popupAds[Math.floor(Math.random() * window.popupAds.length)];
            console.log('Selected popup ad:', randomAd);
            
        const popupHtml = `
            <div class="ad-card-container">
                <div class="ad-card" onclick="trackAdClick(${randomAd.id})">
                    <div class="ad-tag">Advertisement</div>
                    ${randomAd.image ? `
                        <div class="ad-image-wrapper">
                            <img src="${randomAd.image}" 
                                 alt="${randomAd.heading}" 
                                 class="ad-image">
                        </div>
                    ` : ''}
                    <div class="ad-content">
                        <h3 class="ad-title">${randomAd.heading}</h3>
                        <p class="ad-description">${randomAd.description}</p>
                        <div class="ad-actions">
                            ${randomAd.whatsapp_number ? `<button class="action-btn whatsapp-btn" data-action="whatsapp" data-phone="${randomAd.whatsapp_number}"><span class="whatsapp-icon">💬</span><span>WhatsApp</span></button>` : ''}
                            ${randomAd.call_number ? `<button class="action-btn call-btn" data-action="call" data-phone="${randomAd.call_number}"><i class="fas fa-phone"></i><span>Call</span></button>` : ''}
                            ${randomAd.website_url ? `<a href="${randomAd.website_url}" class="action-btn visit-btn"><i class="fas fa-globe"></i><span>Visit</span></a>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
            
            popupContent.innerHTML = popupHtml;
            const modal = new bootstrap.Modal(popupModal);
            modal.show();
            console.log('Popup ad modal shown');
            
            // Track the popup ad view
            trackAdClick(randomAd.id);
        } else {
            console.log('Popup ad conditions not met:', {
                popupModal: !!popupModal,
                popupContent: !!popupContent,
                popupAds: window.popupAds,
                popupAdsLength: window.popupAds ? window.popupAds.length : 0
            });
        }
    }
    
    // Show first popup ad after 5 seconds
    setTimeout(function() {
        showRandomPopupAd();
        lastPopupTime = Date.now();
    }, 5000);
    
    // Add manual trigger for testing (remove in production)
    window.testPopupAd = function() {
        showRandomPopupAd();
    };
    
    // Set up interval to show popup ads every 2 minutes (120000ms)
    popupAdInterval = setInterval(function() {
        const now = Date.now();
        // Only show if at least 2 minutes have passed since last popup
        if (now - lastPopupTime >= 120000) {
            showRandomPopupAd();
            lastPopupTime = now;
        }
    }, 30000); // Check every 30 seconds
    
    // Function to refresh popup ads data
    function refreshPopupAds() {
        fetch('<?php echo base_url('api/ads'); ?>?position=popup_modal&status=active')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    window.popupAds = data.data.map(ad => ({
                        ...ad,
                        image: ad.image ? '<?php echo base_url('public/'); ?>' + ad.image : null
                    }));
                    console.log('Popup ads refreshed:', window.popupAds.length);
                }
            })
            .catch(error => {
                console.log('Error refreshing popup ads:', error);
            });
    }
    
    // Refresh popup ads data every 5 minutes
    setInterval(refreshPopupAds, 300000);
    
    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (popupAdInterval) {
            clearInterval(popupAdInterval);
        }
    });
    
        // Ensure all images are visible by default
        function ensureImagesVisible() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                img.style.display = 'block';
                img.style.visibility = 'visible';
            });
        }
        
        // Make images visible on page load
        ensureImagesVisible();
        
        // Make dynamically loaded images visible
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            const images = node.querySelectorAll ? node.querySelectorAll('img') : [];
                            images.forEach(img => {
                                img.style.display = 'block';
                                img.style.visibility = 'visible';
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
});
</script>
