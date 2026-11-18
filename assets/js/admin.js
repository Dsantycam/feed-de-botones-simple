document.addEventListener('DOMContentLoaded', function () {
  const addButton = document.getElementById('fdbs-add-button');
  const addSeparatorButton = document.getElementById('fdbs-add-separator');
  const collapseAllButton = document.getElementById('fdbs-collapse-all');
  const expandAllButton = document.getElementById('fdbs-expand-all');
  const container = document.getElementById('fdbs-buttons-container');
  const buttonTemplate = document.getElementById('fdbs-button-template');
  const separatorTemplate = document.getElementById('fdbs-separator-template');
  const countEl = document.querySelector('.fdbs-items-count');
  const previewBox = document.querySelector('.fdbs-admin-preview');

  if (!addButton || !container || !buttonTemplate) {
    return;
  }

  const ROW = '.fdbs-item-row';
  const SEPARATOR_STYLES = ['line', 'gradient', 'dashed', 'dotted', 'double', 'wave', 'diamond', 'dots', 'pill', 'space'];
  const HEX_COLOR = /^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/;

  // Índice temporal único para filas nuevas; reindexRows() lo normaliza después.
  let tempIndex = 900000;

  /* ------------------------------------------------------------------
   * Utilidades
   * ------------------------------------------------------------------ */

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
          // Iris dispara "change" antes de actualizar el input.
          window.setTimeout(renderLivePreview, 0);
        },
        clear: function () {
          window.setTimeout(renderLivePreview, 0);
        }
      });
    });
  }

  function getRows() {
    return container.querySelectorAll(ROW);
  }

  function isSeparator(row) {
    return row.dataset.type === 'separator';
  }

  function fieldKey(name) {
    const match = /^fdbs_buttons\[[^\]]*\]\[([^\]]+)\]$/.exec(name || '');
    return match ? match[1] : null;
  }

  function getField(row, key) {
    return row.querySelector('[name$="[' + key + ']"]');
  }

  function getValue(row, key) {
    const field = getField(row, key);
    return field && typeof field.value === 'string' ? field.value.trim() : '';
  }

  function isEnabled(row) {
    const field = getField(row, 'enabled');
    return !!(field && field.checked);
  }

  function prettyLink(url) {
    if (!url) return '';

    try {
      const parsed = new URL(url);
      return (parsed.host + parsed.pathname).replace(/\/$/, '') || url;
    } catch (e) {
      return url;
    }
  }

  function clampInt(value, min, max, fallback) {
    const number = parseInt(value, 10);
    if (isNaN(number)) return fallback;
    return Math.max(min, Math.min(max, number));
  }

  /* ------------------------------------------------------------------
   * Índices, resúmenes y estado minimizado
   * ------------------------------------------------------------------ */

  function reindexRows() {
    getRows().forEach(function (row, index) {
      row.querySelectorAll('input, textarea, select').forEach(function (field) {
        if (field.name) {
          field.name = field.name.replace(/^fdbs_buttons\[[^\]]*\]/, 'fdbs_buttons[' + index + ']');
        }
      });
    });
  }

  function updateButtonThumb(row) {
    const thumb = row.querySelector('.fdbs-item-row__thumb');
    if (!thumb) return;

    const imageUrl = getValue(row, 'image_url');
    const icon = getValue(row, 'icon');
    const key = imageUrl ? 'img:' + imageUrl : icon ? 'icon:' + icon : '';

    if (thumb.dataset.key === key) return;
    thumb.dataset.key = key;
    thumb.innerHTML = '';

    if (imageUrl) {
      const img = document.createElement('img');
      img.src = imageUrl;
      img.alt = '';
      thumb.appendChild(img);
    } else if (icon) {
      const span = document.createElement('span');
      span.setAttribute('data-lucide', icon);
      thumb.appendChild(span);
      renderLucide(thumb);
    }
  }

  function getSeparatorStyleLabel(row) {
    const active = row.querySelector('.fdbs-sep-style-option.is-active');
    return active ? active.dataset.label : 'Separador';
  }

  function refreshSummaries() {
    let buttonCount = 0;
    let separatorCount = 0;

    getRows().forEach(function (row) {
      const enabled = isEnabled(row);
      const titleEl = row.querySelector('.fdbs-item-row__title');
      const metaEl = row.querySelector('.fdbs-item-row__meta');
      const badge = row.querySelector('.fdbs-item-row__badge');

      row.classList.toggle('is-disabled', !enabled);
      if (badge) badge.hidden = enabled;

      if (isSeparator(row)) {
        separatorCount++;

        const label = getSeparatorData(row).label;
        if (titleEl) titleEl.textContent = label ? 'Separador: ' + label : 'Separador';
        if (metaEl) {
          metaEl.textContent =
            getSeparatorStyleLabel(row) +
            ' · ↑ ' + getValue(row, 'spacing_top') + 'px' +
            ' · ↓ ' + getValue(row, 'spacing_bottom') + 'px';
        }
        return;
      }

      buttonCount++;

      const title = getValue(row, 'title');
      if (titleEl) titleEl.textContent = title || 'Botón ' + buttonCount;
      if (metaEl) metaEl.textContent = prettyLink(getValue(row, 'link'));

      updateButtonThumb(row);
    });

    if (countEl) {
      const parts = [buttonCount + (buttonCount === 1 ? ' botón' : ' botones')];
      if (separatorCount) {
        parts.push(separatorCount + (separatorCount === 1 ? ' separador' : ' separadores'));
      }
      countEl.textContent = parts.join(' · ');
    }
  }

  function setCollapsed(row, collapsed) {
    const input = row.querySelector('.fdbs-collapsed-input');
    const toggle = row.querySelector('.fdbs-toggle-row');

    row.classList.toggle('is-collapsed', collapsed);
    if (input) input.value = collapsed ? '1' : '';
    if (toggle) toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
  }

  function highlightRow(row) {
    row.classList.add('is-highlighted');
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    window.setTimeout(function () {
      row.classList.remove('is-highlighted');
    }, 1600);
  }

  /* ------------------------------------------------------------------
   * Imagen e icono (botones)
   * ------------------------------------------------------------------ */

  function syncImageUI(row) {
    const imageUrl = getValue(row, 'image_url');
    const imagePreview = row.querySelector('.fdbs-image-preview');
    const previewWrap = row.querySelector('.fdbs-image-preview-wrap');

    if (imagePreview) imagePreview.src = imageUrl;
    if (previewWrap) previewWrap.classList.toggle('is-empty', !imageUrl);
  }

  function openMediaFrame(row) {
    const imageIdInput = row.querySelector('.fdbs-image-id');
    const imageUrlInput = row.querySelector('.fdbs-image-url');

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

      syncImageUI(row);
      refreshSummaries();
      renderLivePreview();
    });

    frame.open();
  }

  function clearImage(row) {
    const imageIdInput = row.querySelector('.fdbs-image-id');
    const imageUrlInput = row.querySelector('.fdbs-image-url');

    if (imageIdInput) imageIdInput.value = '';
    if (imageUrlInput) imageUrlInput.value = '';

    syncImageUI(row);
    refreshSummaries();
    renderLivePreview();
  }

  function syncIconUI(row, icon) {
    const hiddenInput = row.querySelector('.fdbs-icon-input');
    const currentPreview = row.querySelector('.fdbs-icon-current__preview');
    const currentLabel = row.querySelector('.fdbs-icon-current__label');

    if (hiddenInput) {
      hiddenInput.value = icon || '';
    }

    row.querySelectorAll('.fdbs-icon-option').forEach(function (option) {
      option.classList.toggle('is-active', option.dataset.icon === icon);
    });

    if (currentPreview) {
      currentPreview.innerHTML = '';
      currentPreview.setAttribute('data-lucide', icon || 'circle');
    }

    if (currentLabel) {
      currentLabel.textContent = icon || 'Sin icono seleccionado';
    }

    renderLucide(row.querySelector('.fdbs-icon-current') || row);
  }

  function filterIcons(row, term) {
    term = term.trim().toLowerCase();

    row.querySelectorAll('.fdbs-icon-option').forEach(function (option) {
      const icon = (option.dataset.icon || '').toLowerCase();
      const label = (option.dataset.label || '').toLowerCase();
      option.style.display = icon.includes(term) || label.includes(term) ? '' : 'none';
    });
  }

  /* ------------------------------------------------------------------
   * Separadores
   * ------------------------------------------------------------------ */

  function syncSeparatorUI(row) {
    const style = getValue(row, 'style') || 'gradient';
    const labelField = row.querySelector('.fdbs-sep-label-field');

    row.querySelectorAll('.fdbs-sep-style-option').forEach(function (option) {
      option.classList.toggle('is-active', option.dataset.style === style);
    });

    // "Tres puntos" no admite texto.
    if (labelField) labelField.classList.toggle('is-hidden', style === 'dots');

    row.querySelectorAll('.fdbs-range').forEach(updateRangeLabel);
  }

  function updateRangeLabel(range) {
    const field = range.closest('.fdbs-form-field');
    const output = field ? field.querySelector('.fdbs-range-value') : null;
    if (output) output.textContent = range.value + 'px';
  }

  function getSeparatorData(row) {
    return {
      style: getValue(row, 'style'),
      label: getValue(row, 'label'),
      align: getValue(row, 'align'),
      color: getValue(row, 'color'),
      thickness: getValue(row, 'thickness'),
      width: getValue(row, 'width'),
      spacingTop: getValue(row, 'spacing_top'),
      spacingBottom: getValue(row, 'spacing_bottom')
    };
  }

  // Debe producir el mismo marcado que public/views/feed-separator.php.
  function buildSeparatorElement(data) {
    const style = SEPARATOR_STYLES.includes(data.style) ? data.style : 'gradient';
    const label = style === 'dots' ? '' : data.label || '';
    const align = data.align === 'left' ? 'left' : 'center';
    const color = HEX_COLOR.test(data.color || '') ? data.color : '';
    const width = [25, 50, 75, 100].includes(parseInt(data.width, 10)) ? parseInt(data.width, 10) : 100;

    const el = document.createElement('div');
    el.className = 'fdbs-separator fdbs-separator--' + style + ' fdbs-separator--align-' + align;
    if (color) el.classList.add('has-color');
    if (label) el.classList.add('has-label');
    if (!label) el.setAttribute('role', 'separator');

    el.style.setProperty('--fdbs-sep-thickness', clampInt(data.thickness, 1, 8, 1) + 'px');
    el.style.setProperty('--fdbs-sep-width', width + '%');
    el.style.setProperty('--fdbs-sep-top', clampInt(data.spacingTop, 0, 160, 24) + 'px');
    el.style.setProperty('--fdbs-sep-bottom', clampInt(data.spacingBottom, 0, 160, 24) + 'px');
    if (color) el.style.setProperty('--fdbs-sep-color', color);

    function line() {
      const span = document.createElement('span');
      span.className = 'fdbs-separator__line';
      span.setAttribute('aria-hidden', 'true');
      return span;
    }

    el.appendChild(line());

    if (label) {
      const labelEl = document.createElement('span');
      labelEl.className = 'fdbs-separator__label';
      labelEl.textContent = label;
      el.appendChild(labelEl);
    } else if (style === 'diamond' || style === 'dots') {
      const mark = document.createElement('span');
      mark.className = 'fdbs-separator__mark';
      mark.setAttribute('aria-hidden', 'true');
      el.appendChild(mark);
    }

    el.appendChild(line());

    return el;
  }

  /* ------------------------------------------------------------------
   * Crear, duplicar y eliminar filas
   * ------------------------------------------------------------------ */

  function createRow(type) {
    const template = type === 'separator' ? separatorTemplate : buttonTemplate;
    if (!template) return null;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = template.innerHTML.replace(/__INDEX__/g, String(tempIndex++)).trim();

    return wrapper.firstElementChild;
  }

  function mountRow(row) {
    initColorPickers(row);
    renderLucide(row);
    reindexRows();
    refreshSummaries();
    renderLivePreview();
    highlightRow(row);
  }

  function collectValues(row) {
    const values = {};

    row.querySelectorAll('input, textarea, select').forEach(function (field) {
      const key = fieldKey(field.name);
      if (!key) return;

      values[key] = field.type === 'checkbox' || field.type === 'radio'
        ? { checked: field.checked }
        : { value: field.value };
    });

    return values;
  }

  function applyValues(row, values) {
    row.querySelectorAll('input, textarea, select').forEach(function (field) {
      const key = fieldKey(field.name);
      if (!key || !values[key]) return;

      if ('checked' in values[key]) {
        field.checked = values[key].checked;
      } else {
        field.value = values[key].value;
      }
    });
  }

  function duplicateRow(source) {
    const type = isSeparator(source) ? 'separator' : 'button';
    const copy = createRow(type);
    if (!copy) return;

    applyValues(copy, collectValues(source));
    setCollapsed(copy, false);

    if (type === 'button') {
      const titleInput = copy.querySelector('.fdbs-button-title-input');
      if (titleInput && titleInput.value.trim()) {
        titleInput.value = titleInput.value.trim() + ' (copia)';
      }

      syncImageUI(copy);
      syncIconUI(copy, getValue(copy, 'icon'));
    } else {
      syncSeparatorUI(copy);
    }

    source.after(copy);
    mountRow(copy);
  }

  function removeRow(row) {
    row.remove();
    reindexRows();
    refreshSummaries();
    renderLivePreview();
  }

  /* ------------------------------------------------------------------
   * Vista previa en vivo
   * ------------------------------------------------------------------ */

  function getLayoutData() {
    const gap = document.querySelector('[name="fdbs_layout[button_gap]"]');
    const radius = document.querySelector('[name="fdbs_layout[button_radius]"]');
    const showTitle = document.querySelector('[name="fdbs_layout[show_title]"]');
    const title = document.getElementById('title');

    return {
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

    if (data.imageUrl || data.icon) {
      const media = document.createElement('span');
      media.className = 'fdbs-admin-preview__media';

      if (data.imageUrl) {
        const img = document.createElement('img');
        img.src = data.imageUrl;
        img.alt = '';
        media.appendChild(img);
      } else {
        media.classList.add('fdbs-admin-preview__media--icon');
        const icon = document.createElement('span');
        icon.setAttribute('data-lucide', data.icon);
        media.appendChild(icon);
      }

      item.appendChild(media);
    }

    const content = document.createElement('span');
    content.className = 'fdbs-admin-preview__content';

    const titleEl = document.createElement('span');
    titleEl.className = 'fdbs-admin-preview__item-title';
    titleEl.textContent = data.title || '';
    content.appendChild(titleEl);

    if (data.description) {
      const descEl = document.createElement('span');
      descEl.className = 'fdbs-admin-preview__item-description';
      descEl.textContent = data.description;
      content.appendChild(descEl);
    }

    item.appendChild(content);

    return item;
  }

  function renderLivePreview() {
    if (!previewBox) return;

    const list = previewBox.querySelector('.fdbs-admin-preview__list');
    const titleEl = previewBox.querySelector('.fdbs-admin-preview__title');
    const descriptionEl = previewBox.querySelector('.description');
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

    getRows().forEach(function (row) {
      if (!isEnabled(row)) return;

      if (isSeparator(row)) {
        list.appendChild(buildSeparatorElement(getSeparatorData(row)));
        return;
      }

      const title = getValue(row, 'title');
      if (!title) return;

      activeCount++;

      list.appendChild(createPreviewItem({
        title: title,
        description: getValue(row, 'description'),
        imageUrl: getValue(row, 'image_url'),
        icon: getValue(row, 'icon'),
        bgColor: getValue(row, 'bg_color'),
        textColor: getValue(row, 'text_color'),
        borderColor: getValue(row, 'border_color'),
        radius: layout.radius
      }));
    });

    if (descriptionEl) {
      descriptionEl.style.display = activeCount === 0 ? '' : 'none';
      if (activeCount === 0) {
        descriptionEl.textContent = 'Aún no hay botones activos para mostrar.';
      }
    }

    renderLucide(previewBox);
  }

  /* ------------------------------------------------------------------
   * Eventos (delegados en el contenedor para que funcionen también
   * en filas agregadas o duplicadas)
   * ------------------------------------------------------------------ */

  container.addEventListener('click', function (event) {
    const target = event.target;
    const row = target.closest(ROW);
    if (!row) return;

    if (target.closest('.fdbs-duplicate-row')) {
      duplicateRow(row);
      return;
    }

    if (target.closest('.fdbs-remove-button')) {
      removeRow(row);
      return;
    }

    if (target.closest('.fdbs-toggle-row')) {
      setCollapsed(row, !row.classList.contains('is-collapsed'));
      return;
    }

    // Clic en cualquier zona libre de la cabecera también minimiza / expande.
    if (target.closest('.fdbs-item-row__header')) {
      if (!target.closest('button, a, input, select, textarea, .fdbs-sort-handle')) {
        setCollapsed(row, !row.classList.contains('is-collapsed'));
      }
      return;
    }

    if (target.closest('.fdbs-upload-image')) {
      openMediaFrame(row);
      return;
    }

    if (target.closest('.fdbs-remove-image')) {
      clearImage(row);
      return;
    }

    if (target.closest('.fdbs-toggle-icon-picker')) {
      const panel = row.querySelector('.fdbs-icon-picker-panel');
      const search = row.querySelector('.fdbs-icon-search');
      if (panel) {
        panel.hidden = !panel.hidden;
        if (!panel.hidden && search) search.focus();
      }
      return;
    }

    const iconOption = target.closest('.fdbs-icon-option');
    if (iconOption) {
      syncIconUI(row, iconOption.dataset.icon || '');
      refreshSummaries();
      renderLivePreview();
      return;
    }

    if (target.closest('.fdbs-icon-clear')) {
      syncIconUI(row, '');
      refreshSummaries();
      renderLivePreview();
      return;
    }

    const styleOption = target.closest('.fdbs-sep-style-option');
    if (styleOption) {
      const styleInput = row.querySelector('.fdbs-sep-style-input');
      if (styleInput) styleInput.value = styleOption.dataset.style || 'gradient';
      syncSeparatorUI(row);
      refreshSummaries();
      renderLivePreview();
    }
  });

  function onFieldEvent(event) {
    const target = event.target;
    const row = target.closest(ROW);
    if (!row) return;

    if (target.classList.contains('fdbs-icon-search')) {
      filterIcons(row, target.value);
      return;
    }

    if (target.classList.contains('fdbs-range')) {
      updateRangeLabel(target);
    }

    refreshSummaries();
    renderLivePreview();
  }

  container.addEventListener('input', onFieldEvent);
  container.addEventListener('change', onFieldEvent);

  addButton.addEventListener('click', function () {
    const row = createRow('button');
    if (!row) return;

    container.appendChild(row);
    mountRow(row);

    const titleInput = row.querySelector('.fdbs-button-title-input');
    if (titleInput) titleInput.focus({ preventScroll: true });
  });

  if (addSeparatorButton) {
    addSeparatorButton.addEventListener('click', function () {
      const row = createRow('separator');
      if (!row) return;

      container.appendChild(row);
      syncSeparatorUI(row);
      mountRow(row);
    });
  }

  if (collapseAllButton) {
    collapseAllButton.addEventListener('click', function () {
      getRows().forEach(function (row) {
        setCollapsed(row, true);
      });
    });
  }

  if (expandAllButton) {
    expandAllButton.addEventListener('click', function () {
      getRows().forEach(function (row) {
        setCollapsed(row, false);
      });
    });
  }

  if (window.jQuery && window.jQuery.fn.sortable) {
    window.jQuery(container).sortable({
      items: ROW,
      handle: '.fdbs-sort-handle',
      placeholder: 'fdbs-sort-placeholder',
      forcePlaceholderSize: true,
      stop: function () {
        reindexRows();
        refreshSummaries();
        renderLivePreview();
      }
    });
  }

  document.querySelectorAll('[name^="fdbs_layout["], #title').forEach(function (field) {
    field.addEventListener('input', renderLivePreview);
    field.addEventListener('change', renderLivePreview);
  });

  /* ------------------------------------------------------------------
   * Inicio
   * ------------------------------------------------------------------ */

  getRows().forEach(function (row) {
    if (isSeparator(row)) syncSeparatorUI(row);
  });

  initColorPickers(document);
  refreshSummaries();
  renderLucide(document);
  renderLivePreview();
});
