import { useJourneyStore } from '~/stores/journey'

export function usePrefill() {
  const store = useJourneyStore()
  return {
    prefillFields: computed(() => store.prefillFields),
    journeyToken: computed(() => store.token),
    productKey: computed(() => store.productKey),
  }
}
