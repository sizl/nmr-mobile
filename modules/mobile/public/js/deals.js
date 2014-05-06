(function (NMR) {
    NMR.Deals = {

        page: 1,
        limit: 0,
        loading: false,
        error: false,
        category: null,

        init: function (options) {

            this.limit = options.limit;

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
            var url = this.getPageUrl();

            this.loading = true;

            $.getJSON(url).done(function(result){
                self.deals_container.append(self.cell_template({deals: result.deals}));
            }).error(function() {
                self.error = true;
               alert('An error occurred when tyring to load more deals');
            }).always(function(){
                self.loading = false;
            });
        },

        getPageUrl: function() {

            var url = '/deals';

            this.page++;

            if(this.category) {
                url += '/category/' + this.category;
            }
            url += '?page=' + this.page + '&limit=' + this.limit;

            return url;
        },

        infiniteScroll: function() {
            var self = this;
            $(document).scroll(function(){

                if (self.error) {
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