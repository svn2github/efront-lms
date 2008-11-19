{*Closing template functions*}
<script>var point5 = new Date().getTime();</script>

{assign var = "load_tree" value = false}
{foreach name = 'scripts_list' item = item key = key from = $T_HEADER_LOAD_SCRIPTS}
    {if $item == 'drag-drop-folder-tree'}{assign var = "load_tree" value = true}{/if}
{/foreach}

{if $load_tree}
    <script language = "JavaScript" type = "text/javascript" >
    {literal}
    $$('ul', 'dhtmlgoodies_tree').each(function (s) {
        if (s.id.startsWith('dhtml')) {
            treeObj = new JSDragDropTree();

            treeObj.setTreeId(s.id);
            treeObj.setMaximumDepth(20);
            treeObj.initTree();

            if (treeObj.getNodeOrders().length) {
                if ($('expand_collapse_div') && $('expand_collapse_div').getAttribute("expand")) {
                    $('expand_collapse_div').getAttribute("expand") ? treeObj.expandAll() : treeObj.collapseAll();
                } else {
                    if (treeObj.getNodeOrders().match(/-/g).length > {/literal}{if $T_MODULE_HCD_INTERFACE  && $T_CTG == "module_hcd" && $T_OP == "chart"}100{else}30{/if}{literal}) {
                        treeObj.collapseAll();
                        // using the title attibute to maintain the information whether the tree is expanded or not
                        s.collapsed = true;
                        treeObj.collapsed = true;
                    } else {
                        s.collapsed = false;
                        treeObj.expandAll();
                        treeObj.collapsed = false;
                    }
                }
                {/literal}{if $T_UNIT}var currentUnit = $('node{$T_UNIT.id}');{else}var currentUnit = '';{/if}{literal}
                if ($('expand_collapse_div')) {
                	treeObj.getNodeOrders().match(/-[^0]/g) &&																	//Has subnodes 
                	treeObj.getNodeOrders().match(/-[^0]/g).length > 10 && 														//Has more than 10 root nodes
                	treeObj.getNodeOrders().match(/-/g).length > 30 && 															//Has more than 30 nodes
                	!currentUnit ? $('expand_collapse_div').show() : $('expand_collapse_div').hide(); 							//Do not display "expand/collapse all" link if there are few children
                }
                if (currentUnit && treeObj.status == 0) {                                       //If status = 0, then the tree is collapsed. So we need to make appear the seelcted node
                    var depth = treeObj.dragDropCountLevels(currentUnit, 'up');
                    for (var i = depth; i > 0; i--) {                                           //Make appear the parent nodes of the selected node
                        treeObj.showHideNode(false, currentUnit.id);
                        currentUnit = currentUnit.parentNode.parentNode;
                    }
                }
            } else if ($('expand_collapse_div')) {      //Hide expand/collapse link for an empty tree
                $('expand_collapse_div').update('<span class = "emptyCategory">{/literal}{$smarty.const._NOCONTENTFOUND}{literal}</span>');
            }
            Event.observe(s.id, 'load', positionCorrectly(s.id));
        }
    });
    //This is used to correctly position any content tree tools. It is put here so that html loading and rendering is complete (or nearly complete)
    function positionCorrectly(id) {
        obj   = $(id);
        //obj2 = obj.up().up().up().up().up();
        width = obj.getWidth() + obj.positionedOffset().left;
        //alert('width: '+width+'up: '+obj2+parseInt(parseInt(obj2.getWidth())+parseInt(obj2.positionedOffset().left)));
        $(obj).select('toolsDiv').each(function (s) {s.setStyle({left: width+'px'})});
    }

    {/literal}
    </script>
{/if}
{*These 2 scripts need to be to the end of file to work correctly*}

<div id = "user_table" style = "display:none">
    <table width = "100%">
        <tr><td align = "left" id = "user_box" style = "padding:3px 3px 4px 5px;"></td></tr>
    </table>
</div>

