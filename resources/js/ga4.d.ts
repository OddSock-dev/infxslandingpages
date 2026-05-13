/**
 * GA4 Event Tracking Type Definitions
 *
 * This file provides TypeScript types for GA4 event tracking functions
 * available globally in the browser.
 */

interface GA4EventData {
  [key: string]: string | number | boolean | string[] | number[] | undefined;
}

interface GA4PageViewParams {
  page_title?: string;
  page_location?: string;
  page_path?: string;
  page_type?: string;
  referrer?: string;
  [key: string]: string | number | boolean | undefined;
}

/**
 * Track a GA4 event
 * @param eventName - Name of the event (e.g., 'contact_form_submitted', 'video_start')
 * @param eventData - Additional event parameters
 */
declare function trackGa4Event(eventName: string, eventData?: GA4EventData): void;

/**
 * Track a page view with custom parameters
 * @param params - Custom page parameters
 */
declare function trackPageView(params?: GA4PageViewParams): void;

/**
 * Global GA4 configuration object
 */
declare const ga4Config: {
  measurementId: string;
  enabled: boolean;
  pageData: {
    page_title: string;
    page_location: string;
    page_path: string;
  };
};

/**
 * Data layer for GA4
 */
declare const dataLayer: any[];

/**
 * Google tag manager function
 */
declare function gtag(...args: any[]): void;

/**
 * Global state for scroll tracking
 */
declare const window: {
  ga4Config: typeof ga4Config;
  dataLayer: typeof dataLayer;
  trackGa4Event: typeof trackGa4Event;
  trackPageView: typeof trackPageView;
  ga4ScrollTracked50?: boolean;
  ga4ScrollTracked90?: boolean;
} & Window;
