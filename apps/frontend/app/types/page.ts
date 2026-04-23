export interface PageConfig {
  page_key: string
  page_type: 'landing' | 'product' | 'thank_you' | 'legal'
  slug: string
  seo_title: string | null
  meta_description: string | null
  og_title: string | null
  og_description: string | null
  og_image_url: string | null
  robots: string
  cta_text: string | null
  cta_url: string | null
  trial_url: string | null
  body_config: Record<string, unknown> | null
}
