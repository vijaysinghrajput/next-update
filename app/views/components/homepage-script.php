<?php
// Homepage JavaScript Component
?>

<script>
$(document).ready(function() {
    let currentFilter = 'all';
    let currentPage = 1;
    let isLoading = false;
    let hasMoreNews = true;

    // Filter functionality
    $('.filter-tab, .mobile-btn[data-filter]').click(function() {
        const filter = $(this).data('filter');
        currentFilter = filter;
        currentPage = 1;
        hasMoreNews = true;
        
        // Update active state
        $('.filter-tab, .mobile-btn[data-filter]').removeClass('active mobile-btn-primary').addClass('mobile-btn-secondary');
        $(this).removeClass('mobile-btn-secondary').addClass('active mobile-btn-primary');
        
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

    function loadMoreNews() {
        if (isLoading) return;
        
        isLoading = true;
        $('.mobile-infinite-scroll-trigger').addClass('loading').html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        
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
                    $('.mobile-infinite-scroll-trigger').html('<i class="fas fa-check"></i> No more news');
                }
            },
            error: function() {
                console.log('Error loading more news');
                $('.mobile-infinite-scroll-trigger').html('<i class="fas fa-exclamation-triangle"></i> Error loading');
            },
            complete: function() {
                isLoading = false;
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
            <div class="ad-container between-news-ad">
                <div class="ad-content" onclick="trackAdClick(${ad.id})">
                    <div class="ad-header">
                        <span class="ad-label">Advertisement</span>
                    </div>
                    ${ad.image ? `<img src="${ad.image}" alt="${ad.heading}" class="ad-image">` : ''}
                    <div class="ad-text">
                        <h5>${ad.heading}</h5>
                        <p>${ad.description}</p>
                    </div>
                    <div class="ad-actions">
                        ${ad.whatsapp_number ? `<button class="mobile-btn mobile-btn-success" data-action="whatsapp" data-phone="${ad.whatsapp_number}"><i class="fab fa-whatsapp"></i> WhatsApp</button>` : ''}
                        ${ad.call_number ? `<button class="mobile-btn mobile-btn-primary" data-action="call" data-phone="${ad.call_number}"><i class="fas fa-phone"></i> Call</button>` : ''}
                        ${ad.website_url ? `<a href="${ad.website_url}" class="mobile-btn mobile-btn-secondary"><i class="fas fa-globe"></i> Visit</a>` : ''}
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
                <div onclick="trackAdClick(${randomAd.id})">
                    ${randomAd.image ? `
                        <div class="image-container">
                        <img src="${randomAd.image}" 
                             alt="${randomAd.heading}" 
                             class="ad-image">
                        </div>
                    ` : ''}
                    <div class="ad-text">
                        <h4>${randomAd.heading}</h4>
                        <p>${randomAd.description}</p>
                    </div>
                    <div class="ad-actions">
                        ${randomAd.whatsapp_number ? `<button class="mobile-btn mobile-btn-success" data-action="whatsapp" data-phone="${randomAd.whatsapp_number}"><i class="fab fa-whatsapp"></i> WhatsApp</button>` : ''}
                        ${randomAd.call_number ? `<button class="mobile-btn mobile-btn-primary" data-action="call" data-phone="${randomAd.call_number}"><i class="fas fa-phone"></i> Call</button>` : ''}
                        ${randomAd.website_url ? `<a href="${randomAd.website_url}" class="mobile-btn mobile-btn-secondary"><i class="fas fa-globe"></i> Visit</a>` : ''}
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
