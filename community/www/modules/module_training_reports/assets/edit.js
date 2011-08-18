
var ModuleReportsTableRows = Class.create({
    initialize: function(element, select, addAnchor) {
        this.element = $(element);
        this.select = $(select);
        this.addAnchor = $(addAnchor);

        if (this.element && this.addAnchor) {
            this.addAnchor.observe('click', this.handleClick.bind(this));
            this.transparentSrc = $('module-reports-transparent').src;

            this.element.select('td.elementCell').each( this.addButtons.bind(this));
        }
    },

    addButtons: function(td) {

        td.insert(new Element('img', {
            src: this.transparentSrc,
            className:"sprite16 sprite16-error_delete"
        }).observe('click', this.handleRemoveRow.bind(this)))
        .insert(' ')
        .insert(new Element('img', {
            src: this.transparentSrc,
            className:"sprite16 sprite16-navigate_up"
        }).observe('click', this.handleUpRow.bind(this)))
        .insert(' ')
        .insert(new Element('img', {
            src: this.transparentSrc,
            className:"sprite16 sprite16-navigate_down"
        }).observe('click', this.handleDownRow.bind(this)));
    },


    handleClick: function(event) {
        var select = this.select.clone(true).show();
        select.id = null;

        var tdButtons = new Element('td', {className:'elementCell'}).update('&nbsp;');
        this.addButtons(tdButtons);

        var row = new Element('tr')
            .insert( new Element('td', {className:'labelCell'}).insert(select))
            .insert(tdButtons );

        this.element.down().insert(row);
    },

    handleRemoveRow: function(event) {

        event.findElement('tr').remove();
    },

    handleUpRow: function(event) {

        var tr = event.findElement('tr');
        var simblings = tr.previousSiblings();

        if( simblings.length > 0 ) {
            simblings[0].insert({
                before: tr
            });
        }
    },

    handleDownRow: function(event) {
        var tr = event.findElement('tr');
        var simblings = tr.nextSiblings();

        if( simblings.length > 0 ) {
            simblings[0].insert({
                after: tr
            });
        }
    }
});

new ModuleReportsTableRows('module-reports-fields', 'module-reports-fields-select','module-reports-add-field');
new ModuleReportsTableRows('module-reports-courses', 'module-reports-courses-select','module-reports-add-course');
new ModuleReportsTableRows('module-reports-branches', 'module-reports-branches-select','module-reports-add-branch');
