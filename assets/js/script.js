jQuery(function ($) {

    function cleanFSB() {
        const bars = $('.fsb-bar-wrapper');

        if (bars.length > 1) {
            bars.not(':first').remove();
        }
    }

    // roda ao carregar
    cleanFSB();

    // roda sempre que checkout atualizar (ESSENCIAL)
    $(document.body).on('updated_checkout', function () {
        cleanFSB();
    });

});