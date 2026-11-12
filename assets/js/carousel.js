const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

const initCarousel = (root) => {
  const track = root.querySelector('[data-carousel-track]');
  const slides = [...root.querySelectorAll('[data-carousel-slide]')];
  const prev = root.querySelector('[data-carousel-prev]');
  const next = root.querySelector('[data-carousel-next]');
  const markers = [...root.querySelectorAll('[data-carousel-goto]')];

  if (!track || slides.length < 2) {
    return;
  }

  const offsetOf = (slide) => slide.offsetLeft - slides[0].offsetLeft;
  const maxScroll = () => track.scrollWidth - track.clientWidth;

  const currentIndex = () =>
    slides.reduce(
      (best, slide, index) =>
        Math.abs(offsetOf(slide) - track.scrollLeft) < Math.abs(offsetOf(slides[best]) - track.scrollLeft) ? index : best,
      0,
    );

  const goTo = (index) => {
    const target = slides[Math.max(0, Math.min(slides.length - 1, index))];
    track.scrollTo({ left: offsetOf(target), behavior: reducedMotion.matches ? 'auto' : 'smooth' });
  };

  const setDisabled = (button, disabled) => {
    if (button) {
      button.setAttribute('aria-disabled', String(disabled));
    }
  };

  const update = () => {
    const scrollable = maxScroll() > 2;
    [prev, next].forEach((button) => {
      if (button) {
        button.hidden = !scrollable;
      }
    });
    setDisabled(prev, track.scrollLeft <= 2);
    setDisabled(next, track.scrollLeft >= maxScroll() - 2);

    const active = currentIndex();
    markers.forEach((marker) => {
      const isActive = Number(marker.dataset.carouselGoto) === active;
      marker.querySelector('.dv-marker')?.classList.toggle('is-active', isActive);
      if (isActive) {
        marker.setAttribute('aria-current', 'step');
      } else {
        marker.removeAttribute('aria-current');
      }
    });
  };

  const step = (direction) => {
    if (direction > 0 && track.scrollLeft >= maxScroll() - 2) {
      return;
    }
    goTo(currentIndex() + direction);
  };

  prev?.addEventListener('click', () => step(-1));
  next?.addEventListener('click', () => step(1));

  markers.forEach((marker) => {
    marker.addEventListener('click', (event) => {
      event.preventDefault();
      goTo(Number(marker.dataset.carouselGoto));
    });
  });

  track.addEventListener('keydown', (event) => {
    if (event.target !== track) {
      return;
    }
    const actions = {
      ArrowLeft: () => step(-1),
      ArrowRight: () => step(1),
      Home: () => goTo(0),
      End: () => goTo(slides.length - 1),
    };
    if (actions[event.key]) {
      event.preventDefault();
      actions[event.key]();
    }
  });

  let frame = 0;
  track.addEventListener(
    'scroll',
    () => {
      cancelAnimationFrame(frame);
      frame = requestAnimationFrame(update);
    },
    { passive: true },
  );

  new ResizeObserver(update).observe(track);
  update();
};

document.querySelectorAll('[data-carousel]').forEach(initCarousel);
