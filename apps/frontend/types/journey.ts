export interface QualifyAnswer {
  step_key: string
  field_key: string
  value: string
}

export interface QualifyRequest {
  source_page_key: string
  answers: QualifyAnswer[]
  consent: boolean
  utm_source?: string | null
  utm_medium?: string | null
  utm_campaign?: string | null
  utm_term?: string | null
  utm_content?: string | null
  referrer?: string | null
}

export interface QualifyResponse {
  journey_token: string
  product_key: string
}
