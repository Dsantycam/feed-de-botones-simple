document.addEventListener('DOMContentLoaded', function () {
  const addButton = document.getElementById('fdbs-add-button');
  const container = document.getElementById('fdbs-buttons-container');
  const template = document.getElementById('fdbs-button-template');
  const previewBox = document.querySelector('.fdbs-admin-preview');

  if (!addButton || !container || !template) {
    return;
  }

  function renderLucide(scope) {
    if (window.lucide && scope) {
      window.lucide.createIcons({
        attrs: {
          width: 18,
          height: 18,
          strokeWidth: 2
        },
        nodes: scope.querySelectorAll('[data-lucide]')
      });
    } else if (window.lucide) {
      window.lucide.createIcons();
    }
  }

  function initColorPickers(scope) {
    if (!window.jQuery || !window.jQuery.fn.wpColorPicker) {
      return;
    }

    const $scope = scope ? window.jQuery(scope) : window.jQuery(document);

    $scope.find('.fdbs-color-field').each(function () {
      const $field = window.jQuery(this);

      if ($field.hasClass('wp-color-picker')) {
        return;
      }

      $field.wpColorPicker({
        change: function () {
          renderLivePreview();
        },
        clear: function () {
          renderLivePreview();
        }
      });
    });
  }

  function refreshTitles() {
    const rows = container.querySelectorAll('.fdbs-button-row');

    rows.forEach(function (row, index) {
      const titleEl = row.querySelector('.fdbs-button-row__title');
      const inputEl = row.querySelector('.fdbs-button-title-input');

      if (!titleEl) return;

      const value = inputEl && inputEl.value.trim() ? inputEl.value.trim() : 'Botón ' + (index + 1);
      titleEl.textContent = value;
    });
  }

  function reindexRows() {
    const rows = container.querySelectorAll('.fdbs-button-row');

    rows.forEach(function (row, index) {
      const fields = row.querySelectorAll('input, textarea, select');

      fields.forEach(function (field) {
        if (field.name) {
          field.name = field.name.replace(/fdbs_buttons\[\d+\]/g, 'fdbs_buttons[' + index + ']');
        }
      });
    });
  }

  function openMediaFrame(row) {
    const imageIdInput = row.querySelector('.fdbs-image-id');
    const imageUrlInput = row.querySelector('.fdbs-image-url');
    const imagePreview = row.querySelector('.fdbs-image-preview');
    const previewWrap = row.querySelector('.fdbs-image-preview-wrap');

    const frame = wp.media({
      title: 'Seleccionar imagen',
      button: { text: 'Usar imagen' },
      multiple: false,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();

      if (imageIdInput) imageIdInput.value = attachment.id || '';
      if (imageUrlInput) imageUrlInput.value = attachment.url || '';
      if (imagePreview) imagePreview.src = attachment.url || '';
      if (previewWrap) previewWrap.classList.remove('is-empty');

      renderLivePreview();
    });

    frame.open();
  }

  function clearImage(row) {
    const imageIdInput = row.querySelector('.fdbs-image-id');
    const imageUrlInput = row.querySelector('.fdbs-image-url');
    const imagePreview = row.querySelector('.fdbs-image-preview');
    const previewWrap = row.querySelector('.fdbs-image-preview-wrap');

    if (imageIdInput) imageIdInput.value = '';
    if (imageUrlInput) imageUrlInput.value = '';
    if (imagePreview) imagePreview.src = '';
    if (previewWrap) previewWrap.classList.add('is-empty');

    renderLivePreview();
  }

  function updateCurrentIconUI(row, icon) {
    const hiddenInput = row.querySelector('.fdbs-icon-input');
    const currentPreview = row.querySelector('.fdbs-icon-current__preview');
    const currentLabel = row.querySelector('.fdbs-icon-current__label');
    const options = row.querySelectorAll('.fdbs-icon-option');

    if (hiddenInput) {
      hiddenInput.value = icon || '';
    }

    options.forEach(function (option) {
      option.classList.toggle('is-active', option.dataset.icon === icon);
    });

    if (currentPreview) {
      currentPreview.innerHTML = '';
      currentPreview.setAttribute('data-lucide', icon || 'circle');
    }

    if (currentLabel) {
      currentLabel.textContent = icon || 'Sin icono seleccionado';
    }

    renderLucide(row);
    renderLivePreview();
  }

  function bindIconPicker(row) {
    const toggleBtn = row.querySelector('.fdbs-toggle-icon-picker');
    const panel = row.querySelector('.fdbs-icon-picker-panel');
    const search = row.querySelector('.fdbs-icon-search');
    const clearBtn = row.querySelector('.fdbs-icon-clear');
    const options = row.querySelectorAll('.fdbs-icon-option');

    if (toggleBtn && panel) {
      toggleBtn.addEventListener('click', function () {
        panel.hidden = !panel.hidden;
        if (!panel.hidden && search) {
          search.focus();
        }
      });
    }

    if (search) {
      search.addEventListener('input', function () {
        const term = search.value.trim().toLowerCase();

        options.forEach(function (option) {
          const icon = (option.dataset.icon || '').toLowerCase();
          const label = (option.dataset.label || '').toLowerCase();
          const match = icon.includes(term) || label.includes(term);
          option.style.display = match ? '' : 'none';
        });
      });
    }

    options.forEach(function (option) {
      option.addEventListener('click', function () {
        const icon = option.dataset.icon || '';
        updateCurrentIconUI(row, icon);
      });
    });

    if (clearBtn) {
      clearBtn.addEventListener('click', function () {
        updateCurrentIconUI(row, '');
      });
    }
  }

  function getLayoutData() {
    const columns = document.querySelector('[name="fdbs_layout[columns]"]');
    const gap = document.querySelector('[name="fdbs_layout[button_gap]"]');
    const radius = document.querySelector('[name="fdbs_layout[button_radius]"]');
    const showTitle = document.querySelector('[name="fdbs_layout[show_title]"]');
    const title = document.getElementById('title');

    return {
      columns: columns ? parseInt(columns.value || '1', 10) : 1,
      gap: gap ? parseInt(gap.value || '10', 10) : 10,
      radius: radius ? parseInt(radius.value || '14', 10) : 14,
      showTitle: !!(showTitle && showTitle.checked),
      title: title ? title.value.trim() : ''
    };
  }

  function createPreviewItem(data) {
    const item = document.createElement('div');
    item.className = 'fdbs-admin-preview__item';
    item.style.borderRadius = (data.radius || 14) + 'px';

    if (data.bgColor) item.style.backgroundColor = data.bgColor;
    if (data.textColor) item.style.color = data.textColor;
    if (data.borderColor) item.style.borderColor = data.borderColor;

    let media = '';
    if (data.imageUrl) {
      media = `
        <span class="fdbs-admin-preview__media">
          <img src="${data.imageUrl}" alt="">
        </span>
      `;
    } else if (data.icon) {
      media = `
        <span class="fdbs-admin-preview__media fdbs-admin-preview__media--icon">
          <span data-lucide="${data.icon}"></span>
        </span>
      `;
    }

    item.innerHTML = `
      ${media}
      <span class="fdbs-admin-preview__content">
        <span class="fdbs-admin-preview__item-title"></span>
        ${data.description ? '<span class="fdbs-admin-preview__item-description"></span>' : ''}
      </span>
    `;

    const titleEl = item.querySelector('.fdbs-admin-preview__item-title');
    if (titleEl) titleEl.textContent = data.title || '';

    const descEl = item.querySelector('.fdbs-admin-preview__item-description');
    if (descEl) descEl.textContent = data.description || '';

    return item;
  }

  function renderLivePreview() {
    if (!previewBox) return;

    const list = previewBox.querySelector('.fdbs-admin-preview__list');
    const titleEl = previewBox.querySelector('.fdbs-admin-preview__title');
    const descriptionEl = previewBox.querySelector('.description');
    const rows = container.querySelectorAll('.fdbs-button-row');
    const layout = getLayoutData();

    previewBox.style.setProperty('--fdbs-preview-gap', (layout.gap || 10) + 'px');
    previewBox.style.setProperty('--fdbs-preview-radius', (layout.radius || 14) + 'px');

    if (titleEl) {
      titleEl.textContent = layout.title || '';
      titleEl.style.display = layout.showTitle && layout.title ? '' : 'none';
    }

    if (!list) return;

    list.innerHTML = '';

    let activeCount = 0;

    rows.forEach(function (row) {
      const title = row.querySelector('.fdbs-button-title-input')?.value?.trim() || '';
      const description = row.querySelector('textarea')?.value?.trim() || '';
      const imageUrl = row.querySelector('.fdbs-image-url')?.value?.trim() || '';
      const icon = row.querySelector('.fdbs-icon-input')?.value?.trim() || '';
      const bgColor = row.querySelector('[name*="[bg_color]"]')?.value?.trim() || '';
      const textColor = row.querySelector('[name*="[text_color]"]')?.value?.trim() || '';
      const borderColor = row.querySelector('[name*="[border_color]"]')?.value?.trim() || '';
      const enabled = !!row.querySelector('[name*="[enabled]"]')?.checked;

      if (!enabled || !title) return;

      activeCount++;

      const item = createPreviewItem({
        title,
        description,
        imageUrl,
        icon,
        bgColor,
        textColor,
        borderColor,
        radius: layout.radius
      });

      list.appendChild(item);
    });

    if (descriptionEl) {
      descriptionEl.style.display = activeCount === 0 ? '' : 'none';
      if (activeCount === 0) {
        descriptionEl.textContent = 'Aún no hay botones activos para mostrar.';
      }
    }

    renderLucide(previewBox);
  }

  function bindRowEvents(row) {
    const removeBtn = row.querySelector('.fdbs-remove-button');
    const titleInput = row.querySelector('.fdbs-button-title-input');
    const uploadBtn = row.querySelector('.fdbs-upload-image');
    const removeImageBtn = row.querySelector('.fdbs-remove-image');
    const allFields = row.querySelectorAll('input, textarea, select');

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        row.remove();
        reindexRows();
        refreshTitles();
        renderLivePreview();
      });
    }

    if (titleInput) {
      titleInput.addEventListener('input', function () {
        refreshTitles();
        renderLivePreview();
      });
    }

    if (uploadBtn) {
      uploadBtn.addEventListener('click', function () {
        openMediaFrame(row);
      });
    }

    if (removeImageBtn) {
      removeImageBtn.addEventListener('click', function () {
        clearImage(row);
      });
    }

    allFields.forEach(function (field) {
      field.addEventListener('input', renderLivePreview);
      field.addEventListener('change', renderLivePreview);
    });

    bindIconPicker(row);
    renderLucide(row);
  }

  addButton.addEventListener('click', function () {
    const index = container.querySelectorAll('.fdbs-button-row').length;
    const html = template.innerHTML.replace(/__INDEX__/g, index);
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();

    const row = wrapper.firstElementChild;

    if (row) {
      container.appendChild(row);
      bindRowEvents(row);
      initColorPickers(row);
      reindexRows();
      refreshTitles();
      renderLivePreview();
    }
  });

  container.querySelectorAll('.fdbs-button-row').forEach(function (row) {
    bindRowEvents(row);
  });

  if (window.jQuery && window.jQuery.fn.sortable) {
    window.jQuery(container).sortable({
      items: '.fdbs-button-row',
      handle: '.fdbs-sort-handle',
      placeholder: 'fdbs-sort-placeholder',
      stop: function () {
        reindexRows();
        refreshTitles();
        renderLivePreview();
      }
    });
  }

  document.querySelectorAll('[name^="fdbs_layout["], #title').forEach(function (field) {
    field.addEventListener('input', renderLivePreview);
    field.addEventListener('change', renderLivePreview);
  });

  initColorPickers(document);
  refreshTitles();
  renderLucide(document);
  renderLivePreview();
});