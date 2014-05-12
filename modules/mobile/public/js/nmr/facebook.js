(function (NMR) {
    NMR.Facebook = {
        connected: false,
        fields: {},
        permissions: {},
        options: {
            appId: 0,
            status: false,
            cookie: true,
            xfbml: false
        },

        init: function(options) {

            this.configure(options);

            this.loadScript(function() {
                FB.init(NMR.Facebook.options);
                //Check if user is already connected
                FB.getLoginStatus(function(response) {
                    NMR.Facebook.processResponse(response);
                });
            });
        },

        processResponse: function (response) {
            this.connected = (response.status === 'connected');
            this.bindLogoutHandlers();
            this.bindLoginHandlers();
        },

        bindLogoutHandlers: function() {

            if($(".logout").length){
                $(".logout").on('click', function(e) {
                    if (NMR.Facebook.connected) {
                        e.preventDefault();
                        //Log out of facebook then NMR
                        FB.logout(function(response){
                            if (response.status == "connected") {
                                alert('Could not log you out. Please refresh and try again');
                                return;
                            }
                            if (response.status == "unknown") {
                                //sucessully logged out of FB. no log out of NMR
                                window.location.href = '/logout';
                            }
                        });
                    }
                });
            }
        },

        bindLoginHandlers: function() {
            if($(".fb-btn").length){
                $(".fb-btn").on('click', function() {
                    NMR.Facebook.connect(function(response) {
                        location.reload();
                    });
                });
            }
        },

        bindAuthChange: function() {
            //FB Auth listener
            FB.Event.subscribe('auth.authResponseChange', function(response) {
                if (response.status === 'connected') {
                    NMR.bindFBLogoutHandlers();
                }
                if (response.status === 'connected') {

                }
            });
        },

        prepareParams: function(fbUser) {

            var data = fbUser;
            data.strategy = 'facebook';
            data.email_address = fbUser.email;
            data.access_token = FB.getAuthResponse()['accessToken'];

            delete data['email'];

            return data;
        },

        connect: function(callback) {

            FB.login(function(response) {

                //user either canceled
                if (response.status != 'connected') {
                    return false;
                }

                //Once connected, get FB user session data
                FB.api('/me', {fields: NMR.Facebook.fields}, function(result) {

                    if(typeof(result.error) == 'object'){
                        alert(result.error.message);
                    } else {
                        $.ajax({
                            url: '/login/fbconnect',
                            type: 'post',
                            dataType: 'json',
                            data: NMR.Facebook.prepareParams(result)
                        }).done(function(result){
                            callback(result);
                        });
                    }
                }, {scope: NMR.Facebook.permissions});
            });
        },

        configure: function(options) {

            this.fields = options.fields;
            this.permissions = options.permissions;

            NMR.Facebook.options.appId = options.appId;
            //You can override Fb init options here.

        },

        loadScript: function(callback) {
            $.ajaxSetup({ cache: true });
            $.getScript('//connect.facebook.net/en_US/all.js', callback);
        },

        bindFbConnect: function() {
            $("#fb-connect").on('click', function(e) {
                e.preventDefault();
                NMR.Facebook.connect(function() {
                    window.location.href = '/checkout/address/billing';
                });
            });
        }
    }
})(NMR);