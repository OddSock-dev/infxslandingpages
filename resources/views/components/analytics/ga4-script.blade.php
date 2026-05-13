@php
    use App\Services\AnalyticsService;

    if (! AnalyticsService::isEnabled()) {
        return;
    }

    $measurementId = AnalyticsService::ga4MeasurementId();
@endphp

<script>
    window.dataLayer = window.dataLayer || [];
    window.ga4Config = {
        measurementId: '{{ $measurementId }}',
        pageData: {
            page_title: document.title,
            page_location: window.location.href,
            page_path: window.location.pathname,
        },
        booted: false,
        bootScheduled: false,
        listenersInstalled: false,
    };

    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    };

    window.infxBootGa4 = function () {
        if (window.ga4Config.booted) {
            return;
        }

        window.ga4Config.booted = true;

        if (! document.querySelector('script[data-ga4-loader]')) {
            const loader = document.createElement('script');
            loader.async = true;
            loader.src = 'https://www.googletagmanager.com/gtag/js?id={{ $measurementId }}';
            loader.dataset.ga4Loader = 'true';
            document.head.appendChild(loader);
        }

        window.gtag('js', new Date());
        window.gtag('config', '{{ $measurementId }}', {
            send_page_view: true,
            anonymize_ip: true,
            allow_google_signals: false,
        });
    };

    window.infxScheduleGa4Boot = function () {
        if (window.ga4Config.bootScheduled) {
            return;
        }

        window.ga4Config.bootScheduled = true;

        const boot = () => window.infxBootGa4();

        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(boot, { timeout: 1500 });
            return;
        }

        window.setTimeout(boot, 900);
    };

    window.trackGa4Event = function (eventName, eventData = {}) {
        window.infxScheduleGa4Boot();
        window.gtag('event', eventName, eventData);
    };

    window.trackPageView = function (params = {}) {
        window.infxScheduleGa4Boot();
        window.gtag('config', '{{ $measurementId }}', {
            page_title: document.title,
            page_location: window.location.href,
            page_path: window.location.pathname,
            ...params,
        });
    };

    const installGa4Listeners = () => {
        if (window.ga4Config.listenersInstalled) {
            return;
        }

        window.ga4Config.listenersInstalled = true;

        document.addEventListener('click', (event) => {
            const target = event.target.closest('[data-ga4-track]');

            if (! target) {
                return;
            }

            window.trackGa4Event(target.dataset.ga4Track, {
                event_category: target.dataset.ga4Category || 'engagement',
                event_label: target.dataset.ga4Label || target.textContent || '',
                event_value: target.dataset.ga4Value || '',
            });
        }, true);

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || ! form.dataset.ga4Track) {
                return;
            }

            window.trackGa4Event('form_submit', {
                form_name: form.dataset.ga4Track,
                form_id: form.id || 'unknown',
            });
        }, true);

        const trackScrollDepth = () => {
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;

            if (scrollHeight <= 0) {
                return;
            }

            const scrollPercentage = Math.round((window.scrollY / scrollHeight) * 100);

            if (scrollPercentage >= 50 && ! window.ga4ScrollTracked50) {
                window.trackGa4Event('scroll_depth', {
                    depth_percentage: 50,
                    page_path: window.location.pathname,
                });
                window.ga4ScrollTracked50 = true;
            }

            if (scrollPercentage >= 90 && ! window.ga4ScrollTracked90) {
                window.trackGa4Event('scroll_depth', {
                    depth_percentage: 90,
                    page_path: window.location.pathname,
                });
                window.ga4ScrollTracked90 = true;
            }

            if (window.ga4ScrollTracked50 && window.ga4ScrollTracked90) {
                window.removeEventListener('scroll', trackScrollDepth);
            }
        };

        window.addEventListener('scroll', trackScrollDepth, { passive: true });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', installGa4Listeners, { once: true });
    } else {
        installGa4Listeners();
    }

    if (document.readyState === 'complete') {
        window.infxScheduleGa4Boot();
    } else {
        window.addEventListener('load', window.infxScheduleGa4Boot, { once: true });
    }
</script>
