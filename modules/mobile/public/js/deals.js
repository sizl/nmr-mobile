(function (NMR) {
    NMR.Deals = {

        offset: 0,
        limit: 50,

        init: function (options) {

            this.deals = $("#deals-content");
            this.cell_template = Handlebars.compile($("#product-cell-template").html());

            this.bindOnBeforeChange();
            this.renderDeals();
        },

        bindOnBeforeChange: function() {
            $(document).bind("pagechange", function(e, data) {
                if(typeof(data.toPage) == 'object'){
                    if (data.toPage.data('url').search(/^\/deals\//) !== -1) {
                       NMR.DealView.init();
                    }
                }
            });
        },

        renderDeals: function() {
            var self = this;
            this.showLoader('Loading deals...');
            this.fetchDeals().done(function(result){
                self.offset += NMR.countObj(result.deals);
                self.deals.append(self.cell_template({deals: result.deals}));
                self.hideLoader();
            });
        },

        showLoader: function(msg) {
            $.mobile.loading('show', {
                text: msg,
                textVisible: true,
                theme: 'd',
                html: ""
            });
        },

        hideLoader: function() {
            $.mobile.loading('hide');
        },

        fetchDeals: function() {
            return $.ajax({
                url: '/deals/fetch/' + this.offset + '/' + this.limit,
                dataType: 'json',
                type: 'get'
            });
        }
    }

})(NMR);