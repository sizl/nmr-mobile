(function (NMR) {

    NMR.Cart = {

        cart: null,
        next_url: null,

        init: function (options) {

            this.cart = options.cart || {};
            this.next_url = options.next_url;

            this.auth_modal = $("#login");
            this.content = $("#checkout-container");
            this.checkout_btn = $("#checkout-btn");
            this.edit_modal = $("#edit-item-popup");

            this.cart_summary = $("#cart-summary");
            this.summary_template = Handlebars.compile($("#cart-summary-template").html());

            //this.bindEditItem();
            this.bindChangeQty();
            this.bindRemoveConfirm();
            this.bindCheckoutForm();
        },

        bindChangeQty: function() {

            var self = this;
            this.content.find("select[name=quantity]").on('change', function(e) {

                self.checkout_btn.prop('disabled', true);

                //update view immediately
                var quantity = parseInt($(this).val(), 10);
                var shopping_cart_item_id = $(e.target).data('item-id');

                var row = $("#item_" + shopping_cart_item_id);
                var item = self.cart.items[shopping_cart_item_id];

                var price = parseFloat(item.price);
                var shipping_price = parseFloat(item.shipping_price);

                var shipping_total = quantity * shipping_price;
                var row_total = (quantity * price) + shipping_total;

                row.find('.item-shipping').text(shipping_total.toFixed(2));
                row.find('.item-total').text(row_total.toFixed(2));

                self.updateQuantity(shopping_cart_item_id, $(this).val()).done(function(result) {
                    //update view from update response
                    var item = result.updated_item;
                    var row = $("#item_" + item.id);
                    row.find('.item-shipping').text(item.shipping);
                    row.find('.item-total').text(item.row_total);
                    self.cart_summary.html(self.summary_template({cart: result.cart_summary}));
                }).always(function(){
                    self.checkout_btn.prop('disabled', false);
                });
            });

        },

        bindEditItem: function() {
            var self = this;
            this.edit_template = Handlebars.compile($('#edit-item-template').html());
            this.content.find('.edit-item').click(function(e) {
                e.preventDefault();
                var item = self.cart.items[$(e.target).data('item-id')];
                console.log(item);
                var html = self.edit_template({deal:item});
                self.edit_modal.html(html).popup('open');
            })
        },

        bindCheckoutForm: function() {

            var self = this;
            this.bindOnBeforeChange();

            this.checkout_btn.on('click', function(e) {

                e.preventDefault();

                if(NMR.User.authenticated) {

                    window.location.href = self.next_url;

                } else {

                    self.auth_modal.popup('open');
                    //override auto complete callback
                    NMR.Login.authCompleted = function(){
                        window.location.href = self.next_url;
                    };
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
                self.shopping_cart_item_id = $(this).data("shopping-cart-item-id");
            });

            this.remove_confirm.find("button.dismiss").on('click', function() {
                self.remove_confirm.popup('close');
            });

            this.remove_confirm.find("button.confirm").on('click', function(e) {
                self.checkout_btn.prop('disabled', true);
                self.removeItem(self.shopping_cart_item_id).done(function(result) {
                    if (result.status == 0) {
                        self.cart_summary.html(self.summary_template({cart: result.cart_summary}));
                        self.content.find('#item_' + self.shopping_cart_item_id).fadeOut(500, function() {
                            self.shopping_cart_item_id = null;
                            //if there are no more items in cart, show the empty view
                            if(self.content.find('.cart-item').length === 0){
                                self.content.find('.cart-info').fadeOut();
                                self.content.find('.empty-cart').fadeIn();
                            }
                        }).remove();

                        self.remove_confirm.popup('close');
                    } else {
                        window.alert(result.error);
                    }
                }).always(function() {
                    self.checkout_btn.prop('disabled', false);
                });
            });
        },

        removeItem: function(item_id) {

            return $.ajax({
                url: '/cart/remove',
                data: {
                    shopping_cart_id: this.cart.id,
                    shopping_cart_item_id: item_id},
                type: 'post',
                dataType: 'json'
            });
        },

        updateQuantity: function(item_id, qty) {

            return $.ajax({
                url: '/cart/update',
                type: 'post',
                dataType: 'json',
                data: {
                    shopping_cart_id: this.cart.id,
                    shopping_cart_item_id: item_id,
                    quantity: qty
                }
            });
        }
    }
})(NMR);