(function (NMR) {

    NMR.Register = {

        init: function() {

            this.context = $("#register-container");
            this.email = this.context.find("#email_address");
            this.password = this.context.find("#password");
            this.error = this.context.find("#auth-form-error");

            this.next_url = '/checkout/address/billing';

            this.bindShowPassword();
            this.bindAuthForm();
            this.bindFbConnect();
        },

        bindAuthForm: function() {

            var self = this;

            $("#submit-btn").on('click', function(e) {

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

                NMR.submitRegister(email, password).done(function(result) {
                    if(result.status == 0){
                        window.location.href = self.next_url;
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
            var self = this;
            $("#fb-connect").on('click', function(e) {
                e.preventDefault();
                NMR.Facebook.connect(function(response) {
                    window.location.href = self.next_url;
                });
            });
        }
    };

})(NMR);
