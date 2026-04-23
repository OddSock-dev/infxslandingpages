import { useJourneyStore } from '~/stores/journey'
import type { PrefillResponse } from '~/types'

export function useJourney() {
  const route = useRoute()
  const store = useJourneyStore()
  const config = useRuntimeConfig()

  async function initFromToken() {
    const tokenParam = route.query.t as string | undefined
    if (!tokenParam) return

    store.token = tokenParam

    try {
      const data = await $fetch<PrefillResponse>(
        `${config.public.apiBase}/journeys/${tokenParam}/prefill`,
      )
      store.setJourney(tokenParam, data.product_key)
      store.setPrefill(data.fields)
    } catch {
      // Token invalid/expired — silently continue with blank form
    }
  }

  return { initFromToken, store }
}