{*This table is used to display popups*}
<table id = "popup_table" class = "divPopup" style = "display:none;">
    <tr class = "defaultRowHeight">
        <td class = "topTitle" id = "popup_title"></td>
        <td class = "topTitle" style = "width:1%;"><img src = "images/16x16/error.png" alt = "{$smarty.const._CLOSE}" name = "" id = "popup_close" title = "{$smarty.const._CLOSE}" onclick = "if (document.getElementById('reloadHidden') && document.getElementById('reloadHidden').value == '1')  parent.frames[1].location = parent.frames[1].location;eF_js_showDivPopup('', '', this.name);"/>
    </td></tr>
    <tr><td colspan = "2" id = "popup_data" style = "vertical-align:top;width:100%;height:100%"></td></tr>

    <tr><td colspan = "2" id = "frame_data" style = "width:100%;height:100%">
        <iframe name = "POPUP_FRAME" id = "popup_frame" src = "about:blank" style = "border-width:0px;width:100%;height:100%;padding:0px 0px 0px 0px">Sorry, but your browser needs to support iframes to see this</iframe>
    </td></tr>
</table>
<div id = "error_details" style = "display:none"><pre>{$T_EXCEPTION_TRACE}</pre></div>
<script>if (parent.frames[0].document.getElementById('dimmer')) parent.frames[0].document.getElementById('dimmer').style.display = 'none'</script>
<div id="dimmer" class = "dimmerDiv" style="display:none;" {*onclick = "this.style.display = 'none';"*}></div>
{if isset($div_error)}<script>eF_js_showDivPopup('{$div_error}');</script>{/if}
<div id = 'showMessageDiv' style = "display:none"></div>

<script>
{if $T_ADD_ANOTHER}
{literal}
document.getElementById('add_new_event_link').onclick();
document.getElementById('popup_frame').src ="{/literal}{$smarty.session.s_type}.php?ctg=calendar&view_calendar={$T_VIEW_CALENDAR}{if $smarty.get.show_interval}&show_interval={$smarty.get.show_interval}{/if}&add_calendar=1{$T_CALENDAR_TYPE_LINK}&message={$smarty.get.pmessage}&message_type={$smarty.get.pmessage_type}{literal}";
{/literal}
{/if}
</script>

<script>
{literal}
if (window.Element) {
	//$$('form').each(function (s) {s.observe('submit', function (event) {s.select('input.flatButton').each(function (k) {k.addClassName('loadingButton')})})})
	//$$('form').each(function (s) {s.select('input.flatButton').each(function (k) {k.observe('click', function (event) {alert(k);})})});
}
{/literal}
</script>

{*
<script>
    var end = new Date().getTime();
    var interval1 = (point1 - start)/1000;
    var interval2 = (point2 - point1)/1000;
    var interval3 = (point3 - point2)/1000;
    var interval4 = (point4 - point3)/1000;


    var interval5 = (point5 - point4)/1000;
    var interval6 = (point6 - point5)/1000;
    var interval7 = (point7 - point6)/1000;
    var interval8 = (end - point7)/1000;
    var total = (end - start)/1000;
//    alert('1: '+interval1+'\n 2: '+interval2+'\n 3: '+interval3+'\n 4: '+interval4+'\n 4_1: '+interval4_1+'\n 4_1_1: '+interval4_1_1+'\n 4_1_2: '+interval4_1_2+'\n 4_2: '+interval4_2+'\n 4_2_1: '+interval4_2_1+'\n 4_2_2: '+interval4_2_2+'\n 4_3: '+interval4_3+'\n 4_4: '+interval4_4+'\n 5: '+interval5+'\n 6: '+interval6+'\n 7: '+interval7+'\n total: '+total);
    alert('1: '+interval1+'\n 2: '+interval2+'\n 3: '+interval3+'\n 4: '+interval4+'\n 5: '+interval5+'\n 6: '+interval6+'\n 7: '+interval7+'\n 8: '+interval8+'\n total: '+total);
</script>
*}
