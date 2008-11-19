{include file = "includes/header.tpl"}

<script language = "JavaScript" type = "text/javascript">
{if (isset($T_REFRESH_SIDE))}
    if (top.sideframe)
        {if isset($T_PERSONAL_CTG)}
            top.sideframe.location = "new_sidebar.php?sbctg=personal";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}";
        {/if}
{/if}
{if (isset($T_RELOAD_ALL))}
    top.location = top.location;
{/if}

{* The following code checks whether the sideframe is Loaded, by checking the existence of an element defined at the end of the page   *}
{* If so, then the changeTDcolor function will be called from here, otherwise the sideframe will reload and the changeTDcolor function *}
{* will be called internally *}

{*///MODULES1*}

{literal}
if (top.sideframe && top.sideframe.document.getElementById('hasLoaded')) {
{/literal}
    {if !$T_POPUP_MODE && !$smarty.get.popup}
        {* Patch for solving reports problem: reports ~ ctg=module_hcd&op=reports but all ctg=module_hcd should link elsewhere *}
        if (top.sideframe)
        {if $T_MODULE_HCD_INTERFACE  && ($T_CTG == "module_hcd")}
            {if ($T_OP == "reports") && ($smarty.session.s_type == "administrator")}
                top.sideframe.changeTDcolor('search_employee');
            {elseif isset($T_OP) && $T_OP != ''}
                top.sideframe.changeTDcolor('{$T_OP}');
            {else}
                top.sideframe.changeTDcolor('hcd_control_panel');
            {/if}
        {elseif ($T_CTG == "search_courses")}
            top.sideframe.changeTDcolor('search_employee');
        {elseif ($T_CTG == "statistics") && isset($smarty.get.option)}
            top.sideframe.changeTDcolor('statistics_{$smarty.get.option}');
        {elseif ($T_CTG == "statistics") && !isset($smarty.get.option)}
            top.sideframe.changeTDcolor('control_panel');
        {elseif ($T_CTG == "personal") && isset($smarty.get.tab) && ($smarty.get.tab == "file_record")}
            top.sideframe.changeTDcolor('file_manager');
        {elseif ($T_CTG == "users") && (isset($smarty.get.edit_user)) && $smarty.get.edit_user == $smarty.session.s_login}
            top.sideframe.changeTDcolor('personal');
        {elseif $T_CTG == 'module'}
            top.sideframe.changeTDcolor('{$T_MODULE_HIGHLIGHT}');
        {else}
            top.sideframe.changeTDcolor('{$T_CTG}');
        {/if}
    {/if}
{literal}
} else {
{/literal}
    {if !$T_POPUP_MODE && !$smarty.get.popup}
        {* Patch for solving reports problem: reports ~ ctg=module_hcd&op=reports but all ctg=module_hcd should link elsewhere *}
        if (top.sideframe)
        {if $T_MODULE_HCD_INTERFACE  && ($T_CTG == "module_hcd")}
            {if ($T_OP == "reports") && ($smarty.session.s_type == "administrator")}
                top.sideframe.location = "new_sidebar.php?sbctg=search_employee";
            {elseif isset($T_OP) && $T_OP != ''}
                top.sideframe.location = "new_sidebar.php?sbctg={$T_OP}";
            {else}
                top.sideframe.location = "new_sidebar.php?sbctg=hcd_control_panel";
            {/if}
        {elseif ($T_CTG == "search_courses")}
            top.sideframe.location = "new_sidebar.php?sbctg=search_employee";
        {elseif ($T_CTG == "statistics") && isset($smarty.get.option)}
            top.sideframe.location = "new_sidebar.php?sbctg=statistics_{$smarty.get.option}";
        {elseif ($T_CTG == "statistics") && !isset($smarty.get.option)}
            top.sideframe.location = "new_sidebar.php?sbctg=control_panel";
        {elseif ($T_CTG == "personal") && isset($smarty.get.tab) && ($smarty.get.tab == "file_record")}
            top.sideframe.location = "new_sidebar.php?sbctg=file_manager";
        {elseif ($T_CTG == "users") && (isset($smarty.get.edit_user)) && $smarty.get.edit_user == $smarty.session.s_login}
            top.sideframe.location = "new_sidebar.php?sbctg=personal";
        {elseif $T_CTG == 'module'}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_MODULE_HIGHLIGHT}";
        {else}
            top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}";
        {/if}
    {/if}
{literal}
}
{/literal}


{if $T_MODULE_HCD_INTERFACE}
    var myform = "noform";
{/if}
</script>


{* -------------------------------End of Part 1: initialization etc----------------------------------------------- *}
{if !isset($smarty.get.print_preview) && !isset($smarty.get.print) && !isset($smarty.get.pdf)}
{assign var = "title" value = '<a class = "titleLink" title="'|cat:$smarty.const._HOME|cat:'" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel">'|cat:$smarty.const._HOME|cat:'</a>'}
{/if}

