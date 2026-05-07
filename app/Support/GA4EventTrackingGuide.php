<?php

declare(strict_types=1);

/**
 * GA4 Event Tracking Examples
 *
 * This file documents how to use GA4 event tracking throughout your application.
 */

/**
 * BASIC SETUP
 * ===========
 * GA4 is automatically initialized in layouts/app.blade.php using the ga4-script component.
 * The GA4 Measurement ID is stored in config/analytics.php (pulled from env GA4_MEASUREMENT_ID).
 *
 * AVAILABLE GLOBAL FUNCTIONS
 * ==========================
 *
 * 1. trackGa4Event(eventName, eventData)
 *    - Track custom events
 *    - Example: trackGa4Event('contact_form_submitted', { form_name: 'inquiry' })
 *
 * 2. trackPageView(params)
 *    - Track page views with custom parameters
 *    - Example: trackPageView({ page_type: 'product' })
 *
 *
 * HTML ATTRIBUTE TRACKING
 * =======================
 *
 * Use data-ga4-* attributes on HTML elements for automatic tracking:
 *
 * data-ga4-track="event_name"     - Event name to track (required)
 * data-ga4-category="category"    - Event category (default: 'engagement')
 * data-ga4-label="label"          - Event label (default: element text)
 * data-ga4-value="value"          - Event value
 *
 * EXAMPLES IN BLADE:
 * ==================
 *
 * <!-- Track button clicks -->
 * <button data-ga4-track="watch_video" data-ga4-label="Hero Video">
 *     Watch Demo
 * </button>
 *
 * <!-- Track link clicks -->
 * <a href="/features" data-ga4-track="navigation_click" data-ga4-category="navigation">
 *     Features
 * </a>
 *
 * <!-- Track form submissions automatically -->
 * <form data-ga4-track="contact_inquiry_form">
 *     <input type="email" name="email" required>
 *     <button type="submit">Send</button>
 * </form>
 *
 * <!-- Track CTA clicks with value -->
 * <button data-ga4-track="cta_click" data-ga4-label="Get Started" data-ga4-value="premium">
 *     Upgrade Now
 * </button>
 *
 *
 * JAVASCRIPT TRACKING
 * ===================
 *
 * Track events programmatically in your JavaScript/Alpine/Livewire:
 *
 * // Track purchase
 * trackGa4Event('purchase', {
 *     transaction_id: '12345',
 *     value: 99.99,
 *     currency: 'USD',
 *     items: [
 *         {
 *             item_id: 'SKU-001',
 *             item_name: 'Product Name',
 *             price: 99.99,
 *             quantity: 1,
 *         },
 *     ],
 * });
 *
 * // Track sign-up
 * trackGa4Event('sign_up', {
 *     method: 'email',
 *     user_type: 'free_trial',
 * });
 *
 * // Track download
 * trackGa4Event('download', {
 *     file_name: 'whitepaper.pdf',
 *     file_type: 'pdf',
 * });
 *
 * // Track video engagement
 * trackGa4Event('video_start', {
 *     video_title: 'Product Demo',
 *     video_url: '/videos/demo.mp4',
 * });
 *
 * // Track engagement with specific content
 * trackGa4Event('content_engagement', {
 *     content_type: 'blog_post',
 *     content_title: 'How to Get Started',
 *     content_id: 'blog-001',
 * });
 *
 *
 * LIVEWIRE EVENT TRACKING
 * =======================
 *
 * In Livewire components, you can track events in PHP hooks:
 *
 * class ContactForm extends Component
 * {
 *     public function submit()
 *     {
 *         // Validate and process...
 *
 *         // Track the submission
 *         $this->dispatch('ga4:track', [
 *             'event_name' => 'form_submit',
 *             'event_data' => [
 *                 'form_name' => 'contact_inquiry',
 *                 'success' => true,
 *             ],
 *         ]);
 *     }
 * }
 *
 * // In your Blade template:
 * <script>
 *     Livewire.on('ga4:track', ({ eventName, eventData }) => {
 *         trackGa4Event(eventName, eventData);
 *     });
 * </script>
 *
 *
 * AUTO-TRACKED EVENTS
 * ===================
 *
 * The following events are automatically tracked:
 *
 * 1. page_view
 *    - Automatically sent on page load
 *    - Data: page_title, page_location, page_path
 *
 * 2. scroll_depth
 *    - Automatically sent when user scrolls to 50% and 90%
 *    - Data: depth_percentage, page_path
 *
 * 3. All click events on [data-ga4-track] elements
 *    - Captured automatically via event delegation
 *    - Data: event_category, event_label, event_value
 *
 * 4. All form submissions on [data-ga4-track] forms
 *    - Captured automatically via event delegation
 *    - Data: form_name, form_id
 *
 *
 * CONFIGURATION
 * =============
 *
 * GA4 is configured with:
 * - send_page_view: true (automatically send page_view on config)
 * - anonymize_ip: true (IP anonymization enabled)
 * - allow_google_signals: false (Google signals disabled for privacy)
 *
 *
 * ENVIRONMENT VARIABLES
 * =====================
 *
 * ANALYTICS_ENABLED=true          - Enable/disable GA4 tracking
 * GA4_MEASUREMENT_ID=G-...         - Your GA4 Measurement ID
 *
 * Set ANALYTICS_ENABLED=false to disable GA4 in development or testing.
 *
 *
 * DEBUGGING
 * =========
 *
 * In browser console:
 * - window.ga4Config            - View GA4 configuration
 * - window.dataLayer            - View all events sent to GA4
 * - trackGa4Event('test', {})   - Manually test event tracking
 *
 * Check Google Analytics Real-time view in GA4 console to see events appear.
 */
