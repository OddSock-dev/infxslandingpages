export function useTracking() {
  const route = useRoute()

  const utmSource = computed(() => (route.query.utm_source as string) ?? null)
  const utmMedium = computed(() => (route.query.utm_medium as string) ?? null)
  const utmCampaign = computed(() => (route.query.utm_campaign as string) ?? null)
  const utmTerm = computed(() => (route.query.utm_term as string) ?? null)
  const utmContent = computed(() => (route.query.utm_content as string) ?? null)
  const referrer = computed(() => {
    if (import.meta.client) return document.referrer || null
    return null
  })

  return { utmSource, utmMedium, utmCampaign, utmTerm, utmContent, referrer }
}
