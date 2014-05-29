(function (NMR) {
    NMR.Deals = {

        offset: 0,
        limit: 0,
        loading: false,
        error: false,
        category: null,
        fetchUrl: null,
        scrollStop: false,

        init: function (options) {

            this.fetchUrl = options.fetchUrl;
            this.limit = options.limit;
            this.offset = options.offset;

            if (options.category) {
                this.category = options.category;
            }

            this.prepareView();
            this.bindPDPView();
            this.fetchDeals();
            this.infiniteScroll();
        },

        prepareView: function() {
            this.deals_container = $("#deals-container");
            this.cell_template = Handlebars.compile($("#product-cell-template").html());
        },

        bindPDPView: function() {
            $(document).bind("pagechange", function(e, data) {
                if(typeof(data.toPage) == 'object'){
                    if (data.toPage.data('url').search(/^\/deals\/\d+/) !== -1) {
                        NMR.DealView.init();
                    }
                }
            });
        },

        fetchDeals: function() {

            var self = this;

            var url = this.fetchUrl + '?offset=' + this.offset + '&limit=' + this.limit;

            if(this.category) {
                url += '&category=' + this.category;
            }

            this.offset += this.limit;
            this.loading = true;

            $.getJSON(url).done(function(result){
                self.scrollStop = (result.count == 0);
                self.deals_container.append(self.cell_template({deals: result.deals}));
            }).error(function(xhr, status, msg) {
                self.scrollStop = true;
            }).always(function(){
                self.loading = false;
            });
        },

        infiniteScroll: function() {
            var self = this;

            $(document).on('scroll.nmr', function(){

                if (self.scrollStop) {
                    return;
                }

                if($(document).height() > $(window).height()) {
                    //Start fetching when at 65% scrolled down
                    if ($(window).scrollTop() > ($(document).height() - $(window).height()) * .65) {
                        if(self.loading == false) {
                            self.fetchDeals();
                        }
                    }
                    // Has scrolled to absolute bottom. we could do some clever marketing here for customers that scroll alot
                    //if($(window).scrollTop() == ($(document).height() - $(window).height())){}
                }
            });
        }
    }

})(NMR);