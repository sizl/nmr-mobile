(function() {

    window.NMR = {

        User: null,

        Nav: {

        },

        init: function(customer) {

            this.User = customer;

            this.bindNavigation();
            this.bindSidebarLoginForm();
            this.bindSidebarRegistrationForm();

            this.bindNavInits();

        },

        bindNavInits: function() {

            $(document).bind("pagechange", function(e, data) {
                if(typeof(data.toPage) == 'object'){
                    NMR[data.toPage.data('init')].init();
                }
            });
        },

        /** Navigation *************************************/

        bindNavigation: function() {

            $(document).delegate('#nav-panel', 'touchmove', false);

            $('#nav-back-btn').on('click', function(e){
                e.preventDefault();
                window.history.back();
            });
        },

        bindSidebarLoginForm: function() {
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

        bindSidebarRegistrationForm: function() {
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
                url: '/login',
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
                url: '/register',
                type: 'post',
                dataType: 'json',
                data: {
                    customer:{
                        email_address: email,
                        password: password
                    }
                }
            });
        },

        /** View Helpers ****************************/

        showLoader: function(msg) {
            $.mobile.loading('show', {
                text: msg,
                textVisible: true,
                theme: 'd',
                html: ""
            });
        },

        hideLoader: function() {
            $.mobile.loading('hide');
        },

        authCompleted: function(){
            location.reload();
        },

        /** Core **************************/

        setProperties: function(obj, options) {
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
        }

    }
})();