document.documentElement.classList.add('js')

const revealElements = document.querySelectorAll('[data-reveal]')
const scrollHeader = document.querySelector('[data-scroll-header]')
const navToggles = document.querySelectorAll('[data-nav-toggle]')
const mobileNav = document.querySelector('[data-mobile-nav]')
const deferredVideos = document.querySelectorAll('[data-deferred-media="video"]')

if ('IntersectionObserver' in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return
        }

        entry.target.classList.add('is-visible')
        observer.unobserve(entry.target)
      })
    },
    {
      threshold: 0.16,
      rootMargin: '0px 0px -8% 0px',
    },
  )

  revealElements.forEach((element) => observer.observe(element))
} else {
  revealElements.forEach((element) => element.classList.add('is-visible'))
}

if (scrollHeader) {
  let lastScrollY = window.scrollY
  const scrollThreshold = 8
  const hideOffset = 120

  const syncScrollHeader = () => {
    const currentScrollY = window.scrollY
    const mobileNavOpen = mobileNav?.dataset.open === 'true'

    scrollHeader.classList.toggle('is-scrolled', currentScrollY > 24)

    if (mobileNavOpen || currentScrollY <= hideOffset) {
      scrollHeader.classList.remove('is-hidden')
      lastScrollY = currentScrollY
      return
    }

    if (Math.abs(currentScrollY - lastScrollY) < scrollThreshold) {
      return
    }

    scrollHeader.classList.toggle('is-hidden', currentScrollY > lastScrollY)
    lastScrollY = currentScrollY
  }

  syncScrollHeader()
  window.addEventListener('scroll', syncScrollHeader, { passive: true })
}

window.infxTurnstileMount = (element, siteKey, onSuccess, onReset = null) => {
  const mount = () => {
    if (!window.turnstile || !element) {
      window.setTimeout(mount, 150)
      return
    }

    if (element.dataset.turnstileWidgetId) {
      window.turnstile.remove(element.dataset.turnstileWidgetId)
    }

    element.innerHTML = ''

    const widgetId = window.turnstile.render(element, {
      sitekey: siteKey,
      theme: 'light',
      callback: onSuccess,
      'expired-callback': () => onReset?.(),
      'error-callback': () => onReset?.(),
    })

    element.dataset.turnstileWidgetId = `${widgetId}`
  }

  mount()
}

navToggles.forEach((toggle) => {
  toggle.addEventListener('click', () => {
    if (!mobileNav) {
      return
    }

    const nextExpanded = mobileNav.dataset.open !== 'true'
    mobileNav.dataset.open = nextExpanded ? 'true' : 'false'
    mobileNav.classList.toggle('hidden', !nextExpanded)
    toggle.setAttribute('aria-expanded', nextExpanded ? 'true' : 'false')
    scrollHeader?.classList.toggle('is-scrolled', nextExpanded || window.scrollY > 24)
    scrollHeader?.classList.remove('is-hidden')
  })
})

mobileNav?.querySelectorAll('a').forEach((link) => {
  link.addEventListener('click', () => {
    mobileNav.dataset.open = 'false'
    mobileNav.classList.add('hidden')
    navToggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'))
  })
})

const loadDeferredVideo = (video) => {
  if (!(video instanceof HTMLVideoElement) || video.dataset.loaded === 'true') {
    return
  }

  const source = video.dataset.src

  if (!source) {
    return
  }

  video.src = source
  video.dataset.loaded = 'true'
  video.load()
}

if ('IntersectionObserver' in window) {
  const deferredVideoObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const video = entry.target

        if (!(video instanceof HTMLVideoElement)) {
          return
        }

        if (entry.isIntersecting) {
          loadDeferredVideo(video)

          const playPromise = video.play()
          if (playPromise !== undefined) {
            playPromise.catch(() => {})
          }

          return
        }

        if (video.dataset.loaded === 'true') {
          video.pause()
        }
      })
    },
    {
      rootMargin: '220px 0px',
      threshold: 0.2,
    },
  )

  deferredVideos.forEach((video) => deferredVideoObserver.observe(video))
} else {
  deferredVideos.forEach((video) => {
    if (!(video instanceof HTMLVideoElement)) {
      return
    }

    loadDeferredVideo(video)
    const playPromise = video.play()
    if (playPromise !== undefined) {
      playPromise.catch(() => {})
    }
  })
}
