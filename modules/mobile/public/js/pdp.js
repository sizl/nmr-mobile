(function (NMR) {
    NMR.DealView = {

        init: function () {

            this.content = $("#content");
            this.initTouchSlideShow();

        },

        initTouchSlideShow: function() {
            this.gallery = $('#gallery-container');

            this.gallery.touchSlider({
                mode: 'auto',
                single: true,
                center: true,
                prevLink: 'button.prev',
                nextLink: 'button.next',
                onInit: function () {

                    var gallery = NMR.DealView.gallery;
                    var w = gallery.width() * 90 / 100;
                    var h = gallery.height() * 95 / 100;
                    var l = gallery.find('.gallery').height() + 'px';

                    gallery.find('.gallery-image').each(function () {
                        $(this).css({'max-width': w});
                        $(this).css({'max-height': h});
                        $(this).parent().css({'line-height': l});
                    });
                }
            });
        }
    }
})(NMR);