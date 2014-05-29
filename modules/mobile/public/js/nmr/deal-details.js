(function (NMR) {
    NMR.DealView = {

        deal: null,
        initialized: false,

        init: function (options) {

            this.deal_item_id = options.deal_item_id;
            this.form = $("#add-item");
            this.form_holder = $("#add-item-form-holder");
            this.form_message = $("#form-message");

            this.initTouchSlideShow();
            this.bindSelectors();
            this.bindAddItemForm();

            this.initialized = true;

            this.addToRecentlyViewed();
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
        },

        bindAddItemForm:function() {
            var self = this;

            this.form.bind('submit', function(e) {
                //has unselected attributes?
                e.preventDefault();

                if(self.form_holder.find('.unselected').length) {
                    self.form_message.text('* Please select options for this item').addClass('error');
                    return false;
                }

                var post = $(this).serializeArray();

                self.addToCart(post).done(function(result) {
                    if(result.status == 1) {
                        self.form_message.text('* ' + result.error).addClass('error');
                    }else{
                        self.form_message.empty().removeClass('error');
                        window.location.href = '/cart';
                    }
                });
            });
        },

        addToCart: function(post) {
            return $.ajax({
                url: '/cart/add',
                type: 'post',
                dataType: 'json',
                data: post
            });
        },

        bindSelectors: function() {
            var self = this;
            //Bind Select Dropdowns
            this.form_holder.find("select").on('change', function() {
                if($(this).val() == ""){
                    $(this).parent().removeClass('ui-icon-check').addClass('unselected ui-icon-minus');
                }else{
                    $(this).parent().removeClass('unselected ui-icon-minus').addClass('ui-icon-check');
                }

                if(self.form_holder.find('.unselected').length == 0){
                    self.form_message.empty().removeClass('error');
                }
            });
        },

        addToRecentlyViewed: function() {

            if (NMR.User.authenticated) {
                $.ajax({
                    url: '/recent/add',
                    type: 'post',
                    dataType: 'json',
                    data: {deal_item_id: this.deal_item_id}
                });
            }
        }
    }
})(NMR);