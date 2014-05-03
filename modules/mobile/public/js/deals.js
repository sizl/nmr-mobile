(function (NMR) {
    NMR.Deals = {

        offset: 0,
        limit: 50,

        init: function (options) {

            this.limit = options.limit || 50;
            this.offset = this.limit;

            this.registerHandlers();
            this.registerTemplates();
            this.bindOnBeforeChange();

            this.renderDeals();

        },

        registerHandlers: function()
        {
            this.deals = $("#deals-container");
        },

        registerTemplates: function()
        {
            this.cell_template = Handlebars.compile($("#product-cell-template").html());
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
            this.fetchDeals().done(function(result){
                self.offset += self.limit;
                self.deals.append(self.cell_template({deals: result.deals}));
            });
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