{*-------------------------------Part 2: Modules List ---------------------------------------------*}
{if (isset($T_CTG) && $T_CTG == 'control_panel')}

    {if $T_OP == 'file_manager'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=file_manager">'|cat:$smarty.const._FILEMANAGER|cat:'</a>'}
        {*moduleFileManager: The file manager page*}
            {capture name = "moduleFileManager"}
                    <tr><td class = "moduleCell">
                        {capture name = 't_file_manager_code'}
                            {$T_FILE_MANAGER}
                        {/capture}
                        {eF_template_printInnerTable title=$smarty.const._FILEMANAGER data=$smarty.capture.t_file_manager_code image='/32x32/folder_view.png'}
                    </td></tr>
            {/capture}

    {elseif $T_OP == 'search'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="javascript:void(0)" onclick = "location.reload()">'|cat:$smarty.const._SEARCHRESULTS|cat:'</a>'}
        {*moduleSearchResults: The Search results page*}
            {capture name = "moduleSearchResults"}
                    <tr><td class = "moduleCell">
                            {include file = "includes/module_search.tpl"}
                    </td></tr>
            {/capture}
{*****///MODULES4******}
    {elseif $T_OP == 'modules'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=modules">'|cat:$smarty.const._MODULES|cat:'</a>'}
        {*moduleModules: The modules administration page*}
        {capture name = "moduleModules"}
                    <tr><td class="moduleCell">
                        {capture name = 't_modules_code'}
                        {if !isset($T_CURRENT_USER->coreAccess.modules) || $T_CURRENT_USER->coreAccess.modules == 'change'}
                            <table>
                                <tr><td>
                                    <a href = "javascript:void(0)" onclick = "document.getElementById('upload_file_form').action = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=modules'; eF_js_showDivPopup('{$smarty.const._INSTALLMODULE}', new Array('300px', '100px'), 'upload_file_table')" title = "{$smarty.const._INSTALLMODULE}">
                                        <img src="images/16x16/add2.png" title="{$smarty.const._INSTALLMODULE}" alt="{$smarty.const._INSTALLMODULE}" border="0"/></a>
                                </td><td>
                                    <a href = "javascript:void(0)" onclick = "document.getElementById('upload_file_form').action = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=modules'; eF_js_showDivPopup('{$smarty.const._INSTALLMODULE}', new Array('300px', '100px'), 'upload_file_table')" title = "{$smarty.const._INSTALLMODULE}">
                                        {$smarty.const._INSTALLMODULE}</a>
                                </td></tr>
                            </table>
                        {/if}
                            <table style = "width:100%" class = "sortedTable">
                                <tr class = "defaultRowHeight">
                                    <td class = "topTitle">{$smarty.const._NAME}</td>
                                    <td class = "topTitle">{$smarty.const._TITLE}</td>
                                    <td class = "topTitle">{$smarty.const._AUTHOR}</td>
                                    <td class = "topTitle">{$smarty.const._VERSION}</td>
                                    <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                    <td class = "topTitle centerAlign">{$smarty.const._FUNCTIONS}</td>
                                </tr>
                            {section name = 'modules_list' loop = $T_MODULES}
                                <tr id="row_{$T_MODULES[modules_list].className}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$T_MODULES[modules_list].active}deactivatedTableElement{/if}">
                                    <td>{$T_MODULES[modules_list].className}</td>
                                    <td>{$T_MODULES[modules_list].title}</td>
                                    <td>{$T_MODULES[modules_list].author}</td>
                                    <td>{$T_MODULES[modules_list].version}</td>
                                    <td style = "text-align:center">

                                        {if !$T_MODULES[modules_list].errors}
                                            <a href = "javascript:void(0);" onclick = "activateModule(this, '{$T_MODULES[modules_list].className}')">
                                            {if $T_MODULES[modules_list].active}
                                                    <img id="module_status_img" src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                            {else}
                                                    <img id="module_status_img" src = "images/16x16/trafficlight_red.png"   alt = "{$smarty.const._ACTIVATE}"   title = "{$smarty.const._ACTIVATE}"   border = "0"></a>
                                            {/if}
                                            </a>
                                        {else}
                                                <img src = "images/16x16/error.png"   alt = "{$T_MODULES[modules_list].errors}"   title = "{$T_MODULES[modules_list].errors}"   border = "0">
                                        {/if}

                                    </td>
                                    <td style = "text-align:center">
                                        <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._MODULEINFORMATION}', new Array('400px', '300px'), 'module_info_table_{$smarty.section.modules_list.iteration}')" title = "{$smarty.const._DESCRIPTION}"><img src = "images/16x16/about.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" border = "0"/></a>

                                        {if !isset($T_CURRENT_USER->coreAccess.modules) || $T_CURRENT_USER->coreAccess.modules == 'change'}

                                    <a href = "javascript:void(0)" onclick = "document.getElementById('upload_file_form').action = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=modules&upgrade={$T_MODULES[modules_list].className}'; eF_js_showDivPopup('{$smarty.const._UPGRADEMODULE} {$T_MODULES[modules_list].className}', new Array('300px', '100px'), 'upload_file_table')" title = "{$smarty.const._UPGRADEMODULE}">
                                        <img src="images/16x16/box_out.png" title="{$smarty.const._UPGRADEMODULE}" alt="{$smarty.const._UPGRADEMODULE}" border="0"/></a>


{*<a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=modules&upgrade={$T_MODULES[modules_list].className}" title = "{$smarty.const._UPDATE}" alt = "{$smarty.const._UPDATE}"><img src = "images/16x16/box_out.png" alt = "{$smarty.const._UPDATE}" title = "{$smarty.const._UPDATE}" border = "0"/></a>*}
                                            <a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=modules&delete={$T_MODULES[modules_list].className}" title = "{$smarty.const._DELETE}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETE}" title = "{$smarty.const._DELETE}" border = "0"/></a>
                                        {/if}
                                        <div id = "module_info_table_{$smarty.section.modules_list.iteration}" style = "display:none">
                                            <table style = "text-align:left">
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._TITLE}:&nbsp;</td><td>{$T_MODULES[modules_list].title}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._AUTHOR}:&nbsp;</td><td>{$T_MODULES[modules_list].author}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._VERSION}:&nbsp;</td><td>{$T_MODULES[modules_list].version}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._DESCRIPTION}:&nbsp;</td><td>{$T_MODULES[modules_list].description}</td></tr>
                                                <tr style = "border-bottom:1px dotted gray"><td>{$smarty.const._VALIDFOR}:&nbsp;</td><td>{$T_MODULES[modules_list].permissions}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            {sectionelse}
                                <tr class = "defaultRowHeight emptyCategory"><td colspan = "100%" style = "text-align:center">{$smarty.const._NODATAFOUND}</td></tr>
                            {/section}
                            </table>
                            <div id = "upload_file_table" style = "display:none">
                                {$T_UPLOAD_FILE_FORM.javascript}
                                <form {$T_UPLOAD_FILE_FORM.attributes}>
                                    {$T_UPLOAD_FILE_FORM.hidden}
                                    <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                        <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                            <td class = "elementCell">{$T_UPLOAD_FILE_FORM.file_upload.0.html}</td></tr>
                                        <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$smarty.const.G_MAXFILESIZE/1024}</b> {$smarty.const._KB}</td></tr>
                                        {if $T_UPLOAD_FILE_FORM.file_upload.0.error}<tr><td></td><td class = "formError">{$T_UPLOAD_FILE_FORM.file_upload.0.error}</td></tr>{assign var = 'div_error' value = 'upload_file_table'}{/if}
                                        <tr><td></td><td >&nbsp;</td></tr>
                                        <tr><td></td><td class = "submitCell">{$T_UPLOAD_FILE_FORM.submit_upload_file.html}</td></tr>
                                    </table>
                                </form>
                            </div>
        <script>
        {literal}
        function activateModule(el, module) {
            Element.extend(el);
            if (el.down().src.match('red')) {
                url = '{/literal}{$smarty.session.s_type}{literal}.php?ctg=control_panel&op=modules&activate='+module;
                newSource = 'images/16x16/trafficlight_green.png';
            } else {
                url = '{/literal}{$smarty.session.s_type}{literal}.php?ctg=control_panel&op=modules&deactivate='+module;
                newSource = 'images/16x16/trafficlight_red.png';
            }

            var img = new Element('img', {id: 'img_'+module, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
            el.getOffsetParent().insert(img);
            el.down().src = 'images/16x16/trafficlight_yellow.png';
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onSuccess: function (transport) {
                    img.setStyle({display:'none'});
                    el.down().src = newSource;
                    new Effect.Appear(el.down(), {queue:'end'});

                    if (el.down().src.match('green')) {
                        // When activated
                        var cName = $('row_'+module).className.split(" ");
                        $('row_'+module).className = cName[0];
                        $('module_status_img').alt = "{/literal}{$smarty.const._DEACTIVATE}{literal}";
                        $('module_status_img').title = "{/literal}{$smarty.const._DEACTIVATE}{literal}";
                    } else {
                        $('row_'+module).className += " deactivatedTableElement";
                        $('module_status_img').alt = "{/literal}{$smarty.const._ACTIVATE}{literal}";
                        $('module_status_img').title = "{/literal}{$smarty.const._ACTIVATE}{literal}";

                    }

                    {/literal}{if isset($T_PERSONAL_CTG)}{literal}
                        top.sideframe.location = "new_sidebar.php?sbctg=personal";
                    {/literal}{else}{literal}
                        top.sideframe.location = "new_sidebar.php?sbctg={$T_CTG}";
                    {/literal}{/if}{literal}
                    }
                });
        }
        {/literal}
        </script>
                        {/capture}

                        {eF_template_printInnerTable title=$smarty.const._MODULES data=$smarty.capture.t_modules_code image='/32x32/components.png'}
                    </td></tr>
        {/capture}
    {elseif $T_OP == 'paypal'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=paypal">'|cat:$smarty.const._PAYPALTITLE|cat:'</a>'}
        {*modulePaypal: The configuration settings of paypal*}
    {capture name = 't_paypal_data'}
    <div class = "tabber">
        <div class = "tabbertab">
            <h3>{$smarty.const._PAYPALORDERSSUCCESS}</h3>
            <table style = "width:100%" class = "sortedTable">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEUSER}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLELESSONS}</td>
                    <td class = "topTitle" align="center">{$smarty.const._PAYPALTABLEPRICE}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATESUBMIT}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATEPAYPAL}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLESTATUS}</td>
                    <td class = "topTitle">{$smarty.const._FUNCTIONS}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'orders' from = $T_PAYPALDATA_S}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{$orders.user}</td>
                    <td>{$orders.item_name}</td>
                    <td align="center">{$orders.mc_gross} {$T_CURRENCYSYMBOLS[$orders.mc_currency]}</td>
                    <td>#filter:timestamp_time-{$orders.timestamp}#</td>
                    <td>#filter:timestamp_time-{$orders.timestamp_finish}#</td>
                    <td>{$orders.payment_status}</td>
                    <td>
                        <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALORDERINFO}', new Array('400px', '300px'),
                            'payment_view_{$orders.id}')" title = "{$smarty.const._DESCRIPTION}">
                            <img src = "images/16x16/about.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" border = "0"/>
                        </a>
                        <div id = "payment_view_{$orders.id}" style = "display:none;">
                        <table style = "width:100%">
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>id:</b></td><td align="left">{$orders.id}&nbsp;</td><td align="left"><b>mc_gross:</b></td><td align="left">{$orders.mc_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>settle_amount:</b></td><td align="left">{$orders.settle_amount}&nbsp;</td><td align="left"><b>address_status:</b></td><td align="left">{$orders.address_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>payer_id:</b></td><td align="left">{$orders.payer_id}&nbsp;</td><td align="left"><b>tax:</b></td><td align="left">{$orders.tax}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_street:</b></td><td align="left">{$orders.address_street}&nbsp;</td><td align="left"><b>payment_date:</b></td><td align="left">{$orders.payment_date}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>payment_status:</b></td><td align="left">{$orders.payment_status}&nbsp;</td><td align="left"><b>charset:</b></td><td align="left">{$orders.charset}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_zip:</b></td><td align="left">{$orders.address_zip}&nbsp;</td><td align="left"><b>first_name:</b></td><td align="left">{$orders.first_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>mc_fee:</b></td><td align="left">{$orders.mc_fee}&nbsp;</td><td align="left"><b>address_country_code:</b></td><td align="left">{$orders.address_country_code}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>exchange_rate:</b></td><td align="left">{$orders.exchange_rate}&nbsp;</td><td align="left"><b>address_name:</b></td><td align="left">{$orders.address_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>notify_version:</b></td><td align="left">{$orders.notify_version}&nbsp;</td><td align="left"><b>settle_currency:</b></td><td align="left">{$orders.settle_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>custom:</b></td><td align="left">{$orders.custom}</td><td align="left"<b>payer_status:</b></td><td align="left">{$orders.payer_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>business:</b></td><td align="left">{$orders.business}&nbsp;</td><td align="left"><b>address_country:</b></td><td align="left">{$orders.address_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_city:</b></td><td align="left">{$orders.address_city}&nbsp;</td><td align="left"><b>quantity:</b></td><td align="left">{$orders.quantity}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>verify_sign:</b></td><td align="left">{$orders.verify_sign}&nbsp;</td><td align="left"><b>payer_email:</b></td><td align="left">{$orders.payer_email}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>txn_id:</b></td><td align="left">{$orders.txn_id}&nbsp;</td><td align="left"><b>payment_type:</b></td><td align="left">{$orders.payment_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>last_name:</b></td><td align="left">{$orders.last_name}&nbsp;</td><td align="left"><b>address_state:</b></td><td align="left">{$orders.address_state}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>receiver_email:</b></td><td align="left">{$orders.receiver_email}&nbsp;</td><td align="left"><b>payment_fee:</b></td><td align="left">{$orders.payment_fee}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>receiver_id:</b></td><td align="left">{$orders.receiver_id}&nbsp;</td><td align="left"><b>txn_type:</b></td><td align="left">{$orders.txn_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>item_name:</b></td><td align="left">{$orders.item_name}&nbsp;</td><td align="left"><b>mc_currency:</b></td><td align="left">{$orders.mc_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>item_number:</b></td><td align="left">{$orders.item_number}&nbsp;</td><td align="left"><b>residence_country:</b></td><td align="left">{$orders.residence_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>test_ipn:</b></td><td align="left">{$orders.test_ipn}&nbsp;</td><td align="left"><b>payment_gross:</b></td><td align="left">{$orders.payment_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>shipping:</b></td><td align="left">{$orders.shipping}&nbsp;</td><td align="left"><b>status:</b></td><td align="left">{$orders.status}</td></tr>
                        </table>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
            </table>
        </div>
        <div class="tabbertab">
            <h3>{$smarty.const._PAYPALORDERSNOSUCCESS}</h3>
            <table style = "width:100%" class = "sortedTable">
                <tr class = "topTitle defaultRowHeight">
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEUSER}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLELESSONS}</td>
                    <td class = "topTitle" align="center">{$smarty.const._PAYPALTABLEPRICE}</td>
                    <td class = "topTitle">{$smarty.const._PAYPALTABLEDATESUBMIT}</td>
                    <td class = "topTitle">{$smarty.const._STATUS}</td>
                    <td class = "topTitle">{$smarty.const._FUNCTIONS}</td>
                </tr>
                {foreach name = 'users_list' key = 'key' item = 'orders' from = $T_PAYPALDATA_NS}
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td>{$orders.user}</td>
                    <td>{$orders.item_name}</td>
                    <td align="center">{$orders.mc_gross} {$T_CURRENCYSYMBOLS[$orders.mc_currency]}</td>
                    <td>#filter:timestamp_time-{$orders.timestamp}#</td>
                    <td>{$orders.status}</td>
                    <td>
                        <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALORDERINFO}', new Array('400px', '300px'),
                            'payment_view_{$orders.id}')" title = "{$smarty.const._DESCRIPTION}">
                            <img src = "images/16x16/about.png" alt = "{$smarty.const._DESCRIPTION}" title = "{$smarty.const._DESCRIPTION}" border = "0"/>
                        </a>
                        <div id = "payment_view_{$orders.id}" style = "display:none;">
                        <table style = "width:100%">
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>id:</b></td><td align="left">{$orders.id}&nbsp;</td><td align="left"><b>mc_gross:</b></td><td align="left">{$orders.mc_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>settle_amount:</b></td><td align="left">{$orders.settle_amount}&nbsp;</td><td align="left"><b>address_status:</b></td><td align="left">{$orders.address_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>payer_id:</b></td><td align="left">{$orders.payer_id}&nbsp;</td><td align="left"><b>tax:</b></td><td align="left">{$orders.tax}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_street:</b></td><td align="left">{$orders.address_street}&nbsp;</td><td align="left"><b>payment_date:</b></td><td align="left">{$orders.payment_date}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>payment_status:</b></td><td align="left">{$orders.payment_status}&nbsp;</td><td align="left"><b>charset:</b></td><td align="left">{$orders.charset}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_zip:</b></td><td align="left">{$orders.address_zip}&nbsp;</td><td align="left"><b>first_name:</b></td><td align="left">{$orders.first_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>mc_fee:</b></td><td align="left">{$orders.mc_fee}&nbsp;</td><td align="left"><b>address_country_code:</b></td><td align="left">{$orders.address_country_code}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>exchange_rate:</b></td><td align="left">{$orders.exchange_rate}&nbsp;</td><td align="left"><b>address_name:</b></td><td align="left">{$orders.address_name}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>notify_version:</b></td><td align="left">{$orders.notify_version}&nbsp;</td><td align="left"><b>settle_currency:</b></td><td align="left">{$orders.settle_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>custom:</b></td><td align="left">{$orders.custom}</td><td align="left"<b>payer_status:</b></td><td align="left">{$orders.payer_status}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>business:</b></td><td align="left">{$orders.business}&nbsp;</td><td align="left"><b>address_country:</b></td><td align="left">{$orders.address_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>address_city:</b></td><td align="left">{$orders.address_city}&nbsp;</td><td align="left"><b>quantity:</b></td><td align="left">{$orders.quantity}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>verify_sign:</b></td><td align="left">{$orders.verify_sign}&nbsp;</td><td align="left"><b>payer_email:</b></td><td align="left">{$orders.payer_email}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>txn_id:</b></td><td align="left">{$orders.txn_id}&nbsp;</td><td align="left"><b>payment_type:</b></td><td align="left">{$orders.payment_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>last_name:</b></td><td align="left">{$orders.last_name}&nbsp;</td><td align="left"><b>address_state:</b></td><td align="left">{$orders.address_state}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>receiver_email:</b></td><td align="left">{$orders.receiver_email}&nbsp;</td><td align="left"><b>payment_fee:</b></td><td align="left">{$orders.payment_fee}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>receiver_id:</b></td><td align="left">{$orders.receiver_id}&nbsp;</td><td align="left"><b>txn_type:</b></td><td align="left">{$orders.txn_type}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>item_name:</b></td><td align="left">{$orders.item_name}&nbsp;</td><td align="left"><b>mc_currency:</b></td><td align="left">{$orders.mc_currency}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>item_number:</b></td><td align="left">{$orders.item_number}&nbsp;</td><td align="left"><b>residence_country:</b></td><td align="left">{$orders.residence_country}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>test_ipn:</b></td><td align="left">{$orders.test_ipn}&nbsp;</td><td align="left"><b>payment_gross:</b></td><td align="left">{$orders.payment_gross}</td></tr>
                            <tr class = "{cycle values = "oddRowColor, evenRowColor"}"><td align="left"><b>shipping:</b></td><td align="left">{$orders.shipping}&nbsp;</td><td align="left"><b>status:</b></td><td align="left">{$orders.status}</td></tr>
                        </table>
                        </div>
                    </td>
                </tr>
                {foreachelse}
                <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}
            </table>
        </div>
    </div>
    {/capture}

        {capture name = "modulePaypal"}
                    <tr><td class="moduleCell">
                            {capture name="view_config"}
                <div>&nbsp;<img src = "images/16x16/edit.png" title = "{$smarty.const._PAYPALCONFIGURATIONPANEL}" alt = "{$smarty.const._PAYPALCONFIGURATIONPANEL}"/ > <a href = "paypal_configuration.php" onclick = "eF_js_showDivPopup('{$smarty.const._PAYPALCONFIGURATIONPANEL}', new Array('600px', '400px'))" target = "POPUP_FRAME">{$smarty.const._PAYPALCONFIGURATIONPANEL}</a>&nbsp;</div>
                        {$smarty.capture.t_paypal_data}
                {/capture}
                {eF_template_printInnerTable title = $smarty.const._PAYPALTITLE data = $smarty.capture.view_config image='/32x32/paypal.png'}
               </td></tr>

    {/capture}
    {elseif $T_OP == 'maintenance'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=maintenance">'|cat:$smarty.const._MAINTENANCE|cat:'</a>'}
        {*moduleCleanup: Clean up old data*}
        {capture name = "moduleCleanup"}
                    <tr><td class = "moduleCell">
                            <div class = "tabber">
                                <div class = "tabbertab">
                                    <h3>{$smarty.const._ENVIRONMENTALCHECK}</h3>
                                    {include file = 'check_status.tpl'}
                                </div>
{*do not touch please :)
                                <div class = "tabbertab {if $smarty.get.tab=='lock_down'}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._LOCKDOWN}</h3>
                                   {$T_LOCKDOWN_FORM.javascript}
                                    <form {$T_LOCKDOWN_FORM.attributes}>
                                        {$T_LOCKDOWN_FORM.hidden}
                                        <table class = "formElements">
                                            <tr><td class = "labelCell">{$smarty.const._LOCKDOWNFROM}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.from.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._LOCKDOWNTO}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.to.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._DISPLAYINDEXMESSAGE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.display_message.html}</td>
                                            <tr><td class = "labelCell">{$smarty.const._LOGOUTUSERS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.logout_users.html}</td>
                                            <tr><td class = "labelCell">{$smarty.const._SETANNOUNCEMENT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.set_announcement.html}</td>
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td class = "labelCell"></td>
                                                <td class = "elementCell">{$T_LOCKDOWN_FORM.submit_lockdown.html}</td></tr>
                                       </table>
                                    </form>

                                </div>
*}
                            {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                <div class = "tabbertab {if $smarty.get.tab=='cleanup'}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._CLEANUP}</h3>
                                    {capture name = 't_cleanup_code'}
                                        <table>
                                            <tr><td>{$smarty.const._ORPHANUSERFOLDERSCHECK}:&nbsp;</td>
                                                <td>
                                                {if $T_ORPHAN_USER_FOLDERS}
                                                    <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                                                    <img src = "images/16x16/help2.png"   title = "{$smarty.const._INFO}"    alt = "{$smarty.const._INFO}"    onclick = "eF_js_showDivPopup('{$smarty.const._FOLDERSWITHOUTAUSERASSOCIATED}', new Array('300px', '100px'), 'orphan_user_folders')"/>&nbsp;
                                                    <img src = "images/16x16/brush3.png"  title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:\n\n{$T_ORPHAN_USER_FOLDERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&cleanup=orphan_user_folders'"/>
                                                {else}
                                                    <img src = "images/16x16/check.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
                                                {/if}
                                                </td></tr>
                                            <tr><td>{$smarty.const._USERSWITHOUTFOLDERSCHECK}:&nbsp;</td>
                                                <td>
                                                {if $T_ORPHAN_USERS}
                                                    <img src = "images/16x16/warning.png"    title = "{$smarty.const._PROBLEM}"      alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                                                    <img src = "images/16x16/help2.png"      title = "{$smarty.const._INFO}"         alt = "{$smarty.const._INFO}"         onclick = "eF_js_showDivPopup('{$smarty.const._USERSWITHOUTAFOLDER}', new Array('300px', '100px'), 'users_without_folders')"/>&nbsp;
                                                    <img src = "images/16x16/brush3.png"     title = "{$smarty.const._CLEANUP}"      alt = "{$smarty.const._CLEANUP}"      onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGUSERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&cleanup=users_without_folders'"/>&nbsp;
                                                    <img src = "images/16x16/folder_new.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGUSERFOLDERS}:\n\n{$T_ORPHAN_USERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&create=user_folders'"/>
                                                {else}
                                                    <img src = "images/16x16/check.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
                                                {/if}
                                                </td></tr>
                                            <tr><td>{$smarty.const._ORPHANLESSONFOLDERSCHECK}:&nbsp;</td>
                                                <td>
                                                {if $T_ORPHAN_LESSON_FOLDERS}
                                                    <img src = "images/16x16/warning.png" title = "{$smarty.const._PROBLEM}" alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                                                    <img src = "images/16x16/help2.png"   title = "{$smarty.const._INFO}"    alt = "{$smarty.const._INFO}"    onclick = "eF_js_showDivPopup('{$smarty.const._FOLDERSWITHOUTALESSONASSOCIATED}', new Array('300px', '100px'), 'orphan_lesson_folders')"/>&nbsp;
                                                    <img src = "images/16x16/brush3.png"  title = "{$smarty.const._CLEANUP}" alt = "{$smarty.const._CLEANUP}" onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGFOLDERS}:\n\n{$T_ORPHAN_LESSON_FOLDERS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&cleanup=orphan_lesson_folders'"/>
                                                {else}
                                                    <img src = "images/16x16/check.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
                                                {/if}
                                            </td></tr>
                                            <tr><td>{$smarty.const._LESSONSWITHOUTFOLDERSCHECK}:&nbsp;</td>
                                                <td>
                                                {if $T_ORPHAN_LESSONS}
                                                    <img src = "images/16x16/warning.png"    title = "{$smarty.const._PROBLEM}"      alt = "{$smarty.const._PROBLEM}"/>&nbsp;
                                                    <img src = "images/16x16/help2.png"      title = "{$smarty.const._INFO}"         alt = "{$smarty.const._INFO}"         onclick = "eF_js_showDivPopup('{$smarty.const._LESSONSWITHOUTAFOLDER}', new Array('300px', '100px'), 'lessons_without_folders')"/>&nbsp;
                                                    <img src = "images/16x16/brush3.png"     title = "{$smarty.const._CLEANUP}"      alt = "{$smarty.const._CLEANUP}"      onclick = "if (confirm('{$smarty.const._PEMANENTLYDELETEFOLLOWINGLESSONS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&cleanup=lessons_without_folders'"/>&nbsp;
                                                    <img src = "images/16x16/folder_new.png" title = "{$smarty.const._CREATEFOLDER}" alt = "{$smarty.const._CREATEFOLDER}" onclick = "if (confirm('{$smarty.const._CREATEFOLLOWINGLESSONFOLDERS}:\n\n{$T_ORPHAN_LESSONS}\n\n{$smarty.const._AREYOUSURE}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&create=lesson_folders'"/>
                                                {else}
                                                    <img src = "images/16x16/check.png" title = "{$smarty.const._OK}" alt = "{$smarty.const._OK}"/>
                                                {/if}
                                            </td></tr>
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td><input class = "flatButton" type = "button" value = "{$smarty.const._CHECKAGAIN}" onclick = "location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup'">&nbsp;<input class = "flatButton" type = "button" value = "{$smarty.const._CLEANUPALL}" onclick = "if (confirm('{$smarty.const._THISOPERATIONALTERSYSTEM}')) location = '{$smarty.server.PHP_SELF}?ctg=control_panel&op=maintenance&tab=cleanup&cleanup=all'"></td></tr>
                                        </table>
                                        <div id = "orphan_user_folders" style = "display:none;">
                                            {$T_ORPHAN_USER_FOLDERS}
                                        </div>
                                        <div id = "users_without_folders" style = "display:none;">
                                            {$T_ORPHAN_USERS}
                                        </div>
                                        <div id = "orphan_lesson_folders" style = "display:none;">
                                            {$T_ORPHAN_LESSON_FOLDERS}
                                        </div>
                                        <div id = "lessons_without_folders" style = "display:none;">
                                            {$T_ORPHAN_LESSONS}
                                        </div>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._CLEANUP data=$smarty.capture.t_cleanup_code image='/32x32/brush3.png'}
                                </div>
                                <div class = "tabbertab {if $smarty.get.tab=='reindex'}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._RECREATESEARCHTABLE}</h3>
                                    {capture name = 't_reindex_code'}
                                    <table>
                                        <tr><td class = "labelCell">{$smarty.const._CLICKHERETOREINDEX}:&nbsp;</td>
                                            <td><input type = "button" value = "{$smarty.const._RECREATE}" onclick = "reIndex(this)"/> <img src = "images/others/progress1.gif" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}" style = "vertical-align:middle;display:none"/></td></tr>
                                    </table>
                                    <script>
                                    {literal}
                                    function reIndex(el) {
                                        Element.extend(el);
                                        img = el.next();
                                        img.src = "images/others/progress1.gif";
                                        img.show();
                                        url = 'administrator.php?ctg=control_panel&op=maintenance&ajax=1&reindex=1';
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onFailure: function (transport) {
                                                img.writeAttribute({src:'images/16x16/delete2.png', title: transport.responseText}).hide();
                                                new Effect.Appear(img);
                                                window.setTimeout('Effect.Fade("'+img.identify()+'");', 10000);
                                            },
                                            onSuccess: function (transport) {
                                                img.writeAttribute({src:'images/16x16/check.png', title: '{/literal}{$smarty.const._SEARCHTABLERECREATED}{literal}'}).hide();
                                                new Effect.Appear(img);
                                                }
                                            });

                                    }
                                    {/literal}
                                    </script>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._RECREATESEARCHTABLE data=$smarty.capture.t_reindex_code image='/32x32/exchange.png'}
                                </div>
                            {/if}
                            </div>
                    </td></tr>
        {/capture}
    {elseif $T_OP == 'system_config'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=system_config">'|cat:$smarty.const._CONFIGURATIONVARIABLES|cat:'</a>'}
        {*moduleConfig: The configuration settings page*}
        {capture name = "moduleConfig"}
                    <tr><td class="moduleCell">
                            {capture name="view_config"}
                            <div class="tabber">
                                <div class="tabbertab {if ($smarty.get.tab == 'vars')}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._CONFIGURATIONVARIABLES}</h3>
                                    {capture name="system_vars"}
                                    {$T_SYSTEM_VARIABLES_FORM.javascript}
                                    <form {$T_SYSTEM_VARIABLES_FORM.attributes}>
                                        {$T_SYSTEM_VARIABLES_FORM.hidden}
                                        <table style = "width:100%">
                                            <tr><td class = "labelCell">{$smarty.const._ADMINEMAIL}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.system_email.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.system_email.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.system_email.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._MAXFILESIZE} ({$smarty.const._KB}):&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.max_file_size.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.max_file_size.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.max_file_size.error}</td></tr>{/if}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._MAXFILEISAFFECTEDANDIS}: {$T_MAX_FILE_SIZE} {$smarty.const._KB}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._ALLOWEDIPS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.ip_white_list.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.ip_white_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.ip_white_list.error}</td></tr>{/if}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._COMMASEPARATEDLISTASTERISKEXAMPLE}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._DISALLOWEDIPS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.ip_black_list.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.ip_black_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.ip_black_list.error}</td></tr>{/if}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._CAREFULNOTLOCKOUT}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._ALLOWEDEXTENSIONS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_white_list.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.file_white_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.file_white_list.error}</td></tr>{/if}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._DISALLOWEDEXTENSIONS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_black_list.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.file_black_list.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.file_black_list.error}</td></tr>{/if}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._DEFAULTLANGUAGE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.default_language.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.default_language.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.default_language.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SIDEBARWIDTH}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.sidebar_width.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.sidebar_width.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.sidebar_width.error}</td></tr>{/if}
                                                
                                           <tr><td class = "labelCell">{$smarty.const._EXTERNALLYSIGNUP}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.signup.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._AUTOMATICUSERACTIVATION}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.activation.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._MAILUSERACTIVATION}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.mail_activation.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._ONLYONELANGUAGE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.onelanguage.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._SHOWFOOTER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.show_footer.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._ENABLEDAPI}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.api.html}</td></tr>
											<tr><td class = "labelCell">{$smarty.const._ENABLEMATHCONTENT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.math_content.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._TRANSLATEFILESYSTEM}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_encoding.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._VIEWDIRECTORY}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.lessons_directory.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._SITENAME}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.site_name.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.site_name.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.site_name.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SITEMOTO}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.site_moto.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.site_moto.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.site_moto.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LOGOUTREDIRECT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.logout_redirect.html}</td></tr>
                                            {if $T_SYSTEM_VARIABLES_FORM.logout_redirect.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.logout_redirect.error}</td></tr>{/if}
                                            {if isset($T_SYSTEM_VARIABLES_FORM.paypal)}
                                            <tr><td class = "labelCell">{$smarty.const._PAYPALUSE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.paypal.html}</td></tr>
                                            {/if}
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td class = "submitCell">{$T_SYSTEM_VARIABLES_FORM.submit_system_variables.html}</td></tr>
                                            <tr><td colspan = "2"><b>{$smarty.const._NOTE}: </b>{$smarty.const._DENIALTAKESPRECEDENCE}</td></tr>
                                        </table>
                                    </form>
                                    {/capture}
                                    {eF_template_printInnerTable title=$smarty.const._CONFIGURATIONVARIABLES data=$smarty.capture.system_vars image='32x32/edit.png'}
                                </div>

                        {if $T_EXTENSION_MISSING && $T_EXTENSION_MISSING == 'ldap'}
                                <div class="tabbertab {if ($smarty.get.tab == 'ldap')}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._LDAPCONFIGURATION}</h3>
                                    {capture name = "ldap_vars"}
                                        <table style = "width:100%">
                                            <tr class = "defaultRowHeight"><td class = "emptyCategory centerAlign">{$smarty.const._PHPLDAPEXTENSIONISNOTLOADED}</td></tr>
                                        </table>
                                    {/capture}
                                    {eF_template_printInnerTable title=$smarty.const._LDAPVARIABLES data=$smarty.capture.ldap_vars image='32x32/address_book.png'}
                                </div>
                        {else}
                                <div class="tabbertab {if ($smarty.get.tab == 'ldap')}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._LDAPCONFIGURATION}</h3>
                                    {capture name = "ldap_vars"}
                                    {$T_LDAP_VARIABLES_FORM.javascript}
                                    <form {$T_LDAP_VARIABLES_FORM.attributes}>
                                        {$T_LDAP_VARIABLES_FORM.hidden}
                                        <table class = "formElements">
                                            <tr><td class = "labelCell">{$smarty.const._ACTIVATELDAP}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.activate_ldap.html}</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._SUPPORTONLYLDAP}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.only_ldap.html}</td></tr>
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                            {*                <tr><td class = "labelCell">{$smarty.const._USESSL}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_ssl.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_ssl.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_ssl.error}</td></tr>{/if}
                            *}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPSERVER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_server.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_server.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_server.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPPORT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_port.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_port.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_port.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPBINDDN}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_binddn.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_binddn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_binddn.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPPASSWORD}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_password.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_password.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_password.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPBASEDN}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_basedn.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_basedn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_basedn.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPPROTOCOLVERSION}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_protocol.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_protocol.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_protocol.error}</td></tr>{/if}
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td class = "labelCell">{$smarty.const._LOGINATTRIBUTE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_uid.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_uid.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_uid.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPCOMMONNAME}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_cn.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_cn.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_cn.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPADDRESS}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_postaladdress.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_postaladdress.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_postaladdress.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPLOCALITY}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_l.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_l.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_l.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPTELEPHONENUMBER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_telephonenumber.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_telephonenumber.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_telephonenumber.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPMAIL}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_mail.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_mail.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_mail.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LDAPLANGUAGE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.html}</td></tr>
                                            {if $T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.error}<tr><td></td><td class = "formError">{$T_LDAP_VARIABLES_FORM.ldap_preferredlanguage.error}</td></tr>{/if}
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td class = "submitCell">{$T_LDAP_VARIABLES_FORM.check_ldap.html}&nbsp;{$T_LDAP_VARIABLES_FORM.submit_ldap_variables.html}</td></tr>
                                        </table>
                                    </form>
                                    {/capture}
                                    {eF_template_printInnerTable title=$smarty.const._LDAPVARIABLES data=$smarty.capture.ldap_vars image='32x32/address_book.png'}
                                </div>
                        {/if}

                                <div class="tabbertab {if ($smarty.get.tab == 'smtp')}tabbertabdefault{/if}">
                                    {if ($smarty.get.email_conf == '1')}
                                        {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONARECORRECT type='success'}
                                    {elseif ($smarty.get.email_conf == '-1')}
                                        {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONERROR type='failure'}
                                    {else}
                                    {/if}
                                    <h3>{$smarty.const._EMAILCONFIGURATIONS}</h3>
                                    {capture name = "ldap_vars"}
                                    {$T_SMTP_VARIABLES_FORM.javascript}
                                    <form {$T_SMTP_VARIABLES_FORM.attributes}>
                                        {$T_SMTP_VARIABLES_FORM.hidden}
                                        <table class = "formElements">
                                            <tr><td class = "labelCell">{$smarty.const._SMTPSERVER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_host.html}</td></tr>
                                            <tr><td></td><td class = "infoCell">{$smarty.const._IFUSESSLTHENPHPOPENSSL}</td></tr>
                                            {if $T_SMTP_VARIABLES_FORM.smtp_host.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_host.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SMTPPORT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_port.html}</td></tr>
                                            {if $T_SMTP_VARIABLES_FORM.smtp_port.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_port.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SMTPUSER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_user.html}</td></tr>
                                            {if $T_SMTP_VARIABLES_FORM.smtp_user.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_user.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SMTPPASSWORD}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_pass.html}</td></tr>
                                            {if $T_SMTP_VARIABLES_FORM.smtp_pass.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_pass.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._SMTPTIMEOUT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_timeout.html}</td></tr>
                                            {if $T_SMTP_VARIABLES_FORM.smtp_timeout.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_timeout.error}</td></tr>{/if}
                            {*                <tr><td class = "labelCell">{$smarty.const._USESSL}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_ssl.html}</td></tr>*}
                                            <tr><td class = "labelCell">{$smarty.const._SMTPAUTH}:&nbsp;</td>
                                                <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_auth.html}</td></tr>
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td class = "submitCell">{$T_SMTP_VARIABLES_FORM.check_smtp.html}&nbsp;{$T_SMTP_VARIABLES_FORM.submit_smtp_variables.html}</td></tr>

                                        </table>
                                    </form>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._SMTPSERVERCONFIGURATIONS data=$smarty.capture.ldap_vars image='/32x32/mail2.png'}
                                </div>

                                <div class="tabbertab {if ($smarty.get.tab == 'locale')}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._LOCALESETTINGS}</h3>
                                    {capture name = "locale_vars"}
                                    {$T_LOCALE_VARIABLES_FORM.javascript}
                                    <form {$T_LOCALE_VARIABLES_FORM.attributes}>
                                        {$T_LOCALE_VARIABLES_FORM.hidden}
                                        <table class = "formElements">
                                            <tr><td class = "labelCell">{$smarty.const._TIMEZONE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.time_zone.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.time_zone.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.time_zone.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._LOCATION}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.location.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.location.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.location.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._CURRENCY}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.currency.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.currency.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.currency.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._DECIMALPOINT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.decimal_point.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.decimal_point.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.decimal_point.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._THOUSANDSSEPARATOR}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.thousands_sep.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.thousands_sep.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.thousands_sep.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._DATEFORMAT}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.date_format.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.date_format.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.date_format.error}</td></tr>{/if}
                                            {*<tr><td class = "labelCell">{$smarty.const._SPECIFICLOCALE}:&nbsp;</td>
                                                <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.set_locale.html}</td></tr>
                                            {if $T_LOCALE_VARIABLES_FORM.set_locale.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.set_locale.error}</td></tr>{/if}*}
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td class = "submitCell">{$T_LOCALE_VARIABLES_FORM.submit_locale.html}</td></tr>

                                        </table>
                                    </form>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._LOCALESETTINGS data=$smarty.capture.locale_vars image='/32x32/earth.png'}
                                </div>

                                <div class="tabbertab {if ($smarty.get.tab == 'php')}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._PHPSETTINGS}</h3>
                                    {capture name = "php_vars"}
                                    {$T_PHP_VARIABLES_FORM.javascript}
                                    <form {$T_PHP_VARIABLES_FORM.attributes}>
                                        {$T_PHP_VARIABLES_FORM.hidden}
                                        <table class = "formElements">
                                            <tr><td class = "labelCell">{$smarty.const._MEMORYLIMIT} (memory_limit):&nbsp;</td>
                                                <td class = "elementCell">{$T_PHP_VARIABLES_FORM.memory_limit.html} {$smarty.const._MEGABYTES}</td></tr>
                                            {if $T_PHP_VARIABLES_FORM.memory_limit.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.memory_limit.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._MAXEXECUTIONTIME} (max_execution_time):&nbsp;</td>
                                                <td class = "elementCell">{$T_PHP_VARIABLES_FORM.max_execution_time.html} {$smarty.const._SECONDS}</td></tr>
                                            {if $T_PHP_VARIABLES_FORM.max_execution_time.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.max_execution_time.error}</td></tr>{/if}
                                            <tr><td class = "labelCell">{$smarty.const._GZHANDLER}:&nbsp;</td>
                                                <td class = "elementCell">{$T_PHP_VARIABLES_FORM.gz_handler.html}</td></tr>
                                            {if $T_PHP_VARIABLES_FORM.gz_handler.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.gz_handler.error}</td></tr>{/if}
{*                                            <tr><td class = "labelCell">{$smarty.const._DISPLAYERRORS} (display_errors):&nbsp;</td>
                                                <td class = "elementCell">{$T_PHP_VARIABLES_FORM.display_errors.html}</td></tr>
                                            {if $T_PHP_VARIABLES_FORM.display_errors.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.display_errors.error}</td></tr>{/if}
*}
                                            <tr><td></td><td class = "infoCell">{$smarty.const._LEAVEBLANKTOUSEPHPINI}</td></tr>
                                            <tr><td colspan = "2">&nbsp;</td></tr>
                                            <tr><td></td><td class = "submitCell">{$T_PHP_VARIABLES_FORM.submit_php.html}</td></tr>

                                        </table>
                                    </form>
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._PHPSETTINGS data=$smarty.capture.php_vars image='/32x32/mail2.png'}
                                </div>
                                <div class = "tabbertab {if ($smarty.get.tab == 'layout')}tabbertabdefault{/if}" title = "{$smarty.const._LAYOUT}">
                                	{include file = "includes/layout.tpl"}                                
                                </div>
                            </div>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._CONFIGURATIONVARIABLES data = $smarty.capture.view_config image='/32x32/pencil.png'}

							
                    </td></tr>
        {/capture}
    {elseif $T_OP == 'users'}
        {assign var="title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class= "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=users" >'|cat:$smarty.const._USERSDATA|cat:'</a>'}
    {*moduleImportUsers: The page to import user data*}
    {capture name = "moduleImportExportUsers"}
        <tr><td class="moduleCell">
                {capture name = "t_import_export_users_code"}
                <div class = "tabber">
                {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                    <div class = "tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'import')} tabbertabdefault{/if}">
                        <h3>{$smarty.const._USERSIMPORT}</h3>
                         {$T_IMPORT_USERS_FORM.javascript}
                        <form {$T_IMPORT_USERS_FORM.attributes}>
                        {$T_IMPORT_USERS_FORM.hidden}
                        <table align="center">
                            <tr><td class="labelCell">{$smarty.const._DATAFILE}:</td><td>{$T_IMPORT_USERS_FORM.users_file.html}</td></tr>
                            <tr><td class="labelCell">{$smarty.const._KEEPEXISTINGUSERS}:</td><td>{$T_IMPORT_USERS_FORM.replace_users.keep.html}</td></tr>
                            <tr><td class="labelCell">{$smarty.const._REPLACEEXISTINGUSERS}:</td><td>{$T_IMPORT_USERS_FORM.replace_users.replace.html}</td></tr>
                            <tr><td class="labelCell">{$smarty.const._SENDINFOVIAEMAIL}:</td><td>{$T_IMPORT_USERS_FORM.send_email.html}</td>
                            <tr><td></td><td>{$T_IMPORT_USERS_FORM.submit_import_users.html}</td>
                            <tr><td colspan="2" class="horizontalSeparator"></td></tr>
                            <tr><td colspan="2">{$smarty.const._THEFIELDSINYOURCSVFILEMUSTCONTAINTHEFOLLOWINGFIELDS} (<a href = "{$smarty.server.PHP_SELF}?ctg=control_panel&op=users&csv_sample=1">{$smarty.const._DOWNLOADEXAMPLE}</a>):</td></tr>
                            <tr>
                                <td colspan = "2">
                                    {strip}
                                    {section name='fields' loop=$T_FIELDS}
                                        <span {if ($T_FIELDS[fields] == 'login' || $T_FIELDS[fields] == 'email' || $T_FIELDS[fields] == 'name' || $T_FIELDS[fields] == 'surname')}style = "color:red"{/if}>{$T_FIELDS[fields]};</span>
                                    {/section}
                                    <br>
                                    {section name='fields' loop=$T_FIELDS}
                                        XXX;
                                    {/section}
                                    {/strip}
                                </td>
                            </tr>
                            <tr><td colspan="2">({$smarty.const._IFEMPTYNEWPASSWORD})</td></tr>
                        </table>
                        </form>
                    </div>
                {/if}
                    <div class="tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'export')} tabbertabdefault{/if}">
                        <h3>{$smarty.const._USERSEXPORT}</h3>
                        {$T_EXPORT_USERS_FORM.javascript}
                        <form {$T_EXPORT_USERS_FORM.attributes}>
                        {$T_EXPORT_USERS_FORM.hidden}
                            <table>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATCOMMA}:</td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.export_users.csvA.html}</td></tr>
                                <tr><td class = "labelCell">{$smarty.const._USERSEXPORTUSINGCSVFORMATQM}:</td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.export_users.csvB.html}</td></tr>
                                <tr><td colspan = "2">&nbsp;</td></tr>
                                <tr><td></td>
                                    <td class = "elementCell">{$T_EXPORT_USERS_FORM.submit_export_users.html}</td></tr>
                            </table>
                        </form>
                    </div>
                </div>
                {/capture}
                {eF_template_printInnerTable title=$smarty.const._USERSDATA data=$smarty.capture.t_import_export_users_code image='32x32/users_data.png'}

        </td></tr>
    {/capture}
    {elseif $T_OP == 'user_profile'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=user_profile">'|cat:$smarty.const._CUSTOMIZEUSERSPROFILE|cat:'</a>'}
        {*moduleCustomizeUsersProfile: The users profile customization page*}
        {capture name = "moduleCustomizeUsersProfile"}
            <tr><td class="moduleCell">
                {if $smarty.get.add_field || $smarty.get.edit_field}
                                    {if $smarty.get.edit_field}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=user_profile&edit_field='|cat:$smarty.get.edit_field|cat:'">'|cat:$smarty.const._EDITFIELD|cat:'</a>'}
                                    {else}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=user_profile&add_field=1">'|cat:$smarty.const._ADDFIELD|cat:'</a>'}
                                     {/if}
                            {capture name = 'field_form_code'}
                                {$T_FIELD_FORM.javascript}
                                <form {$T_FIELD_FORM.attributes}>
                                    {$T_FIELD_FORM.hidden}
                                    <table>
                                        <tr><td class = "labelCell">{$smarty.const._FIELDNAME}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.name.html}</td></tr>
                                        {if $T_FIELD_FORM.name.error}<tr><td></td><td class = "formError">{$T_FIELD_FORM.name.error}</td></tr>{/if}
                                        <tr><td class = "labelCell">{$smarty.const._FIELDDESCRIPTION}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.description.html}</td></tr>
                                        <tr><td></td><td class = "infoCell">{$smarty.const._INTHESELECTEDLANGUAGE}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._DBTYPE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.db_type.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._TYPE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.type.html}</td></tr>
                                        <tr id = "select_values" style = "display:none"><td class = "labelCell">{$smarty.const._VALUES}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.values[0].html}&nbsp;<img src = "images/16x16/add2.png" title = "{$smarty.const._ADDVALUES}" alt = "{$smarty.const._ADDVALUES}" border = "0" style = "vertical-align:middle" onclick = "addValue()"></td></tr>
                                        {if $smarty.get.edit_field}
                                            {section name = 'field_list' loop = $T_FIELD_FORM.values}
                                                {if !$smarty.section.field_list.first}
                                        <tr><td></td><td class = "elementCell">{$T_FIELD_FORM.values[field_list].html}&nbsp;<img src = "images/16x16/delete.png" title = "{$smarty.const._DELETEVALUE}" alt = "{$smarty.const._DELETEVALUE}" border = "0" style = "vertical-align:middle" onclick = "event.findElement('tr').remove();elementCount--;"/></td></tr>
                                                {/if}
                                            {/section}
                                        {/if}
                                        <tr id = "after_row"><td class = "labelCell">{$smarty.const._DEFAULTVALUE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.default_value.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._ACTIVENEUTRAL}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.active.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._ISVISIBLEFROMOTHERUSERS}&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.visible.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._ISMANDATORY}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.mandatory.html}</td></tr>
                                        <tr><td class = "labelCell">{$smarty.const._LANGUAGE}:&nbsp;</td>
                                            <td class = "elementCell">{$T_FIELD_FORM.languages_NAME.html}</td></tr>
                                        <tr><td colspan = "2">&nbsp;</td></tr>
                                        <tr><td></td><td>{$T_FIELD_FORM.submit_field.html}</td></tr>
                                    </table>
                                </form>
                                <script>
                                    var elementCount = 0;
                                {if isset($T_SELECT_OPTIONS)}
                                    changeType();
                                    elementCount = {$T_SELECT_OPTIONS};
                                {/if}
                                {literal}
                                    function addValue() {
                                        elementCount++;
                                        $('after_row').insert({before: new Element('tr')
                                                            .insert(new Element('td'))
                                                            .insert(new Element('td')
                                                                 .insert(new Element('input', {type: 'text', name:'values['+elementCount+']', id:'values['+elementCount+']'}).toggleClassName('inputText')).insert('&nbsp')
                                                                 .insert(new Element('img', {src:'images/16x16/delete.png'}).setStyle({verticalAlign:'middle'}).observe('click', function(event) {event.findElement('tr').remove();elementCount--;}))
                                                                 .insert(new Element('br')))});
                                        $('default_value').insert(new Element('option', {value:elementCount}).update(elementCount));
                                    }
                                    function changeType() {
                                        if ($('select_values').visible()) {
                                            $('select_values').hide();
                                            $('after_row').show();          //Default value row
                                        } else {
                                            $('select_values').show();
                                            $('after_row').hide();
                                        }
                                    }
                                {/literal}
                                </script>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._CUSTOMIZEUSERSPROFILE data = $smarty.capture.field_form_code image = '/32x32/businessman_add.png'}
                {else}
                    {capture name = 't_fields_list'}
                        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                <table>
                                    <tr><td style = "vertical-align:middle">
                                            <a href = "administrator.php?ctg=control_panel&op=user_profile&add_field=1"><img  style = "vertical-align:middle" src="images/16x16/add2.png" title="{$smarty.const._ADDFIELD}" alt="{$smarty.const._ADDNEWFIELD}"border="0"/></a>
                                            <a href = "administrator.php?ctg=control_panel&op=user_profile&add_field=1" style = "vertical-align:middle">{$smarty.const._ADDNEWFIELD}</a>
                                        </td></tr>
                                </table>
                        {/if}
                                <table width = "100%" class = "sortedTable">
                                    <tr class = "topTitle defaultRowHeight">
                                        <td class = "topTitle">{$smarty.const._FIELDNAME}</td>
                                        <td class = "topTitle">{$smarty.const._TYPE}</td>
                                        <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                                        <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                    {/if}
                                    </tr>
                    {foreach name = 'fields_list' key = "key" item = "field" from = $T_PROFILE_FIELDS}
                                    <tr id="row_{$field.name}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$field.active}deactivatedTableElement{/if}">
                                        <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                            <a href = "administrator.php?ctg=control_panel&op=user_profile&edit_field={$field.name}" class = "editLink"><span id="column_{$field.name}" {if !$field.active}style="color:red"{/if}>{$field.name}</span></a>
                                    {else}
                                            {$field.name}
                                    {/if}
                                        </td>
                                        <td>{if $field.type == 'text'}{$smarty.const._TEXTBOX}{else}{$smarty.const._SELECTBOX}{/if}</td>
                                        <td>{$field.languages_NAME}</td>
                                        <td class = "centerAlign">
                                            <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "activate(this, '{$field.name}')"{/if}>
                                            {if $field.active}
                                                <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">
                                            {else}
                                                <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0">
                                            {/if}
                                            </a></td>
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                        <td class = "centerAlign">
                                            <a href = "administrator.php?ctg=control_panel&op=user_profile&edit_field={$field.name}"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                            <a href = "administrator.php?ctg=control_panel&op=user_profile&delete_field={$field.name}" onclick = "return confirm('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                        </td>
                                    {/if}
                                    </tr>
                    {foreachelse}
                                    <tr class = "defaultRowHeight oddRowColor"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                                </table>
                                    <script>
                                    {literal}
                                    function activate(el, field) {
                                        Element.extend(el);
                                        if (el.down().src.match('red')) {
                                            url = 'administrator.php?ctg=control_panel&op=user_profile&activate_field='+field;
                                            newSource = 'images/16x16/trafficlight_green.png';
                                        } else {
                                            url = 'administrator.php?ctg=control_panel&op=user_profile&deactivate_field='+field;
                                            newSource = 'images/16x16/trafficlight_red.png';
                                        }

                                        var img = new Element('img', {id: 'img_'+field, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                        el.up().insert(img);
                                        el.down().src = 'images/16x16/trafficlight_yellow.png';
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onFailure: function (transport) {
                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                new Effect.Appear(img_id);
                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                            },
                                            onSuccess: function (transport) {
                                                img.hide();
                                                el.down().src = newSource;
                                                new Effect.Appear(el.down(), {queue:'end'});

                                                if (el.down().src.match('green')) {
                                                    // When activated
                                                    var cName = $('row_'+field).className.split(" ");
                                                    $('row_'+field).className = cName[0];
                                                    $('column_'+field).setStyle({color:'green'});
                                                } else {
                                                    $('row_'+field).className += " deactivatedTableElement";
                                                    $('column_'+field).setStyle({color:'red'});
                                                }
                                                }
                                            });
                                    }
                                    {/literal}
                                    </script>
                        {/capture}
                        {eF_template_printInnerTable title = $smarty.const._CUSTOMIZEUSERSPROFILE data = $smarty.capture.t_fields_list image = '/32x32/businessman_add.png'}
                {/if}
            </td></tr>
        {/capture}

        {elseif $T_OP == 'news'}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=news">'|cat:$smarty.const._ANNOUNCEMENTS|cat:'</a>'}
            {*moduleNewsPage: The news page*}
                {capture name = "moduleNewsPage"}
                                        <tr><td class = "moduleCell">
                                            {capture name = "t_news_code"}
                                                {if !$T_CURRENT_USER->coreAccess.news || $T_CURRENT_USER->coreAccess.news == 'change'}
                                                    <div class = "headerTools">
                                                        <img src = "images/16x16/add2.png" title = "{$smarty.const._ANNOUNCEMENTADD}" alt = "{$smarty.const._ANNOUNCEMENTADD}"/>
                                                        <a href = "news.php?op=insert" onclick = "eF_js_showDivPopup('{$smarty.const._ANNOUNCEMENTADD}', 1)" title = "{$smarty.const._ANNOUNCEMENTADD}" target = "POPUP_FRAME" style = "vertical-align:middle">{$smarty.const._ANNOUNCEMENTADD}</a>
                                                	</div>
                                                {/if}
                                            	{include file = "news_list.tpl"}
                                            {/capture}
    
                                            {eF_template_printInnerTable title = $smarty.const._ANNOUNCEMENTS data = $smarty.capture.t_news_code image = '32x32/news.png'}
                                        </td></tr>
                {/capture}

        {elseif $T_OP == 'backup'}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=backup">'|cat:$smarty.const._BACKUP|cat:' - '|cat:$smarty.const._RESTORE|cat:'</a>'}
            {capture name = "moduleBackup"}
                <tr><td class = "moduleCell">
                {if $T_DEFAULT_URI} {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:$T_DEFAULT_URI|cat:'&'}
                {else}              {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:'?'}
                {/if}
                {capture name="t_backup_code"}
                    <script>
                    {literal}
                    function restore(el, id) {
                        if (confirm('{/literal}{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}{literal}')) {
                            location = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=control_panel&op=backup&restore='+id;
                        }
                    }
                    {/literal}
                    </script>

                   {$T_FILE_MANAGER}
                   <div id = "backup_table" style = "display:none;">
                               {$T_BACKUP_FORM.javascript}
                               <form {$T_BACKUP_FORM.attributes}>
                                   {$T_BACKUP_FORM.hidden}
                                   <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                                       <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                           <td class = "elementCell">{$T_BACKUP_FORM.backupname.html}</td></tr>
                                       <tr><td class = "labelCell">{$smarty.const._TYPE}:&nbsp;</td>
                                           <td class = "elementCell">{$T_BACKUP_FORM.backuptype.html}</td></tr>
                                       <tr><td colspan = "2">&nbsp;</td></tr>
                                       <tr><td></td><td class = "elementCell">{$T_BACKUP_FORM.submit_backup.html}</td></tr>
                                   </table>
                               </form>
                               <img src = "images/others/progress_big.gif" id = "backup_image" title = "{$smarty.const._UPLOADING}" alt = "{$smarty.const._UPLOADING}" style = "display:none;margin-top:30px;vertical-align:middle;"/>
                   </div>
                {/capture}

                {eF_template_printInnerTable title = $smarty.const._BACKUP|cat:' - '|cat:$smarty.const._RESTORE data = $smarty.capture.t_backup_code image = '/32x32/server_client_exchange.png'}
            </td></tr>
        {/capture}

    {elseif $T_OP == 'languages'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=languages">'|cat:$smarty.const._LANGUAGEADMINISTRATION|cat:'</a>'}
        {capture name="moduleLanguages"}
            <tr><td class = "moduleCell">
                {capture name = "languageAdmin"}
                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                        <table>
                             <tr><td>
                                    <a href = "javascript:void(0)"  onclick = "eF_js_showDivPopup('{$smarty.const._ADDLANGUAGE}', 0, 'language_table');$('language_name').value = '';$('language_translation').value = '';$('selected_language').value = '';">
                                        <img src="images/16x16/add2.png" title="{$smarty.const._ADDLANGUAGE}" alt="{$smarty.const._ADDLANGUAGE}"border="0"/></a></td>
                                <td>
                                    <a href = "javascript:void(0)"  onclick = "$('selected_language').value = '';eF_js_showDivPopup('{$smarty.const._ADDLANGUAGE}', 0, 'language_table')">{$smarty.const._ADDLANGUAGE}</a>
                                </td></tr>
                        </table>
                    {/if}
                        <table style = "width:100%">
                            <tr class = "defaultRowHeight">
                                <td class = "topTitle">{$smarty.const._CURRENTLANGUAGES}</td>
                                <td class = "topTitle">{$smarty.const._TRANSLATION}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}

                               <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                    {/if}
                            </tr>
                    {foreach name = 'language_list' key = "name" item = "language" from = $T_LANGUAGES}
                            <tr id="row_{$language.name}" class = "{cycle name = "languages" values = "oddRowColor, evenRowColor"} {if !$language.active}deactivatedTableElement{/if}">
                                <td>{$language.name}</td>
                                <td>{$language.translation}</td>
                                <td class = "centerAlign">
                                    <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "activate(this, '{$language.name}')"{/if}>
                                    {if $language.active}
                                        <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">
                                    {else}
                                        <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0">
                                    {/if}
                                    </a>
                                </td>
                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                <td class = "centerAlign">
                                    <a href = "view_file.php?file={$language.file_path}&action=download"><img src = "images/16x16/import2.png" title = "{$smarty.const._DOWNLOADLANGUAGEFILE}" alt = "{$smarty.const._DOWNLOADLANGUAGEFILE}"  border = "0"/></a>
                        {if $name != 'english'}
                                    <a href = "javascript:void(0)" onclick = "eF_js_showDivPopup('{$smarty.const._EDIT}', 0, 'language_table');$('language_name').value = '{$language.name}';$('language_translation').value = '{$language.translation}';$('selected_language').value = '{$language.name}';" title = "{$smarty.const._EDIT}"><img src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" border = "0"/></a>
                                    <a href = "javascript:void(0)" onclick = "if (confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')) deleteLanguage(this, '{$language.name}')"><img src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}"  border = "0"/></a>
                        {/if}
                                </td>
                    {/if}
                             </tr>
                    {foreachelse}
                            <tr class = "oddRowColor defaultRowHeight"><td class = "emptyCategory centerAlign" colspan = "3">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                        </table>
                        <script>
                        {literal}
                        function activate(el, language) {
                            Element.extend(el);
                            if (el.down().src.match('red')) {
                                url = 'administrator.php?ctg=control_panel&op=languages&activate_language='+language;
                                newSource = 'images/16x16/trafficlight_green.png';
                            } else {
                                url = 'administrator.php?ctg=control_panel&op=languages&deactivate_language='+language;
                                newSource = 'images/16x16/trafficlight_red.png';
                            }

                            var img = new Element('img', {id: 'img_'+language, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                            img_id = img.identify();
                            el.up().insert(img);
                            el.down().src = 'images/16x16/trafficlight_yellow.png';
                            new Ajax.Request(url, {
                                method:'get',
                                asynchronous:true,
                                onFailure: function (transport) {
                                    img.writeAttribute({src:'images/16x16/delete2.png', title: transport.responseText}).hide();
                                    new Effect.Appear(img_id);
                                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                },
                                onSuccess: function (transport) {
                                    img.hide();
                                    el.down().src = newSource;
                                    new Effect.Appear(el.down(), {queue:'end'});

                                    if (el.down().src.match('green')) {
                                        // When activated
                                        var cName = $('row_'+language).className.split(" ");
                                        $('row_'+language).className = cName[0];
                                        //$('column_'+field).setStyle({color:'green'});
                                    } else {
                                        $('row_'+language).className += " deactivatedTableElement";
                                        //$('column_'+field).setStyle({color:'red'});
                                    }
                                    }
                                });
                        }
                        function deleteLanguage(el, language) {
                            Element.extend(el);
                            url = 'administrator.php?ctg=control_panel&op=languages&delete_language='+language;

                            var img = new Element('img', {id: 'img_'+language, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                            img_id = img.identify();
                            el.up().insert(img);

                            new Ajax.Request(url, {
                                method:'get',
                                asynchronous:true,
                                onFailure: function (transport) {
                                    img.writeAttribute({src:'images/16x16/delete2.png', title: transport.responseText}).hide();
                                    new Effect.Appear(img_id);
                                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                },
                                onSuccess: function (transport) {
                                    img.hide();
                                    new Effect.Fade(el.up().up(), {queue:'end'});
                                    }
                                });
                        }
                        {/literal}
                        </script>
                        <div id = "language_table" style = "display:none;">
                            {$T_CREATE_LANGUAGE_FORM.javascript}
                            <form {$T_CREATE_LANGUAGE_FORM.attributes}>
                                {$T_CREATE_LANGUAGE_FORM.hidden}
                                <table class = "formElements">
                                    <tr><td class = "labelCell">{$smarty.const._ENGLISHNAME}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.english_name.html}</td></tr>
                                    <tr><td class = "labelCell">{$smarty.const._TRANSLATION}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.translation.html}</td></tr>
                                    <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.language_upload.html}</td></tr>
                                    <tr><td></td>
                                        <td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILE_SIZE}</b> {$smarty.const._KB}</td></tr>
                                    {if $T_CREATE_LANGUAGE_FORM.language_upload.error}<tr><td></td><td class = "formError" colspan = "2">{$T_CREATE_LANGUAGE_FORM.language_upload.error}</td></tr>{assign var = 'div_error' value = 'upload_language_table'|cat:$smarty.section.form_list.index}{/if}
                                    <tr><td colspan = "2">&nbsp;</td></tr>
                                    <tr><td></td>
                                        <td class = "elementCell">{$T_CREATE_LANGUAGE_FORM.submit_upload_language.html}</td></tr>
                                </table>
                            </form>
                        </div>

                 {/capture}

                {eF_template_printInnerTable title = $smarty.const._LANGUAGEADMINISTRATION data = $smarty.capture.languageAdmin image = '/32x32/languages.png'}
            </td></tr>
        {/capture}

    {elseif $T_OP == 'versionkey'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=versionkey">'|cat:$smarty.const._VERSIONKEYTITLE|cat:'</a>'}
        {capture name="moduleVersionKey"}
                <tr><td class = "moduleCell">
                {capture name="changeKey"}
        {$T_VERSIONKEY_DEFAULT.javascript}
            <form {$T_VERSIONKEY_DEFAULT.attributes}>
                {$T_VERSIONKEY_DEFAULT.hidden}
                <table class = "formElements" align="center" width="100%">
                <tr>
                    <td colspan="2">
                    {if $T_VERSIONKEY_DEFAULT_MSG.users > 0}
            <table style = "width:100%">
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><b>{$smarty.const._VERSIONTYPE}</b></td><td>{$T_VERSIONKEY_DEFAULT_MSG.type}</td>
                </tr>
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><b>{$smarty.const._VERSIONALLOEDUSERS}</b></td><td>{$T_VERSIONKEY_DEFAULT_MSG.users}</td>
                </tr>
                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                    <td><b>{$smarty.const._VERSIONSERIAL}</b></td><td>{$T_VERSIONKEY_DEFAULT_MSG.serial}</td>
                </tr>
            </table>
                    <br>
                    {/if}
                    </td>
                </tr>
                <tr>
                    <td class="labelCell">{$smarty.const._VERSIONKEY}:&nbsp;</td>
                    <td>&nbsp;{$T_VERSIONKEY_DEFAULT.version_key.html}</td>
                </tr>
                <tr>
                    <td></td>
                    <td><br />{$T_VERSIONKEY_DEFAULT.submit_config.html}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                </table>
            </form>
                {/capture}
                {eF_template_printInnerTable title = $smarty.const._VERSIONKEYTITLE data = $smarty.capture.changeKey image = '/32x32/keys.png'}
            </td></tr>
        {/capture}
        {elseif $T_OP == 'style'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op=style">'|cat:$smarty.const._CHANGESTYLE|cat:'</a>'}
        {capture name="moduleStyle"}
                <tr><td class = "moduleCell">
                {if $T_DEFAULT_URI} {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:$T_DEFAULT_URI|cat:'&'}
                {else}              {assign var = "query_string" value = $smarty.server.PHP_SELF|cat:'?'}
                {/if}
                {capture name="changeStyle"}
                    {$T_FILE_MANAGER}
                    <script>
                    {literal}
                        function useDefaultStyle(el) {
                            Element.extend(el);
                            var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=control_panel&op=style&use_none=1';
                            var img = new Element('img', {src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                            img_id = img.identify();
                            el.up().insert(img);
                            new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onFailure: function (transport) {
                                        img.writeAttribute({src:'images/16x16/delete2.png', title: transport.responseText}).hide();
                                        new Effect.Appear(img_id);
                                        window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                    },
                                    onSuccess: function (transport) {
                                        location.reload();
                                    }
                                });
                        }
                        function useStyle(el, style) {
                            Element.extend(el);
                            if (el.src.match('pin_green.png')) {
                                var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=control_panel&op=style&use_none=1';
                                var set_style = 0;
                            } else {
                                var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=control_panel&op=style&set_style='+style;
                                var set_style = 1;
                            }
                            el.src = 'images/others/progress1.gif';

                            new Ajax.Request(url, {
                                    method:'get',
                                    asynchronous:true,
                                    onFailure: function (transport) {
                                        el.src = 'images/16x16/pin_red.png';
                                        var img = new Element('img', {src:'images/16x16/delete2.png', title: transport.responseText}).setStyle({borderWidth:'0px'}).hide();
                                        el.up().insert(img);
                                        new Effect.Appear(img);
                                        window.setTimeout('Effect.Fade("'+img.identify()+'")', 5000);
                                    },
                                    onSuccess: function (transport) {
                                        location.reload();
                                    }
                                });
                        }
                    {/literal}
                    </script>
                {/capture}

                {eF_template_printInnerTable title = $smarty.const._CHANGESTYLE data = $smarty.capture.changeStyle image = '/32x32/colors.png'}
            </td></tr>
        {/capture}
    {elseif isset($T_OP_MODULE)}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=control_panel&op='|cat:$T_OP|cat:'">'|cat:$T_OP_MODULE|cat:'</a>'}
        {capture name = "importedModule"}
                                <tr><td class = "moduleCell">
                                    {include file = $smarty.const.G_MODULESPATH|cat:$T_OP|cat:'/module.tpl'}
                                </td></tr>
        {/capture}

    {else}
        <div id = "set_logo_table" style = "display:none">
        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
            {$T_UPLOAD_LOGO_FORM.javascript}
            <form {$T_UPLOAD_LOGO_FORM.attributes}>
                {$T_UPLOAD_LOGO_FORM.hidden}
                <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                    <tr><td colspan = "2" style = "text-align:center"><img src = "images/{$T_LOGO}" alt = "{$smarty.const._EFRONTLOGO}" title = "{$smarty.const._EFRONTLOGO}"
                    {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                    /></td></tr>
                    <tr><td colspan = "2">&nbsp;</td></tr>
                    <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                        <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.logo.html}</td></tr>
                    <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_UPLOAD_SIZE}</b> {$smarty.const._KB}</td></tr>
                    {if $T_UPLOAD_LOGO_FORM.logo.error}<tr><td></td><td class = "formError">{$T_UPLOAD_LOGO_FORM.logo.error}</td></tr>{assign var = 'div_error' value = 'upload_file_table'}{/if}
                    <tr><td class = "labelCell">{$smarty.const._USEDEFAULTLOGO}:&nbsp;</td>
                        <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.default_logo.html}</td></tr>
                    <tr><td></td><td >&nbsp;</td></tr>
                    <tr><td></td><td class = "submitCell">{$T_UPLOAD_LOGO_FORM.submit_upload_logo.html}</td></tr>
                </table>
            </form>
        {else}
                <table style = "margin: 2em 2em 2em 2em" class = "formElements">
                    <tr><td colspan = "2" style = "text-align:center"><img src = "images/{$T_LOGO}" alt = "{$smarty.const._EFRONTLOGO}" title = "{$smarty.const._EFRONTLOGO}"
                    {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}
                    /></td></tr>
                </table>
        {/if}
        </div>

        {if $T_INACTIVE_USERS}
        {*moduleNewUsersApplications: The list of inactive users, waiting for activation*}
        {capture name = "moduleNewUsersApplications"}
                                <tr><td class = "moduleCell">
                                    {capture name = 't_inactive_users_code'}
                                        {section name = 'inactive_users_list' loop = "$T_INACTIVE_USERS"}
                                                    <span class = "counter">{$smarty.section.inactive_users_list.iteration}.</span>
                                                    <a href = "administrator.php?ctg=users&edit_user={$T_INACTIVE_USERS[inactive_users_list].login}">{$T_INACTIVE_USERS[inactive_users_list].login} ({$T_INACTIVE_USERS[inactive_users_list].surname} {$T_INACTIVE_USERS[inactive_users_list].name})</a><br/>
                                        {sectionelse}
                                                    <span class = "emptyCategory">{$smarty.const._NONEWAPPLICATIONS}</span>
                                        {/section}
                                    {/capture}

                                    {eF_template_printInnerTable title = $smarty.const._NEWUSERS data = $smarty.capture.t_inactive_users_code image = '/32x32/user1_into.png' array = $T_INACTIVE_USERS link=$T_INACTIVE_USERS_LINK}
                                </td></tr>
        {/capture}
        {/if}

        {*moduleNewsList: A list with system announcements*}
        {capture name = "moduleSystemAnnouncementsList"}
            {if $T_CURRENT_USER->coreAccess.news != 'hidden'}
                            <tr><td class = "moduleCell">
                                    {capture name='t_news_code'}
                                        {eF_template_printNews data=$T_NEWS}
                                    {/capture}

                                    {eF_template_printInnerTable title=$smarty.const._SYSTEMANNOUNCEMENTS data=$smarty.capture.t_news_code image='/32x32/news.png' navigation=$T_NEWS_NAV array=$T_NEWS options=$T_NEWS_OPTIONS link=$T_NEWS_LINK}
                            </td></tr>
            {/if}
        {/capture}

        {*moduleCalendar: Display the calendar innertable*}
        {capture name = "moduleCalendar"}
            {if $T_CURRENT_USER->coreAccess.news != 'hidden'}
                    <tr><td class = "moduleCell">
                        {capture name = "t_calendar_code"}
                            {eF_template_printCalendar events=$T_CALENDAR_EVENTS timestamp=$T_VIEW_CALENDAR}
                        {/capture}
                        {assign var="calendar_title"  value = `$smarty.const._CALENDAR`&nbsp;(#filter:timestamp-`$T_VIEW_CALENDAR`#)}
                        {eF_template_printInnerTable title=$calendar_title data=$smarty.capture.t_calendar_code image='32x32/calendar.png' options=$T_CALENDAR_OPTIONS link=$T_CALENDAR_LINK}
                    </td></tr>
            {/if}
        {/capture}


        {if $T_NEW_LESSONS}
        {*moduleNewLessonsApplications: The list of new lessons applications*}
        {capture name = "moduleNewLessonsApplications"}
                            <tr><td class = "moduleCell">
                                {capture name = 't_new_lessons_code'}
                                    {section name = 'new_lessons_list' loop = "$T_NEW_LESSONS"}
                                                {counter}. <a href = "administrator.php?ctg=users&edit_user={$T_NEW_LESSONS[new_lessons_list].users_LOGIN}&tab=lessons">{$T_NEW_LESSONS[new_lessons_list].users_LOGIN} ({$T_NEW_LESSONS[new_lessons_list].count} {if $T_NEW_LESSONS[new_lessons_list].count == 1}{$smarty.const._LESSON}{else}{$smarty.const._LESSONS}{/if})</a><br/>
                                    {/section}
                                {/capture}

                                {eF_template_printInnerTable title = $smarty.const._LESSONSREGISTRATIONS data = $smarty.capture.t_new_lessons_code image = '/32x32/book_blue_next.png' array = $T_NEW_LESSONS link = 'administrator.php?ctg=lessons'}
                            </td></tr>
        {/capture}
        {/if}

        {if $T_PERSONAL_MESSAGES}
        {*modulePersonalMessages: The list of unread personal messages*}
        {capture name = "modulePersonalMessages"}
                            <tr><td class = "moduleCell">
                                {capture name='t_personal_messages_code'}
                                    {eF_template_printPersonalMessages data=$T_PERSONAL_MESSAGES}
                                {/capture}

                                {eF_template_printInnerTable title = $smarty.const._RECENTUNREADPERSONALMESSAGES data = $smarty.capture.t_personal_messages_code image='/32x32/mail2.png' navigation=$T_PERSONAL_MESSAGES_NAV array=$T_PERSONAL_MESSAGES options=$T_PERSONAL_MESSAGES_OPTIONS link=$T_PERSONAL_MESSAGES_LINK}
                            </td></tr>
        {/capture}
        {/if}


{*///MODULES CAPTURING*}
        {*Inner table modules *}
        {foreach name = 'module_inner_tables_list' key = key item = moduleItem from = $T_INNERTABLE_MODULES}
            {capture name = $key|replace:"_":""}                    {*We cut off the underscore, since scriptaculous does not seem to like them*}
                <tr><td class = "moduleCell">
                    {if $moduleItem.smarty_file}
                        {include file = $moduleItem.smarty_file}
                    {else}
                        {$moduleItem.html_code}
                    {/if}
                </td></tr>
            {/capture}
        {/foreach}

{*
        {if $T_INNERTABLE_MODULES}

            {foreach name = 'module_inner_tables_list' key = key item = item from = $T_MODULES}
                {if in_array($key, $T_INNERTABLE_MODULES)}
                    {capture name = $key|replace:"_":""}
                                        <tr><td class = "moduleCell">
                                            {include file = $smarty.const.G_MODULESPATH|cat:$key|cat:'/module_innerTable.tpl'}
                                        </td></tr>
                    {/capture}
                {/if}
            {/foreach}
        {/if}
*}


    {*moduleIconFunctions: Print icon Table with lesson options*}
        {capture name = "moduleIconFunctions"}
            {if $T_CURRENT_USER->coreAccess.control_panel != 'hidden'}
                            <tr><td class = "moduleCell">
                                    {eF_template_printIconTable title=$smarty.const._OPTIONS columns=4 links=$T_ADMIN_OPTIONS image='/32x32/gears.png'}
                            </td></tr>
            {/if}
        {/capture}

    {/if}
{/if}

{if (isset($T_CTG) && ($T_CTG == 'users' || $T_CTG == 'personal'))}

    {if !isset($smarty.get.print_preview) && !isset($smarty.get.print) && !$T_POPUP_MODE}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users">'|cat:$smarty.const._USERS|cat:'</a>'}
    {/if}

    {if $smarty.get.add_user || $smarty.get.edit_user}
    {*moduleNewUser: Create a new user*}
            {capture name = "moduleNewUser"}
                                <tr><td class = "moduleCell">
                                {if !isset($smarty.get.print_preview) && !isset($smarty.get.print) && !$T_POPUP_MODE}
                                    {if $smarty.get.edit_user != ""}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&edit_user='|cat:$smarty.get.edit_user|cat:'">'|cat:$smarty.const._EDITUSEREDIT|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$smarty.get.edit_user|cat:'&quot;</span></a>'}
                                    {else}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=users&add_user=1">'|cat:$smarty.const._NEWUSER|cat:'</a>'}
                                     {/if}
                                 {/if}

                                        <table width = "100%">
                                            <tr><td class = "topAlign" width = "50%">
                                                    {if isset($T_PERSONAL)}
                                                        {include file = "includes/module_personal.tpl"}
                                                    {/if}
                                                </td>
                                            </tr>
                                        </table>
                                </td></tr>
        {/capture}
    {else}
{*moduleUsers: The users functions*}

    {capture name = "moduleUsers"}
        {if $T_MODULE_HCD_INTERFACE}
            {include file = "module_hcd.tpl"}
        {else}
                            <tr><td class = "moduleCell">
                                    {capture name = 't_users_code'}
                                            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                    <table border = "0" >
                                                        <tr><td>
                                                            <a href="administrator.php?ctg=users&add_user=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWUSER}" alt="{$smarty.const._NEWUSER}"/ border="0"></a></td><td><a href="administrator.php?ctg=users&add_user=1">{$smarty.const._NEWUSER}</a>
                                                        </td></tr>
                                                    </table>
                                            {/if}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = {$T_USERS_SIZE} sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "20" url = "administrator.php?ctg=users&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                            <td class = "topTitle centerAlign" name = "lessons_num">{$smarty.const._LESSONSNUMBER}</td>
															<td class = "topTitle centerAlign" name = "courses_num">{$smarty.const._COURSESNUMBER}</td>
															<td class = "topTitle centerAlign" name = "groups_num">{$smarty.const._GROUPSNUMBER}</td>
                                                            <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                            <td class = "topTitle noSort" align="center">{$smarty.const._STATISTICS}</td>
                                                        {/if}
                                                        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <td class = "topTitle" align="center">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                        </tr>
                                                {foreach name = 'users_list' key = 'key' item = 'user' from = $T_USERS}
                                                        <tr id="row_{$user.login}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                                <td><a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink" {if ($user.pending == 1)}style="color:red;"{/if}><span id="column_{$user.login}" {if !$user.active}style="color:red;"{/if}>{$user.login}</span></a></td>
                                                                <td>{$user.name}</td>
                                                                <td>{$user.surname}</td>
                                                                <td>{if $user.user_types_ID}{$T_ROLES[$user.user_types_ID]}{else}{$T_ROLES[$user.user_type]}{/if}</td>
                                                                <td>{$T_LANGUAGES[$user.languages_NAME]}</td>
                                                                <td class = "centerAlign">{$user.lessons_num}</td>
																<td class = "centerAlign">{$user.courses_num}</td>
																<td class = "centerAlign">{$user.groups_num}</td>
                                                                <td class = "centerAlign">
                                                                {if $user.login != $smarty.session.s_login}
                                                                    <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}onclick = "activate(this, '{$user.login}')"{/if}>
                                                                    {if $user.active == 1}
                                                                        <img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0">
                                                                    {else}
                                                                        <img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0">
                                                                    {/if}
                                                                    </a>
                                                                {else}
                                                                    <img src = "images/16x16/trafficlight_green_gray.png" alt = "{$smarty.const._ACTIVE}" title = "{$smarty.const._ACTIVE}" border = "0">
                                                                {/if}
                                                                </td>
                                                            {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                                <td class = "centerAlign"><a href="administrator.php?ctg=statistics&option=user&sel_user={$user.login}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                                                            {/if}
                                                            {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                                <td class = "centerAlign">
                                                                    <a href = "administrator.php?ctg=users&edit_user={$user.login}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                    <a href = "administrator.php?ctg=users&delete_user={$user.login}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSER}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                                </td>
                                                            {/if}
                                                        </tr>
                                                        {foreachelse}
                                                        <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                                        {/foreach}
                                                    </table>
<!--/ajax:usersTable-->
                                                        <script>
                                                        {literal}
                                                        function activate(el, user) {
                                                            Element.extend(el);
                                                            if (el.down().src.match('red')) {
                                                                url = 'administrator.php?ctg=users&activate_user='+user;
                                                                newSource = 'images/16x16/trafficlight_green.png';
                                                            } else {
                                                                url = 'administrator.php?ctg=users&deactivate_user='+user;
                                                                newSource = 'images/16x16/trafficlight_red.png';
                                                            }

                                                            var img = new Element('img', {id: 'img_'+user, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                            el.up().insert(img);
                                                            el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                            new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                    new Effect.Appear(img_id);
                                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    img.hide();
                                                                    el.down().src = newSource;
                                                                    new Effect.Appear(el.down(), {queue:'end'});

                                                                    if (el.down().src.match('green')) {
                                                                        // When activated
                                                                        var cName = $('row_'+user).className.split(" ");
                                                                        $('row_'+user).className = cName[0];
                                                                        $('column_'+user).setStyle({color:'green'});
                                                                    } else {
                                                                        $('row_'+user).className += " deactivatedTableElement";
                                                                        $('column_'+user).setStyle({color:'red'});
                                                                    }
                                                                    }
                                                                });
                                                        }
                                                        {/literal}
                                                        </script>
                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._UPDATEUSERS data = $smarty.capture.t_users_code image = '/32x32/user1.png'}
                            </td></tr>
        {/if}
    {/capture}

    {/if}

{/if}


{if (isset($T_CTG) && $T_CTG == 'lessons')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons">'|cat:$smarty.const._LESSONS|cat:'</a>'}

    {if $smarty.get.add_lesson || $smarty.get.edit_lesson}
    {*moduleNewLessonDirection: Create a new direction or lesson forms*}
        {capture name = "moduleNewLessonDirection"}
                            <tr><td class = "moduleCell">
                                {if $smarty.get.edit_lesson}
                                            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&edit_lesson='|cat:$smarty.get.edit_lesson|cat:'">'|cat:$smarty.const._EDITLESSON|cat:' <span class = "innerTableName">&quot;'|cat:$T_LESSON_FORM.name.value|cat:'&quot;</span></a>'}
                                {else}
                                            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&add_lesson=1">'|cat:$smarty.const._NEWLESSON|cat:'</a>'}
                                {/if}
                                 {capture name = 't_lesson_code'}
                                        <div class = "tabber">
                                            <div class = "tabbertab">
                                                    <h3>{$smarty.const._EDITLESSON}</h3>
                                                    <table width = "100%">
                                                        <tr><td class = "topAlign" width = "50%">

                                                            {$T_LESSON_FORM.javascript}
                                                            <form {$T_LESSON_FORM.attributes}>
                                                            {$T_LESSON_FORM.hidden}
                                                            <table class = "formElements">
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.name.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.name.html}</td></tr>
                                                                {if $T_LESSON_FORM.name.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.name.error}</td></tr>{/if}

                                                             {if isset($T_LESSON_FORM.languages_NAME.label)}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.languages_NAME.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.languages_NAME.html}</td></tr>
                                                                {if $T_LESSON_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.languages_NAME.error}</td></tr>{/if}
                                                            {/if}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.directions_ID.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.directions_ID.html}</td></tr>
                                                                {if $T_LESSON_FORM.directions_ID.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.directions_ID.error}</td></tr>{/if}
                                                            {* MODULE HCD: No price exists in HCD - which employee would ever pay to attend a seminar???*}
                                                            {if !$T_MODULE_HCD_INTERFACE}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.price.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.price.html} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td></tr>
                                                                {if $T_LESSON_FORM.price.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.price.error}</td></tr>{/if}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.recurring.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.recurring.html}</td></tr>
                                                                {if $T_LESSON_FORM.recurring.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.recurring.error}</td></tr>{/if}
                                                            {/if}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.course_only.0.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.course_only.0.html}</td></tr>
                                                                <tr><td class = "labelCell"></td>
                                                                    <td>{$T_LESSON_FORM.course_only.1.html}</td></tr>
                                                                {if $T_LESSON_FORM.course_only.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.course_only.error}</td></tr>{/if}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.active.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.active.html}</td></tr>
                                                                {if $T_LESSON_FORM.active.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.active.error}</td></tr>{/if}
                                                            {if $smarty.get.add_lesson}
                                                                <tr><td class = "labelCell">{$T_LESSON_FORM.import_content.label}:&nbsp;</td>
                                                                    <td>{$T_LESSON_FORM.import_content.html}</td></tr>
                                                                {if $T_LESSON_FORM.import_content.error}<tr><td></td><td class = "formError">{$T_LESSON_FORM.import_content.error}</td></tr>{/if}
                                                                <tr><td></td><td class = "infoCell">{$smarty.const._ONLYEFRONTNOTSCORM}</td></tr>
                                                            {/if}
                                                                <tr><td colspan = "2">&nbsp;</td></tr>
                                                                <tr><td></td><td>{$T_LESSON_FORM.submit_lesson.html}</td></tr>
                                                            </table>
                                                            </form>
                                                        </td></tr>
                                                    </table>
                                            </div>


                                {capture name = 't_users_to_lessons_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$smarty.get.edit_lesson}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle" name = "role">{$smarty.const._USERROLEINLESSON}</td>
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                            <td class = "topTitle centerAlign" name = "partof">{$smarty.const._CHECK}</td>
                                                        </tr>
                                {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>{$user.login}</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
                                                                <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;ajaxPost('{$user.login}', this);">
                                    {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
                                                                    <option value="{$role_key}" {if ($user.role == $role_key)}selected{/if} {if $user.basic_user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
                                    {/foreach}
                                                                </select>
                                    {else}
                                                                {$T_ROLES[$user.role]}
                                    {/if}
                                                            </td>
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td align="center">
                                                                <table>
                                                                    <tr><td width="45%">
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&add_evaluation=1">
                                                                            <img src="images/16x16/edit.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWEVALUATION}"/ border="0">
                                                                        </a>
                                                                        </td>
                                                                        <td width="45%">
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&tab=evaluations">
                                                                            <img src="images/16x16/view.png" title="{$smarty.const._SHOWEVALUATIONS}" alt="{$smarty.const._SHOWEVALUATIONS}"/ border="0">
                                                                        </a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        {/if}
                                                            <td class = "centerAlign">
                                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$user.login}" id = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if in_array($user.login, $T_LESSON_USERS)}checked = "checked"{/if} />{if in_array($user.login, $T_LESSON_USERS)}<span style = "display:none">checked</span>{/if} {*Text for sorting*}
                                                        {else}
                                                                {if in_array($user.login, $T_LESSON_USERS)}<img src = "images/16x16/check2.png" alt = "{$smarty.const._LESSONUSER}" title = "{$smarty.const._LESSONUSER}"><span style = "display:none">checked</span>{/if}
                                                        {/if}
                                                            </td>
                                                    </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->

                                {literal}
                                <script>
                                function ajaxPost(id, el, table_id) {
                                    Element.extend(el);
                                    //Since in the same page there are 2 ajax post lists, we create a "wrapper" which decides which one to call
                                    table_id == 'skillsTable' ? ajaxLessonSkillUserPost(1, id, el, table_id) : usersAjaxPost(id, el, table_id);
                                }

                                function  usersAjaxPost(login, el, table_id) {
                                    Element.extend(el);
                                    var baseUrl =  'administrator.php?ctg=lessons&edit_lesson={/literal}{$smarty.get.edit_lesson}{literal}&postAjaxRequest=1';

                                    if (login) {
                                        var userType = $('type_'+login).options[$('type_'+login).selectedIndex].value;
                                        var checked  = $('checked_'+login).checked;
                                        var url      = baseUrl + '&login='+login+'&user_type='+userType;
                                        var img_id   = 'img_'+login;
                                    } else if (table_id && table_id == 'usersTable') {
                                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                                        var img_id   = 'img_selectAll';
                                    }

                                    var position = eF_js_findPos(el);
                                    var img      = Element.extend(document.createElement("img"));

                                    img.style.position = 'absolute';
                                    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                    img.setAttribute("id", img_id);
                                    img.setAttribute('src', 'images/others/progress1.gif');

                                    el.parentNode.appendChild(img);

                                        new Ajax.Request(url, {
                                                method:'get',
                                                asynchronous:true,
                                                onSuccess: function (transport) {
                                                    img.style.display = 'none';
                                                    img.setAttribute('src', 'images/16x16/check.png');
                                                    new Effect.Appear(img_id);
                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                    }
                                            });
                                }
                                </script>
                                {/literal}

                                {/capture}

                                {if $smarty.get.edit_lesson && !$T_EDIT_LESSON.course_only}
                                <div class="tabbertab {if $smarty.get.tab=='users'}tabbertabdefault{/if}">
                                    <h3>{$smarty.const._EDITUSERSLESSON}</h3>

                                    {eF_template_printInnerTable title = $smarty.const._UPDATEUSERSTOLESSONS data = $smarty.capture.t_users_to_lessons_code image = '/32x32/book_blue_preferences.png'}
                                </div>
                                {/if}

                                {* MODULE HCD: Create tab with all skills -  the skills offered by this lesson-seminar are to be selected *}
                                {if $T_MODULE_HCD_INTERFACE && $smarty.get.edit_lesson}
                                {*  ****************************************************
                                    This is the form that contains the skills offered by the seminar
                                    **************************************************** *}
                                    {capture name = 't_lesson_skills'}
                                        {literal}
                                            <script>
                                                function show_hide_spec(i)
                                                {
                                                    var spec = document.getElementById("spec_skill_" + i);
                                                    if (spec.style.visibility == "hidden")
                                                        spec.style.visibility = "visible";
                                                    else
                                                        spec.style.visibility = "hidden";
                                                }
                                            </script>
                                        {/literal}
                                            {if $smarty.session.s_type == "administrator"}
                                            <table>
                                                <tr>
                                                    <td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a></td>
                                                </tr>
                                            </table>
                                            {/if}

<!--ajax:skillsTable-->
                                            <table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=lessons&edit_lesson={$smarty.get.edit_lesson}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name="description" width="35%">{$smarty.const._SKILL}</td>
                                                    <td class = "topTitle" name="specification" width="*">{$smarty.const._SPECIFICATION}</td>
                                                    <td class = "topTitle noSort centerAlign" name="skill_ID" idth="5%">{$smarty.const._CHECK}</td>
                                                </tr>

                                        {if isset($T_SKILLS)}
                                            {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
                                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                    <td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a></td>
                                                    <td><input class = "inputText" width = "*" type="text" name="spec_skill_{$skill.skill_ID}"  id="spec_skill_{$skill.skill_ID}" onchange="ajaxLessonSkillUserPost(2,'{$skill.skill_ID}', this);" value="{$skill.specification}"{if $skill.lesson_ID != $smarty.get.edit_lesson} style="visibility:hidden" {/if}></td>
                                                    <td class = "centerAlign"><input class = "inputCheckBox" type = "checkbox" name = "{$skill.skill_ID}" onclick="javascript:show_hide_spec({$skill.skill_ID});ajaxLessonSkillUserPost(1,'{$skill.skill_ID}', this);" {if $skill.lesson_ID == $smarty.get.edit_lesson} checked {/if} ></td>
                                                </tr>
                                            {/foreach}
                                            </table>
<!--/ajax:skillsTable-->
                                        {else}
                                                <tr><td colspan = 3>
                                                    <table width = "100%">
                                                        <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign">{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
                                                    </table>
                                                    </td>
                                                </tr>
                                            </table>
<!--/ajax:skillsTable-->
                                        {/if}
                                    {/capture}

            {* Script for posting ajax requests regarding skill to employees assignments *}
            {literal}
            <script>
            // type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
            // id: the users_login of the employee to get the skill
            // el: the element of the form corresponding to that skill/lesson
            // table_id: the id of the ajax-enabled table
            function ajaxLessonSkillUserPost(type, id, el, table_id) {
                Element.extend(el);
                var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=lessons&edit_lesson={/literal}{$smarty.get.edit_lesson}{literal}&postAjaxRequest=1';
                if (type == 1) {
                    if (id) {
                        var url = baseUrl + '&add_skill=' + id + '&insert='+el.checked + '&specification='+encodeURI(document.getElementById('spec_skill_'+id).value);
                        var img_id   = 'img_'+ id;
                    } else if (table_id && table_id == 'skillsTable') {
                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                        url += '&add_skill=1';
                        var img_id   = 'img_selectAll';
                    }
                } else if (type == 2) {
                    if (id) {
                        var url = baseUrl + '&add_skill=' + id + '&insert=true&specification='+el.value;
                        var img_id   = 'img_'+ id;
                    }
                } else {
                    return false;
                }

                var position = eF_js_findPos(el);
                var img      = Element.extend(document.createElement("img"));

                img.style.position = 'absolute';
                img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                img.setAttribute("id", img_id);
                img.setAttribute('src', 'images/others/progress1.gif');

                el.parentNode.appendChild(img);

                  new Ajax.Request(url, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                img.style.display = 'none';
                                img.setAttribute('src', 'images/16x16/check.png');
                                new Effect.Appear(img_id);
                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                }
                        });

            }
            </script>
            {/literal}

                                    <script>var myform = "skills_to_lesson";</script>
                                    <div class="tabbertab {if (isset($smarty.post.lesson_skills) || ($smarty.get.tab == "skills"))} tabbertabdefault {/if}">
                                        <h3>{$smarty.const._SKILLSOFFERED}</h3>
                                        {eF_template_printInnerTable title = $smarty.const._LESSONSKILLSSELECTION data = $smarty.capture.t_lesson_skills image = '/32x32/wrench.png'}
                                    </div>
                                {/if}




                                        </div>
                            {/capture}
            {if $smarty.get.add_lesson}
                    {eF_template_printInnerTable title = $smarty.const._NEWLESSONOPTIONS data = $smarty.capture.t_lesson_code image = '/32x32/board.png'}
            {else}
                    {eF_template_printInnerTable title = "`$smarty.const._LESSONOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_LESSON_FORM.name.value`&quot;</span>" data = $smarty.capture.t_lesson_code image = '/32x32/board.png'}
            {/if}


                            </td></tr>
    {/capture}



    {else}
    {*moduleLessons: The lessons list*}
        {capture name = "moduleLessons"}
                            <tr><td class = "moduleCell">
                        {if $smarty.get.lesson_info}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_info='|cat:$smarty.get.lesson_info|cat:'">'|cat:$smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
                                {capture name = 't_lesson_info_code'}
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONINFORMATION}</legend>
                                        {$T_LESSON_INFO_HTML}
                                    </fieldset>
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONMETADATA}</legend>
                                        {$T_LESSON_METADATA_HTML}
                                    </fieldset>
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._INFORMATIONFORLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;' data = $smarty.capture.t_lesson_info_code image = '/32x32/about.png'}
                        {elseif $smarty.get.lesson_settings}

                                {if isset($T_OP) && $T_OP == 'reset_lesson'}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_settings='|cat:$smarty.get.lesson_settings|cat:'&op=reset_lesson">'|cat:$smarty.const._RESTARTLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
                                                                {capture name = 't_reset_lesson_code'}
                                                                    {$T_RESET_LESSON_FORM.javascript}
                                                                    <form {$T_RESET_LESSON_FORM.attributes}>
                                                                        {$T_RESET_LESSON_FORM.hidden}
                                                                        <table class = "formElements" style = "margin-left:0px">
                                                                            <tr><td colspan = "100%">{$smarty.const._CHOOSEWHATTODELETE}</td></tr>
                                                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._USERS}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.users.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._ANNOUNCEMENTS}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.news.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.comments.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._ACCESSRULES}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.rules.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._CALENDAR}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.calendar.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._GLOSSARY}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.glossary.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._USERTRACKINGINFORMATION}:&nbsp;</td>
                                                                                <td>{$T_RESET_LESSON_FORM.tracking.html}</td></tr>
                                                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                                                            <tr><td></td><td>{$T_RESET_LESSON_FORM.submit_reset_lesson.html}</td></tr>
                                                                        </table>
                                                                    </form>
                                                                {/capture}
                                                                {eF_template_printInnerTable title = $smarty.const._RESTARTLESSON data = $smarty.capture.t_reset_lesson_code image = '32x32/refresh.png' main_options = $T_TABLE_OPTIONS}
                                {elseif isset($T_OP) && $T_OP == 'import_lesson'}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_settings='|cat:$smarty.get.lesson_settings|cat:'&op=import_lesson">'|cat:$smarty.const._IMPORTLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
                                    {capture name = 't_import_lesson_code'}
                                        <fieldset>
                                        <legend>{$smarty.const._IMPORTLESSON}</legend>
                                        {$T_IMPORT_LESSON_FORM.javascript}
                                        <form {$T_IMPORT_LESSON_FORM.attributes}>
                                            {$T_IMPORT_LESSON_FORM.hidden}
                                            <table class = "formElements">
{*
                                                <tr><td colspan = "100%">{$smarty.const._DELETEEXISTINGDATAFROM}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._CONTENT}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.content.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._PERIODS}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.periods.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._FILES}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.files.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._USERS}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.users.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._ANNOUNCEMENTS}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.news.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.comments.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._ACCESSRULES}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.rules.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._CALENDAR}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.calendar.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._GLOSSARY}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.glossary.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._USERTRACKINGINFORMATION}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.tracking.html}</td></tr>
                                                <tr><td class = "labelCell">{$smarty.const._SURVEYS}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.surveys.html}</td></tr>
