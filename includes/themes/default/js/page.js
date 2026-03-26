var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

// core_nav_demo: Transparent Overlay + Scroll-Fill
;(function () {
  function initNavScrollFill() {
    var navs = document.querySelectorAll('nav.nx-nav-core-demo[data-nx-scrollfill="1"], nav.nx-nav-core-demo[data-nx-overlay="1"]')
    if (!navs.length) return
    var pushedHero = null

    function update() {
      // Reset hero push if needed (re-applied below)
      if (pushedHero) {
        pushedHero.classList.remove('nx-overlay-push')
        pushedHero = null
      }
      for (var i = 0; i < navs.length; i++) {
        var nav = navs[i]
        var overlay = nav.getAttribute('data-nx-overlay') === '1'
        var scrollFill = nav.getAttribute('data-nx-scrollfill') === '1'
        var offsetRaw = nav.getAttribute('data-nx-scrollfill-offset') || '80'
        var offset = /^[0-9]+$/.test(offsetRaw) ? parseInt(offsetRaw, 10) : 80
        var filledShadow = nav.getAttribute('data-nx-filled-shadow') || ''

        if (overlay) nav.classList.add('nx-nav-overlay')
        else nav.classList.remove('nx-nav-overlay', 'nx-nav-filled')

        // Abstand zum ersten Text: Navbar-Höhe + Zusatzabstand
        if (overlay) {
          try {
            var h = (nav.getBoundingClientRect().height || 0) + 28
            document.documentElement.style.setProperty('--nx-overlay-safe-top', h + 'px')
            // Header nicht als Block nach unten schieben – nur Hero bekommt padding-top.
            var hero = document.querySelector('.nx-hero, .nx-hero-split')
            if (hero) {
              hero.classList.add('nx-overlay-push')
              pushedHero = hero
            }
            try { document.documentElement.classList.add('nx-has-overlay-nav') } catch (e) {}
          } catch (e) {}
        }

        if (!scrollFill) {
          // Overlay ohne ScrollFill soll NICHT weiß gefüllt sein.
          // Standalone-Navbar ohne ScrollFill bleibt "sofort gefüllt".
          if (overlay) {
            nav.style.setProperty('--nx-overlay-progress', '0')
            nav.classList.remove('nx-nav-filled')
            nav.classList.remove('shadow-sm', 'shadow', 'shadow-lg')
            if (filledShadow) nav.classList.remove(filledShadow)
          } else {
            // Ohne "Füllen nach scroll" soll sofort gefüllt sein.
            nav.style.setProperty('--nx-overlay-progress', '1')
            nav.classList.add('nx-nav-filled')
            nav.classList.remove('shadow-sm', 'shadow', 'shadow-lg')
            if (filledShadow) nav.classList.remove(filledShadow)
            if (filledShadow) nav.classList.add(filledShadow)
          }
          continue
        }

        var y = window.scrollY || document.documentElement.scrollTop || 0
        var shouldFill = y >= offset
        nav.style.setProperty('--nx-overlay-progress', shouldFill ? '1' : '0')

        // Shadow-Classes immer zuerst bereinigen (sonst bleibt ein Shadow "stehen")
        nav.classList.remove('shadow-sm', 'shadow', 'shadow-lg')
        if (filledShadow) nav.classList.remove(filledShadow)
        if (shouldFill) {
          nav.classList.add('nx-nav-filled')
          if (filledShadow) nav.classList.add(filledShadow)
        } else {
          nav.classList.remove('nx-nav-filled')
        }
      }
      // Marker entfernen, wenn keine Overlay-Nav vorhanden ist
      try {
        var anyOverlay = false
        for (var k = 0; k < navs.length; k++) {
          if (navs[k].getAttribute('data-nx-overlay') === '1') { anyOverlay = true; break }
        }
        if (!anyOverlay) document.documentElement.classList.remove('nx-has-overlay-nav')
      } catch (e) {}
    }

    window.addEventListener('scroll', update, { passive: true })
    window.addEventListener('resize', update)
    update()
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initNavScrollFill)
  else initNavScrollFill()
})()

