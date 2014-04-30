(function (NMR) {

    remove_id: null,

    NMR.Checkout = {

        init: function (options) {

            NMR.setOptions(this, options);

            this.content = $("#checkout-container");
            this.checkout_btn = $("#checkout-btn");

            this.auth_modal = $("#login");

            this.bindRemoveConfirm();
            this.bindCheckoutForm();
        },

        bindCheckoutForm: function() {

            var self = this;
            this.bindOnBeforeChange();

            this.checkout_btn.on('click', function(e) {
                if(NMR.User.authenticated == false) {
                    e.preventDefault();
                    self.auth_modal.popup('open');
                    //override auto complete callback
                    NMR.authCompleted = function(){
                        window.location.href = self.next_url;
                    };
                }else{
                    window.location.href = self.next_url;
                }
            });
        },

        bindOnBeforeChange: function() {

            $(document).bind("pagechange", function(e, data) {
                if (typeof data.toPage === "object") {
                    if (data.toPage.data('url').search(/^\/checkout\/address/) !== -1) {
                        NMR.Address.init();
                    }
                }
            });
        },

        bindRemoveConfirm: function() {

            var self = this;
            this.remove_confirm = $("#confirm");

            this.content.find(".remove-item").on('click', function(e) {
                //memoize the item id that is about to be removed
                //then the confirm popup can have a handle on it
                self.remove_id = $(this).data("item-id");
            });

            this.remove_confirm.find("button.dismiss").on('click', function() {
                self.remove_confirm.popup('close');
            });

            this.remove_confirm.find("button.confirm").on('click', function() {

                self.content.find('#' + self.remove_id).fadeOut(500, function() {
                    $(this).remove();
                    self.remove_id = null;

                    //if there are no more items in cart, show the empty view
                    if(self.content.find('.cart-item').length === 0){
                        self.content.find('.cart-disclaimer').hide();
                        self.content.find('.empty-cart').fadeIn();
                    }
                });

                self.remove_confirm.popup('close');
            });
        }
    }
})(NMR);