*}
                                                <tr><td class = "labelCell">{$smarty.const._LESSONDATAFILE}:&nbsp;</td>
                                                    <td>{$T_IMPORT_LESSON_FORM.file_upload.html}</td></tr>
                                                <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_FILESIZE}</b> {$smarty.const._KB}</td></tr>
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_IMPORT_LESSON_FORM.submit_import_lesson.html}</td></tr>
                                            </table>
                                        </form>
                                        </fieldset>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._IMPORTLESSON data = $smarty.capture.t_import_lesson_code image = '32x32/import2.png'  main_options = $T_TABLE_OPTIONS}
                                {elseif isset($T_OP) && $T_OP == 'export_lesson'}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_settings='|cat:$smarty.get.lesson_settings|cat:'&op=export_lesson">'|cat:$smarty.const._EXPORTLESSON|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
                                                                {capture name = 't_export_lesson_code'}
                                                                    <fieldset>
                                                                    <legend>{$smarty.const._EXPORTLESSON}</legend>
                                                                    {$T_EXPORT_LESSON_FORM.javascript}
                                                                    <form {$T_EXPORT_LESSON_FORM.attributes}>
                                                                        {$T_EXPORT_LESSON_FORM.hidden}
                                                                        <table class = "formElements" style = "margin-left:0px">
{*
                                                                            <tr><td colspan = "100%">{$smarty.const._CHOOSEWHATTOEXPORT}</td></tr>
                                                                            <tr><td colspan = "100%">&nbsp;</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._PERIODS}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.periods.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._ANNOUNCEMENTS}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.news.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.comments.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._ACCESSRULES}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.rules.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._CALENDAR}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.calendar.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._GLOSSARY}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.glossary.html}</td></tr>
                                                                            <tr><td class = "labelCell">{$smarty.const._SURVEYS}:&nbsp;</td>
                                                                                <td>{$T_EXPORT_LESSON_FORM.surveys.html}</td></tr>
                                                                            <tr><td colspan = "100%">&nbsp;</td></tr>
