(function (NMR) {

    NMR.Account = {

        init: function(options) {

            this.bindShowPassword();
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
        }
    };

})(NMR);
