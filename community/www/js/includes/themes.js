function setBrowser(el, id, browser) {
 parameters = {'set_browser':id, browser: browser, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onSetBrowser);
}
function onSetBrowser(el, response) {
    $('themesTable').select('img.sprite16-pin_green').each(function (s) {if (s.up().hasClassName(response)) {s.writeAttribute({alt: usetheme, title: usetheme});setImageSrc(s, 16, 'pin_red.png');}});
    el.writeAttribute({alt: activetheme, title: activetheme});
    setImageSrc(el, 16, 'pin_green.png');
    if (response.match(/\.php/)) {
     top.location = response;
    }
}
/**

 * 

 * @param el

 * @param id

 * @return

 */
function useTheme(el, id) {
 parameters = {'set_theme':id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onUseTheme);
}
/**

 * 

 * @param el

 * @param response

 * @return

 */
function onUseTheme(el, response) {
    //$('themesTable').select('img.sprite16-pin_green').each(function (s) {if (s.up().hasClassName('currentTheme')) {s.writeAttribute({alt: usetheme, title: usetheme});setImageSrc(s, 16, 'pin_red.png');}});
 $('themesTable').select('img.sprite16-pin_green').each(function (s) {s.writeAttribute({alt: usetheme, title: usetheme});setImageSrc(s, 16, 'pin_red.png');});
    el.writeAttribute({alt: activetheme, title: activetheme});
    setImageSrc(el, 16, 'pin_green.png');
    //top.location.reload();
    top.location = response;
    //Can't do a top.location=top.location+'&tab=set_theme' because of sidebar;
/*    

    if (top.sideframe) {

    	top.sideframe.location.reload();

    }

    if (top.mainframe) {

    	top.mainframe.location = top.mainframe.location.toString()+'&tab=set_theme';

    } else {

    	top.location = top.location.toString()+'&tab=set_theme';

    }

*/
}
/**

 * 

 * @param el

 * @param page

 * @return

 */
function usePage(el, page) {
    if (el.src.match('pin_green.png')) {
     parameters = {use_none: 1, method: 'get'};
        var set_page = 0;
    } else {
     parameters = {set_page: page, method: 'get'};
        var set_page = 1;
    }
 var url = location.toString();
 ajaxRequest(el, url, parameters, onUsePage);
}
/**

 * 

 * @param el

 * @param response

 * @return

 */
function onUsePage(el, response) {
    $('cms_table').select('img').each(function (s) {if (s.src.match('pin_green.png')) s.src = 'themes/default/images/others/transparent.gif'; s.addClassName('sprite16').addClassName('sprite16-pin_red');});
    if (response == '1') {
        el.src = 'themes/default/images/others/transparent.gif';
        el.addClassName('sprite16').addClassName('sprite16-pin_green');
    }
}
/**

 * 

 * @param el

 * @return

 */
function resetHeader(el) {
    window.location.toString().match(/header/) ? edit = 'header' : edit = 'footer';
 parameters = {reset:1, method: 'get'};
 var url = location.toString()+'&edit_'+edit;
 ajaxRequest(el, url, parameters, onResetHeader);
}
/**

 * 

 * @param el

 * @param response

 * @return

 */
function onResetHeader(el, response) {
 window.location.toString().match(/header/) ? edit = 'header' : edit = 'footer';
 window.location = location.toString()+'&edit_'+edit+'=1&tab=layout';
}
/**

 * 

 * @param el

 * @param type

 * @return

 */
function hideHeader(el, type) {
 type == 'header' ? edit = 'header' : edit = 'footer';
 parameters = {hide:1, method: 'get'};
 var url = location.toString()+'&edit_'+edit;
 ajaxRequest(el, url, parameters, onHideHeader);
}
/**

 * 

 * @param el

 * @param response

 * @return

 */
function onHideHeader(el, response) {
 if (parseInt(response) == 0) {
     el.up().addClassName('collapse');
     el.removeClassName('sprite16-navigate_up').addClassName('sprite16-navigate_down');
 } else {
  el.up().removeClassName('collapse');
  el.removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up');
 }
}
/**

 * 

 * @param el

 * @return

 */
