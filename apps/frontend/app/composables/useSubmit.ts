import { useJourneyStore } from '~/stores/journey'
import type { SubmitRequest, SubmitResponse } from '~/types'

export function useSubmit() {
  const config = useRuntimeConfig()
  const store = useJourneyStore()
  const router = useRouter()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  async function submit(payload: Omit<SubmitRequest, 'journey_token'>) {
    isLoading.value = true
    error.value = null

    const requestBody: SubmitRequest = {
      ...payload,
      journey_token: store.token ?? undefined,
    }

    try {
      await $fetch<SubmitResponse>(
        `${config.public.apiBase}/funnel/submit`,
        { method: 'POST', body: requestBody },
      )

      store.clear()
      await router.push('/thanks')
    } catch (e: unknown) {
      error.value = 'Something went wrong. Please try again.'
      console.error('Submit error:', e)
    } finally {
      isLoading.value = false
    }
  }

  return { submit, isLoading, error }
}
