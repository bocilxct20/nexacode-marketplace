(function () {
    let startTime = Date.now();
    let maxScroll = 0;

    // Track scroll depth
    window.addEventListener('scroll', () => {
        let scrollY = window.scrollY;
        let pageHeight = document.documentElement.scrollHeight - window.innerHeight;
        let progress = Math.round((scrollY / pageHeight) * 100);
        if (progress > maxScroll) maxScroll = progress;
    });

    // Send data on visibility change or unload
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            sendTrackingData();
        }
    });

    function sendTrackingData() {
        const productId = document.querySelector('[data-product-id]')?.dataset.productId;
        const data = {
            type: 'page_exit',
            product_id: productId || null,
            url: window.location.href,
            payload: {
                time_on_page: Math.round((Date.now() - startTime) / 1000),
                max_scroll_depth: maxScroll,
                referrer: document.referrer
            }
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Use Beacon API for non-blocking analytics
        const blob = new Blob([JSON.stringify(data)], { type: 'application/json' });
        navigator.sendBeacon('/tracking/track', blob);
    }
})();
