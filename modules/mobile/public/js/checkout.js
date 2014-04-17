(function (NMR) {

    remove_id: null,

    NMR.Checkout = {

        init: function (options) {

            //NMR.setOptions(this, options);

            this.is_logged_out = true;
            this.has_addresses = false;

            this.content = $("#content");
            this.checkout_btn = $("#checkout-btn");

            this.bindRemoveConfirm();
            this.bindCheckoutForm();
        },

        bindCheckoutForm: function() {
            var self = this;
            this.checkout_btn.on('click', function() {

                if(self.is_logged_out) {
                    window.location.href = '/account/setup';
                }else{
                    if(self.has_addresses){
                        window.location.href = '/checkout/options';
                    }else{
                        window.location.href = '/account/address/billing';
                    }
                }
            });
        },

        bindRemoveConfirm: function() {

            var self = this;
            this.remove_confirm = $("#confirm");

            this.content.find(".remove-item").on('click', function(e) {
                self.remove_id = $(this).data("item-id");
            });

            this.remove_confirm.find("button.dismiss").on('click', function(){
                $('[data-role=popup]').popup('close');
            });

            this.remove_confirm.find("button.confirm").on('click', function(){
                self.content.find('#' + self.remove_id).fadeOut(500, function(){
                    $(this).remove();
                    if(self.content.find('.cart-item').length === 0){
                        self.content.find('.cart-disclaimer').hide();
                        self.content.find('.empty-cart').fadeIn();
                    }
                });
                $('[data-role=popup]').popup('close');
            });
        }
    }
})(NMR);