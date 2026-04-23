document.documentElement.classList.add('js')

const revealElements = document.querySelectorAll('[data-reveal]')

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
