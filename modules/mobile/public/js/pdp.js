(function (NMR) {
    NMR.DealView = {

        initialized: false,

        init: function () {

            this.form = $("#add-item");
            this.form_holder = $("#add-item-form-holder");
            this.form_message = $("#form-message");

            this.initTouchSlideShow();
            this.bindAddItemForm();

            this.initialized = true;
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

            this.form.on('submit', function(e) {
                e.preventDefault();
                if(self.form_holder.find('.unselected').length) {
                    self.form_message.text('* Please select options for this item').addClass('error');
                    return false;
                }

                $.ajax({
                    url: e.target.action,
                    type: 'post',
                    dataType: 'json',
                    data: $(e.target).serializeArray(),
                    success: function(result) {
                        if(result.status == 1) {
                            self.form_message.text(result.error).addClass('error');
                        }else{
                            window.location.href = '/checkout';
                        }
                    }
                });
            });

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
        }
    }
})(NMR);