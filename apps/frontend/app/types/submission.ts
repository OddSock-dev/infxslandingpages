export interface SubmitRequest {
  product_key: string
  journey_token?: string | null
  name: string
  email: string
  phone?: string | null
  company?: string | null
}

export interface SubmitResponse {
  message: string
}
