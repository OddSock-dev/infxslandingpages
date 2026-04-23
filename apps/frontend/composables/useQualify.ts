import { useJourneyStore } from '~/stores/journey'
import { useTracking } from '~/composables/useTracking'
import type { QualifyRequest, QualifyResponse } from '~/types'

export function useQualify() {
  const config = useRuntimeConfig()
  const store = useJourneyStore()
  const tracking = useTracking()
  const router = useRouter()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const productKeyToSlug: Record<string, string> = {
    zoho_one: '/products/zoho-one',
    zoho_marketing_plus: '/products/zoho-marketing-plus',
    zoho_workplace: '/products/zoho-workplace',
  }

  async function qualify(payload: QualifyRequest) {
    isLoading.value = true
    error.value = null

    const requestBody: QualifyRequest = {
      ...payload,
      utm_source: tracking.utmSource.value,
      utm_medium: tracking.utmMedium.value,
      utm_campaign: tracking.utmCampaign.value,
      utm_term: tracking.utmTerm.value,
      utm_content: tracking.utmContent.value,
      referrer: tracking.referrer.value,
    }

    try {
      const data = await $fetch<QualifyResponse>(
        `${config.public.apiBase}/funnel/qualify`,
        { method: 'POST', body: requestBody },
      )

      store.setJourney(data.journey_token, data.product_key)

      const slug = productKeyToSlug[data.product_key] ?? '/products/zoho-one'
      await router.push(`${slug}?t=${data.journey_token}`)
    } catch (e: unknown) {
      error.value = 'Something went wrong. Please try again.'
      console.error('Qualify error:', e)
    } finally {
      isLoading.value = false
    }
  }

  return { qualify, isLoading, error }
}
