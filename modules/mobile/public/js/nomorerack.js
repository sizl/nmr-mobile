(function() {
    window.NMR = {

        init: function(options) {

            $(document).delegate('#nav-panel', 'touchmove', false);

            $('#nav-back-btn').on('click', function(e){
                e.preventDefault();
                window.history.back();
            });
        }


    }
})();