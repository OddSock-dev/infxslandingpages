import { defineStore } from 'pinia'
import type { PrefillFields } from '~/types'

export const useJourneyStore = defineStore('journey', () => {
  const tokenCookie = useCookie<string | null>('journey_token', {
    maxAge: 86400, // 24 hours
    sameSite: 'lax',
    secure: true,
  })

  const token = ref<string | null>(tokenCookie.value ?? null)
  const productKey = ref<string | null>(null)
  const prefillFields = ref<PrefillFields | null>(null)

  function setJourney(journeyToken: string, product: string) {
    token.value = journeyToken
    productKey.value = product
    tokenCookie.value = journeyToken
  }

  function setPrefill(fields: PrefillFields) {
    prefillFields.value = fields
  }

  function clear() {
    token.value = null
    productKey.value = null
    prefillFields.value = null
    tokenCookie.value = null
  }

  return { token, productKey, prefillFields, setJourney, setPrefill, clear }
})
