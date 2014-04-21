(function() {
    window.NMR = {

        User: null,

        init: function(options) {

            this.User = options;

            $(document).delegate('#nav-panel', 'touchmove', false);

            $('#nav-back-btn').on('click', function(e){
                e.preventDefault();
                window.history.back();
            });
        },

        setOptions: function(obj, options) {
            for(var o in options){
                if(!obj.hasOwnProperty(o)){
                    obj[o] = options[o];
                }
            }
        },

        countObj: function(obj) {
            var o, c = 0;
            for(o in obj) {
                c++;
            }
            return c;
        },

        fbConnect: function(callback) {
            FB.api('/me', function(user) {
                console.log(user);
                /*
                 email: "sandra_akqcwxk_moiduwitz@tfbnw.net"
                 first_name: "Sandra"
                 gender: "female"
                 id: "100008158214106"
                 last_name: "Moiduwitz"
                 link: "https://www.facebook.com/profile.php?id=100008158214106"
                 locale: "en_US"
                 name: "Sandra Moiduwitz"
                 timezone: 0
                 updated_time: "2014-04-18T19:07:47+0000"
                 verified: false
                 */
                //TODO: connect on backend

                result = true;

                callback(result);
            });
        }
    }
})();