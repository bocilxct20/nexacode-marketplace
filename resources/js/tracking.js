// Use global window.axios configured in bootstrap.js
import { debounce } from 'lodash-es';

class BehavioralTracker {
    constructor() {
        this.productId = document.querySelector('[data-product-id]')?.dataset.productId;
        this.startTime = Date.now();
        this.maxScroll = 0;
        this.sentScrollMilestones = new Set();
        this.init();
    }

    init() {
        if (!this.productId) return;

        // 1. Track Scroll Depth
        window.addEventListener('scroll', debounce(() => this.trackScroll(), 500));

        // 2. Track Time Spent (Active Dwell Time)
        window.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.trackTimeSpent();
            } else {
                this.startTime = Date.now();
            }
        });

        // Track before unload
        window.addEventListener('beforeunload', () => this.trackTimeSpent());

        // 3. Track Key Interactions
        document.querySelectorAll('[data-track-click]').forEach(el => {
            el.addEventListener('click', (e) => this.trackClick(e.currentTarget.dataset.trackClick));
        });
    }

    trackScroll() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = Math.round((scrollTop / docHeight) * 100);

        const milestones = [25, 50, 75, 100];
        milestones.forEach(m => {
            if (scrollPercent >= m && !this.sentScrollMilestones.has(m)) {
                this.sentScrollMilestones.add(m);
                this.send('scroll', { percentage: m });
            }
        });
    }

    trackTimeSpent() {
        const endTime = Date.now();
        const duration = Math.round((endTime - this.startTime) / 1000); // in seconds

        if (duration > 5) {
            this.send('time', { duration: duration });
        }
    }

    trackClick(elementId) {
        this.send('click', { element_id: elementId });
    }

    send(type, payload) {
        axios.post('/tracking/track', {
            type: type,
            product_id: this.productId,
            payload: payload,
            url: window.location.href
        }).catch(err => console.debug('Tracking failed', err));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new BehavioralTracker();
});
