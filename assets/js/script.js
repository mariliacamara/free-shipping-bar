jQuery(function ($) {
  if (!$('body').hasClass('woocommerce-cart')) return;


  function replaceUpdateButton() {
    const btn = $('button[name="update_cart"]');

    if (!btn.length) return;

    const container = btn.parent();

    container.find('.fsb-cart-button').remove();

    const link = (typeof fsbData !== 'undefined' && fsbData.link) ? fsbData.link : '#';
    const label = (typeof fsbData !== 'undefined' && fsbData.label) ? fsbData.label : 'Ver promoções';

    const buttonHTML = `
      <a href="${link}" class="button fsb-cart-button">
          ${label}
      </a>
    `;

    container.append(buttonHTML);
  }

  replaceUpdateButton();

  $(document.body).on('updated_cart_totals', function () {
    replaceUpdateButton();
  });
});