*}
                                                                    {if $T_NEW_EXPORTED_FILE}
                                                                            <tr><td class = "labelCell">{$smarty.const._DOWNLOADEXPORTED}:&nbsp;</td>
                                                                                <td class = "elementCell"><a href = "view_file.php?file={$T_NEW_EXPORTED_FILE.id}&action=download">{$T_NEW_EXPORTED_FILE.name}</a> ({$T_NEW_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_NEW_EXPORTED_FILE.timestamp}#)</td></tr>
                                                                    {elseif $T_EXPORTED_FILE}
                                                                            <tr><td class = "labelCell">{$smarty.const._EXISTINGFILE}:&nbsp;</td>
                                                                                <td class = "elementCell"><a href = "view_file.php?file={$T_EXPORTED_FILE.id}&action=download">{$T_EXPORTED_FILE.name}</a> ({$T_EXPORTED_FILE.size} {$smarty.const.KB}, #filter:timestamp-{$T_EXPORTED_FILE.timestamp}#)</td></tr>
                                                                    {/if}
                                                                            <tr><td class = "labelCell">{$smarty.const._CLICKTOEXPORT}:&nbsp;</td>
                                                                                <td class = "elementCell">{$T_EXPORT_LESSON_FORM.submit_export_lesson.html}</td></tr>
                                                                        </table>
                                                                    </form>
                                                                    </fieldset>
                                                                {/capture}
                                                                {eF_template_printInnerTable title = $smarty.const._EXPORTLESSON data = $smarty.capture.t_export_lesson_code image = '32x32/export1.png' main_options = $T_TABLE_OPTIONS}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=lessons&lesson_settings='|cat:$smarty.get.lesson_settings|cat:'">'|cat:$smarty.const._LESSONSETTINGS|cat:' &quot;'|cat:$T_CURRENT_LESSON->lesson.name|cat:'&quot;</a>'}
                                    {*moduleLessonSettings: Left options list in the Lesson settings page*}
                                                                {eF_template_printIconTable title=$smarty.const._LESSONOPTIONS columns = 4 links = $T_LESSON_SETTINGS image='32x32/book_blue_view.png' options = $T_TABLE_OPTIONS}
                                                                <script>
                                                                {literal}
                                                                    function activate(el, action) {
                                                                        var src = Element.down(el).src;
                                                                        src.match(/_gray/) ? url = 'administrator.php?ctg=lessons&lesson_settings={/literal}{$T_CURRENT_LESSON->lesson.id}{literal}&ajax=1&activate='+action : url = 'administrator.php?ctg=lessons&lesson_settings={/literal}{$T_CURRENT_LESSON->lesson.id}{literal}&ajax=1&deactivate='+action;
                                                                        Element.down(el).blur();
                                                                        Element.down(el).setAttribute('src', 'images/others/progress_big.gif');
                                                                        new Ajax.Request(url, {
                                                                                method:'get',
                                                                                asynchronous:true,
                                                                                onSuccess: function (transport) {
                                                                                    if (src.match(/_gray/)) {
                                                                                        Element.down(el).setAttribute('src', src.replace(/_gray/, ''));
                                                                                        el.setStyle({color:'inherit'});
                                                                                    } else {
                                                                                        Element.down(el).setAttribute('src', src.replace(/.png/, '_gray.png'));
                                                                                        el.setStyle({color:'gray'});
                                                                                    }
                                                                                    //parent.sideframe.location = parent.sideframe.location + '&sbctg=settings';
                                                                                }
                                                                            });
                                                                    }
                                                                {/literal}
                                                                </script>
                                {/if}
                        {else}
                                    {capture name = 't_lessons_code'}
                                                    <table border = "0">
                                                        <tr><td>
                                                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
                                                                <a href="administrator.php?ctg=lessons&add_lesson=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWLESSON}" alt="{$smarty.const._NEWLESSON}" border="0"></a></td><td><a href="administrator.php?ctg=lessons&add_lesson=1">{$smarty.const._NEWLESSON}</a>
                                                            {/if}
                                                        </td></tr>
                                                    </table>
<!--ajax:lessonsTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "administrator.php?ctg=lessons&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle" name = "direction_name">{$smarty.const._CATEGORY}</td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                            <td class = "topTitle centerAlign" name = "course_only">{$smarty.const._LESSONAVAILABLE}</td>
                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                                        {else}
                                                            <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                        {/if}
                                                            <td class = "topTitle centerAlign" name = "active">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                            <td class = "topTitle noSort centerAlign">{$smarty.const._STATISTICS}</td>
                                                        {/if}
                                                            <td class = "topTitle noSort centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        </tr>
                                        {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                        <tr id="row_{$lesson.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                                            <td id = "column_{$lesson.id}" class = "editLink">{$lesson.link}</td>
                                                            <td>{$lesson.direction_name}</td>
                                                            <td>{$lesson.languages_NAME}</td>
                                                            <td class = "centerAlign">
                                                        {if $lesson.course_only}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "setLessonAccess(this, '{$lesson.id}')"{/if}><img src = "images/16x16/books.png" alt = "{$smarty.const._COURSEONLY}" title = "{$smarty.const._COURSEONLY}" border = "0"></a>
                                                        {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}onclick = "setLessonAccess(this, '{$lesson.id}')"{/if}><img src = "images/16x16/book_open.png" alt = "{$smarty.const._DIRECTLY}" title = "{$smarty.const._DIRECTLY}" border = "0"></a>
                                                        {/if}
                                                            </td>
                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td align="center">{if $lesson.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$lesson.skills_offered}{/if}</td>
                                                        {else}
                                                            <td align="center">{if $lesson.price == 0}{$smarty.const._FREE}{else}{$lesson.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                        {/if}
                                                            <td style = "text-align:center">
                                                        {if $lesson.active == 1}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "activate(this, '{$lesson.id}')"{/if}><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                        {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}onclick = "activate(this, '{$lesson.id}')"{/if}><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                        {/if}
                                                            </td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                            <td align = "center"><a href="administrator.php?ctg=statistics&option=lesson&tab=overall&sel_lesson={$lesson.id}"><img src = "images/16x16/chart.png" alt = "{$smarty.const._STATISTICS}" title = "{$smarty.const._STATISTICS}" border = "0"></a></td>
                                                        {/if}
                                                            <td class = "centerAlign" style = "white-space:nowrap">
                                                                <a href = "administrator.php?ctg=lessons&lesson_settings={$lesson.id}"><img border = "0" src = "images/16x16/gear.png" title = "{$smarty.const._LESSONSETTINGS}" alt = "{$smarty.const._LESSONSETTINGS}" /></a>
                                                                <a href = "administrator.php?ctg=lessons&lesson_info={$lesson.id}"><img border = "0" src = "images/16x16/about.png" title = "{$smarty.const._LESSONINFORMATION}" alt = "{$smarty.const._LESSONINFORMATION}" /></a>
                                                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons== 'change'}
                                                                <a href = "administrator.php?ctg=lessons&edit_lesson={$lesson.id}"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                <a href = "administrator.php?ctg=lessons&delete_lesson={$lesson.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETELESSON}')"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                            {/if}
                                                            </td>
                                                        </tr>

                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
<!--/ajax:lessonsTable-->
                                                    <script>
                                                    {literal}
                                                    function activate(el, lesson) {
                                                        Element.extend(el);
                                                        if (el.down().src.match('red')) {
                                                            url = 'administrator.php?ctg=lessons&activate_lesson='+lesson;
                                                            newSource = 'images/16x16/trafficlight_green.png';
                                                        } else {
                                                            url = 'administrator.php?ctg=lessons&deactivate_lesson='+lesson;
                                                            newSource = 'images/16x16/trafficlight_red.png';
                                                        }

                                                        var img = new Element('img', {id: 'img_'+lesson, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        el.up().insert(img);
                                                        el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                        new Ajax.Request(url, {
                                                            method:'get',
                                                            asynchronous:true,
                                                            onFailure: function (transport) {
                                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                            },
                                                            onSuccess: function (transport) {
                                                                img.hide();
                                                                el.down().src = newSource;
                                                                new Effect.Appear(el.down(), {queue:'end'});

                                                                if (el.down().src.match('green')) {
                                                                    // When activated
                                                                    var cName = $('row_'+lesson).className.split(" ");
                                                                    $('row_'+lesson).className = cName[0];
                                                                } else {
                                                                    $('row_'+lesson).className += " deactivatedTableElement";
                                                                }

                                                                }
                                                            });
                                                    }

                                                    function setLessonAccess(el, lesson) {
                                                        Element.extend(el);
                                                        if (el.down().src.match('book_open')) {
                                                            url = 'administrator.php?ctg=lessons&set_course_only='+lesson;
                                                            newSource = 'images/16x16/books.png';
                                                            newTitle  = '{/literal}{$smarty.const._COURSEONLY}{literal}';
                                                        } else {
                                                            url = 'administrator.php?ctg=lessons&unset_course_only='+lesson;
                                                            newSource = 'images/16x16/book_open.png';
                                                            newTitle  = '{/literal}{$smarty.const._DIRECTLY}{literal}';
                                                        }

                                                        var img = new Element('img', {id: 'img_'+lesson, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        el.up().insert(img);
                                                        //el.down().src = 'images/16x16/book_open.png';
                                                        new Ajax.Request(url, {
                                                            method:'get',
                                                            asynchronous:true,
                                                            onFailure: function (transport) {
                                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                            },
                                                            onSuccess: function (transport) {
                                                                img.hide();
                                                                el.down().src = newSource;
                                                                el.down().alt = el.down().title = newTitle;
                                                                new Effect.Appear(el.down(), {queue:'end'});
                                                                }
                                                            });
                                                    }
                                                    {/literal}
                                                    </script>
                                    {/capture}

                                        {eF_template_printInnerTable title = $smarty.const._UPDATELESSONS data = $smarty.capture.t_lessons_code image = '/32x32/board.png'}
            {/if}
                                        </td></tr>
        {/capture}
    {/if}
{/if}
{if (isset($T_CTG) && $T_CTG == 'tests')}
        {*moduleTests: Print the Tests page*}
        {capture name = "moduleTests"}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:'<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests">'|cat:$smarty.const._SKILLGAPTESTS|cat:'</a>'}
            {if $smarty.get.edit_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_test=`$smarty.get.edit_test`'>`$smarty.const._EDITSKILLGAPTEST`<span class='innerTableName'>&nbsp;&quot;`$T_CURRENT_TEST.name`&quot;</span></a>"}
            {elseif $smarty.get.add_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_test=1'>`$smarty.const._ADDSKILLGAPTEST`</a>"}
            {elseif $smarty.get.edit_question}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&edit_question=`$smarty.get.edit_question`&question_type=`$smarty.get.question_type`&lessonId=`$smarty.get.lessonId`'>`$smarty.const._EDITQUESTION`</a>"}
            {elseif $smarty.get.add_question}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&add_question=1&question_type=`$smarty.get.question_type`'>`$smarty.const._ADDQUESTION`</a>"}
            {elseif $smarty.get.test_results}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&test_results='|cat:$smarty.get.test_results|cat:'">'|cat:$smarty.const._SKILLGAPTESTRESULTS|cat:'</a>'}
            {elseif $smarty.get.show_test}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_test='|cat:$smarty.get.show_test|cat:'">'|cat:$smarty.const._PREVIEW|cat:'</a>'}
            {elseif $smarty.get.show_solved_test}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&test_results=`$T_TEST_DATA->completedTest.testsId`'>`$smarty.const._SKILLGAPTESTRESULTS`</a>"}
                {if !$smarty.get.test_analysis}
                    {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&show_solved_test=`$T_TEST_DATA->completedTest.id`'>`$smarty.const._VIEWSOLVEDTEST`: &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._BYUSER`: `$T_TEST_DATA->completedTest.login`</a>"}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=tests&show_solved_test='|cat:$smarty.get.show_solved_test|cat:'&test_analysis='|cat:$smarty.get.test_analysis|cat:'&user='|cat:$smarty.get.user|cat:'">'|cat:$smarty.const._USERRESULTS|cat:'</a>'}
                {/if}
            {elseif $smarty.get.solved_tests}
                {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=tests&solved_tests=1'>`$smarty.const._SHOWALLSOLVEDSKILLGAPTESTS`</a>"}
            {/if}

            <tr><td class = "moduleTests">
                {include file = "includes/module_tests.tpl"}

            </td></tr>
        {/capture}
{/if}

{if (isset($T_CTG) && $T_CTG == 'directions')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions">'|cat:$smarty.const._CATEGORIES|cat:'</a>'}
{*moduleLessons: The directions list*}
    {capture name = "moduleDirections"}
                            <tr><td class = "moduleCell">
                                {if $smarty.get.add_direction || $smarty.get.edit_direction}
                                    {if $smarty.get.add_direction}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions&add_direction=1">'|cat:$smarty.const._NEWCATEGORY|cat:'</a>'}
                                    {else}
                                        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=directions&edit_direction='|cat:$smarty.get.edit_direction|cat:'">'|cat:$smarty.const._EDITCATEGORY|cat:'<span class="innerTableName">&nbsp;&quot;'|cat:$T_DIRECTIONS_FORM.name.value|cat:'&quot;</span></a>'}
                                    {/if}
                                     {capture name = 't_direction_code'}

                                            <div class = "tabber">
                                                <div class = "tabbertab" title = "{$smarty.const._CATEGORYSETTINGS}">
                                                    {$T_DIRECTIONS_FORM.javascript}
                                                    <form {$T_DIRECTIONS_FORM.attributes}>
                                                        {$T_DIRECTIONS_FORM.hidden}
                                                        <table class = "formElements">
                                                            <tr><td class = "labelCell">{$T_DIRECTIONS_FORM.name.label}:&nbsp;</td>
                                                                <td>{$T_DIRECTIONS_FORM.name.html}</td></tr>
                                                            {if $T_DIRECTIONS_FORM.name.error}<tr><td></td><td class = "formError">{$T_DIRECTIONS_FORM.name.error}</td></tr>{/if}
                                                             <tr><td class = "labelCell">{$T_DIRECTIONS_FORM.parent_direction_ID.label}:&nbsp;</td>
                                                                <td>{$T_DIRECTIONS_FORM.parent_direction_ID.html}</td></tr>
                                                            {if $T_DIRECTIONS_FORM.parent_direction_ID.error}<tr><td></td><td class = "formError">{$T_DIRECTIONS_FORM.parent_direction_ID.error}</td></tr>{/if}

                                                            <tr><td class = "labelCell">{$T_DIRECTIONS_FORM.active.label}:&nbsp;</td>
                                                                <td>{$T_DIRECTIONS_FORM.active.html}</td></tr>
                                                            {if $T_DIRECTIONS_FORM.active.error}<tr><td></td><td class = "formError">{$T_DIRECTIONS_FORM.active.error}</td></tr>{/if}

                                                            <tr><td>&nbsp;</td></tr>
                                                            <tr><td></td><td>
                                                                    {$T_DIRECTIONS_FORM.submit_direction.html}</td></tr>
                                                        </table>
                                                    </form>
                                                </div>


                                 {if $smarty.get.edit_direction}
                                    {capture name = 't_lessons_to_directions_code'}
<!--ajax:lessonsTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "administrator.php?ctg=directions&edit_direction={$smarty.get.edit_direction}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                            <td class = "topTitle centerAlign" >{$smarty.const._SELECT}</td>
                                                        </tr>
                                        {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                                            <td>{$lesson.name}</td>
                                                            <td>{$lesson.languages_NAME}</td>
                                                            <td class = "centerAlign">
                                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <select name = "directions" id = "{$lesson.id}" onchange = "ajaxPost('{$lesson.id}', this);">
                                                {foreach name = 'directions_list' key = "key" item = "item" from = $T_DIRECTIONS_PATHS}
                                                                    <option value = "{$key}" {if $lesson.directions_ID == $key}selected{/if}>{$item}</option>
                                                {/foreach}
                                                                </select>
                                            {else}
                                                                {$T_DIRECTIONS_PATHS[$lesson.directions_ID]}
                                            {/if}
                                                            </td>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
<!--/ajax:lessonsTable-->

                                {literal}
                                <script>
                                function ajaxPost(id, el, table_id) {
                                    Element.extend(el);
                                    var url    =  'administrator.php?ctg=directions&edit_direction={/literal}{$smarty.get.edit_direction}{literal}&postAjaxRequest=1&id='+id+'&directions_ID='+$(id).options[$(id).options.selectedIndex].value;
                                    var img_id = 'img_' + id;

                                    var position = eF_js_findPos(el);
                                    var img      = Element.extend(document.createElement("img"));

                                    img.style.position = 'absolute';
                                    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                    img.setAttribute("id", img_id);
                                    img.setAttribute('src', 'images/others/progress1.gif');

                                    el.parentNode.appendChild(img);

                                    new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            onFailure: function (transport) {
                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                new Effect.Appear(img_id);
                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                            },
                                            onSuccess: function (transport) {
                                                img.style.display = 'none';
                                                img.setAttribute('src', 'images/16x16/check.png');
                                                new Effect.Appear(img_id);
                                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                }
                                        });
                                }
                                </script>
                                {/literal}

                                    {/capture}
                                            <div class="tabbertab {if ($smarty.get.tab == 'direction_lessons') } tabbertabdefault {/if}">
                                                <h3>{$smarty.const._EDITLESSONSDIRECTION}</h3>
                                                {$smarty.capture.t_lessons_to_directions_code}
                                            </div>
                                {/if}
                                        </div>

            {/capture}
            {if $smarty.get.add_direction}
                {eF_template_printInnerTable title = $smarty.const._NEWDIRECTIONOPTIONS data = $smarty.capture.t_direction_code image = '/32x32/kdf.png'}
            {else}
                {eF_template_printInnerTable title ="`$smarty.const._DIRECTIONOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_DIRECTIONS_FORM.name.value`&quot;</span>" data = $smarty.capture.t_direction_code image = '/32x32/kdf.png'}
            {/if}

                                {else}

                                    {capture name = 't_directions_code'}
                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                    <table border = "0" >
                                                        <tr><td><a href = "{$smarty.server.PHP_SELF}?ctg=directions&add_direction=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWDIRECTION}" alt="{$smarty.const._NEWDIRECTION}"/ border="0"></a></td>
                                                            <td><a href = "{$smarty.server.PHP_SELF}?ctg=directions&add_direction=1">{$smarty.const._NEWDIRECTION}</a></td></tr>
                                                    </table>
                                        {/if}

                                                    <table border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "pathString">{$smarty.const._PARENTDIRECTIONS}</td>
                                                            <td class = "topTitle centerAlign" name = "lessons">{$smarty.const._LESSONS}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                        </tr>
                                        {foreach name = 'directions_list' key = 'key' item = 'direction' from = $T_DIRECTIONS_DATA}
                                                        <tr id="row_{$direction.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$direction.active}deactivatedTableElement{/if}">
                                                            <td><a href = "{$smarty.server.PHP_SELF}?ctg=directions&edit_direction={$direction.id}" class = "editLink"><span id="column_{$direction.id}" {if !$direction.active}style="color:red"{/if}>{$direction.name}</span></a></td>
                                                            <td>{$direction.pathString}</td>
                                                            <td class = "centerAlign">{$direction.lessons}</td>
                                                            <td class = "centerAlign">

                                                {if $direction.active == 1}
                                                    {if $direction.lessons > 0}
                                                                <a href = "javascript:void(0)" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "alert('{$smarty.const._YOUCANNOTDEACTIVATEANONEMPTYDIRECTION}')"{/if}><img src = "images/16x16/trafficlight_green_gray.png" alt = "{$smarty.const._YOUCANNOTDEACTIVATEANONEMPTYDIRECTION}" title = "{$smarty.const._YOUCANNOTDEACTIVATEANONEMPTYDIRECTION}" border = "0"></a>
                                                    {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "activate(this, '{$direction.id}')"{/if}><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                    {/if}
                                                {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "activate(this, '{$direction.id}')"{/if}><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                {/if}
                                                            </td>
                                                {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                            <td class = "centerAlign">
                                                                <a href = "{$smarty.server.PHP_SELF}?ctg=directions&edit_direction={$direction.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                            {if $direction.lessons > 0}
                                                                <a href = "javascript:void(0)" onclick = "alert('{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}" alt = "{$smarty.const._YOUCANNOTDELETEANONEMPTYDIRECTION}" /></a>
                                                            {else}
                                                                <a href = "administrator.php?ctg=directions&delete_direction={$direction.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEDIRECTION}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                            {/if}
                                                            </td>
                                                {/if}
                                                    </tr>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
                                                    <script>
                                                    {literal}
                                                    function activate(el, direction) {
                                                        Element.extend(el);
                                                        if (el.down().src.match('red')) {
                                                            url = 'administrator.php?ctg=directions&activate_direction='+direction;
                                                            newSource = 'images/16x16/trafficlight_green.png';
                                                        } else {
                                                            url = 'administrator.php?ctg=directions&deactivate_direction='+direction;
                                                            newSource = 'images/16x16/trafficlight_red.png';
                                                        }

                                                        var img = new Element('img', {id: 'img_'+direction, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        el.up().insert(img);
                                                        el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                        new Ajax.Request(url, {
                                                            method:'get',
                                                            asynchronous:true,
                                                            onFailure: function (transport) {
                                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                            },
                                                            onSuccess: function (transport) {
                                                                img.hide();
                                                                el.down().src = newSource;
                                                                new Effect.Appear(el.down(), {queue:'end'});

                                                                if (el.down().src.match('green')) {
                                                                    // When activated
                                                                    var cName = $('row_'+direction).className.split(" ");
                                                                    $('row_'+direction).className = cName[0];
                                                                    $('column_'+direction).setStyle({color:'green'});
                                                                } else {

                                                                    $('row_'+direction).className += " deactivatedTableElement";
                                                                    $('column_'+direction).setStyle({color:'red'});
                                                                }
                                                                }
                                                            });
                                                    }
                                                    {/literal}
                                                                        </script>
                                    {/capture}

                                    {eF_template_printInnerTable title = $smarty.const._UPDATEDIRECTIONS data = $smarty.capture.t_directions_code image = '/32x32/kdf.png'}
                                {/if}
                            </td></tr>
    {/capture}
{/if}

{if (isset($T_CTG) && $T_CTG == 'courses')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses">'|cat:$smarty.const._COURSES|cat:'</a>'}

    {*moduleCourses: The Courses list*}
        {capture name = "moduleCourses"}
                            <tr><td class = "moduleCell">
    {if $smarty.get.add_course || $smarty.get.edit_course}
                                {if $smarty.get.edit_course}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&edit_course='|cat:$smarty.get.edit_course|cat:'">'|cat:$smarty.const._EDITCOURSE|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_COURSE_FORM.name.value|cat:'&quot;</span></a>'}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&add_course=1">'|cat:$smarty.const._NEWCOURSE|cat:'</a>'}
                                {/if}
                                 {capture name = 't_course_code'}
                                        <div class = "tabber">
                                            <div class = "tabbertab">
                                                <h3>{$smarty.const._EDITCOURSE}</h3>
                                                    <table width = "100%">
                                                        <tr><td class = "topAlign" width = "50%">

                                                            {$T_COURSE_FORM.javascript}
                                                            <form {$T_COURSE_FORM.attributes}>
                                                            {$T_COURSE_FORM.hidden}
                                                            <table class = "formElements">
                                                            <tr><td class = "labelCell">{$T_COURSE_FORM.name.label}:&nbsp;</td>
                                                                <td>{$T_COURSE_FORM.name.html}</td></tr>
                                                                {if $T_COURSE_FORM.name.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.name.error}</td></tr>{/if}

                                                            <tr><td class = "labelCell">{$T_COURSE_FORM.directions_ID.label}:&nbsp;</td>
                                                                <td>{$T_COURSE_FORM.directions_ID.html}</td></tr>
                                                                {if $T_COURSE_FORM.directions_ID.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.directions_ID.error}</td></tr>{/if}

                                                            {if isset($T_COURSE_FORM.languages_NAME.label)}
                                                                <tr><td class = "labelCell">{$T_COURSE_FORM.languages_NAME.label}:&nbsp;</td>
                                                                <td>{$T_COURSE_FORM.languages_NAME.html}</td></tr>
                                                                {if $T_COURSE_FORM.languages_NAME.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.languages_NAME.error}</td></tr>{/if}
                                                            {/if}

                                                            {* MODULE HCD: The price should not appear *}
                                                            {if !$T_MODULE_HCD_INTERFACE}
                                                                <tr><td class = "labelCell">{$T_COURSE_FORM.price.label}:&nbsp;</td>
                                                                <td>{$T_COURSE_FORM.price.html} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}</td></tr>
                                                                {if $T_COURSE_FORM.price.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.price.error}</td></tr>{/if}
                                                            {/if}

                                                                <tr><td class = "labelCell">{$T_COURSE_FORM.active.label}:&nbsp;</td>
                                                                <td>{$T_COURSE_FORM.active.html}</td></tr>
                                                                {if $T_COURSE_FORM.active.error}<tr><td></td><td class = "formError">{$T_COURSE_FORM.active.error}</td></tr>{/if}

                                                                <tr><td colspan = "2">&nbsp;</td></tr>
                                                                <tr><td></td><td>{$T_COURSE_FORM.submit_course.html}</td></tr>
                                                            </table>
                                                            </form>
                                                        </td></tr>
                                                    </table>
                                            </div>


                                    {capture name = 't_lessons_to_courses_code'}
<!--ajax:lessonsTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_LESSONS_SIZE}" sortBy = "0" useAjax = "1" id = "lessonsTable" rowsPerPage = "20" url = "administrator.php?ctg=courses&edit_course={$smarty.get.edit_course}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle" name = "course_only">{$smarty.const._COURSEONLY}</td>
                                                            <td class = "topTitle" name = "languages_NAME">{$smarty.const._LANGUAGE}</td>
                                                            <td class = "topTitle" name = "directionsPath">{$smarty.const._DIRECTION}</td>
                                                            <td class = "topTitle centerAlign" name = "course_assigned" >{$smarty.const._SELECT}</td>
                                                        </tr>
                                        {foreach name = 'lessons_list2' key = 'key' item = 'lesson' from = $T_LESSONS_DATA}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$lesson.active}deactivatedTableElement{/if}">
                                                            <td>{$lesson.name}</td>
                                                            <td>{if $lesson.course_only}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                                                            <td>{$lesson.languages_NAME}</td>
                                                            <td>{$lesson.directionsPath}</td>
                                                            <td class = "centerAlign">
                                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <input type = "checkbox" id = "{$lesson.id}" onclick = "lessonsAjaxPost('{$lesson.id}', this);" {if $lesson.course_assigned == $lesson.id}checked{/if}>{if $lesson.course_assigned == $lesson.id}<span style = "display:none">checked</span>{/if} {*Span is for sorting here*}
                                                        {else}
                                                                {if $lesson.course_assigned == $lesson.id}<img src = "images/16x16/check2.png" alt = "{$smarty.const._COURSELESSON}" title = "{$smarty.const._COURSELESSON}"><span style = "display:none">checked</span>{/if}
                                                        {/if}
                                                            </td>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
<!--/ajax:lessonsTable-->
                                        {literal}
                                        <script>
                                        function ajaxPost(id, el, table_id) {
                                            Element.extend(el);
                                            //Since in the same page there are 2 ajax post lists, we create a "wrapper" which decides which one to call
                                            table_id == 'lessonsTable' ? lessonsAjaxPost(id, el, table_id) : usersAjaxPost(id, el, table_id);
                                        }
                                        function lessonsAjaxPost(id, el, table_id) {
                                            Element.extend(el);
                                            var baseUrl =  'administrator.php?ctg=courses&edit_course={/literal}{$smarty.get.edit_course}{literal}&postAjaxRequest=lessons';
                                            if (id) {
                                                var checked  = $(id).checked;
                                                var url      = baseUrl + '&id='+id;
                                                var img_id   = 'img_'+id;
                                            } else if (table_id && table_id == 'lessonsTable') {
                                                el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                                                var img_id   = 'img_selectAll';
                                            }

                                            var position = eF_js_findPos(el);
                                            var img      = Element.extend(document.createElement("img"));

                                            img.style.position = 'absolute';
                                            img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                            img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                            img.setAttribute("id", img_id);
                                            img.setAttribute('src', 'images/others/progress1.gif');

                                            el.parentNode.appendChild(img);

                                            new Ajax.Request(url, {
                                                    method:'get',
                                                    asynchronous:true,
                                                    onFailure: function (transport) {
                                                        img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                        new Effect.Appear(img_id);
                                                        window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                    },
                                                    onSuccess: function (transport) {
                                                        img.hide();
                                                        img.setAttribute('src', 'images/16x16/check.png');
                                                        new Effect.Appear(img_id);
                                                        window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                        }
                                                });
                                        }
                                        </script>
                                        {/literal}

                                    {/capture}
                                {if $smarty.get.edit_course}
                                <div class="tabbertab {if $smarty.get.tab == 'lessons'} tabbertabdefault {/if}">
                                        <h3>{$smarty.const._EDITLESSONSCOURSE}</h3>
                                    {$smarty.capture.t_lessons_to_courses_code}
                                </div>
                                {/if}


                                {capture name = 't_users_to_courses_code'}
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle" name = "role">{$smarty.const._USERROLE}</td>
                                                            {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                            {/if}
                                                            <td class = "topTitle centerAlign" name = "basic_user_type">{$smarty.const._CHECK}</td>
                                                        </tr>
                                {foreach name = 'users_to_lessons_list' key = 'login' item = 'user' from = $T_ALL_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>{$user.login}</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>
                                    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <select name="type_{$user.login}" id = "type_{$user.login}" onchange = "$('checked_{$user.login}').checked=true;usersAjaxPost('{$user.login}', this);">
                                        {foreach name = 'roles_list' key = 'role_key' item = 'role_item' from = $T_ROLES}
                                                                    <option value="{$role_key}" {if !$user.role && $user.basic_user_type == $role_key}selected{elseif ($user.role == $role_key)}selected{/if} {if $user.user_types_ID == $role_key || $user.basic_user_type == $role_key}style = "font-weight:bold"{/if}>{$role_item}</option>
                                        {/foreach}
                                                                </select>
                                    {else}
                                                                {$T_ROLES[$user.user_type]}
                                    {/if}
                                                            </td>
                                                            {if $T_MODULE_HCD_INTERFACE}
                                                            <td align="center">
                                                                <table>
                                                                    <tr><td width="45%">
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&add_evaluation=1">
                                                                            <img src="images/16x16/edit.png" title="{$smarty.const._NEWEVALUATION}" alt="{$smarty.const._NEWEVALUATION}"/ border="0">
                                                                        </a>
                                                                        </td>
                                                                        <td width="45%">
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$user.login}&tab=evaluations">
                                                                            <img src="images/16x16/view.png" title="{$smarty.const._SHOWEVALUATIONS}" alt="{$smarty.const._SHOWEVALUATIONS}"/ border="0">
                                                                        </a>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            {/if}
                                                            <td class = "centerAlign">
                                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" name = "checked_{$login}" id = "checked_{$login}" onclick = "usersAjaxPost('{$login}', this);" {if in_array($login, $T_COURSE_USERS, true)}checked = "checked"{/if} />{if in_array($login, $T_COURSE_USERS, true)}<span style = "display:none">checked</span>{/if}
                                                        {else}
                                                                {if in_array($login, $T_COURSE_USERS, true)}<img src = "images/16x16/check2.png" alt = "{$smarty.const._COURSEUSER}" title = "{$smarty.const._COURSEUSER}"><span style = "display:none">checked</span>{/if}
                                                        {/if}
                                                            </td>
                                                    </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                </table>
<!--/ajax:usersTable-->

                                {literal}
                                <script>
                                function ajaxPost(id, el, table_id) {
                                    Element.extend(el);
                                    //Since in the same page there are 2 ajax post lists, we create a "wrapper" which decides which one to call
                                    table_id == 'skillsTable' ? ajaxCourseSkillUserPost(1, id, el, table_id) : usersAjaxPost(id, el, table_id);
                                }

                                function usersAjaxPost(login, el, table_id) {
                                    Element.extend(el);
                                    var baseUrl =  'administrator.php?ctg=courses&edit_course={/literal}{$smarty.get.edit_course}{literal}&postAjaxRequest=users';
                                    if (login) {
                                        var userType = $('type_'+login).options[$('type_'+login).selectedIndex].value;
                                        var checked  = $('checked_'+login).checked;
                                        var url      = baseUrl + '&login='+login+'&user_type='+userType;
                                        var img_id   = 'img_'+login;
                                    } else if (table_id && table_id == 'usersTable') {
                                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                                        var img_id   = 'img_selectAll';
                                    }

                                    var position = eF_js_findPos(el);
                                    var img      = Element.extend(document.createElement("img"));

                                    img.style.position = 'absolute';
                                    img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                    img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                    img.setAttribute("id", img_id);
                                    img.setAttribute('src', 'images/others/progress1.gif');

                                    el.parentNode.appendChild(img);

                                        new Ajax.Request(url, {
                                                method:'get',
                                                asynchronous:true,
                                                onFailure: function (transport) {
                                                    img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                    new Effect.Appear(img_id);
                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                },
                                                onSuccess: function (transport) {
                                                    img.hide();
                                                    img.setAttribute('src', 'images/16x16/check.png');
                                                    new Effect.Appear(img_id);
                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                    }
                                            });
                                }
                                </script>
                                {/literal}

                                {/capture}

                                {if $smarty.get.edit_course}
                                <div class="tabbertab {if $smarty.get.tab == 'users' } tabbertabdefault {/if}">
                                    <h3>{$smarty.const._EDITUSERSCOURSE}</h3>
                                    {$smarty.capture.t_users_to_courses_code}
                                </div>
                                {/if}


                                {* MODULE HCD: Create tab with all skills -  the skills offered by this course are to be selected *}
                                {if $T_MODULE_HCD_INTERFACE && $smarty.get.edit_course}
                                {*  ****************************************************
                                    This is the form that contains the skills offered by the seminar
                                    **************************************************** *}
                                    {capture name = 't_course_skills'}
                                        {literal}
                                            <script>
                                                function show_hide_spec(i)
                                                {
                                                    var spec = document.getElementById("spec_skill_" + i);
                                                    if (spec.style.visibility == "hidden")
                                                        spec.style.visibility = "visible";
                                                    else
                                                        spec.style.visibility = "hidden";
                                                }
                                            </script>
                                        {/literal}
                                        <form method="post" action="{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&tab=skills">
                                            {if $smarty.session.s_type == "administrator"}
                                            <table>
                                                <tr>
                                                    <td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWSKILL}" alt="{$smarty.const._NEWSKILL}"/ border="0"></a></td><td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&add_skill=1">{$smarty.const._NEWSKILL}</a></td>
                                                </tr>
                                            </table>
                                            {/if}

<!--ajax:skillsTable-->
                                            <table style = "width:100%" class = "sortedTable" size = "{$T_SKILLS_SIZE}" sortBy = "0" id = "skillsTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=courses&edit_course={$smarty.get.edit_course}&">
                                                <tr class = "topTitle">
                                                    <td class = "topTitle" name="description" width="35%">{$smarty.const._SKILL}</td>
                                                    <td class = "topTitle" name="specification" width="*">{$smarty.const._SPECIFICATION}</td>
                                                    <td class = "topTitle noSort centerAlign" name="skill_ID" idth="5%">{$smarty.const._CHECK}</td>
                                                </tr>

                                        {if isset($T_SKILLS)}
                                            {foreach name = 'skill_list' key = 'key' item = 'skill' from = $T_SKILLS}
                                                <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                    <td><a href="{$smarty.server.PHP_SELF}?ctg=module_hcd&op=skills&edit_skill={$skill.skill_ID}">{$skill.description}</a></td>
                                                    <td><input class = "inputText" width = "*" type="text" name="spec_skill_{$skill.skill_ID}"  id="spec_skill_{$skill.skill_ID}" onchange="ajaxCourseSkillUserPost(2,'{$skill.skill_ID}', this);" value="{$skill.specification}"{if $skill.courses_ID != $smarty.get.edit_course} style="visibility:hidden" {/if}></td>
                                                    <td class = "centerAlign"><input class = "inputCheckBox" type = "checkbox" name = "{$skill.skill_ID}" onclick="javascript:show_hide_spec({$skill.skill_ID});ajaxCourseSkillUserPost(1,'{$skill.skill_ID}', this);" {if $skill.courses_ID == $smarty.get.edit_course} checked {/if} ></td>
                                                </tr>
                                            {/foreach}
                                            </table>
<!--/ajax:skillsTable-->
                                        {else}
                                                <tr><td colspan = 3>
                                                    <table width = "100%">
                                                        <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign">{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
                                                    </table>
                                                    </td>
                                                </tr>
                                            </table>
<!--/ajax:skillsTable-->
                                        {/if}
                                        </form>
                                    {/capture}

            {* Script for posting ajax requests regarding skill to employees assignments *}
            {literal}
            <script>
            // type: 1 - inserting/deleting the skill to an employee | 2 - changing the specification
            // id: the users_login of the employee to get the skill
            // el: the element of the form corresponding to that skill/course
            // table_id: the id of the ajax-enabled table
            function ajaxCourseSkillUserPost(type, id, el, table_id) {
                Element.extend(el);
                var baseUrl =  '{/literal}{$smarty.session.s_type}{literal}.php?ctg=courses&edit_course={/literal}{$smarty.get.edit_course}{literal}&postAjaxRequest=1';
                if (type == 1) {
                    if (id) {
                        var url = baseUrl + '&add_skill=' + id + '&insert='+el.checked + '&specification='+document.getElementById('spec_skill_'+id).value;
                        var img_id   = 'img_'+ id;
                    } else if (table_id && table_id == 'skillsTable') {
                        el.checked ? url = baseUrl + '&addAll=1' : url = baseUrl + '&removeAll=1';
                        url += '&add_skill=1';
                        var img_id   = 'img_selectAll';
                    }
                } else if (type == 2) {
                    if (id) {
                        var url = baseUrl + '&add_skill=' + id + '&insert=true&specification='+el.value;
                        var img_id   = 'img_'+ id;
                    }
                } else {
                    return false;
                }

                var position = eF_js_findPos(el);
                var img      = Element.extend(document.createElement("img"));

                img.style.position = 'absolute';
                img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                img.setAttribute("id", img_id);
                img.setAttribute('src', 'images/others/progress1.gif');

                el.parentNode.appendChild(img);

                  new Ajax.Request(url, {
                            method:'get',
                            asynchronous:true,
                            onSuccess: function (transport) {
                                img.style.display = 'none';
                                img.setAttribute('src', 'images/16x16/check.png');
                                new Effect.Appear(img_id);
                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                }
                        });

            }
            </script>
            {/literal}

                                    <script>var myform = "skills_to_course";</script>
                                    <div class="tabbertab {if (isset($smarty.post.course_skills) || ($smarty.get.tab == "skills"))} tabbertabdefault {/if}">
                                        <h3>{$smarty.const._SKILLSOFFERED}</h3>
                                        {$smarty.capture.t_course_skills}
                                    </div>
                                {/if}
                                </div>
                                {/capture}
            {if $smarty.get.add_course}
                {eF_template_printInnerTable title = $smarty.const._NEWCOURSEOPTIONS data = $smarty.capture.t_course_code image = '/32x32/books.png'}
            {else}
                {eF_template_printInnerTable title ="`$smarty.const._COURSEOPTIONSFOR` <span class = 'innerTableName'>&quot;`$T_COURSE_NAME`&quot;</span>" data = $smarty.capture.t_course_code image = '/32x32/books.png'}
            {/if}

                            {elseif $T_OP == course_info}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_info">'|cat:$smarty.const._INFORMATIONFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                {capture name = 't_course_info_code'}
                                    <fieldset>
                                        <legend>{$smarty.const._COURSEINFORMATION}</legend>
                                        {$T_COURSE_INFO_HTML}
                                    </fieldset>
                                    <fieldset>
                                        <legend>{$smarty.const._COURSEMETADATA}</legend>
                                        {$T_COURSE_METADATA_HTML}
                                    </fieldset>
                                {/capture}
                                {eF_template_printInnerTable title = $smarty.const._INFORMATIONFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;' data = $smarty.capture.t_course_info_code image = '/32x32/about.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_certificates'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_certificates">'|cat:$smarty.const._CERTIFICATESFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                {if $smarty.get.edit_user}
                                    {capture name = 't_course_user_progress'}
                                    <fieldset>
                                        <legend>{$smarty.const._LESSONSPROGRESS}</legend>
                                        <table width = "100%">
                                            <tr>
                                        {foreach name = 'lessons_list' item = "lesson" key = "id" from = $T_USER_PROGRESS.lesson_status}
                                                <td width = "50%">
                                                <table>
                                                    <tr><td colspan = "2" style = "font-weight:bold">{$lesson.lesson_name}</td></tr>
                                                    <tr><td>{$smarty.const._COMPLETED}:&nbsp;</td><td>{if $lesson.completed}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td></tr>
                                                    {if $lesson.score}<tr><td>{$smarty.const._SCORE}:&nbsp;</td><td>{$lesson.score}&nbsp;%</td></tr>{/if}
                                                    <tr><td>{$smarty.const._CONTENTDONE}:&nbsp;</td>
                                                        <td class = "progressCell" style = "vertical-align:top">
                                                            <span class = "progressNumber">{$lesson.overall_progress}%</span>
                                                            <span class = "progressBar" style = "width:{$lesson.percentage_done}px;">&nbsp;</span>
                                                        </td></tr>
                                                </table>
                                                </td>
                                            {if $smarty.foreach.lessons_list.iteration%2 == 0}</tr><tr>{/if}
                                        {/foreach}
                                            </tr>
                                        </table>
                                    </fieldset>
                                    <fieldset>
                                        <legend>{$smarty.const._COMPLETECOURSE}</legend>
                                        {$T_COMPLETE_LESSON_FORM.javascript}
                                        <form {$T_COMPLETE_COURSE_FORM.attributes}>
                                            {$T_COMPLETE_COURSE_FORM.hidden}
                                            <table class = "formElements">
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.completed.label}&nbsp;:</td>
                                                    <td class = "elementCell">{$T_COMPLETE_COURSE_FORM.completed.html}</td></tr>
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.score.label}&nbsp;:</td>
                                                    <td class = "elementCell">{$T_COMPLETE_COURSE_FORM.score.html}</td></tr>
                                                {if !$T_USER_PROGRESS.completed}<tr><td></td><td class = "infoCell">{$smarty.const._PROPOSEDSCOREISAVERAGELESSONSCORE}</td></tr>{/if}
                                                {if $T_COMPLETE_COURSE_FORM.score.error}<tr><td></td><td class = "formError">{$T_COMPLETE_COURSE_FORM.score.error}</td></tr>{/if}
                                                <tr><td class = "labelCell">{$T_COMPLETE_COURSE_FORM.comments.label}&nbsp;:</td>
                                                    <td class = "elementCell">{$T_COMPLETE_COURSE_FORM.comments.html}</td></tr>
                                                {if $T_COMPLETE_COURSE_FORM.comments.error}<tr><td></td><td class = "formError">{$T_COMPLETE_COURSE_FORM.comments.error}</td></tr>{/if}
                                                <tr><td colspan = "100%">&nbsp;</td></tr>
                                                <tr><td></td><td>{$T_COMPLETE_COURSE_FORM.submit_course_complete.html}</td></tr>
                                            </table>
                                        </form>
                                    </fieldset>
                                    {/capture}
                                    {eF_template_printInnerTable title = "`$T_USER_PROGRESS.name` `$T_USER_PROGRESS.surname`&#039s `$smarty.const._PROGRESS`" data = $smarty.capture.t_course_user_progress image = '32x32/books_preferences.png'}
                                {elseif $smarty.get.issue_certificate}

                                {else}
                                    {capture name = 't_course_certificates_code'}
                                                    {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                        <table>
                                                            <tr><td style = "padding-right:5px">
                                                                    <img src = "images/16x16/certificate_preferences.png" title = "{$smarty.const._FORMATCERTIFICATE}" alt = "{$smarty.const._FORMATCERTIFICATE}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=format_certificate" >
                                                                        {$smarty.const._FORMATCERTIFICATE}
                                                                    </a>
                                                                </td>
                                                                <td style = "padding-right:5px;border-left:1px solid black;padding-left:5px;">
                                                                    <img src = "images/16x16/book_green.png" title = "{$smarty.const._AUTOCOMPLETE}" alt = "{$smarty.const._AUTOCOMPLETE}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&auto_complete">
                                                                        {$smarty.const._AUTOCOMPLETE}: {if $T_CURRENT_COURSE->course.auto_complete}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
                                                                    </a>
                                                                </td>
                                                        {if $T_CURRENT_COURSE->course.auto_complete}
                                                                <td style = "padding-right:5px;border-left:1px solid black;padding-left:5px;">
                                                                    <img src = "images/16x16/certificate_refresh.png" title = "{$smarty.const._AUTOCERTIFICATES}" alt = "{$smarty.const._AUTOCERTIFICATES}" border = "0" style = "vertical-align:middle"/>
                                                                    <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&auto_certificate">
                                                                        {$smarty.const._AUTOMATICCERTIFICATES}: {if $T_CURRENT_COURSE->course.auto_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}
                                                                    </a>
                                                                </td>
                                                        {/if}
                                                            </tr>
                                                        </table>
                                                    {/if}
<!--ajax:usersTable-->
                                                        <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&">
                                                            <tr class = "topTitle">
                                                                <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                                <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                                <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                                <td class = "topTitle centerAlign" name = "conditions_passed">{$smarty.const._LESSONSCOMPLETED}</td>
                                                                <td class = "topTitle centerAlign" name = "completed" >{$smarty.const._COURSESTATUS}</td>
                                                                <td class = "topTitle centerAlign" name = "score">{$smarty.const._COURSESCORE}</td>
                                                                <td class = "topTitle centerAlign" name = "issued_certificate">{$smarty.const._CERTIFICATEISSUED}</td>
                                                                <td class = "topTitle centerAlign noSort">{$smarty.const._FUNCTIONS}</td>
                                                            </tr>
                                                {foreach name = 'users_progress_list' item = 'item' key = 'login' from = $T_USERS_PROGRESS}
                                                            <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$item.active}deactivatedTableElement{/if}">
                                                                <td>{$item.login}</td>
                                                                <td>{$item.name}</td>
                                                                <td>{$item.surname}</td>
                                                                <td style = "text-align:center">
                                                                    {$item.completed_lessons}/{$item.total_lessons}
                                                                </td>
                                                                <td style = "text-align:center">
                                                                    {if $item.completed}
                                                                        <img src = "images/16x16/check.png" title = "{$smarty.const._COMPLETED}" alt = "{$smarty.const._COMPLETED}" />
                                                                    {elseif $item.completed_lessons == $item.total_lessons}
                                                                        <img src = "images/16x16/contract.png" title = "{$smarty.const._LESSONSCOMPLETED}" alt = "{$smarty.const._LESSONSCOMPLETED}" />
                                                                    {else}
                                                                        <img src = "images/16x16/forbidden.png" title = "{$smarty.const._NOTCOMPLETED}" alt = "{$smarty.const._NOTCOMPLETED}" />
                                                                    {/if}
                                                                </td>
                                                                <td style = "text-align:center">{if $item.score}{$item.score}{/if}</td>
                                                                <td style = "text-align:center">{if $item.issued_certificate}{$smarty.const._YES}{else}{$smarty.const._NO}{/if}</td>
                                                                <td style = "text-align:center">{strip}
                                                                    {if $item.completed && $item.issued_certificate}
                                                                        {* Create a write evaluation link for this employee *}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&revoke_certificate={$item.login}" title = "{$smarty.const._REVOKECERTIFICATE}">
                                                                            <img src = "images/16x16/certificate_broken.png" title = "{$smarty.const._REVOKECERTIFICATE}" alt = "{$smarty.const._REVOKECERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                        <a href = "javascript:void(0)" onclick = "javascript:window.open('{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&export=rtf&user={$item.login}&course={$smarty.get.course}')" title = "{$smarty.const._VIEWCERTIFICATE}">
                                                                            <img src = "images/16x16/certificate_view.png" title = "{$smarty.const._VIEWCERTIFICATE}" alt = "{$smarty.const._VIEWCERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                    {elseif $item.completed}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&issue_certificate={$item.login}&popup=1" title = "{$smarty.const._ISSUECERTIFICATE}">
                                                                            <img src = "images/16x16/certificate.png" title = "{$smarty.const._ISSUECERTIFICATE}" alt = "{$smarty.const._ISSUECERTIFICATE}" border = "0"/>
                                                                        </a>&nbsp;
                                                                    {else}
                                                                        <img src = "images/16x16/certificate_gray.png" title = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" alt = "{$smarty.const._THEUSERHASNOTCOMPLETEDTHELESSON}" />&nbsp;
                                                                    {/if}
                                                                {/if}
                                                                        <a href = "{$smarty.server.PHP_SELF}?ctg=courses&course={$smarty.get.course}&op=course_certificates&edit_user={$item.login}&popup=1" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PROGRESS}', 2)" title = "{$smarty.const._VIEWUSERLESSONPROGRESS}">
                                                                            <img src = "images/16x16/clipboard.png" title = "{$smarty.const._VIEWUSERCOURSEPROGRESS}" alt = "{$smarty.const._VIEWUSERCOURSEPROGRESS}" border = "0"/>
                                                                        </a>
                                                                {/strip}
                                                                </td>
                                                            </tr>
                                                {foreachelse}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"} defaultRowHeight"><td colspan = "100%" class = "emptyCategory centerAlign">{$smarty.const._NOUSERDATAFOUND}</td></tr>
                                                {/foreach}
                                                    </table>
<!--/ajax:usersTable-->

                                    {/capture}
                                    {eF_template_printInnerTable title = "&quot;`$T_CURRENT_COURSE->course.name`&quot; `$smarty.const._CERTIFICATES`" data = $smarty.capture.t_course_certificates_code image = '/32x32/certificate.png'  main_options = $T_TABLE_OPTIONS}
                                {/if}
                            {elseif $T_OP == 'format_certificate'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=format_certificate">'|cat:$smarty.const._FORMATCERTIFICATEFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                                                                        {capture name = 't_certificate_code'}
                                                {$T_CERTIFICATE_FORM.javascript}
                                                <form {$T_CERTIFICATE_FORM.attributes}>
                                                    {$T_CERTIFICATE_FORM.hidden}
                                                    <table class = "formElements" style = "width:100%">
                                                        <tr><td class = "labelCell">{$T_CERTIFICATE_FORM.file_upload.label}:&nbsp;</td>
                                                            <td class = "elementCell" colspan="3">{$T_CERTIFICATE_FORM.file_upload.html}</td></tr>
                                                        <tr><td class = "labelCell">{$T_CERTIFICATE_FORM.existing_certificate.label}:&nbsp;</td>
                                                            <td class = "elementCell" colspan="1">{$T_CERTIFICATE_FORM.existing_certificate.html}&nbsp;</td>
                                                        </tr>
                                                        <tr><td colspan = "1"></td><td class = "infoCell" style = "white-space:normal;" colspan = "3">
                                                            {$smarty.const._CERTIFICATEINSTRUCTIONS}
                                                            </td>
                                                        </tr>
                                                        <tr><td></td>
                                                            <td colspan="3">{$T_CERTIFICATE_FORM.preview.html} &nbsp;
                                                                            {$T_CERTIFICATE_FORM.submit_certificate.html}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._FORMATCERTIFICATE data = $smarty.capture.t_certificate_code image = '/32x32/certificate_preferences.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_rules'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_rules">'|cat:$smarty.const._RULESFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}

                                    {capture name = 't_course_rules_code'}
                                            {if sizeof($T_COURSE_LESSONS) <= 1}
                                                        <table style = "width:100%">
                                                {foreach name = 'rules_list' item = 'item' key = 'key' from = $T_COURSE_LESSONS}
                                                            <tr class = "defaultRowHeight {if !$item.active}deactivatedTableElement{/if}">
                                                                <td id = "first_node_{$item.id}" style = "white-space:nowrap">{$item.name}</td>
                                                                <td id = "label_{$item.id}"      style = "white-space:nowrap;width:100%">&nbsp;{$smarty.const._GENERALLYAVAILABLE}&nbsp;</td>
                                                            </tr>
                                                {foreachelse}
                                                            <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                                {/foreach}
                                                        </table>
                                            {else}
                                                    {$T_COURSE_RULES_FORM.javascript}
                                                    <form {$T_COURSE_RULES_FORM.attributes}>
                                                        {$T_COURSE_RULES_FORM.hidden}
                                                        <table style = "max-width:100%">
                                                {foreach name = 'rules_list' item = 'item' key = 'key' from = $T_COURSE_LESSONS}
                                                            <tr class = "defaultRowHeight {if !$item.active}deactivatedTableElement{/if}">
                                                                <td id = "first_node_{$item.id}" style = "white-space:nowrap">{$item.name}</td>
                                                                <td id = "label_{$item.id}"      style = "white-space:nowrap;">&nbsp;{$smarty.const._GENERALLYAVAILABLE}&nbsp;</td>
                                                                <td id = "insert_node_{$item.id}"></td>
                                                                <td id = "last_node_{$item.id}"  style = "white-space:nowrap;text-align:right;vertical-align:bottom">
                                                                    &nbsp;<img src = "images/16x16/delete.png" title = "{$smarty.const._DELETECONDITION}" alt = "{$smarty.const._DELETECONDITION}" border = "0" id = "delete_icon_{$item.id}" onclick = "eF_js_removeCourseRule({$item.id})" style = "display:none"/>
                                                                    &nbsp;<img src = "images/16x16/add2.png"   title = "{$smarty.const._ADDCONDITION}"    alt = "{$smarty.const._ADDCONDITION}"    border = "0" id = "add_icon_{$item.id}" onclick = "eF_js_addCourseRule({$item.id})"/></td>
                                                            </tr>
                                                {/foreach}
                                                            <tr><td>&nbsp;</td></tr>
                                                            <tr><td></td><td class = "submitCell">{$T_COURSE_RULES_FORM.submit_rule.html}</td></tr>
                                                        </table>
                                                        </form>
                                                        {*Auxilliary select element, used below in building conditions*}
                                                        <select name = "condition" id = "conditions" style = "display:none;margin-left:5px;vertical-align:middle">
                                                            <option value = "and">{$smarty.const._AND}</option>
                                                            <option value = "or">{$smarty.const._OR}</option>
                                                        </select>

                                                        <script type = "text/javascript">
                                                        <!--
                                                        var lessonsIds   = new Array();
                                                        var lessonsNames = new Array();
                                                {foreach name = 'lessons_list' item = 'lesson' key = 'key' from = $T_COURSE_LESSONS}    {*Create javascript arrays*}
                                                            lessonsIds.push('{$lesson.id}');
                                                            lessonsNames.push('{$lesson.name}');
                                                {/foreach}

                                                        {literal}
                                                        Array.prototype.inArray = function (value)
                                                        {
                                                            var i;
                                                            for (i = 0; i < this.length; i++) {
                                                                if (this[i] === value) {
                                                                    return true;
                                                                }
                                                            }
                                                            return false;
                                                        };

                                                        function eF_js_removeCourseRule(id) {
                                                            var insertCell    = document.getElementById('insert_node_'  + id);
                                                            var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2);

                                                            if (numConditions > 0) {              //This means there are more than 1 conditions set
                                                                child = document.getElementById('lessonCell['+id+']['+numConditions+']');
                                                                child.parentNode.removeChild(child);

                                                                if (numConditions % 5 == 0) {       //This is for wrapping fields (since IE won't automatically wrap them)
                                                                    insertCell.removeChild(insertCell.lastChild);
                                                                }
                                                            }
                                                            if (numConditions == 1) {
                                                                document.getElementById('delete_icon_' + id).style.display = 'none';
                                                                document.getElementById('label_' + id).innerHTML = '&nbsp;{/literal}{$smarty.const._GENERALLYAVAILABLE}{literal}&nbsp;';    //Set the correct label
                                                          }
                                                        }

                                                        function eF_js_addCourseRule(id, selectedLesson, selectedCondition) {

                                                            if (!selectedLesson) {
                                                                selectedLesson = 0;
                                                            }
                                                            if (!selectedCondition) {
                                                                selectedCondition = 0;
                                                            }
                                                            var insertCell    = document.getElementById('insert_node_'  + id);
                                                            var numConditions = Math.round(insertCell.parentNode.getElementsByTagName('select').length / 2 + 1);

                                                            selectedValues = new Array();
                                                            for (var i = 1; i < numConditions; i++) {           //Calculate selected options, to remove them from the new selects
                                                                previous_select = document.getElementById('rules['+id+'][lesson]['+(i)+']');
                                                                selectedValues.push(previous_select.options[previous_select.options.selectedIndex].value);
                                                            }

                                                            if (selectedValues.length == lessonsIds.length - 1) {       //This means no more options are left. so return without doing anything
                                                                return false;
                                                            }

                                                            document.getElementById('label_' + id).innerHTML = '&nbsp;{/literal}{$smarty.const._DEPENDSON}{literal}:&nbsp;';    //Set the correct label

                                                            var lessonsSpan = document.createElement('span');
                                                            lessonsSpan.id = 'lessonCell['+id+']['+numConditions+']';
                                                            insertCell.appendChild(lessonsSpan);
                                                            if (numConditions % 5 == 0) {       //This is for wrapping fields (since IE won't automatically wrap them)
                                                                insertCell.appendChild(document.createElement('br'));
                                                            }
                                                            if (numConditions > 1) {              //This means there are other conditions set
                                                                var conditionsSelect           = document.getElementById('conditions').cloneNode(true);
                                                                conditionsSelect.id            = 'rules['+id+'][condition]['+numConditions+']';
                                                                conditionsSelect.name          = conditionsSelect.id;
                                                                conditionsSelect.selectedIndex = selectedCondition;
                                                                conditionsSelect.style.display = '';
                                                                lessonsSpan.appendChild(conditionsSelect);
                                                            }
                                                            //var lessonsSelect  = document.getElementById('lessons_list').cloneNode(true);    //This is the right way to do it, but IE won't cloneNode correctly (sic) so we need to build the select list from scratch
                                                            lessonsSelect = document.createElement('select');
                                                            lessonsSelect.style.marginLeft    = '5px';
                                                            lessonsSelect.style.verticalAlign = 'middle';
                                                            lessonsSelect.id   = 'rules['+id+'][lesson]['+numConditions+']';
                                                            lessonsSelect.name = lessonsSelect.id;

                                                            for (var i = 0; i < lessonsIds.length; i++) {
                                                                if (!selectedValues.inArray(lessonsIds[i])) {
                                                                    option           = document.createElement('option');
                                                                    option.value     = lessonsIds[i];
                                                                    option.innerHTML = lessonsNames[i];
                                                                    lessonsSelect.appendChild(option);
                                                                }
                                                            }

                                                            for (i = 0; i < lessonsSelect.options.length; i++) {                                      //Remove selected lesson from list
                                                                if (lessonsSelect.options[i].value == selectedLesson) {
                                                                    lessonsSelect.options[i].selected = true;
                                                                }
                                                            }
                                                            //In separate loop, because setting to null seems to reindex select options (in IE)
                                                            for (i = 0; i < lessonsSelect.options.length; i++) {                                      //Remove selected lesson from list
                                                                if (lessonsSelect.options[i].value == id) {
                                                                    lessonsSelect.options[i] = null;
                                                                }
                                                            }
                                                            lessonsSelect.style.display = '';
                                                            lessonsSpan.appendChild(lessonsSelect);

                                                            document.getElementById('delete_icon_' + id).style.display = '';

                                                        }//-->
                                                        {/literal}
                                                        </script>
                                                    {foreach name = 'course_rules_list' item = "rule" key = "key" from = $T_COURSE_RULES}
                                                        {foreach name = 'lesson_rules' item = "lesson_id" key = "index" from = $rule.lesson}
                                                            {if !$rule.condition.$index || $rule.condition.$index == 'and'}{assign var = 'condition' value = 0}{else}{assign var = 'condition' value = 1}{/if}
                                                            <script>eF_js_addCourseRule({$key}, {$lesson_id}, {$condition})</script>
                                                        {/foreach}
                                                    {/foreach}
                                                {/if}
                                            {/capture}
                                            {eF_template_printInnerTable title = $smarty.const._COURSERULES data = $smarty.capture.t_course_rules_code image = '/32x32/recycle.png'  main_options = $T_TABLE_OPTIONS}
                            {elseif $T_OP == 'course_order'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_order">'|cat:$smarty.const._ORDERFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}
                                    {capture name = 't_course_rules_code'}
                                        {if sizeof($T_COURSE_LESSONS) > 0}
                                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                            <fieldset>
                                                <legend>{$smarty.const._DRAGITEMSTOCHANGELESSONSORDER}</legend>
                                                <ul id = "dhtmlgoodies_lessons_tree" class = "dhtmlgoodies_tree">
                                                {foreach name = 'lessons_list' key = 'key' item = 'lesson'  from = $T_COURSE_LESSONS}
                                                    <li id = "dragtree_{$lesson.id}" noChildren = "true">
                                                        <a class = "{if !$lesson.active}deactivatedLinkElement{/if}" href = "#">&nbsp;{$lesson.name|eF_truncate:100}</a>
                                                    </li>
                                                {/foreach}
                                                </ul>
                                            </fieldset>
                                            <br/>
                                            <input id = "save_button" class = "flatButton" type="button" onclick="saveQuestionTree()" value="{$smarty.const._SAVECHANGES}">
                                            {else}
                                            <fieldset>
                                                <legend>{$smarty.const._LESSONSORDER}</legend>
                                                <table>
                                                {foreach name = 'lessons_list' key = 'key' item = 'lesson'  from = $T_COURSE_LESSONS}
                                                    <tr><td>{$lesson.name|eF_truncate:100}</td></tr>
                                                {/foreach}
                                                </table>
                                            </fieldset>
                                            <br/>
                                            {/if}
                                        {else}
                                            <table style = "width:100%"><tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr></table>
                                        {/if}
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_rules_code image = '/32x32/replace2.png'  main_options = $T_TABLE_OPTIONS}
                                    <script>
                                    {literal}
                                    function saveQuestionTree() {
                                        Element.extend($('save_button'));
                                        progressImg = new Element('img', {id:'progress_image', src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                        progressImg.style.top      = Element.positionedOffset($('save_button')).top + 1 + 'px';
                                        progressImg.style.left     = Element.positionedOffset($('save_button')).left + 6 + Element.getDimensions($('save_button')).width + 'px';
                                        document.body.appendChild(progressImg);
                                        //alert(treeObj.getNodeOrders());
                                        new Ajax.Request('administrator.php?ctg=courses&course={/literal}{$smarty.get.course}{literal}&op=course_order&ajax=1&order='+treeObj.getNodeOrders(), {
                                            method:'get',
                                            asynchronous:true,
                                            onFailure: function (transport) {
                                                alert(transport.responseText);
                                            },
                                            onSuccess: function (transport) {
                                                progressImg.hide();
                                                progressImg.setAttribute('src', 'images/16x16/check.png');
                                                new Effect.Appear('progress_image');
                                                window.setTimeout('Effect.Fade("progress_image")', 2500);
                                            }
                                        });

                                    }
                                    {/literal}
                                    </script>
                            {elseif $T_OP == 'course_scheduling'}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=courses&course='|cat:$smarty.get.course|cat:'&op=course_scheduling">'|cat:$smarty.const._SCHEDULINGFORCOURSE|cat:' &quot;'|cat:$T_CURRENT_COURSE->course.name|cat:'&quot;</a>'}

                                    {capture name = 't_course_scheduling_code'}
                                        {if sizeof($T_COURSE_LESSONS) > 0}
                                            <table>
                                            {foreach name = 'lessons_list' key = "id" item = "lesson" from = $T_COURSE_LESSONS}
                                                <tr {if !$lesson.active}class = "deactivatedTableElement"{/if}><td>{$lesson.name}:&nbsp;</td>
                                                    <td id = "schedule_dates_{$id}">{if $lesson.from_timestamp}{$smarty.const._FROM} #filter:timestamp_time_nosec-{$lesson.from_timestamp}# {$smarty.const._TO} #filter:timestamp_time_nosec-{$lesson.to_timestamp}#{else}<span class = "emptyCategory">{$smarty.const._NOSCHEDULESET}</span>{/if}&nbsp;</td>
                                                {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                    <td>
                                                        <span id = "add_schedule_link_{$id}">
                                                            <a href = "javascript:void(0)" onclick = "showEdit({$id})"><img src = "images/16x16/{if $lesson.from_timestamp}edit.png{else}add2.png{/if}" alt = "{$smarty.const._ADDSCHEDULE}" title = "{$smarty.const._ADDSCHEDULE}" style = "vertical-align:middle" border = "0"/></a>
                                                            <a href = "javascript:void(0)" onclick = "deleteSchedule(this, {$id})" {if !$lesson.from_timestamp}style = "display:none"{/if}><img src = "images/16x16/delete.png" alt = "{$smarty.const._DELETESCHEDULE}" title = "{$smarty.const._DELETESCHEDULE}" style = "vertical-align:middle" border = "0"/></a>
                                                        </span>&nbsp;
                                                    </td>
                                                {/if}
                                                    <td id = "schedule_dates_form_{$id}" style = "display:none">
                                                        <table>
                                                            <tr><td>{$smarty.const._FROM}&nbsp;</td><td>{eF_template_html_select_date prefix="from_" time=$lesson.from_timestamp start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $lesson.from_timestamp display_seconds = false}&nbsp;</td></tr>
                                                            <tr><td>{$smarty.const._TO}&nbsp;</td><td>{eF_template_html_select_date prefix="to_"   time=$lesson.to_timestamp   start_year="-2" end_year="+2" field_order = 'YMD'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $lesson.to_timestamp   display_seconds = false}&nbsp;</td></tr>
                                                        </table>
                                                    </td>
                                                {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                    <td>
                                                        <a id = "set_schedules_link_{$id}" style = "display:none" href = "javascript:void(0)" onclick = "setSchedule(this, {$id})">
                                                            <img src = "images/16x16/check2.png" alt = "{$smarty.const._SAVE}" title = "{$smarty.const._SAVE}" style = "vertical-align:middle" border = "0"/></a>&nbsp;
                                                        <a id = "remove_schedule_link_{$id}" href = "javascript:void(0)" onclick = "hideEdit({$id})" style = "display:none" onclick = ""><img src = "images/16x16/delete2.png" alt = "{$smarty.const._CANCEL}" title = "{$smarty.const._CANCEL}" style = "vertical-align:middle" border = "0"/></a>
                                                    </td>
                                                {/if}
                                                </tr>
                                            {/foreach}
                                            </table>
                                            <script>
                                            {literal}
                                            function showEdit(id) {
                                                $('add_schedule_link_'+id).hide();
                                                $('remove_schedule_link_'+id).show();
                                                $('schedule_dates_form_'+id).show();
                                                $('set_schedules_link_'+id).show();
                                            }
                                            function hideEdit(id) {
                                                $('remove_schedule_link_'+id).hide();
                                                $('add_schedule_link_'+id).show();
                                                $('schedule_dates_form_'+id).hide();
                                                $('set_schedules_link_'+id).hide();
                                            }
                                            function setSchedule(el, id) {
                                                Element.extend(el);
                                                url = 'administrator.php?ctg=courses&course={/literal}{$T_CURRENT_COURSE->course.id}{literal}&op=course_scheduling&set_schedule='+id;
                                                $('schedule_dates_form_'+id).select('select').each(function (s) {url+='&'+s.name+'='+s.options[s.selectedIndex].value});

                                                el.down().src = 'images/others/progress1.gif';
                                                new Ajax.Request(url, {
                                                        method:'get',
                                                        asynchronous:true,
                                                        onFailure: function (transport) {
                                                            el.down().writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                            new Effect.Appear(el.down().identify());
                                                            window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                                        },
                                                        onSuccess: function (transport) {
                                                            $('schedule_dates_'+id).update(transport.responseText);
                                                            hideEdit(id);
                                                            el.down().src = 'images/16x16/check2.png';
                                                            $('add_schedule_link_'+id).down().down().src = 'images/16x16/edit.png';
                                                            $('add_schedule_link_'+id).down().next().show();
                                                        }
                                                });
                                            }
                                            function deleteSchedule(el, id) {
                                                Element.extend(el);
                                                url = 'administrator.php?ctg=courses&course={/literal}{$T_CURRENT_COURSE->course.id}{literal}&op=course_scheduling&delete_schedule='+id;
                                                el.down().src = 'images/others/progress1.gif';
                                                new Ajax.Request(url, {
                                                        method:'get',
                                                        asynchronous:true,
                                                        onFailure: function (transport) {
                                                            el.down().src = 'images/16x16/delete.png';
                                                            errorImg = new Element('img', {id: 'error_icon', src:'images/16x16/delete2.png', title: transport.responseText, border: 0}).setStyle({verticalAlign:'middle'}).hide();
                                                            el.insert(errorImg);
                                                            new Effect.Appear(errorImg.identify());
                                                            window.setTimeout('Effect.Fade("'+errorImg.identify()+'")', 10000);
                                                        },
                                                        onSuccess: function (transport) {
                                                            $('schedule_dates_'+id).update('<span class = "emptyCategory">{/literal}{$smarty.const._NOSCHEDULESET}{literal}</span>');
                                                            el.down().writeAttribute({src: 'images/16x16/delete.png'});
                                                            el.hide();
                                                            el.previous().down().src = 'images/16x16/add2.png';
                                                        }
                                                });

                                            }
                                            {/literal}
                                            </script>
                                        {else}
                                            <table style = "width:100%"><tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr></table>
                                        {/if}
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._COURSEORDER data = $smarty.capture.t_course_scheduling_code image = '32x32/calendar.png'  main_options = $T_TABLE_OPTIONS}
                            {else}
                                    {capture name = 't_courses_code'}
                                        {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                    <table border = "0" >
                                                        <tr><td><a href="administrator.php?ctg=courses&add_course=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWCOURSE}" alt="{$smarty.const._NEWCOURSE}"/ border="0"></a></td>
                                                            <td><a href="administrator.php?ctg=courses&add_course=1">{$smarty.const._NEWCOURSE}</a></td></tr>
                                                    </table>
                                        {/if}
                                                    <table border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME} </td>
                                                            <td class = "topTitle">{$smarty.const._LANGUAGE}</td>
                                                            <td class = "topTitle">{$smarty.const._DIRECTION}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._LESSONS}</td>
                                                            {* MODULE HCD: The price should not appear *}
                                                            {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "topTitle centerAlign" name ="skills_offered">{$smarty.const._SKILLSOFFERED}</td>
                                                            {else}
                                                            <td class = "topTitle centerAlign" name = "price">{$smarty.const._PRICE}</td>
                                                            {/if}
                                                            <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                            <td class = "topTitle centerAlign">{$smarty.const._STATISTICS}</td>
                                                        {/if}
                                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                                        </tr>
                                        {foreach name = 'courses_list2' key = 'key' item = 'course' from = $T_COURSES_DATA}
                                                        <tr id="row_{$course.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$course.active}deactivatedTableElement{/if}">
                                                            <td class = "editLink">{$course.link}</td>
                                                            <td>{$course.languages_NAME}</td>
                                                            <td>{$course.directionsPath}</td>
                                                            <td class = "centerAlign">{$course.lessons_num}</td>
                                                        {* MODULE HCD: Prices are replaced by the number of skills offered *}
                                                        {if $T_MODULE_HCD_INTERFACE}
                                                            <td class = "centerAlign">{if $course.skills_offered == 0}{$smarty.const._NOSKILLSOFFERED}{else}{$course.skills_offered}{/if}</td>
                                                        {else}
                                                            <td class = "centerAlign">{if $course.price == 0}{$smarty.const._FREECOURSE}{else}{$course.price} {$T_CURRENCYSYMBOLS[$T_CONFIGURATION.currency]}{/if}</td>
                                                        {/if}
                                                            <td class = "centerAlign"">
                                                            {if $course.active == 1}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "activate(this, '{$course.id}')"{/if}><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                            {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}onclick = "activate(this, '{$course.id}')"{/if}><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                            {/if}
                                                            </td>
                                            {if !isset($T_CURRENT_USER->coreAccess.statistics) || $T_CURRENT_USER->coreAccess.statistics != 'hidden'}
                                                            <td class = "centerAlign"><a href="administrator.php?ctg=statistics&option=course&sel_course={$course.id}"><img border = "0" src = "images/16x16/chart.png" title = "{$smarty.const._STATISTICS}" alt = "{$smarty.const._STATISTICS}" /></a></td>
                                            {/if}
                                                            <td class = "centerAlign">
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_info"><img border = "0" src = "images/16x16/about.png" title = "{$smarty.const._COURSEINFORMATION}" alt = "{$smarty.const._COURSEINFORMATION}" /></a>
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_certificates"><img border = "0" src = "images/16x16/certificate_add.png" title = "{$smarty.const._COURSECERTIFICATES}" alt = "{$smarty.const._COURSECERTIFICATES}" /></a>
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_rules"><img border = "0" src = "images/16x16/recycle.png" title = "{$smarty.const._COURSERULES}" alt = "{$smarty.const._COURSERULES}" /></a>
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_order"><img border = "0" src = "images/16x16/replace2.png" title = "{$smarty.const._COURSEORDER}" alt = "{$smarty.const._COURSEORDER}" /></a>
                                                                <a href = "administrator.php?ctg=courses&course={$course.id}&op=course_scheduling"><img border = "0" src = "images/16x16/calendar.png" title = "{$smarty.const._COURSESCHEDULE}" alt = "{$smarty.const._COURSESCHEDULE}" /></a>
                                            {if !isset($T_CURRENT_USER->coreAccess.lessons) || $T_CURRENT_USER->coreAccess.lessons == 'change'}
                                                                <a href = "administrator.php?ctg=courses&edit_course={$course.id}" class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                <a href = "administrator.php?ctg=courses&delete_course={$course.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETECOURSE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                            {/if}
                                                            </td>
                                                        </tr>
                                        {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                        {/foreach}
                                                    </table>
                                                    <script>
                                                    {literal}
                                                    function activate(el, course) {
                                                        Element.extend(el);
                                                        if (el.down().src.match('red')) {
                                                            url = 'administrator.php?ctg=courses&activate_course='+course;
                                                            newSource = 'images/16x16/trafficlight_green.png';
                                                        } else {
                                                            url = 'administrator.php?ctg=courses&deactivate_course='+course;
                                                            newSource = 'images/16x16/trafficlight_red.png';
                                                        }

                                                        var img = new Element('img', {id: 'img_'+course, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        el.up().insert(img);
                                                        el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                        new Ajax.Request(url, {
                                                            method:'get',
                                                            asynchronous:true,
                                                            onFailure: function (transport) {
                                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                            },
                                                            onSuccess: function (transport) {
                                                                img.setStyle({display:'none'});
                                                                el.down().src = newSource;
                                                                new Effect.Appear(el.down(), {queue:'end'});
                                                                if (el.down().src.match('green')) {
                                                                    // When activated
                                                                    var cName = $('row_'+course).className.split(" ");
                                                                    $('row_'+course).className = cName[0];
                                                                    //$('column_'+course).setStyle({color:'green'});
                                                                } else {

                                                                    $('row_'+course).className += " deactivatedTableElement";
                                                                    //$('column_'+course).setStyle({color:'red'});
                                                                }
                                                                }
                                                            });
                                                    }
                                                    {/literal}
                                                    </script>
                                                    <div id = 'course_info_div' style = "display:none">
                                                    {*
                                                            {$T_COURSE_INFO_FORM.javascript}
                                                            <form {$T_COURSE_INFO_FORM.attributes}>
                                                            {$T_COURSE_INFO_FORM.hidden}
                                                            <table class = "formElements">
                                                                <tr><td class = "labelCell">{$T_COURSE_INFO_FORM.name.label}:&nbsp;</td>
                                                                    <td class = "elementCell">{$T_COURSE_INFO_FORM.name.html}</td></tr>
                                                                {if $T_COURSE_INFO_FORM.name.error}<tr><td></td><td class = "formError">{$T_COURSE_INFO_FORM.name.error}</td></tr>{/if}
                                                                <tr><td>&nbsp;</td></tr>
                                                                <tr><td></td><td>{$T_COURSE_INFO_FORM.submit_info.html}</td></tr>
                                                            </table>
                                                            </form>
                                                    *}
                                                    </div>
                                    {/capture}
                                    {eF_template_printInnerTable title = $smarty.const._UPDATECOURSES data = $smarty.capture.t_courses_code image = '/32x32/books.png'}
    {/if}
                            </td></tr>
    {/capture}
{/if}

{if (isset($T_CTG) && $T_CTG == 'user_types')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types">'|cat:$smarty.const._USERTYPES|cat:'</a>'}

{*moduleRoles: The user types list*}
    {capture name = "moduleRoles"}
                            <tr><td class = "moduleCell">
                        {if $smarty.get.add_user_type || $smarty.get.edit_user_type}
                            {capture name = "moduleNewUserType"}
                                            <tr><td class = "moduleCell">
                                            {capture name='t_new_role_code'}
                                                    <table width = "50%">
                                                        <tr><td class = "topAlign" width = "50%">
                                                            {$T_USERTYPES_FORM.javascript}
                                                            <form {$T_USERTYPES_FORM.attributes}>
                                                            {$T_USERTYPES_FORM.hidden}
                                                            <table class = "formElements">
                                                                <tr><td class = "labelCell">{$T_USERTYPES_FORM.name.label}:&nbsp;</td>
                                                                    <td class = "elementCell">{$T_USERTYPES_FORM.name.html}</td></tr>
                                                                {if $T_USERTYPES_FORM.name.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.name.error}</td></tr>{/if}
                                                                <tr><td class = "labelCell">{$T_USERTYPES_FORM.basic_user_type.label}:&nbsp;</td>
                                                                    <td class = "elementCell">{$T_USERTYPES_FORM.basic_user_type.html}</td></tr>
                                                                {if $T_USERTYPES_FORM.basic_user_type.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.basic_user_type.error}</td></tr>{/if}

                                                    {foreach name = 'usertype_options' key = 'option' item = 'value' from = $T_USERTYPES_OPTIONS}
                                                                <tr><td class = "labelCell">{$T_USERTYPES_FORM.core_access.$option.label}:&nbsp;</td>
                                                                    <td class = "elementCell">{$T_USERTYPES_FORM.core_access.$option.html}</td></tr>
                                                                {if $T_USERTYPES_FORM.core_access.$option.error}<tr><td></td><td class = "formError">{$T_USERTYPES_FORM.core_access.$option.error}</td></tr>{/if}
                                                    {/foreach}
                                                                <tr><td>&nbsp;</td></tr>
                                                                <tr><td></td>
                                                                    <td class = "elementCell">{$T_USERTYPES_FORM.submit_type.html}</td></tr>
                                                            </table>
                                                            </form>
                                                    </td></tr>
                                                </table>
                                            {/capture}

                                 {if $smarty.get.edit_user_type != ""}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types&edit_user_type='|cat:$smarty.get.edit_user_type|cat:'">'|cat:$smarty.const._EDITUSERTYPE|cat:' <span class = "innerTableName">&quot;'|cat:$T_USER_TYPE_NAME|cat:'&quot;</span></a>'}
                                    {eF_template_printInnerTable title = $smarty.const._OPTIONSUSERTYPEFOR|cat:"&nbsp;<span class = 'innerTableName'>&quot;"|cat:$T_USER_TYPE_NAME|cat:"&quot;</span>" data = $smarty.capture.t_new_role_code image = '/32x32/users_family.png'}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_types&add_user_type=1">'|cat:$smarty.const._NEWUSERTYPE|cat:'</a>'}
                                    {eF_template_printInnerTable title = $smarty.const._NEWUSERTYPE data = $smarty.capture.t_new_role_code image = '/32x32/users_family.png'}
                                {/if}
                  </td></tr>
                            {/capture}
                        {else}
                            {capture name = 't_roles_code'}
                                {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                    <table>
                                                        <tr><td>
                                                                <a href = "administrator.php?ctg=user_types&add_user_type=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWUSERTYPE}" alt="{$smarty.const._NEWUSERTYPE}"/ border="0" style = "vertical-align:middle"></a>
                                                                <a href = "administrator.php?ctg=user_types&add_user_type=1" style = "vertical-align:middle">{$smarty.const._NEWUSERTYPE}</a>
                                                            </td></tr>
                                                    </table>
                                {/if}
                                                    <table border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle">{$smarty.const._BASICUSERTYPE}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                        </tr>
                                {foreach name = 'usertype_list' key = 'key' item = 'type' from = $T_USERTYPES_DATA}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                            <td>
                                                                <a href = "administrator.php?ctg=user_types&edit_user_type={$type.id}"  class = "editLink">{$type.name}</a>
                                                            </td>
                                                            <td>{$T_BASIC_USER_TYPES[$type.basic_user_type]}</td>
                                                            <td class = "centerAlign">
                                                            {if $type.active == 1}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "activate(this, '{$type.id}')"{/if}><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                            {else}
                                                                <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "activate(this, '{$type.id}')"{/if}><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                            {/if}
                                                            </td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                            <td class = "centerAlign">
                                                                <a href = "administrator.php?ctg=user_types&edit_user_type={$type.id}"  class = "editLink"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                {if $type.id != $T_CURRENT_USER->user.user_types_ID}
                                                                    <a href = "administrator.php?ctg=user_types&delete_user_type={$type.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEUSERTYPE}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                                {else}
                                                                    <img border = "0" src = "images/16x16/delete_gray.png" title = "{$smarty.const._CANNOTDELETEOWNTYPE}" alt = "{$smarty.const._CANNOTDELETEOWNTYPE}" />
                                                                {/if}
                                                            </td>
                                                        {/if}
                                                        </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                                    </table>
                                                        <script>
                                                        {literal}
                                                        function activate(el, user_type) {
                                                            Element.extend(el);
                                                            if (el.down().src.match('red')) {
                                                                url = 'administrator.php?ctg=user_types&activate_user_type='+user_type;
                                                                newSource = 'images/16x16/trafficlight_green.png';
                                                            } else {
                                                                url = 'administrator.php?ctg=user_types&deactivate_user_type='+user_type;
                                                                newSource = 'images/16x16/trafficlight_red.png';
                                                            }

                                                            var img = new Element('img', {id: 'img_'+user_type, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                            el.up().insert(img);
                                                            el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                            new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                    new Effect.Appear(img.identify());
                                                                    window.setTimeout('Effect.Fade("'+img.identify()+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    img.setStyle({display:'none'});
                                                                    el.down().src = newSource;
                                                                    new Effect.Appear(el.down(), {queue:'end'});

                                                                    if (el.down().src.match('green')) {
                                                                        // When activated
                                                                        var cName = $('row_'+user_type).className.split(" ");
                                                                        $('row_'+user_type).className = cName[0];
                                                                        $('column_'+user_type).setStyle({color:'green'});
                                                                    } else {
                                                                        $('row_'+user_type).className += " deactivatedTableElement";
                                                                        $('column_'+user_type).setStyle({color:'red'});
                                                                    }
                                                                    }
                                                                });
                                                        }
                                                        {/literal}
                                                        </script>
                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._UPDATEUSERTYPES data = $smarty.capture.t_roles_code image = '/32x32/users_family.png'}
                        {/if}
                            </td></tr>
        {/capture}

{/if}
{if (isset($T_CTG) && $T_CTG == 'user_groups')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups">'|cat:$smarty.const._GROUPS|cat:'</a>'}

{*moduleGroups: The user groups list*}
    {capture name = "moduleGroups"}
                            <tr><td class = "moduleCell">
                        {if $smarty.get.add_user_group || $smarty.get.edit_user_group}
                            {if $smarty.get.add_user_group}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups&add_user_group=1">'|cat:$smarty.const._NEWGROUP|cat:'</a>'}
                            {else}
                                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=user_groups&edit_user_group='|cat:$smarty.get.edit_user_group|cat:'">'|cat:$smarty.const._EDITGROUP|cat:'&nbsp;<span class="innerTableName">&quot;'|cat:$T_USERGROUPS_FORM_R.name.value|cat:'&quot;</span></a>'}
                            {/if}
                            {capture name = "moduleNewUserGroup"}
                                            <tr><td class = "moduleCell">
                                            {capture name='t_new_group_code'}
                                            <div class = "tabber">
                                                <div class = "tabbertab" title = "{$smarty.const._GROUPOPTIONS}">
                                                            {$T_USERGROUPS_FORM_R.javascript}
                                                            <form {$T_USERGROUPS_FORM_R.attributes}>
                                                            {$T_USERGROUPS_FORM_R.hidden}
                                                            <table class = "formElements">
                                                                <tr><td class = "labelCell">{$T_USERGROUPS_FORM_R.name.label}:&nbsp;</td>
                                                                    <td>{$T_USERGROUPS_FORM_R.name.html}</td></tr>
                                                                {if $T_USERGROUPS_FORM_R.name.error}<tr><td></td><td class = "formError">{$T_USERGROUPS_FORM_R.name.error}</td></tr>{/if}
                                                                <tr><td class = "labelCell">{$T_USERGROUPS_FORM_R.description.label}:&nbsp;</td>
                                                                    <td>{$T_USERGROUPS_FORM_R.description.html}</td></tr>
                                                                {if $T_USERGROUPS_FORM_R.description.error}<tr><td></td><td class = "formError">{$T_USERGROUPS_FORM_R.description.error}</td></tr>{/if}
                                                                <tr><td>&nbsp;</td></tr>
                                                                <tr><td></td><td>{$T_USERGROUPS_FORM_R.submit_type.html}</td></tr>
                                                            </table>
                                                            </form>
                                                </div>

                        {if $smarty.get.edit_user_group}
                                                <div class = "tabbertab {if $smarty.get.tab=='users'}tabbertabdefault{/if}" title = "{$smarty.const._GROUPUSERS}">
<!--ajax:usersTable-->
                                                    <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "administrator.php?ctg=user_groups&edit_user_group={$smarty.get.edit_user_group}&">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
                                                            <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
                                                            <td class = "topTitle" name = "user_type">{$smarty.const._USERTYPE}</td>
                                                            <td class = "topTitle centerAlign" name = "in_group">{$smarty.const._CHECK}</td>
                                                        </tr>
                                        {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_GROUP_USERS}
                                                        <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
                                                            <td>{$user.login}</td>
                                                            <td>{$user.name}</td>
                                                            <td>{$user.surname}</td>
                                                            <td>{$user.user_type}</td>
                                                            <td align = "center">
                                                        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                                <input class = "inputCheckbox" type = "checkbox" id = "checked_{$user.login}" name = "checked_{$user.login}" onclick = "ajaxPost('{$user.login}', this);" {if $user.in_group == 1}checked = "checked"{/if} />
                                                        {else}
                                                                {if $user.in_group == 1}<img src = "images/16x16/check2.png" alt = "{$smarty.const._GROUPUSER}" title = "{$smarty.const._GROUPUSER}">{/if}
                                                        {/if}
                                                            </td>
                                                    </tr>
                                        {/foreach}
                                                </table>
<!--/ajax:usersTable-->

                                        {literal}
                                        <script>
                                               function ajaxPost(login, el, table_id) {
                                                Element.extend(el);
                                                if (login) {
                                                    var checked  = document.getElementById('checked_'+login).checked;
                                                    var url      = 'administrator.php?ctg=user_groups&edit_user_group={/literal}{$smarty.get.edit_user_group}{literal}&postAjaxRequest=1&login='+login;
                                                    var img_id   = 'img_'+login;
                                                } else if (table_id && table_id == 'usersTable') {
                                                    var url      = 'administrator.php?ctg=user_groups&edit_user_group={/literal}{$smarty.get.edit_user_group}{literal}&postAjaxRequest=1&selectAll=true&status='+el.checked;
                                                    var img_id   = 'img_selectAll';
                                                }
                                                var position = eF_js_findPos(el);
                                                var img      = Element.extend(document.createElement("img"));

                                                img.style.position = 'absolute';
                                                img.style.top      = Element.positionedOffset(Element.extend(el)).top  + 'px';
                                                img.style.left     = Element.positionedOffset(Element.extend(el)).left + 6 + Element.getDimensions(Element.extend(el)).width + 'px';

                                                img.setAttribute("id", img_id);
                                                img.setAttribute('src', 'images/others/progress1.gif');

                                                el.parentNode.appendChild(img);

                                                    new Ajax.Request(url, {
                                                            method:'get',
                                                            asynchronous:true,
                                                            onFailure: function (transport) {
                                                                img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                            },
                                                            onSuccess: function (transport) {
                                                                img.style.display = 'none';
                                                                img.setAttribute('src', 'images/16x16/check.png');
                                                                new Effect.Appear(img_id);
                                                                window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                                }
                                                        });
                                               }
                                        </script>
                                        {/literal}
                                {/if}
                                </div>
                            </div>
                                            {/capture}
                            {if $smarty.get.add_user_group}
                                    {eF_template_printInnerTable title = $smarty.const._NEWGROUP data = $smarty.capture.t_new_group_code image = '/32x32/users1.png'}
                            {else}
                                    {eF_template_printInnerTable title = "`$smarty.const._OPTIONSFORGROUP` <span class = 'innerTableName'>&quot;`$T_USERGROUPS_FORM_R.name.value`&quot;</span>" data = $smarty.capture.t_new_group_code image = '/32x32/users1.png'}
                            {/if}
                            </td></tr>
                            {/capture}
                        {else}
                            {capture name = 't_groups_code'}
                                                {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                    <table border = "0">
                                                        <tr><td width="10%"><a href = "administrator.php?ctg=user_groups&add_user_group=1"><img src="images/16x16/add2.png" title="{$smarty.const._NEWGROUP}" alt="{$smarty.const._NEWGROUP}"/ border="0"></a></td><td width="90%" align="left"><a href = "administrator.php?ctg=user_groups&add_user_group=1">{$smarty.const._NEWGROUP}</a></td></tr>
                                                    </table>
                                                {/if}
                                                    <table border = "0" width = "100%"  class = "sortedTable" sortBy = "0">
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle">{$smarty.const._DESCRIPTION}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._USERS}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._ACTIVE2}</td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <td class = "topTitle centerAlign noSort">{$smarty.const._OPERATIONS}</td>
                                                        {/if}
                                                        </tr>
                                                {foreach name = 'group_list' key = 'key' item = 'group' from = $T_USERGROUPS}
                                                        <tr id="row_{$group.id}" class = "{cycle values = "oddRowColor, evenRowColor"} {if !$group.active}deactivatedTableElement{/if}">
                                                            <td><a href = "administrator.php?ctg=user_groups&edit_user_group={$group.id}"  class = "editLink"><span id="column_{$group.id}" {if !$group.active}style="color:red"{/if}>{$group.name}</span></a></td>
                                                            <td>{$group.description}</td>
                                                            <td class = "centerAlign">{$group.num_users}</td>
                                                            <td class = "centerAlign">
                                                                {if $group.active == 1}
                                                                    <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}onclick = "activate(this, '{$group.id}')"{/if}><img src = "images/16x16/trafficlight_green.png" alt = "{$smarty.const._DEACTIVATE}" title = "{$smarty.const._DEACTIVATE}" border = "0"></a>
                                                                {else}
                                                                     <a href = "javascript:void(0);" {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}onclick = "activate(this, '{$group.id}')"{/if}><img src = "images/16x16/trafficlight_red.png" alt = "{$smarty.const._ACTIVATE}" title = "{$smarty.const._ACTIVATE}" border = "0"></a>
                                                                {/if}
                                                            </td>
                                                        {if !isset($T_CURRENT_USER->coreAccess.users) || $T_CURRENT_USER->coreAccess.users == 'change'}
                                                            <td class = "centerAlign">
                                                                    <a href = "administrator.php?ctg=user_groups&edit_user_group={$group.id}" ><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                    <a href = "administrator.php?ctg=user_groups&delete_user_group={$group.id}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEGROUP}')" class = "deleteLink"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                            </td>
                                                        {/if}
                                                        </tr>
                                {foreachelse}
                                                    <tr class = "defaultRowHeight oddRowColor"><td class = "emptyCategory centerAlign" colspan = "100%">{$smarty.const._NODATAFOUND}</td></tr>
                                {/foreach}
                                                    </table>
                                                        <script>
                                                        {literal}
                                                        function activate(el, group) {
                                                            Element.extend(el);
                                                            if (el.down().src.match('red')) {
                                                                url = 'administrator.php?ctg=user_groups&activate_user_group='+group;
                                                                newSource = 'images/16x16/trafficlight_green.png';
                                                            } else {
                                                                url = 'administrator.php?ctg=user_groups&deactivate_user_group='+group;
                                                                newSource = 'images/16x16/trafficlight_red.png';
                                                            }

                                                            var img = new Element('img', {id: 'img_'+group, src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                            el.up().insert(img);
                                                            el.down().src = 'images/16x16/trafficlight_yellow.png';
                                                            new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    img.writeAttribute({src:'images/16x16/delete.png', title: transport.responseText}).hide();
                                                                    new Effect.Appear(img.identify());
                                                                    window.setTimeout('Effect.Fade("'+img.identify()+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    img.setStyle({display:'none'});
                                                                    el.down().src = newSource;
                                                                    new Effect.Appear(el.down(), {queue:'end'});

                                                                    if (el.down().src.match('green')) {
                                                                        // When activated
                                                                        var cName = $('row_'+group).className.split(" ");
                                                                        $('row_'+group).className = cName[0];
                                                                        $('column_'+group).setStyle({color:'green'});
                                                                    } else {
                                                                        $('row_'+group).className += " deactivatedTableElement";
                                                                        $('column_'+group).setStyle({color:'red'});
                                                                    }
                                                                    }
                                                                });
                                                        }
                                                        {/literal}
                                                        </script>

                            {/capture}
                            {eF_template_printInnerTable title = $smarty.const._UPDATEGROUPS data = $smarty.capture.t_groups_code image = '/32x32/users3.png'}
                        {/if}
                            </td></tr>
        {/capture}

{/if}
{if (isset($T_CTG) && $T_CTG == 'cms')}
    {assign var = "title" value = '<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'">'|cat:$smarty.const._HOME|cat:'</a>'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=cms">'|cat:$smarty.const._CMS|cat:'</a>'}
    {*moduleCms: The CMS page *}
    {capture name = "moduleCms"}
                           <tr><td class = "moduleCell">
							{if $smarty.get.file_manager}
                                {$T_FILE_MANAGER}
                            {elseif $smarty.get.add_page || $smarty.get.edit_page}
                                {capture name='t_new_page_code'}
                                                {$T_CMS_FORM.javascript}
                                                <form {$T_CMS_FORM.attributes}>
                                                {$T_CMS_FORM.hidden}
                                                <table class = "formElements" style = "width:100%">
                                                    <tr><td class = "labelCell">{$T_CMS_FORM.name.label}:&nbsp;</td>
                                                        <td class = "elementCell">{$T_CMS_FORM.name.html}</td></tr>
                                                    {if $T_CMS_FORM.name.error}<tr><td></td><td class = "formError">{$T_CMS_FORM.name.error}</td></tr>{/if}
                                                    <tr><td></td><td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_0');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
                                                    <tr><td></td><td id="filemanager_cell"></td></tr>
                                                    <tr><td></td><td id="toggleeditor_cell2"></td></tr>
                                                    <tr><td class = "labelCell">{$T_CMS_FORM.page.label}:&nbsp;</td>
                                                        <td  class = "elementCell">{$T_CMS_FORM.page.html}</td>
                                                        </tr>

                                                    <tr><td></td><td class = "infoCell">{$smarty.const._YOUMUSTPROVIDELOGINLINK}</td></tr>

                                                    {if $T_CMS_FORM.page.error}<tr><td></td><td class = "formError">{$T_CMS_FORM.page.error}</td></tr>{/if}
                                                <tr><td colspan = "2">&nbsp;</td></tr>

                                                    <tr><td></td>
                                                        <td  class = "submitCell">
                                                            {$T_CMS_FORM.submit_cms.html}&nbsp;
                                                        </td></tr>
                                                </table>
                                                </form>
                                                <table><tr><td id="fmInitial">
                                                <div  id="filemanager_div" style="display:none;">
                                                    {$T_FILE_MANAGER}
                                                    <br/>
                                                </div>
                                                </td></tr></table>
                                                <script type="text/javascript">
                                        {literal}
                                           var tinyMCEmode = true;
                                                function toogleEditorMode(sEditorID) {
                                                    try {
                                                        if(tinyMCEmode) {
                                                            tinyMCE.removeMCEControl(tinyMCE.getEditorId(sEditorID));
                                                            tinyMCEmode = false;
                                                        } else {
                                                            mceAddControlDynamic(sEditorID, 'editor_cms_data' ,'templateEditor');
                                                            tinyMCEmode = true;
                                                        }
                                                    } catch(e) {
                                                        alert('editor error');
                                                    }
                                                }
                                        function insertatcursor(myField, myValue) {

                                        if (document.selection) {
                                            myField.focus();
                                            sel = document.selection.createRange();
                                            sel.text = myValue;
                                        }
                                        else if (myField.selectionStart || myField.selectionStart == '0') {
                                            var startPos = myField.selectionStart;
                                            var endPos = myField.selectionEnd;
                                            myField.value = myField.value.substring(0, startPos)+ myValue+ myField.value.substring(endPos, myField.value.length);
                                        } else {
                                            myField.value += myValue;
                                        }
                                    }

                                    function insert_editor(element, id) {
                                    {/literal}{if !$smarty.get.edit_page}{literal}
                                        var url = 'administrator.php?ctg=cms&add_page=1&postAjaxRequest_insert=1';
                                    {/literal}{else}{literal}
                                        var url = 'administrator.php?ctg=cms&edit_page={/literal}{$smarty.get.edit_page}{literal}&postAjaxRequest_insert=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_0','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.add_page_form.editor_cms_data, transport.responseText);
                                                }
                                            }
                                        });
                                    }
                                    var file_manager_hidden = 1;
                                    function toggle_file_manager(){
                                        if(file_manager_hidden){
                                            $('filemanager_cell').insert($('filemanager_div'));
                                            $('filemanager_div').style.display = "block";
                                            $('arrow_down').src = "images/16x16/navigate_up.png";
                                            $('toggleeditor_cell2').insert($('toggleeditor_link'));
                                            $('open_manager').update('{/literal}{$smarty.const._CLOSEFILEMANAGER}{literal}');
                                            file_manager_hidden = 0;
                                        }else{
                                            $('filemanager_div').style.display = "none";
                                            $('fmInitial').insert($('filemanager_div'));
                                            $('toggleeditor_cell1').insert($('toggleeditor_link'));
                                            $('arrow_down').src = "images/16x16/navigate_down.png";
                                            $('open_manager').update('{/literal}{$smarty.const._OPENFILEMANAGER}{literal}');
                                            file_manager_hidden = 1;
                                        }
                                    }

                                        {/literal}
                                        </script>

                                {/capture}

                                {if $smarty.get.edit_page != ""}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=cms&edit_page='|cat:$smarty.get.edit_page|cat:'">'|cat:$smarty.const._UPDATEPAGE|cat:'</a>'}
                                    {eF_template_printInnerTable title = "`$smarty.const._UPDATEPAGE` <span class = 'innerTableName'>&quot;`$smarty.get.edit_page` &quot;</span>" data = $smarty.capture.t_new_page_code image = '/32x32/document_text.png'}
                                {else}
                                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=cms&add_page=1">'|cat:$smarty.const._NEWPAGE|cat:'</a>'}
                                    {eF_template_printInnerTable title = $smarty.const._NEWPAGE data = $smarty.capture.t_new_page_code image = '/32x32/document_text.png'}
                                {/if}
                            {else}
                                {capture name = 't_cms_code'}
                                    {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                <div class = "headerTools">
                                                	<span>
                                                        <img src = "images/16x16/add2.png" alt = "{$smarty.const._NEWPAGE}" title = "{$smarty.const._NEWPAGE}">
                                                        <a href = "administrator.php?ctg=cms&add_page=1">{$smarty.const._NEWPAGE}</a>
                                                    </span>
                                                </div>
                                    {/if}
                                                    <table class = "sortedTable" width = "100%" id = 'cms_table'>
                                                        <tr class = "topTitle">
                                                            <td class = "topTitle">{$smarty.const._NAME}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._DEFAULTPAGE}</td>
                                                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>
                                                        </tr>
                                    {foreach name = 'pages_list' key = 'key' item = 'page' from = $T_CMS_PAGES}
                                                        <tr class = "{cycle values = "oddRowColor, evenRowColor"}">
                                                            <td>
                                        {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                                <a href = "administrator.php?ctg=cms&edit_page={$page}" class = "editLink">{$page}</a>
                                        {else}
                                                                {$page}
                                        {/if}
                                                            </td>
                                                            <td align = "center">
                                                                {if ($page == $T_DEFAULT_PAGE)}
                                                                    <a href = "javascript:void(0)" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "usePage(this, '{$page}')"{/if}><img src = "images/16x16/pin_green.png" alt = "{$smarty.const._USENONE}" title = "{$smarty.const._USENONE}" border = "0" ></a>
                                                                {else}
                                                                    <a href = "javascript:void(0)" {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}onclick = "usePage(this, '{$page}')"{/if}><img src = "images/16x16/pin_red.png" alt = "{$smarty.const._USETHIS}" title = "{$smarty.const._USETHIS}" border = "0" /></a>
                                                                {/if}
                                                            </td>
                                                            <td align = "center">
                                                                <a href = "{$smarty.const.G_ADMINLINK}{$page}.php" target = "POPUP_FRAME" onclick = "eF_js_showDivPopup('{$smarty.const._PREVIEW}', 2)"><img border = "0" src = "images/16x16/view.png" title = "{$smarty.const._PREVIEW}" alt = "{$smarty.const._PREVIEW}" /></a>
                                                            {if !isset($T_CURRENT_USER->coreAccess.configuration) || $T_CURRENT_USER->coreAccess.configuration == 'change'}
                                                                <a href = "administrator.php?ctg=cms&edit_page={$page}"><img border = "0" src = "images/16x16/edit.png" title = "{$smarty.const._EDIT}" alt = "{$smarty.const._EDIT}" /></a>
                                                                <a href = "administrator.php?ctg=cms&delete_page={$page}" onclick = "return confirm('{$smarty.const._AREYOUSUREYOUWANTTODELETEPAGE}')"><img border = "0" src = "images/16x16/delete.png" title = "{$smarty.const._DELETE}" alt = "{$smarty.const._DELETE}" /></a>
                                                            {/if}
                                                            </td></tr>
                                    {foreachelse}
                                                        <tr class = "defaultRowHeight oddRowColor"><td colspan = "3" class = "emptyCategory centerAlign">{$smarty.const._NODATAFOUND}</td></tr>
                                    {/foreach}
                                                    </table>
                                                    <script>
                                                    {literal}
                                                    function usePage(el, page) {
                                                        Element.extend(el);
                                                        if (el.down().src.match('pin_green.png')) {
                                                            var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=cms&use_none=1';
                                                            var set_page = 0;
                                                        } else {
                                                            var url = '{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=cms&set_page='+page;
                                                            var set_page = 1;
                                                        }
                                                        var img = new Element('img', {src:'images/others/progress1.gif'}).setStyle({position:'absolute'});
                                                        img_id = img.identify();
                                                        el.up().insert(img);
                                                        new Ajax.Request(url, {
                                                                method:'get',
                                                                asynchronous:true,
                                                                onFailure: function (transport) {
                                                                    img.writeAttribute({src:'images/16x16/delete2.png', title: transport.responseText}).hide();
                                                                    new Effect.Appear(img_id);
                                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 10000);
                                                                },
                                                                onSuccess: function (transport) {
                                                                    $('cms_table').select('img').each(function (s) {if (s.src.match('pin_green.png')) s.src = 'images/16x16/pin_red.png'; });
                                                                    if (set_page) {
                                                                        el.down().src = 'images/16x16/pin_green.png';
                                                                    }
                                                                    img.style.display = 'none';
                                                                    img.setAttribute('src', 'images/16x16/check.png');
                                                                    new Effect.Appear(img_id);
                                                                    window.setTimeout('Effect.Fade("'+img_id+'")', 2500);
                                                                }
                                                            });
                                                    }

                                                    {/literal}
                                                    </script>
                                {/capture}

                                {eF_template_printInnerTable title = $smarty.const._UPDATEPAGES data = $smarty.capture.t_cms_code image = '/32x32/document_text.png'}
                            {/if}
                            </td></tr>
        {/capture}
{/if}


{if (isset($T_CATEGORY) && $T_CATEGORY == 'statistics')}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
{*moduleStatistics: The administrator statistics page*}
    {if $smarty.get.option == 'user'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user">'|cat:$smarty.const._USERSTATISTICS|cat:'</a>'}
        {if $smarty.get.sel_user}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=user&sel_user='|cat:$smarty.get.sel_user|cat:'">'|cat:$smarty.get.sel_user|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'lesson'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson">'|cat:$smarty.const._LESSONSTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_lesson)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=lesson&sel_lesson='|cat:$smarty.get.sel_lesson|cat:'">'|cat:$T_INFO_LESSON.name|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'test'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=test">'|cat:$smarty.const._TESTSTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_test)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=test&sel_test='|cat:$smarty.get.sel_test|cat:'">'|cat:$T_TEST_INFO.general.name|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'course'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course">'|cat:$smarty.const._COURSESTATISTICS|cat:'</a>'}
        {if isset($smarty.get.sel_course)}
            {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=course&sel_course='|cat:$smarty.get.sel_course|cat:'">'|cat:$T_COURSE_INFO.name|cat:'</a>'}
        {/if}
    {elseif $smarty.get.option == 'system'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=system">'|cat:$smarty.const._SYSTEMSTATISTICS|cat:'</a>'}
    {elseif $smarty.get.option == 'queries'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=statistics&option=queries">'|cat:$smarty.const._GENERICQUERIES|cat:'</a>'}
    {/if}

    {capture name = "moduleStatistics"}
            <tr><td class = "moduleCell">
                {include file = "module_statistics.tpl"}
            </td></tr>
    {/capture}
{/if}



{if ($T_CTG == 'calendar')}

    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=calendar">'|cat:$smarty.const._CALENDAR|cat:'</a>'}
    {*moduleCalendarPage: Display the calendar page*}
    {capture name = "moduleCalendarPage"}
                            <tr><td class = "moduleCell">
                                {include file = "calendar.tpl"}
                                {eF_template_printInnerTable title=$T_CALENDAR_TITLE data=$smarty.capture.t_calendar_code image='/32x32/calendar.png' main_options=$T_CALENDAR_OPTIONS}
                            </td></tr>
    {/capture}
{/if}


{if $T_CTG == 'search_courses'}

    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.session.s_type|cat:'.php?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=search_courses">'|cat:$smarty.const._SEARCHCOURSEUSERS|cat:'</a>'}

    {* assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=search_courses">'|cat:$smarty.const._SEARCHCOURSEUSERS|cat:'</a>' *}
    {*moduleSearchCoursesPage: Display the search courses page*}
    {capture name = "moduleSearchCoursesPage"}
                            <tr><td class = "moduleCell">
                                {include file = "search_courses.tpl"}
                                {eF_template_printInnerTable title=$smarty.const._FINDEMPLOYEES data=$smarty.capture.t_search_course_code image='/32x32/book_red.png' main_options=$T_TABLE_OPTIONS}

                                <br />
                                {eF_template_printInnerTable title=$smarty.const._EMPLOYEESFULFILLINGCRITERIA data=$smarty.capture.t_found_employees_code image='/32x32/user1.png' options = $T_SENDALLMAIL_LINK}

                            </td></tr>
    {/capture}
{/if}

{*///MODULES2*}
{if $T_CTG == 'module'}
    {assign var = "title" value = $T_MODULE_NAVIGATIONAL_LINKS}
    {capture name = "importedModule"}
        <tr><td class = "moduleCell">
            {if $T_MODULE_SMARTY}
                {include file = $T_MODULE_SMARTY}
            {else}
                {$T_MODULE_PAGE}
            {/if}
            {*include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'*}
        </td></tr>
    {/capture}
{/if}

{if $T_CTG_MODULE}
    {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg='|cat:$T_CTG|cat:'">'|cat:$T_CTG_MODULE|cat:'</a>'}
    {capture name = "importedModule"}
                            <tr><td class = "moduleCell">
                                {include file = $smarty.const.G_MODULESPATH|cat:$T_CTG|cat:'/module.tpl'}
                            </td></tr>
    {/capture}
{/if}

{if (isset($smarty.post.search_text))}
{*moduleSearchResults: The Search results page*}
    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;'|cat:$smarty.const._SEARCHRESULTS}
    {capture name = "moduleSearchResults"}
                            <tr><td class = "moduleCell">
                                    {include file = "includes/module_search.tpl"}
                            </td></tr>
    {/capture}
{/if}

{* MODULE HCD: *}
{if (isset($T_CTG) && $T_CTG == 'module_hcd')}
{*moduleHCD: The resuls of control panel*}

    {if $smarty.get.op != 'reports' && $smarty.get.op != 'skill_cat'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd">'|cat:$smarty.const._ORGANIZATION|cat:'</a>'}
    {else}
        {assign var = "title" value = ''}
    {/if}

    {if $smarty.get.op == "branches"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches">'|cat:$smarty.const._BRANCHES|cat:'</a>'}
        {if $smarty.get.add_branch || $smarty.get.edit_branch}
                {if $smarty.get.edit_branch != ""}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches&edit_branch='|cat:$smarty.get.edit_branch|cat:'">'|cat:$smarty.const._BRANCHRECORD|cat:'</a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=branches&add_branch=1">'|cat:$smarty.const._BRANCHRECORD|cat:'</a>'}
                {/if}
        {/if}
    {/if}
    {if $smarty.get.op == "skills"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills">'|cat:$smarty.const._SKILLS|cat:'</a>'}
        {if $smarty.get.add_skill || $smarty.get.edit_skill}
                {if $smarty.get.edit_skill != ""}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&edit_skill='|cat:$smarty.get.edit_skill|cat:'">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
                {else}
                    {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=skills&add_skill=1">'|cat:$smarty.const._SKILLDATA|cat:'</a>'}
                {/if}
         {/if}
    {/if}

    {if $smarty.get.op == "job_descriptions"}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions">'|cat:$smarty.const._JOBDESCRIPTIONS|cat:'</a>'}
        {if $smarty.get.add_job_description || $smarty.get.edit_job_description}
            {if $smarty.get.edit_job_description != ""}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions&edit_job_description='|cat:$smarty.get.edit_job_description|cat:'">'|cat:$smarty.const._JOBDESCRIPTIONDATA|cat:'</a>'}
            {else}
                {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href = "'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=job_descriptions&add_job_description=1">'|cat:$smarty.const._JOBDESCRIPTIONDATA|cat:'</a>'}
            {/if}
        {/if}
    {/if}

    {if $smarty.get.op == 'reports'}
        {assign var = "title" value = '<a class="titleLink" href ="'|cat:$smarty.session.s_type|cat:'.php?ctg=statistics">'|cat:$smarty.const._STATISTICS|cat:'</a>'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=reports">'|cat:$smarty.const._SEARCHFOREMPLOYEE|cat:'</a>'}
    {/if}

    {if $smarty.get.op == 'chart'}
        {assign var = "title" value = $title|cat:'&nbsp;&raquo;&nbsp;<a class = "titleLink" href ="'|cat:$smarty.server.PHP_SELF|cat:'?ctg=module_hcd&op=chart">'|cat:$smarty.const._ORGANIZATIONCHARTTREE|cat:'</a>'}
    {/if}

    {capture name = "moduleHCD"}
                            <tr><td class = "moduleCell">
                                {include file = 'module_hcd.tpl'}
                            </td></tr>
    {/capture}
{/if}





{*----------------------------End of Part 2: Modules List------------------------------------------------*}

{*-----------------------------Part 3: Display table-------------------------------------------------*}
{*
{if $T_TEST_MESSAGE}
<div id = "messageDiv" class = "messageDiv" style = "display:none">{$T_TEST_MESSAGE}</div>
{literal}
<script>
    //var messageDiv = new Element('div', { 'class': 'messageDiv' }).update("{/literal}{$T_TEST_MESSAGE}{literal}");
    //messageDiv.setStyle({display:'none'});

    //$$('body').insert(messageDiv);

    new Effect.Appear('messageDiv', {delay: 1.0, queue: 'front'});
    new Effect.Fade('messageDiv', {delay: 5.0, queue: 'end'});
</script>
{/literal}
{/if}
*}
<table class = "mainTable">
    <tr>
        <td style = "vertical-align: top;">

            <table class = "centerTable">
{if !$T_POPUP_MODE}
                <tr class = "topTitle">
                    <td colspan = "2" class = "topTitle">{$title}</td>         {*Header*}
               </tr>
{/if}
               <tr><td colspan = "2"></td></tr>
{if $smarty.get.message}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$smarty.get.message type=$smarty.get.message_type}</td>        {*Display Message passed through get, if any*}
                </tr>
{/if}
{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}

{if $T_SEARCH_MESSAGE || $smarty.get.search_message}
    {if $smarty.get.search_message}{assign var = T_SEARCH_MESSAGE value = $smarty.get.search_message}{/if}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_SEARCH_MESSAGE}</td>        {*Display Search Message, if any*}
                </tr>
{/if}


{if ($T_CTG == 'control_panel' && !$T_OP)}        {*Pages with 2-column layout*}

{*LEFT MAIN COLUMN*}
                <tr>
                    <td class = "singleColumn" id = "singleColumn" colspan = "2">
                        <div id="sortableList">
                            <div style="float:left; width:50%; height:100%;margin-left:1px;">
                                <ul class="sortable" id="firstlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_FIRST}
                                    <li id="firstlist_{$module}">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
                {/foreach}
                {if !in_array('moduleIconFunctions', $T_POSITIONS) && $smarty.capture.moduleIconFunctions}
                                    <li id="firstlist_moduleIconFunctions">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleIconFunctions}
                                        </table>
                                    </li>
                {/if}
                               </ul>
                            </div>
                            <div style="float: right; width:49%;height: 100%;margin-right:1px;">
                                <ul class="sortable" id="secondlist" style="height:100px;width:100%;">
                {foreach name=positions_first key=key item=module from=$T_POSITIONS_SECOND}
                                    <li id="secondlist_{$module}">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.$module}
                                        </table>
                                    </li>
                {/foreach}
                {if !in_array('moduleSystemAnnouncementsList', $T_POSITIONS) && $smarty.capture.moduleSystemAnnouncementsList}
                                    <li id="secondlist_moduleSystemAnnouncementsList">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleSystemAnnouncementsList}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('modulePersonalMessages', $T_POSITIONS) && $smarty.capture.modulePersonalMessages}
                                    <li id="secondlist_modulePersonalMessages">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.modulePersonalMessages}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleNewDirection', $T_POSITIONS) && $smarty.capture.moduleNewDirection}
                                    <li id="secondlist_moduleNewDirection">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewDirection}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleNewUsersApplications', $T_POSITIONS) && $smarty.capture.moduleNewUsersApplications}
                                    <li id="secondlist_moduleNewUsersApplications">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewUsersApplications}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleNewLessonsApplications', $T_POSITIONS) && $smarty.capture.moduleNewLessonsApplications}
                                    <li id="secondlist_moduleNewLessonsApplications">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewLessonsApplications}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleNewLesson', $T_POSITIONS) && $smarty.capture.moduleNewLesson}
                                    <li id="secondlist_moduleNewLesson">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleNewLesson}
                                        </table>
                                    </li>
                {/if}
                {if !in_array('moduleCalendar', $T_POSITIONS) && $smarty.capture.moduleCalendar}
                                    <li id="secondlist_moduleCalendar">
                                        <table class = "singleColumnData">
                                            {$smarty.capture.moduleCalendar}
                                        </table>
                                    </li>
                {/if}

{*///MODULES INNERTABLES APPEARING*}
                {foreach name = 'module_inner_tables_list' key = key item = module from = $T_INNERTABLE_MODULES}
                    {assign var = module_name value = $key|replace:"_":""}
                    {if !in_array($module_name, $T_POSITIONS)}
                            <li id="secondlist_{$module_name}">
                                <table class = "singleColumnData">
                                    {$smarty.capture.$module_name}
                                </table>
                            </li>
                    {/if}
                {/foreach}

{*
                {if $T_INNERTABLE_MODULES}
                    {foreach name = 'module_inner_tables_list' key = key item = item from = $T_MODULES}
                        {if in_array($key, $T_INNERTABLE_MODULES)}
                            {assign var = module_name value = $key|replace:"_":""}
                            {if !in_array($module_name, $T_POSITIONS)}
                                <li id="secondlist_{$module_name}">
                                    <table class = "singleColumnData">
                                        {$smarty.capture.$module_name}
                                    </table>
                                </li>
                            {/if}
                        {/if}
                    {/foreach}
                {/if}
*}
                               </ul>
                            </div>
                        </div>
                    </td>
                </tr>

{else}                                                                          {*Pages with single-column layout*}
{*SINGLE MAIN COLUMN*}
                <tr>
                    <td class = "singleColumn" id = "singleColumn" colspan = "2">
                        <table class = "singleColumnData">
                                {$smarty.capture.moduleUsers}
                                {$smarty.capture.moduleNewUser}
                                {$smarty.capture.moduleNewLessonDirection}
                                {$smarty.capture.moduleLessons}
                                {$smarty.capture.moduleNewCourse}
                                {$smarty.capture.moduleCourses}
                                {$smarty.capture.moduleDirections}
                                {$smarty.capture.moduleTests}
                                {$smarty.capture.moduleRoles}
                                {$smarty.capture.moduleNewUserType}
                                {$smarty.capture.moduleGroups}
                                {$smarty.capture.moduleNewUserGroup}
                                {$smarty.capture.moduleNewsPage}
                                {$smarty.capture.moduleCms}
                                {$smarty.capture.moduleStatistics}
                                {$smarty.capture.moduleBackup}
                                {$smarty.capture.moduleLanguages}
                                {$smarty.capture.moduleStyle}
                                {$smarty.capture.moduleEmail}
                                {$smarty.capture.moduleImportExportUsers}
                                {$smarty.capture.moduleSearchResults}
                                {$smarty.capture.moduleConfig}
                                {$smarty.capture.moduleModules}
                                {$smarty.capture.moduleFileManager}
                                {$smarty.capture.moduleCleanup}
                                {$smarty.capture.moduleImportUsers}
                                {$smarty.capture.moduleCustomizeUsersProfile}
                                {$smarty.capture.importedModule}
                                {$smarty.capture.moduleHCD}
                                {$smarty.capture.moduleCalendarPage}
                                {$smarty.capture.moduleSearchCoursesPage}
                                {$smarty.capture.modulePaypal}
                                {$smarty.capture.moduleVersionKey}
                        </table>
                    </td>
                </tr>
{/if}
            </table>
        </td>
    </tr>

{if $T_CONFIGURATION.show_footer && !$smarty.get.popup && !$T_POPUP_MODE}
    {include file = "includes/footer.tpl"}
{/if}
</table>
{*-----------------------------End of Part 3: Display table-------------------------------------------------*}

{*-----------------------------Part 4: Finalization data etc-------------------------------------------------*}
{if $T_CTG == 'control_panel' && !$T_OP}
{literal}
        <script type = "text/javascript">
           Sortable.create("firstlist", {
             containment:["firstlist", "secondlist"], constraint:false,
             onUpdate: function() {
                new Ajax.Request('set_positions.php', {
                    method:'post',
                    asynchronous:true,
                    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
                    onSuccess: function (transport) {}
                });
            }});
           Sortable.create("secondlist",
             {containment:["firstlist","secondlist"],constraint:false,
             onUpdate: function() {
                new Ajax.Request('set_positions.php', {
                    method:'post',
                    asynchronous:true,
                    parameters: { firstlist: Sortable.serialize('firstlist'), secondlist: Sortable.serialize('secondlist') },
                    onSuccess: function (transport) {}
                });
            }});
        </script>
{/literal}
{/if}

{include file = "includes/closing.tpl"}

</body>
</html>

{if ($T_MODULE_HCD_INTERFACE && $T_CTG == 'users' && $smarty.get.print == 1 && $smarty.const.MSIE_BROWSER == 1)}
{literal}
<script>
printPartOfPage('singleColumn');
</script>
{/literal}
{/if}
