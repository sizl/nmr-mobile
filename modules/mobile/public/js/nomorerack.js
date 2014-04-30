(function() {
    window.NMR = {

        User: null,

        FB_PERMS: {scope: 'email, publish_actions'},

        authCompleted: $.noop,

        init: function(options) {

            this.User = options;

            $(document).delegate('#nav-panel', 'touchmove', false);

            this.bindSidebarLogin();
            this.bindSidebarRegister();
            this.bindSidebarFbConnect();

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

        bindSidebarFbConnect: function() {
            if($(".fb-btn").length){
                $(".fb-btn").on('click', function() {
                    NMR.authCompleted = function(){
                        location.reload();
                    }
                    NMR.fbLogin();
                });
            }
        },

        bindLogoutBtn: function() {
            if($(".logout").length){
                $(".logout").on('click', function() {
                    FB.logout(function(response){
                        window.location.href = '/account/logout';
                    });
                });
            }
        },

        fbLogin: function() {
            FB.getLoginStatus(function(response) {
                if (response.status === 'connected') {
                    NMR.fbConnect(NMR.authCompleted);
                } else {
                    FB.login(function(response) {
                        if(response.authResponse){
                            NMR.fbConnect(NMR.authCompleted);
                        }
                    }, NMR.FB_PERMS);
                }
            });
        },

        fbConnect: function(callback) {

            FB.api('/me', {fields: 'id, email, first_name, last_name, gender, timezone'}, function(user) {
                var connect;
                user.strategy = 'facebook';
                user.access_token = FB.getAuthResponse()['accessToken'];
                user.email_address = user.email;

                delete user['email'];
                //console.log('ACCESS TOKEN: ' + user.access_token);

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
        },

        bindSidebarLogin: function() {
            var self = this;
            var login_form = $("#sb-login-form");

            if(login_form.length){
                login_form.bind('submit', function(e) {

                    var form = e.target;
                    e.preventDefault();

                    form.email_address.value = form.email_address.value.trim();
                    if(form.email_address.value == ''){
                        form.email_address.focus();
                        return false;
                    }
                    if(form.password.value == ''){
                        form.password.focus();
                        return false;
                    }

                    self.submitLogin(form.email_address.value, form.password.value).done(function(result) {
                        if(result.status == 0){
                            location.reload();
                        }else{
                            window.alert(result.error);
                        }
                    });
                });
            }
        },

        bindSidebarRegister: function() {
            var self = this;
            var login_form = $("#sb-reg-form");

            if(login_form.length){
                login_form.bind('submit', function(e) {

                    var form = $(e.target);
                    e.preventDefault();

                    var email_address = form.find('[name=customer\\[email_address\\]]');
                    var password = form.find('[name=customer\\[password\\]]');

                    email_address.val($.trim(email_address.val()));

                    if(email_address.val() == ''){
                        email_address.focus();
                        return false;
                    }
                    if(password.val() == ''){
                        password.focus();
                        return false;
                    }

                    self.submitRegister(email_address.val(), password.val()).done(function(result) {
                        if(result.status == 0){
                            location.reload();
                        }else{
                            window.alert(result.error);
                        }
                    });
                });
            }
        },

        submitLogin: function(email, password) {
            return $.ajax({
                url: '/account/login',
                type: 'post',
                dataType: 'json',
                data: {
                    email_address: email,
                    password: password
                }
            });
        },

        submitRegister: function(email, password) {
            return $.ajax({
                url: '/account/create',
                type: 'post',
                dataType: 'json',
                data: {
                    customer:{
                        email_address: email,
                        password: password
                    }
                }
            });
        }
    }
})();