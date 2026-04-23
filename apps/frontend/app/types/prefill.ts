export interface PrefillFields {
  name: string | null
  email: string | null
  phone: string | null
  company: string | null
}

export interface PrefillResponse {
  product_key: string
  fields: PrefillFields
}
