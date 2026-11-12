const toggle = document.querySelector('[data-menu-toggle]');
const panel = toggle && document.getElementById(toggle.getAttribute('aria-controls'));

if (toggle && panel) {
  const label = toggle.querySelector('[data-menu-label]');
  const desktop = window.matchMedia('(min-width: 64rem)');
  const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

  const setOpen = (open) => {
    panel.classList.toggle('is-open', open);
    toggle.setAttribute('aria-expanded', String(open));
    label.textContent = open ? toggle.dataset.labelClose : toggle.dataset.labelOpen;
    document.documentElement.classList.toggle('overflow-hidden', open);
    document.documentElement.classList.toggle('menu-open', open);
    document.querySelectorAll('main, footer').forEach((element) => {
      element.inert = open;
    });
  };

  toggle.addEventListener('click', () => {
    setOpen(!isOpen());
    if (isOpen()) {
      panel.querySelector('a')?.focus();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen()) {
      setOpen(false);
      toggle.focus();
    }
  });

  desktop.addEventListener('change', (event) => {
    if (event.matches && isOpen()) {
      setOpen(false);
    }
  });
}
