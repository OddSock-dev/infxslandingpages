# Google Analytics GA4 Implementation Guide

## Overview

This application includes a complete Google Analytics 4 (GA4) implementation with:

- **Async script loading** for zero performance impact
- **Automatic event tracking** for clicks, form submissions, and scroll depth
- **HTML attribute-based tracking** for easy integration
- **JavaScript API** for programmatic event tracking
- **Livewire integration** for real-time tracking in reactive components
- **Privacy-friendly defaults** (IP anonymization, no Google Signals)

## Setup

### 1. Environment Variables

GA4 is configured via environment variables:

```bash
# .env
ANALYTICS_ENABLED=true              # Enable/disable GA4 tracking
GA4_MEASUREMENT_ID=G-BB9THWHSWE     # Your GA4 Measurement ID
```

- `ANALYTICS_ENABLED`: Set to `false` in development to disable tracking
- `GA4_MEASUREMENT_ID`: Get this from your [Google Analytics 4 property](https://analytics.google.com/)

### 2. Configuration

GA4 configuration is centralized in `config/analytics.php`:

```php
return [
    'enabled' => (bool) env('ANALYTICS_ENABLED', true),
    'ga4' => [
        'measurement_id' => env('GA4_MEASUREMENT_ID', ''),
    ],
];
```

### 3. How It's Loaded

The GA4 script is automatically included in your main layout via the `x-analytics.ga4-script` Blade component:

**File:** `resources/views/layouts/app.blade.php`

```blade
<!-- In <head> -->
<x-analytics.ga4-script />
```

The script:

- Loads asynchronously via `https://www.googletagmanager.com/gtag/js`
- Does not block page rendering
- Automatically sends page views and session data
- Makes tracking functions available globally

## Automatic Event Tracking

The following events are automatically tracked without any additional code:

### 1. Page Views

- **Event:** `page_view` (GA4 default)
- **Triggered:** On page load
- **Data:** `page_title`, `page_location`, `page_path`

### 2. Scroll Depth

- **Event:** `scroll_depth`
- **Triggered:** When user scrolls to 50% and 90% of page
- **Data:** `depth_percentage`, `page_path`

### 3. Form Submissions

- **Event:** `form_submit`
- **Triggered:** When form with `data-ga4-track` attribute is submitted
- **Data:** `form_name`, `form_id`

### 4. Click Events

- **Event:** Custom event (from `data-ga4-track` attribute)
- **Triggered:** When element with `data-ga4-track` is clicked
- **Data:** `event_category`, `event_label`, `event_value`

## HTML Attribute Tracking

Add GA4 tracking to any HTML element using data attributes:

### Basic Click Tracking

```html
<button data-ga4-track="button_click" data-ga4-label="Sign Up">Sign Up</button>
```

**Result in GA4:**

- Event: `button_click`
- Event Label: `Sign Up`
- Event Category: `engagement` (default)

### With Custom Category

```html
<a
    href="/pricing"
    data-ga4-track="navigation_click"
    data-ga4-category="navigation"
    data-ga4-label="Pricing"
>
    View Pricing
</a>
```

### With Event Value

```html
<button
    data-ga4-track="cta_click"
    data-ga4-label="Get Started"
    data-ga4-value="premium_plan"
>
    Upgrade Now
</button>
```

### Form Submission Tracking

```html
<form data-ga4-track="contact_form" id="contact-inquiry">
    <input type="email" name="email" required />
    <button type="submit">Send</button>
</form>
```

**Result in GA4:**

- Event: `form_submit`
- Form Name: `contact_form`
- Form ID: `contact-inquiry`

## Data Attributes Reference

| Attribute           | Required | Purpose                                | Example                  |
| ------------------- | -------- | -------------------------------------- | ------------------------ |
| `data-ga4-track`    | Yes      | Event name to track                    | `contact_form_submitted` |
| `data-ga4-category` | No       | Event category (default: `engagement`) | `conversion`             |
| `data-ga4-label`    | No       | Event label (default: element text)    | `Submit Button`          |
| `data-ga4-value`    | No       | Event value (numeric or string)        | `premium`                |

## JavaScript API

Use the global `trackGa4Event()` function to track events programmatically:

### Basic Event Tracking

```javascript
trackGa4Event("user_action", {
    action_type: "video_play",
    video_title: "Product Demo",
});
```

### Purchase Tracking

```javascript
trackGa4Event("purchase", {
    transaction_id: "order_12345",
    value: 99.99,
    currency: "USD",
    items: [
        {
            item_id: "SKU_001",
            item_name: "Professional Plan",
            price: 99.99,
            quantity: 1,
        },
    ],
});
```

### Sign-Up Tracking

```javascript
trackGa4Event("sign_up", {
    method: "email",
    user_type: "free_trial",
});
```

### Video Event Tracking

```javascript
// Video start
trackGa4Event("video_start", {
    video_title: "Product Demo",
    video_url: "/videos/demo.mp4",
});

// Video progress (e.g., at 50%)
trackGa4Event("video_progress", {
    video_title: "Product Demo",
    progress: 50,
});

// Video completion
trackGa4Event("video_complete", {
    video_title: "Product Demo",
    watch_time: 300, // seconds
});
```

### Content Engagement

```javascript
trackGa4Event("content_engagement", {
    content_type: "blog_post",
    content_title: "How to Get Started",
    content_id: "blog-001",
    engagement_time: 120, // seconds
});
```

### Search Tracking

```javascript
trackGa4Event("search", {
    search_term: "pricing plans",
});
```

### File Download Tracking

```javascript
trackGa4Event("file_download", {
    file_name: "whitepaper.pdf",
    file_type: "pdf",
});
```

## TypeScript Support

TypeScript definitions are provided in `resources/js/ga4.d.ts`:

```typescript
import "./ga4";

trackGa4Event("purchase", {
    transaction_id: "order_123",
    value: 99.99,
    currency: "USD",
});
```

## Integration Examples

### Blade Views

```blade
<!-- Track link navigation -->
<a href="/features"
   data-ga4-track="navigation"
   data-ga4-label="Features">
    Features
</a>

<!-- Track CTA button -->
<button data-ga4-track="cta_click"
        data-ga4-label="Get Started"
        class="btn btn-primary">
    Get Started
</button>
```

### Alpine Components

```html
<div x-data="{ isOpen: false }">
    <button
        @click="
        isOpen = true;
        trackGa4Event('modal_open', { modal_name: 'feature_details' })
    "
    >
        Learn More
    </button>
</div>
```

### Livewire Components

```php
class ContactForm extends Component
{
    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Process form...

        // Track in Livewire
        $this->js(<<<'JS'
            trackGa4Event('contact_form_submitted', {
                form_id: 'contact-form',
                success: true,
            });
        JS);
    }
}
```

## Advanced Usage

### Custom Page View Tracking

Track page views with custom parameters:

```javascript
trackPageView({
    page_type: "product",
    product_category: "software",
});
```

### Global Configuration

Access GA4 configuration:

```javascript
console.log(window.ga4Config);
// {
//   measurementId: 'G-BB9THWHSWE',
//   enabled: true,
//   pageData: { page_title, page_location, page_path }
// }
```

### Debug Mode

Check the browser console for tracking activity:

```javascript
// View all events sent to GA4
console.log(window.dataLayer);

// Manually test an event
trackGa4Event("test_event", { test_value: "debug" });
```

## Debugging

### In Google Analytics

1. Go to [Google Analytics 4](https://analytics.google.com/)
2. Select your property
3. Go to **Real-time** → **Overview**
4. Trigger an event in your app
5. The event should appear within seconds

### In Browser Console

```javascript
// View GA4 configuration
console.log(window.ga4Config);

// View all tracked events
console.log(window.dataLayer);

// Manually track an event
trackGa4Event("debug_event", { test: true });
```

### Using Google Tag Manager Preview

1. Go to [Google Tag Manager](https://tagmanager.google.com/)
2. Select your container
3. Click **Preview**
4. Enter your site URL
5. Preview mode shows all events in real-time

## Performance Impact

GA4 is optimized for performance:

- ✅ Loaded **asynchronously** (does not block page rendering)
- ✅ Script size: **~20 KB** (gzipped)
- ✅ No impact on First Contentful Paint (FCP)
- ✅ No impact on Largest Contentful Paint (LCP)
- ✅ Minimal impact on Cumulative Layout Shift (CLS)

## Privacy & Compliance

Default configuration prioritizes privacy:

- ✅ IP anonymization enabled (`anonymize_ip: true`)
- ✅ Google Signals disabled (`allow_google_signals: false`)
- ✅ No user ID tracking by default
- ✅ Compliant with GDPR/CCPA when properly configured

**Note:** Ensure you have proper consent mechanisms before tracking user data in regions with privacy regulations.

## Disabling Analytics

### Development Environment

```bash
# .env
ANALYTICS_ENABLED=false
```

### Per-Session

```php
// In middleware or service provider
Config::set('analytics.enabled', false);
```

## Files Modified/Created

- **Created:** `config/analytics.php` - GA4 configuration
- **Created:** `app/Services/AnalyticsService.php` - Service class
- **Created:** `resources/views/components/analytics/ga4-script.blade.php` - GA4 script component
- **Created:** `resources/js/ga4.d.ts` - TypeScript definitions
- **Modified:** `resources/views/layouts/app.blade.php` - Added GA4 component
- **Modified:** `.env` - Added GA4 environment variables
- **Modified:** `.env.example` - Added GA4 configuration template

## Support Files

- `app/Support/GA4EventTrackingGuide.php` - Comprehensive tracking guide
- `app/Support/GA4ImplementationExamples.php` - Real-world implementation examples

## Common Events to Track

| Event                    | Purpose                  | Example                                           |
| ------------------------ | ------------------------ | ------------------------------------------------- |
| `page_view`              | Track page visits        | Auto-tracked                                      |
| `sign_up`                | Track user registrations | `trackGa4Event('sign_up', { method: 'email' })`   |
| `purchase`               | Track purchases          | `trackGa4Event('purchase', { value: 99.99 })`     |
| `search`                 | Track searches           | `trackGa4Event('search', { term: 'pricing' })`    |
| `video_start`            | Track video views        | `trackGa4Event('video_start', { title: 'Demo' })` |
| `contact_form_submitted` | Track form submissions   | Auto-tracked with `data-ga4-track`                |
| `download`               | Track downloads          | `trackGa4Event('download', { file: 'pdf' })`      |
| `scroll_depth`           | Track engagement depth   | Auto-tracked at 50% and 90%                       |

## Resources

- [Google Analytics 4 Documentation](https://support.google.com/analytics/answer/9304153)
- [GA4 Event Reference](https://support.google.com/analytics/answer/9267744)
- [GA4 Best Practices](https://support.google.com/analytics/answer/9976101)
- [Google Tag Manager Help](https://support.google.com/tagmanager/answer/6102821)

## Next Steps

1. ✅ GA4 is installed and ready to use
2. Verify setup in [Google Analytics Real-time](https://analytics.google.com/) → Real-time → Overview
3. Add tracking to your marketing pages using data attributes or JavaScript
4. Set up goals/conversions in GA4 dashboard
5. Monitor performance and user behavior in GA4 reports
