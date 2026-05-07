@php
    use App\Services\AnalyticsService;

    if (! AnalyticsService::isEnabled()) {
        return;
    }

    $measurementId = AnalyticsService::ga4MeasurementId();
@endphp

<!-- Google Analytics GA4 -->
<script>
    // Initialize dataLayer
    window.dataLayer = window.dataLayer || [];

    // GA4 config
    window.ga4Config = {
        measurementId: '{{ $measurementId }}',
        pageData: {
            page_title: document.title,
            page_location: window.location.href,
            page_path: window.location.pathname,
        },
    };

    // gtag function
    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', '{{ $measurementId }}', {
        'send_page_view': true,
        'anonymize_ip': true,
        'allow_google_signals': false,
    });
</script>

<!-- Google Tag Manager Script (async) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $measurementId }}"></script>

<!-- GA4 Event Tracking -->
<script>
    /**
     * Track a GA4 event
     * @param {string} eventName - Event name
     * @param {Object} eventData - Event data
     */
    window.trackGa4Event = function(eventName, eventData = {}) {
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, eventData);
        }
    };

    /**
     * Track page view with custom parameters
     * @param {Object} params - Custom page parameters
     */
    window.trackPageView = function(params = {}) {
        if (typeof gtag !== 'undefined') {
            gtag('config', '{{ $measurementId }}', {
                page_title: document.title,
                page_location: window.location.href,
                page_path: window.location.pathname,
                ...params,
            });
        }
    };

    /**
     * Track clicks on elements with data-ga4-track attribute
     */
    document.addEventListener('click', function(event) {
        const target = event.target.closest('[data-ga4-track]');
        if (target) {
            const eventName = target.dataset.ga4Track;
            const eventLabel = target.dataset.ga4Label || target.textContent || '';
            const eventValue = target.dataset.ga4Value || '';
            const eventCategory = target.dataset.ga4Category || 'engagement';

            window.trackGa4Event(eventName, {
                'event_category': eventCategory,
                'event_label': eventLabel,
                'event_value': eventValue,
            });
        }
    }, true);

    /**
     * Track form submissions
     */
    document.addEventListener('submit', function(event) {
        const form = event.target;
        if (form.dataset.ga4Track) {
            const formName = form.dataset.ga4Track;
            window.trackGa4Event('form_submit', {
                'form_name': formName,
                'form_id': form.id || 'unknown',
            });
        }
    }, true);

    /**
     * Track scrolling depth
     */
    let scrollEventFired = false;
    window.addEventListener('scroll', function() {
        if (scrollEventFired) return;

        const scrollPercentage = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);

        if (scrollPercentage >= 50 && !window.ga4ScrollTracked50) {
            window.trackGa4Event('scroll_depth', {
                'depth_percentage': 50,
                'page_path': window.location.pathname,
            });
            window.ga4ScrollTracked50 = true;
        }

        if (scrollPercentage >= 90 && !window.ga4ScrollTracked90) {
            window.trackGa4Event('scroll_depth', {
                'depth_percentage': 90,
                'page_path': window.location.pathname,
            });
            window.ga4ScrollTracked90 = true;
        }
    });
</script>
