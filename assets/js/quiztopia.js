(function () {
  'use strict';

  /* ─── Countdown timer ──────────────────────────────────────────── */
  function initCountdown() {
    const daysEl  = document.getElementById('qt-cd-days');
    const hoursEl = document.getElementById('qt-cd-hours');
    const minsEl  = document.getElementById('qt-cd-mins');
    const secsEl  = document.getElementById('qt-cd-secs');
    if (!daysEl) return;

    const targetDate = (window.QT && window.QT.nextEventDate)
      ? new Date(window.QT.nextEventDate + 'T19:00:00+03:00')
      : null;

    if (!targetDate || isNaN(targetDate)) {
      document.querySelector('.qt-hero__countdown')?.remove();
      return;
    }

    function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

    function tick() {
      const diff = targetDate - Date.now();
      if (diff <= 0) {
        daysEl.textContent  = '00';
        hoursEl.textContent = '00';
        minsEl.textContent  = '00';
        if (secsEl) secsEl.textContent = '00';
        return;
      }
      const days  = Math.floor(diff / 86400000);
      const hours = Math.floor((diff % 86400000) / 3600000);
      const mins  = Math.floor((diff % 3600000) / 60000);
      const secs  = Math.floor((diff % 60000) / 1000);
      daysEl.textContent  = pad(days);
      hoursEl.textContent = pad(hours);
      minsEl.textContent  = pad(mins);
      if (secsEl) secsEl.textContent = pad(secs);
    }

    tick();
    setInterval(tick, 1000);
  }

  /* ─── FAQ accordion ────────────────────────────────────────────── */
  function initFaq() {
    document.querySelectorAll('.qt-faq__question').forEach(btn => {
      btn.addEventListener('click', function () {
        const item = this.closest('.qt-faq__item');
        const isOpen = item.classList.contains('is-open');

        // Close all
        document.querySelectorAll('.qt-faq__item.is-open').forEach(el => {
          el.classList.remove('is-open');
          el.querySelector('.qt-faq__question').setAttribute('aria-expanded', 'false');
        });

        // Open clicked if it was closed
        if (!isOpen) {
          item.classList.add('is-open');
          this.setAttribute('aria-expanded', 'true');
        }
      });

      btn.setAttribute('aria-expanded', 'false');
    });
  }

  /* ─── Scroll reveal (IntersectionObserver) ─────────────────────── */
  function initReveal() {
    if (!('IntersectionObserver' in window)) {
      // Fallback: make everything visible immediately
      document.querySelectorAll('[data-animate]').forEach(el => {
        el.classList.add('is-visible');
      });
      return;
    }

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -48px 0px' });

    // Auto-stagger children inside [data-stagger] containers
    document.querySelectorAll('[data-stagger]').forEach(parent => {
      const children = parent.querySelectorAll('[data-animate]');
      children.forEach((child, i) => {
        child.style.setProperty('--stagger-delay', `${i * 90}ms`);
      });
    });

    document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
  }

  /* ─── Mobile nav drawer ────────────────────────────────────────── */
  function initMobileNav() {
    const burger = document.querySelector('.qt-nav__burger');
    const drawer = document.querySelector('.qt-nav__drawer');
    if (!burger || !drawer) return;

    burger.addEventListener('click', function () {
      const expanded = this.getAttribute('aria-expanded') === 'true';
      this.setAttribute('aria-expanded', String(!expanded));
      drawer.classList.toggle('is-open', !expanded);
      document.body.style.overflow = !expanded ? 'hidden' : '';
    });

    // Close on link click
    drawer.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        burger.setAttribute('aria-expanded', 'false');
        drawer.classList.remove('is-open');
        document.body.style.overflow = '';
      });
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
        burger.setAttribute('aria-expanded', 'false');
        drawer.classList.remove('is-open');
        document.body.style.overflow = '';
        burger.focus();
      }
    });
  }

  /* ─── Newsletter form ──────────────────────────────────────────── */
  function initNewsletter() {
    const form = document.querySelector('.qt-footer__nl-form');
    if (!form) return;

    const input = form.querySelector('.qt-footer__nl-input');
    const btn   = form.querySelector('.qt-footer__nl-btn');
    const msg   = form.querySelector('.qt-footer__nl-msg');

    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      if (!input || !window.QT) return;

      const email = input.value.trim();
      if (!email) return;

      btn.disabled = true;
      btn.textContent = 'Sending…';
      if (msg) { msg.textContent = ''; msg.className = 'qt-footer__nl-msg'; }

      try {
        const body = new URLSearchParams({
          action: 'qt_subscribe',
          email,
          nonce: window.QT.nonce,
        });
        const res  = await fetch(window.QT.ajaxUrl, { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
          if (msg) { msg.textContent = data.data.message; msg.classList.add('is-success'); }
          input.value = '';
        } else {
          if (msg) { msg.textContent = data.data?.message || 'Something went wrong.'; msg.classList.add('is-error'); }
        }
      } catch {
        if (msg) { msg.textContent = 'Could not connect. Try again.'; msg.classList.add('is-error'); }
      } finally {
        btn.disabled = false;
        btn.textContent = 'Notify me';
      }
    });
  }

  /* ─── Sticky nav shadow on scroll ─────────────────────────────── */
  function initNavScroll() {
    const nav = document.querySelector('.qt-nav');
    if (!nav) return;

    const onScroll = () => {
      nav.style.borderColor = window.scrollY > 40
        ? 'oklch(74% 0.18 65 / 0.25)'
        : 'oklch(74% 0.18 65 / 0.15)';
    };

    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ─── Quantity stepper (+/− buttons) ──────────────────────────── */
  function initQtyStepper() {
    function wrapInput(input) {
      if (input.closest('.qt-qty-stepper')) return;

      const wrapper = document.createElement('div');
      wrapper.className = 'qt-qty-stepper';
      input.parentNode.insertBefore(wrapper, input);

      const minus = document.createElement('button');
      minus.type = 'button';
      minus.className = 'qt-qty-btn qt-qty-btn--minus';
      minus.textContent = '−';
      minus.setAttribute('aria-label', 'Decrease quantity');

      const plus = document.createElement('button');
      plus.type = 'button';
      plus.className = 'qt-qty-btn qt-qty-btn--plus';
      plus.textContent = '+';
      plus.setAttribute('aria-label', 'Increase quantity');

      wrapper.appendChild(minus);
      wrapper.appendChild(input);
      wrapper.appendChild(plus);

      minus.addEventListener('click', () => {
        const val = parseInt(input.value, 10) || 1;
        const min = parseInt(input.getAttribute('min'), 10) || 1;
        if (val > min) {
          input.value = val - 1;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });

      plus.addEventListener('click', () => {
        const val = parseInt(input.value, 10) || 1;
        const max = parseInt(input.getAttribute('max'), 10) || 9999;
        if (val < max) {
          input.value = val + 1;
          input.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });
    }

    // Wrap existing inputs
    document.querySelectorAll('.quantity input.qty').forEach(wrapInput);

    // Re-wrap on WC cart updates (AJAX)
    document.body.addEventListener('updated_cart_totals', () => {
      document.querySelectorAll('.quantity input.qty').forEach(wrapInput);
    });
  }

  /* ─── Init ─────────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initFaq();
    initReveal();
    initMobileNav();
    initNewsletter();
    initNavScroll();
    initQtyStepper();
  });
})();
