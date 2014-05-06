/** Handlebars Helpers *************************************/

Handlebars.registerHelper('times', function(n, block) {
    var accum = '';
    for(var i = 1; i < n; ++i)
        accum += block.fn(i);
    return accum;
});

// Swipe Up & Down
// http://stackoverflow.com/questions/17131815/how-to-swipe-top-down-jquery-mobile
