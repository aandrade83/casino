(function () {
  'use strict';

  // Staggered card entrance via IntersectionObserver
  function initCardEntrance() {
    var cards = document.querySelectorAll('.game_card');
    if (!cards.length) return;

    if (!('IntersectionObserver' in window)) {
      // Fallback: reveal all immediately
      cards.forEach(function (card) { card.classList.add('visible'); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var card = entry.target;
        var delay = parseInt(card.dataset.cardIndex || 0, 10) * 55;
        setTimeout(function () {
          card.classList.add('visible');
        }, delay);
        observer.unobserve(card);
      });
    }, { threshold: 0.08 });

    cards.forEach(function (card, i) {
      card.dataset.cardIndex = i;
      observer.observe(card);
    });
  }

  // Stop click propagation on play button so the card onclick still fires,
  // but we can add sounds/analytics here in future without double-firing.
  function initPlayButtons() {
    document.addEventListener('click', function (e) {
      if (e.target && e.target.classList.contains('play_btn')) {
        // button click bubbles up to card onclick — intentional
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initCardEntrance();
    initPlayButtons();
  });
})();
