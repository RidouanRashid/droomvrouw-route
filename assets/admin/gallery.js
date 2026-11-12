document.querySelectorAll('[data-dv-gallery]').forEach((box) => {
  const input = box.querySelector('input[name="dv_gallery"]');
  const list = box.querySelector('[data-dv-gallery-list]');
  const template = box.querySelector('[data-dv-gallery-template]');
  const addButton = box.querySelector('[data-dv-gallery-add]');
  let frame;

  const sync = () => {
    input.value = [...list.children].map((item) => item.dataset.id).join(',');
  };

  const createItem = (attachment) => {
    const item = template.content.firstElementChild.cloneNode(true);
    item.dataset.id = attachment.id;
    item.querySelector('img').src = attachment.sizes?.thumbnail?.url ?? attachment.url;
    item.querySelector('.dv-gallery__alert').hidden = Boolean(attachment.alt?.trim());
    return item;
  };

  addButton.addEventListener('click', () => {
    if (!frame) {
      frame = wp.media({
        title: addButton.textContent,
        library: { type: 'image' },
        multiple: 'add',
        button: { text: addButton.textContent },
      });
      frame.on('select', () => {
        const existing = new Set([...list.children].map((item) => item.dataset.id));
        frame
          .state()
          .get('selection')
          .toJSON()
          .filter((attachment) => !existing.has(String(attachment.id)))
          .forEach((attachment) => list.append(createItem(attachment)));
        sync();
      });
    }
    frame.open();
  });

  list.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-action]');
    if (!button) {
      return;
    }
    const item = button.closest('li');
    const actions = {
      left: () => item.previousElementSibling?.before(item),
      right: () => item.nextElementSibling?.after(item),
      remove: () => item.remove(),
    };
    actions[button.dataset.action]();
    sync();
    if (button.dataset.action !== 'remove') {
      button.focus();
    } else {
      addButton.focus();
    }
  });
});
