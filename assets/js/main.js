(function ($) {
    $(document).on('ready', function () {
        var db = new Object();
        db.menuResponsive = function () {
            $('.menu-icon').on('click', function () {
                var menu = $('.menu-res');
                if ($(menu).is(":visible")) {
                    $(menu).slideUp();
                } else {
                    $(menu).slideDown();
                }
            });
        }
        db.menuResponsive();
    });
})(jQuery);
