(function (NMR) {
    NMR.Events = {

        offset: 0,
        limit: 50,

        init: function (options) {

            this.limit = options.limit || 50;
            this.offset = this.limit;

            this.registerHandlers();
            this.registerTemplates();
            this.renderEvents();
        },

        registerHandlers: function() {
            this.events_container = $("#events-container");
        },

        registerTemplates: function() {
            this.cell_template = Handlebars.compile($("#event-row-template").html());
        },

        renderEvents: function() {
            var self = this;
            this.fetchEvents().done(function(result){
                self.offset += self.limit;
                self.events_container.append(self.cell_template({events: result.events}));
            });
        },

        fetchEvents: function() {
            return $.ajax({
                url: '/events/fetch/' + this.offset + '/' + this.limit,
                dataType: 'json',
                type: 'get'
            });
        }
    }

})(NMR);