function setSelected(el) {
 Element.extend(el);
 el.up().select('div.layout').each(function (s) {s.removeClassName('selectedLayout');});
 el.addClassName('selectedLayout');
 if (el.hasClassName('hideRight')) {
  resetLayouts('left');
  $('editLayout').className = 'layout edit hideLayoutRight';
 } else if (el.hasClassName('hideLeft')) {
  resetLayouts('right');
  $('editLayout').className = 'layout edit hideLayoutLeft';
 } else if (el.hasClassName('hideBoth')) {
  resetLayouts('simple');
  $('editLayout').className = 'layout edit hideLayoutBoth';
 } else {
  resetLayouts('three');
  $('editLayout').className = 'layout edit';
 }
}
/**

 * 

 * @return

 */
function createSortables() {
    Sortable.create("rightList", {
        containment:["leftList", "centerList", "rightList", "toolsList"], constraint:false
    });
    Sortable.create("leftList", {
        containment:["leftList", "centerList", "rightList", "toolsList"], constraint:false
    });
    Sortable.create("centerList", {
        containment:["leftList", "centerList", "rightList", "toolsList"], constraint:false
    });
    Sortable.create("toolsList", {
        containment:["leftList", "centerList", "rightList", "toolsList"], constraint:false
    });
}
/**

* Update positions

* 

* This function performs an ajax post query to the administrator file, in order to persist

* the current layout. Alternatively, it may reset the current layout to the default one.

*/
function updatePositions(el, reset) {
    parameters = {leftList: $('leftList') ? Sortable.serialize('leftList') : null,
      centerList: $('centerList') ? Sortable.serialize('centerList') : null,
      rightList: $('rightList') ? Sortable.serialize('rightList') : null,
      layout: currentLayout,
      method: 'post'};
    reset ? url = location.toString()+'&ajax=reset_layout' : url = location.toString()+'&ajax=set_layout';
 ajaxRequest(el, url, parameters, onUpdatePostions);
}
function onUpdatePostions(el, response) {
    if (response == 'reset') {
     window.location.reload();
    }
}
/**

* Add block

* 

* This function adds a block to the layout, either to one of the 3 main columns (left, right, center), or to 

* the tools list.

*/
function addBlock(list, block) {
 var li = new Element('li', {id:list+'_'+block}).insert(new Element('div').addClassName('layoutBlock').insert(new Element('div').update(blocks.get(block))));
 li.observe('dblclick', function (event) {if (!this.descendantOf($('toolsList'))) {$('toolsList').insert(this.remove())}}); //On double click, remove blocks frmo layout and put them in the tools list
 if (!isNaN(parseInt(block))) { //This means that this is a custom block, since for custom blocks indexes are numeric (as opposed to default blocks, which are 'login', 'online' etc)
  currentPositions.get('enabled') && currentPositions.get('enabled')[block] ? toggleImg = 'success' : toggleImg = 'forbidden';
  li.childElements()[0].insert(new Element('img', {src: 'themes/default/images/others/transparent.gif', alt: toggletag, title: toggletag}).addClassName('sprite16').addClassName('sprite16-'+toggleImg).addClassName('tool').observe('click', function (event) {toggleBlockAccess(this, block);}));
  li.childElements()[0].insert(new Element('img', {src: 'themes/default/images/others/transparent.gif', alt: edittag, title: edittag}).addClassName('sprite16').addClassName('sprite16-edit').addClassName('tool').observe('click', function (event) {location = location.toString()+'&edit_block='+block;}));
  li.childElements()[0].insert(new Element('img', {src: 'themes/default/images/others/transparent.gif', alt: deletetag, title: deletetag}).addClassName('sprite16').addClassName('sprite16-error_delete').addClassName('tool').observe('click', function (event) {if (confirm(irreversible)) deleteBlock(this, block);}));
 }
 $(list).insert(li);
 remainingBlocks.unset(block);
}
function toggleBlockAccess(el, block) {
 parameters = {toggle_block:block, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onToggleBlockAccess);
 //el.src.match('forbidden') ? el.writeAttribute({src:'themes/default/images/16x16/success.png'}) : el.writeAttribute({src:'themes/default/images/16x16/forbidden.png'});
}
function onToggleBlockAccess(el, response) {
 response.evalJSON(true).enabled ? el.writeAttribute({src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').removeClassName('sprite16-forbidden').addClassName('sprite16-success') : el.writeAttribute({src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').removeClassName('sprite16-success').addClassName('sprite16-forbidden');
}
/**

 * 

 * @param el

 * @param block

 * @return

 */
function deleteBlock(el, block) {
 parameters = {delete_block:block, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onDeleteBlock);
}
/**

 * 

 * @param el

 * @param response

 * @return

 */
function onDeleteBlock(el, response) {
 new Effect.Fade(el.up().up());
}
/**

* Reset layouts

* 

* This function is used to reset the layouts, when a different layout is selected. 

* All contents from all layouts are removed, and only the selected one is populated

* with the default contents. 

*/
function resetLayouts(layout) {
 remainingBlocks = blocks.clone();
 $('toolsList').childElements().each(function(s) {s.remove();}); //Remove all existing children from the tools sidebar
 $('editLayout').select('li').each(function(s) {s.remove();}); //Remove all blocks from all layouts
 if (currentPositions && currentPositions.get('layout') == layout) { //Set the user-defined layout
  currentPositions.get('leftList').length ? currentPositions.get('leftList').each(function (s) {addBlock('leftList', s, blocks.s);}) : null;
  currentPositions.get('rightList').length ? currentPositions.get('rightList').each(function (s) {addBlock('rightList', s, blocks.s);}) : null;
  currentPositions.get('centerList').length ? currentPositions.get('centerList').each(function (s) {addBlock('centerList', s, blocks.s);}) : null;
 } else {
     switch (layout) { //Create the default layouts containments
      case 'three':
       addBlock('leftList', 'login', blocks.login);
       addBlock('leftList', 'online', blocks.online);
       addBlock('centerList', 'lessons', blocks.lessons);
       addBlock('rightList', 'news', blocks.news);
       addBlock('rightList', 'selectedLessons', blocks.selectedLessons);
      break;
      case 'left':
       addBlock('leftList', 'login', blocks.login);
       addBlock('leftList', 'online', blocks.online);
       addBlock('leftList', 'news', blocks.news);
       addBlock('leftList', 'selectedLessons', blocks.selectedLessons);
       addBlock('centerList', 'lessons', blocks.lessons);
      break;
      case 'right':
       addBlock('rightList', 'login', blocks.login);
       addBlock('rightList', 'online', blocks.online);
       addBlock('rightList', 'news', blocks.news);
       addBlock('rightList', 'selectedLessons', blocks.selectedLessons);
       addBlock('centerList', 'lessons', blocks.lessons);
      break;
      case 'simple':
       addBlock('centerList', 'login', blocks.lessons);
       //blocks.each(function (s) {if (s[0] != 'login') {addBlock('toolsList', s[0], s[1]);}});	//Add the remaining blocks to the tools sidebar
      break;
      default: break;
     }
    }
 remainingBlocks.each(function (s) {addBlock('toolsList', s[0], s[1]);}); //Add the remaining blocks to the tools sidebar
 $('toolsList').insert(new Element('li').update('&nbsp;')); //Add a li that will create the required space to put elements when ul is empty
 $('editLayout').select('ul').each(function(s) {s.insert(new Element('li').update('&nbsp;'))}); //Add a li that will create the required space to put elements when ul is empty
 currentLayout = layout; //Set the current layout to the selected one
 createSortables(); //Refresh sortables
}
function exportTheme(el, id) {
 parameters = {export_theme:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onExportTheme);
}
function onExportTheme(el, response) {
 //window.open()
 $('popup_frame').src = 'view_file.php?file='+response;
 //alert(response);
}
function resetTheme(el, id) {
 parameters = {reset_theme:id, method: 'get'};
 var url = location.toString();
 ajaxRequest(el, url, parameters, onResetTheme);
}
function onResetTheme(el, response) {
 top.location.reload();
/*	

    if (top.sideframe) {

    	top.sideframe.location.reload();

    }

    if (top.mainframe) {

    	top.mainframe.location = top.mainframe.location.toString()+'&tab=set_theme';

    } else {

    	top.location = top.location.toString()+'&tab=set_theme';

    }	

*/
}
function exportLayout(el) {
 parameters = {export_layout:1, method: 'get'};
 var url = location.toString();
 ajaxRequest(Element.extend(el).previous(), url, parameters, onExportLayout);
}
function onExportLayout(el, response) {
 //alert(response.evalJSON(true).file);
 $('popup_frame').src = 'view_file.php?file='+response.evalJSON(true).file;
}
if (blocks && positions) {
 var blocks = $H(blocks.evalJSON(true));
 positions.evalJSON(true) ? currentPositions = $H(positions.evalJSON(true)) : currentPositions = false;
 currentPositions && currentPositions.get('layout') ? setSelected($('layout_'+currentPositions.get('layout'))) : setSelected($('layout_three'));
}
