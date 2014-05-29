(function (NMR) {
    NMR.Events = {

        offset: 0,
        limit: 0,
        loading: false,
        error: false,
        scrollStop: false,

        init: function (options) {

            this.limit = options.limit;
            this.offset = options.offset;

            this.registerHandlers();
            this.registerTemplates();

            this.fetchEvents();
            this.infiniteScroll();
        },

        registerHandlers: function() {
            this.events_container = $("#events-container");
        },

        registerTemplates: function() {
            this.cell_template = Handlebars.compile($("#event-row-template").html());
        },

        fetchEvents: function() {

            var self = this;
            var url = '/events/fetch?page=' + this.offset + '&limit=' + this.limit;

            this.loading = true;

            $.getJSON(url).done(function(result){

                self.scrollStop = (result.count == 0);

                this.offset += result.count;

                self.events_container.append(self.cell_template({events: result.events}));
            }).error(function(xhr, status, msg) {
                self.scrollStop = true;
            }).always(function(){
                self.loading = false;
            });
        },

        infiniteScroll: function() {
            var self = this;

            $(document).scroll(function(){

                if (self.scrollStop) {
                    return;
                }

                if($(document).height() > $(window).height()) {
                    //Start fetching when at 65% scrolled down
                    if ($(window).scrollTop() > ($(document).height() - $(window).height()) * .65) {
                        if(self.loading == false) {
                            self.fetchEvents();
                        }
                    }
                    // Has scrolled to absolute bottom. we could do some clever marketing here for customers that scroll alot
                    //if($(window).scrollTop() == ($(document).height() - $(window).height())){}
                }
            });
        }
    }

})(NMR);