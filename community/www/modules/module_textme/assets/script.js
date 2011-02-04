function module_textme_swapContacts(from, to)
{
    $(from).select('option').each(function(o) {
        if( o.selected )
        {
            $(from).removeChild(o)
            $(to).appendChild(o);
        }
    });

    var options = $A($(to).options).sort(function(a, b) {
        return a.text - b.text;
    });

    $(to).select('options').each(function(o){
        $(to).removeChild(o)
    });

    options.each(function(o) {
        $(to).appendChild(o);
    });
}

function module_textme_selectAllOptions(select) {
    $(select).select('option').each(function(o) {
        o.selected = true;
    });
}

function module_textme_toggleExtraMessages(message_table_id, handle_id, module_textme_collapsed) {

    var el = $(handle_id);
    Element.extend(el);

    var img = el.down();
    var elements = $(message_table_id).select('tr');

    if( module_textme_collapsed == true )
    {
        elements.each(function (s) {s.show();});
        img.src = img.src.replace('arrow_down_blue.png', 'arrow_up_blue.png');
    }
    else
    {
        var counter = 0;
        elements.each(function (s) {((counter++) >= 10) ? s.hide() : s.show();});
        img.src = img.src.replace('arrow_up_blue.png', 'arrow_down_blue.png');
    }
}

if( $('module_textme_show_all_messages') )
{
    var module_textme_collapsed = true;
    $('module_textme_show_all_messages').observe('click', function(event) {
        module_textme_toggleExtraMessages('module_textme_messages_list', 'module_textme_show_all_messages', module_textme_collapsed);
        module_textme_collapsed = !module_textme_collapsed;
    });
}

if( $('module_textme_group_form') )
{
    $('module_textme_group_form').observe('submit', function(event) {
        module_textme_selectAllOptions('module_textme_in_group');
    });

    $('module_textme_group_add').observe('click', function (event) {
        module_textme_swapContacts('module_textme_out_group', 'module_textme_in_group');
    });

    $('module_textme_group_remove').observe('click', function (event) {
        module_textme_swapContacts('module_textme_in_group', 'module_textme_out_group');
    });

}

if( $('module_textme_compose_form') )
{
    $('module_textme_recipients').observe('change', function(event) {

        if( $F('module_textme_recipients') == 'select')
        {
            $('module_textme_usersrow').show();
            $('module_textme_groupsrow').show();
        }
        else
        {
            $('module_textme_usersrow').hide();
            $('module_textme_groupsrow').hide();
        }
    });

    $('module_textme_schedule').observe('change', function(event) {
        if( $F('module_textme_schedule') == 'later')
        {
            $('module_textme_daterow').show();
        }
        else
        {
            $('module_textme_daterow').hide();
        }
    });
}
