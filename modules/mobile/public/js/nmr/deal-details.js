(function (NMR) {
    NMR.DealView = {

        deal: null,
        initialized: false,
        product_item_id: null,
        selected_image_index: null,
        changed_from_attribute: null,

        init: function (options) {

            this.deal_item_id = options.deal_item_id;
            this.product_items = options.product_items || {};
            this.image_map = options.image_map;

            this.form = $("#add-item");
            this.form_holder = $("#add-item-form-holder");
            this.form_message = $("#form-message");
            this.attr_holder = $(".attr-holder");
            this.attribute_selectors = this.attr_holder.find("select");
            this.gallery = $('#gallery-container').find('.gallery').eq(0);

            this.initTouchSlideShow();
            this.bindSelectors();
            this.bindAddItemForm();
            this.addToRecentlyViewed();
            this.prepareProductItem();
        },

        initTouchSlideShow: function() {

            var self = this;

            this.gallery.touchSlider({
                mode: 'index',
                center: true,
                single: true,
                prevLink: 'button.prev',
                nextLink: 'button.next',
                touch: true,
                delta: 70,
                duration: 500,
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
                },

                onChange: function(prev, curr) {

                    if (typeof(self.image_map['sequence']) != 'undefined' &&
                        self.changed_from_attribute === false &&
                        self.selected_image_index !== null) {

                        if (typeof(self.image_map.sequence[curr]) != 'undefined') {
                            self.form_holder.find("select[name^='attribute']").each(function() {
                                var option = $(this).find('option[value="' + self.image_map.sequence[curr][0] + '"]');
                                if (option.length) {
                                    $(this).val(self.image_map.sequence[curr][0]).change();
                                    return false;
                                }
                            });
                        }
                    }

                    self.selected_image_index = curr;
                    self.changed_from_attribute = false;
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

                if (self.form_holder.find('input[name=product_item_id]').val() == '') {
                    //TODO: send error alert to backend if this ever happens
                    self.form_message.text('* Product Item Id is not set').addClass('error');
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

            this.form_holder.find("select").on('change', function() {

                if($(this).val() == ""){
                    $(this).parent().removeClass('ui-icon-check').addClass('unselected ui-icon-minus');
                }else{
                    $(this).parent().removeClass('unselected ui-icon-minus').addClass('ui-icon-check');
                }

                if(self.form_holder.find('.unselected').length == 0){
                    self.form_message.empty().removeClass('error');
                }

                if ($(this).attr('name').indexOf('attribute') === 0) {
                    self.setProductItem();
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
        },

        prepareProductItem: function() {
            if (NMR.countObj(this.product_items) == 1) {
                for(var o in this.product_items) {
                    this.product_item_id = o;
                    this.form_holder.find('input[name=product_item_id]').val(this.product_item_id);
                    break;
                }
            }
        },

        setProductItem: function() {

            var self = this;
            var attr_key, attr_val, attributes = {};

            this.attribute_selectors.each(function() {
                attr_val = $.trim($(this).val());
                attr_key = $.trim($(this).data('attr').toLowerCase());
                if (attr_val != "") {
                    attributes[attr_key] = attr_val;
                }
            });

            //product_item has images mapped. change the image in the slide show
            if (typeof(self.image_map['attribute']) != 'undefined') {
                for(var a in attributes) {
                    var key = attributes[a].replace(/[^a-zA-Z0-9]/gi,'').toLowerCase();
                    if (typeof (self.image_map.attribute[key]) !== 'undefined') {
                        self.selectImageFromAttribute(self.image_map.attribute[key][0]);
                        break;
                    }
                }
            }

            //not all attributes are selected. reset product_item_id
            if (this.attribute_selectors.length != NMR.countObj(attributes)) {
                self.product_item_id = null;
                self.form_holder.find('input[name=product_item_id]').val('');
                return;
            }

            //all attributes are selected. find the product item id
            if (this.attribute_selectors.length === NMR.countObj(attributes)) {
                self.product_item_id = self.getProductItem(attributes);

                if (!self.product_item_id) {
                    self.form_holder.find('input[name=product_item_id]').val('');
                    console.log('unsetting product item id');
                    return;
                }
                console.log('setting product item id: '  +  self.product_item_id);
                self.form_holder.find('input[name=product_item_id]').val(self.product_item_id);
            }
        },

        getProductItem: function(attributes) {

            var counter = {};
            var product_item_id = null;

            for (var o in this.product_items) {

                if (typeof(counter[o]) == 'undefined') {
                    counter[o] = 0;
                }

                $.each(this.product_items[o], function(key, value) {
                    if (typeof (attributes[key]) != 'undefined') {
                        if ($.trim(attributes[key]) == $.trim(value)) {
                            counter[o]++;
                        }
                    }
                });
            }

            for (var pid in counter) {
                if (counter[pid] == NMR.countObj(attributes)) {
                    product_item_id = pid;
                    break;
                }
            }

            return product_item_id;
        },

        selectImageFromAttribute: function(imageIndex) {

            var self = this;
            var holder = this.gallery.find('.holder');
            var current_index = holder.find('.item.active').index();

            this.selected_image_index = imageIndex;

            //already showing proper image. do nothing...
            if (current_index == this.selected_image_index) {
                return;
            }

            //set slide duration to be really quick temporarily
            this.gallery.get(0).setDuration(50);

            //hide slide effect while jumping to image
            holder.animate({opacity: 0}, 150, function() {
                self.changed_from_attribute = true;
                self.gallery.get(0).moveTo(self.selected_image_index);
                setTimeout(function(){
                    holder.animate({opacity: 1}, 150);
                    self.gallery.get(0).setDuration(500);
                }, 50);
            });
        }
    }
})(NMR);