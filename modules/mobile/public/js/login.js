(function (NMR) {

    NMR.Login = {

        init: function() {

            this.context = $("#login");
            this.email = this.context.find("#email");
            this.password = this.context.find("#password");
            this.type = this.context.find("#form-type");
            this.error = this.context.find("#auth-form-error");

            this.bindShowPassword();
            this.bindAuthForm();
            this.bindFbConnect();
        },

        bindAuthForm: function() {

            var self = this;
            $("#register-btn").on('click', function() {
                self.type.val('register');
            });

            $("#submit-btn").on('click', function(e) {

                var path, data;

                if($.trim(self.email.val()) == '') {
                    self.email.focus();
                    return false;
                }

                if($.trim(self.password.val()) == '') {
                    self.password.focus();
                    return false;
                }

                if(self.type.val() == 'login') {
                    path = '/account/login';
                }else{
                    path = '/account/create';
                }

                data = {
                    email: $.trim(self.email.val()),
                    password: $.trim(self.password.val())
                };

                self.submitForm(path, data).done(function(result) {
                    if(result.status == 0){
                        self.authCompleted(true);
                    }else{
                        self.error.text(result.error);
                    }
                });
            });
        },

        submitForm: function(path, data) {
            return $.ajax({
                url: path,
                type: 'post',
                data: data,
                dataType: 'json'
            });
        },

        bindShowPassword: function() {

            var self = this;
            this.password = $("#password");

            $("#show-password").on('click', function(){
                if(this.checked){
                    self.password.prop('type', 'text');
                }else{
                    self.password.prop('type', 'password');
                }
            });
        },

        bindFbConnect: function() {

            $("#fb-connect").on('click', function() {
                $(this).prop('disabled', true);

                FB.getLoginStatus(function(response) {
                    //already logged into facebook but not NMR
                    if (response.status === 'connected') {
                        NMR.fbConnect(NMR.Login.authCompleted);
                    } else {
                        //not logged into either
                        FB.login(function(response) {
                            //after successfully logging into fb, log into nmr..
                            if(response.authResponse){
                                NMR.fbConnect(NMR.Login.authCompleted);
                            }
                        }, {scope: 'email,publish_actions'});
                    }
                });
            });
        },

        authCompleted: function(result) {
            $("#login").popup('close');
            $("#auth-success").popup('open');
            NMR.User.authenticated = result;
        }
    };

})(NMR);
