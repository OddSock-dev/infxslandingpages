import type { SeoMeta } from '~/types'

export function useSeo(meta: SeoMeta) {
  useHead({
    title: meta.title,
    meta: [
      ...(meta.description ? [{ name: 'description', content: meta.description }] : []),
      ...(meta.robots ? [{ name: 'robots', content: meta.robots }] : []),
      ...(meta.ogTitle ? [{ property: 'og:title', content: meta.ogTitle }] : []),
      ...(meta.ogDescription
        ? [{ property: 'og:description', content: meta.ogDescription }]
        : []),
      ...(meta.ogImage ? [{ property: 'og:image', content: meta.ogImage }] : []),
    ],
  })
}
