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

            FB.api('/me', {fields: 'id, email, first_name, last_name, gender, timezone'}, function(user) {
                var connect;
                user.stategy = 'facebook';
                user.access_token = FB.getAuthResponse()['accessToken'];

                console.log('ACCESS TOKEN: ' + user.access_token);

                connect = $.ajax({
                    url: '/account/fbconnect',
                    type: 'post',
                    dataType: 'json',
                    data: user
                });

                connect.done(function(result){
                    var authenticated = (result.status == 0);
                    callback(authenticated);
                });
            });
        }
    }
})();