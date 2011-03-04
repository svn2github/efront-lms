document.observe("dom:loaded", function () {
    if( $('module-reports-select') ) {
        $('module-reports-select').observe('change', function (event) {
            event.findElement('form').submit();
        });
    }
}, false);
