(function (NMR) {

    NMR.Login = {

        init: function() {

            this.context = $("#login");
            this.email = this.context.find("#email_address");
            this.password = this.context.find("#password");
            this.error = this.context.find("#auth-form-error");

            this.bindShowPassword();
            this.bindAuthForm();
            this.bindFbConnect();
        },

        bindAuthForm: function() {

            var self = this;

            $("#submit-btn").on('click', function() {

                var email = $.trim(self.email.val());
                var password = self.password.val();

                if(email == '') {
                    self.email.focus();
                    return false;
                }

                if(password == '') {
                    self.password.focus();
                    return false;
                }

                NMR.submitLogin(email, password).done(function(result) {
                    if(result.status == 0){
                        self.authCompleted(true);
                        self.error.text('').hide();
                    }else{
                        self.error.text(result.error).show();
                    }
                });
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
            $("#fb-connect").on('click', function(e) {
                e.preventDefault();
                NMR.Facebook.connect(function() {
                    window.location.href = '/checkout/address/billing';
                });
            });
        }
    };

})(NMR);
