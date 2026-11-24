jQuery(function ($) {
  var $form = $('.fsb-admin form');
  var $button = $('.fsb-preview-button');

  function currentType() {
    return $form.find('input[name="fsb_settings[button_type]"]:checked').val() || '';
  }

  function selectedText(id) {
    var $opt = $('#' + id + ' option:selected');
    // wp_dropdown_* põe &nbsp; à esquerda nas subcategorias.
    return $opt.val() && $opt.val() !== '0' ? $.trim($opt.text().replace(/ /g, ' ')) : '';
  }

  // Mesma regra de includes/button.php (fsb_get_button).
  function buttonLabel(type) {
    var custom = $.trim($('#fsb_label').val());
    if (type === 'category') {
      var cat = selectedText('fsb_category');
      if (!cat) return '';
      var lower = cat.toLowerCase();
      return (lower === 'promoções' || lower === 'promocoes') ? 'Ver produtos em promoção' : 'Ver ' + cat;
    }
    if (type === 'page') {
      var page = selectedText('fsb_page');
      return page ? (custom || 'Ver ' + page) : '';
    }
    if (type === 'url') {
      return $.trim($('#fsb_url').val()) ? (custom || 'Ver promoções') : '';
    }
    return '';
  }

  function update() {
    var type = currentType();

    $form.find('[data-fsb-show]').each(function () {
      $(this).toggle(this.getAttribute('data-fsb-show').split(' ').indexOf(type) !== -1);
    });

    var label = buttonLabel(type);
    $button.text(label).prop('hidden', !label);
  }

  function paint(key, color) {
    $('[data-fsb-bg="' + key + '"]').css('background-color', color);
  }

  $('.fsb-color-input').each(function () {
    var key = $(this).data('fsb-color');
    paint(key, this.value);
    $(this).wpColorPicker({
      change: function (event, ui) { paint(key, ui.color.toString()); },
      clear: function () { paint(key, $(this).closest('.wp-picker-container').find('.fsb-color-input').data('default-color')); }
    });
  });

  $form.on('change input', 'input[name="fsb_settings[button_type]"], #fsb_category, #fsb_page, #fsb_url, #fsb_label', update);
  update();
});
