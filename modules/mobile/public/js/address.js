(function (NMR) {

    NMR.Address = {

        init: function(options) {

            debugger;

            this.bindSubmitForm();


        },

        bindSubmitForm: function() {

            $("#address-form").submit(function(){
                if(!this.clone.checked){
                    window.location.href = '/account/address/shipping';
                }
            });
        }
    };

})(NMR);
