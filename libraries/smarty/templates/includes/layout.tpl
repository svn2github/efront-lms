{if $T_ADD_BLOCK_FORM}
    {$T_ADD_BLOCK_FORM.javascript}
    <form {$T_ADD_BLOCK_FORM.attributes}>
        {$T_ADD_BLOCK_FORM.hidden}
        <table style = "width:100%">
        	<tr><td class = "labelCell">{$T_ADD_BLOCK_FORM.title.label}:&nbsp;</td>
        		<td class = "elementCell">{$T_ADD_BLOCK_FORM.title.html}</td></tr>
			<tr><td></td><td style = "vertical-align:middle" id="toggleeditor_cell1"><a title="{$smarty.const._OPENCLOSEFILEMANAGER}" href="javascript:void(0)" onclick="toggle_file_manager();" style = "vertical-align:middle"><img id="arrow_down" src="images/16x16/navigate_down.png" border="0" alt="{$smarty.const._OPENCLOSEFILEMANAGER}" title="{$smarty.const._OPENCLOSEFILEMANAGER}" style="vertical-align:middle"/>&nbsp;<span id="open_manager" style = "vertical-align:middle">{$smarty.const._OPENFILEMANAGER}</span></a>&nbsp;&nbsp;<a href="javascript:toogleEditorMode('mce_editor_0');" id="toggleeditor_link"><img src = "images/16x16/replace2.png" title = "{$smarty.const._TOGGLEHTMLEDITORMODE}" alt = "{$smarty.const._TOGGLEHTMLEDITORMODE}" style = "vertical-align:middle" border = "0"/>&nbsp;<span  style = "vertical-align:middle">{$smarty.const._TOGGLEHTMLEDITORMODE}</span></a></td></tr>
            <tr><td></td><td id="filemanager_cell"></td></tr>
            <tr><td></td><td id="toggleeditor_cell2"></td></tr>
        	<tr><td class = "labelCell">{$T_ADD_BLOCK_FORM.content.label}:&nbsp;</td>
        		<td class = "elementCell">{$T_ADD_BLOCK_FORM.content.html}</td></tr>
        	<tr><td colspan = "2">&nbsp;</td></tr>
        	<tr><td></td></tr>
        	<tr><td></td>
        		<td class = "elementCell">{$T_ADD_BLOCK_FORM.submit_block.html}</td></tr>
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
                                                            mceAddControlDynamic(sEditorID, 'block_content_data' ,'mceEditor');
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
                                    {/literal}{if !$smarty.get.edit_block}{literal}
                                        var url = 'administrator.php?ctg=control_panel&op=system_config&add_block=1&postAjaxRequest_insert=1';
                                    {/literal}{else}{literal}
                                        var url = 'administrator.php?ctg=control_panel&op=system_config&edit_block={/literal}{$smarty.get.edit_block}{literal}&postAjaxRequest_insert=1';
                                    {/literal}{/if}{literal}
                                        new Ajax.Request(url, {
                                            method:'get',
                                            asynchronous:true,
                                            parameters: {file_id: id, editor_mode: tinyMCEmode},
                                            onSuccess: function (transport) {
                                                if(tinyMCEmode) {
                                                    tinyMCE.execInstanceCommand('mce_editor_0','mceInsertContent', false , transport.responseText);
                                                }else {
                                                    insertatcursor(window.document.add_block_form.block_content_data, transport.responseText);
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
	
{else}
    {capture name = "t_layout_code"}
    	{capture name = "layoutPreview"}
    	<table class = "layout preview">
    		<tr><td class = "header" colspan = "3"></td></tr>
    		<tr><td class = "left">
        			<div class = "layoutBlock">&nbsp;</div>
        			<div class = "layoutBlock">&nbsp;</div>
        			<div class = "layoutBlock">&nbsp;</div>
        		</td><td class = "center">
                	<div class = "layoutBlock">&nbsp;</div>
                	<div class = "layoutBlock">&nbsp;</div>
        		</td><td class = "right">
        			<div class = "layoutBlock">&nbsp;</div>
        			<div class = "layoutBlock">&nbsp;</div>
        			<div class = "layoutBlock">&nbsp;</div>
        		</td></tr>
    		<tr><td class = "footer" colspan = "3"></td></tr>
    	</table>
    	{/capture}
    
    	{capture name = "layoutEdit"}
    	<table class = "layout edit" id = "editLayout">
    		<tr><td class = "header" colspan = "3"></td></tr>
    		<tr><td class = "left"><ul class = "sortable" id = "leftList"></ul></td>
    			<td class = "center"><ul class = "sortable" id = "centerList"></ul></td>
    			<td class = "right"><ul class = "sortable" id = "rightList"></ul></td></tr>
    		<tr><td class = "footer" colspan = "3"></td></tr>
    	</table>
    	{/capture}
    
    <table id = "layoutMenu">
    	<tr>
    		<td id = "left">
            	<div class = "layoutLabel mediumHeader">{$smarty.const._SELECTLAYOUT}</div>
            	<div id = "layout_simple" class = "layout hideBoth" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
            	<div class = "layoutLabel">{$smarty.const._SIMPLE}</div>
            	<div id = "layout_left" class = "layout hideRight" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
            	<div class = "layoutLabel">{$smarty.const._TWOCOLUMNSLEFT}</div>
            	<div id = "layout_three" class = "layout" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
            	<div class = "layoutLabel">{$smarty.const._THREECOLUMNS} ({$smarty.const._DEFAULT})</div>
            	<div id = "layout_right" class = "layout hideLeft" onclick = "setSelected(this);">{$smarty.capture.layoutPreview}</div>
            	<div class = "layoutLabel">{$smarty.const._TWOCOLUMNSRIGHT}</div>
            </td>
            <td id = "center">
            	<div class = "layoutLabel mediumHeader">{$smarty.const._CURRENTLAYOUT}</div>
            	<div class = "layout">{$smarty.capture.layoutEdit}</div>
            </td>
            <td id = "right">
            	<div class = "layoutLabel mediumHeader">{$smarty.const._AVAILABLEBLOCKS}</div>
    			<ul class = "sortable" id = "toolsList"></ul>
            	<div class = "layoutBlock">{$smarty.const._ADDCUSTOMBLOCK}<br><img src = "images/32x32/add2.png" alt = "{$smarty.const._ADDBLOCK}" title = "{$smarty.const._ADDBLOCK}" onclick = "location = location + '&tab=layout&add_block=1'" style = "cursor:pointer"></div>
            </td>
        </tr>
        <tr><td colspan = "3" id = "buttons">
        		<div>
        			<a href = "javascript:void(0)" onclick = "updatePositions(this, true)">    				
        				<img src = "images/32x32/recycle.png" alt = "{$smarty.const._RESTOREDEFAULTLAYOUT}" title = "{$smarty.const._RESTOREDEFAULTLAYOUT}">
    	    			<br/>{$smarty.const._RESTOREDEFAULTLAYOUT}
        			</a>
        			<a href = "javascript:void(0)" onclick = "location=location+'&tab=layout'">    				
        				<img src = "images/32x32/undo.png" alt = "{$smarty.const._UNDOALLCHANGES}" title = "{$smarty.const._UNDOALLCHANGES}">
    	    			<br/>{$smarty.const._UNDOALLCHANGES}
        			</a>
        			<a href = "javascript:void(0)" onclick = "updatePositions(this)">    				
        				<img src = "images/32x32/check.png" alt = "{$smarty.const._SAVELAYOUT}" title = "{$smarty.const._SAVELAYOUT}">
    	    			<br/>{$smarty.const._SAVELAYOUT}
        			</a>
        		</div>
        </td></tr>
    </table>
    {/capture}
    
    
    {eF_template_printInnerTable title = $smarty.const._LAYOUT data = $smarty.capture.t_layout_code image = '/32x32/layout_center.png'}
    
    <script type="text/javascript">
    var currentLayout = '';
    var blocks = $H('{$T_BLOCKS}'.evalJSON(true));
    '{$T_POSITIONS}'.evalJSON(true) ? currentPositions = $H('{$T_POSITIONS}'.evalJSON(true)) : currentPositions = false;
    
    <!--
    {literal}
    
        function setSelected(el) {
        	Element.extend(el);
        	el.up().select('div.layout').each(function (s) {s.removeClassName('selectedLayout')})
        	el.addClassName('selectedLayout');
        	
        	if (el.hasClassName('hideRight')) {
        		resetLayouts('left');
        		$('editLayout').className = 'layout edit hideRight';
        	} else if (el.hasClassName('hideLeft')) {
        		resetLayouts('right');
        		$('editLayout').className = 'layout edit hideLeft';
        	} else if (el.hasClassName('hideBoth')) {
        		resetLayouts('simple');
        		$('editLayout').className = 'layout edit hideBoth';
        	} else {
        		resetLayouts('three');
        		$('editLayout').className = 'layout edit';
        	}    	
        	
        }
            
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
        	Element.extend(el);
        	reset ? url = 'administrator.php?control_panel&op=system_config&tab=layout&ajax=reset_layout' : url = 'administrator.php?control_panel&op=system_config&tab=layout&ajax=set_layout';
    
    		currentSource = el.down().src;
    		el.down().src = "images/others/progress_big.gif";
            
            new Ajax.Request(url, {
                method:'post',
                asynchronous:true,
                parameters: { leftList:   $('leftList')   ? Sortable.serialize('leftList')   : null, 
                			  centerList: $('centerList') ? Sortable.serialize('centerList') : null, 
                			  rightList:  $('rightList')  ? Sortable.serialize('rightList')  : null, 
                			  layout: currentLayout},
                onFailure: function (transport) {
                	showMessage(transport.responseText, 'failure');
                },
                onSuccess: function (transport) {         
                    if (reset) {
                    	location=location+'&tab=layout';
                    } else {
    	                el.down().src = currentSource;
                    }
                }
            });		
    
        }	    
    
    	/**
    	* Add block
    	* 
    	* This function adds a block to the layout, either to one of the 3 main columns (left, right, center), or to 
    	* the tools list.
    	*/
    	function addBlock(list, block) {
    		var li = new Element('li', {id:list+'_'+block}).insert(new Element('div').addClassName('layoutBlock').insert(new Element('div').update(blocks.get(block))));
    		li.observe('dblclick', function (event) {if (!this.descendantOf($('toolsList'))) {$('toolsList').insert(this.remove())}});	//On double click, remove blocks frmo layout and put them in the tools list
    		if (!isNaN(parseInt(block))) {		//This means that this is a custom block, since for custom blocks indexes are numeric (as opposed to default blocks, which are 'login', 'online' etc)
    			li.childElements()[0].insert(new Element('img', {src: 'images/16x16/edit.png', alt: '{/literal}{$smarty.const._EDIT}{literal}', title: '{/literal}{$smarty.const._EDIT}{literal}'}).addClassName('tool').observe('click', function (event) {location = 'administrator.php?control_panel&op=system_config&tab=layout&edit_block='+block}));
    			li.childElements()[0].insert(new Element('img', {src: 'images/16x16/delete.png', alt: '{/literal}{$smarty.const._DELETE}{literal}', title: '{/literal}{$smarty.const._DELETE}{literal}'}).addClassName('tool').observe('click', function (event) {if (confirm('{/literal}{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}{literal}')) deleteBlock(this, block)}));
    		}
    		$(list).insert(li);
    		remainingBlocks.unset(block);		    		
    	}
    	
    	function deleteBlock(el, block) {
    		Element.extend(el);
    		el.src = 'images/others/progress1.gif';
    		url = 'administrator.php?control_panel&op=system_config&tab=layout&ajax=1&delete_block='+block;
            new Ajax.Request(url, {
                method:'get',
                asynchronous:true,
                onFailure: function (transport) {
                	el.src = 'images/16x16/delete.png';
                	showMessage(transport.responseText, 'failure');
                },
                onSuccess: function (transport) {
                	el.up().up().remove();
                }
			}); 
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
        	$('toolsList').childElements().each(function(s) {s.remove()});					//Remove all existing children from the tools sidebar
        	$('editLayout').select('li').each(function(s) {s.remove()});					//Remove all blocks from all layouts
        	if (currentPositions && currentPositions.get('layout') == layout) {				//Set the user-defined layout
        		currentPositions.get('leftList').each(function (s) {addBlock('leftList', s, blocks.s)});
        		currentPositions.get('rightList').each(function (s) {addBlock('rightList', s, blocks.s)});
        		currentPositions.get('centerList').each(function (s) {addBlock('centerList', s, blocks.s)});
        	} else {
            	switch (layout) {																//Create the default layouts containments
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
        	remainingBlocks.each(function (s) {addBlock('toolsList', s[0], s[1]);});							//Add the remaining blocks to the tools sidebar
        	$('toolsList').insert(new Element('li').update('&nbsp;'));											//Add a li that will create the required space to put elements when ul is empty
        	$('editLayout').select('ul').each(function(s) {s.insert(new Element('li').update('&nbsp;'))});		//Add a li that will create the required space to put elements when ul is empty
        	
        	currentLayout = layout;																				//Set the current layout to the selected one
        	createSortables();																					//Refresh sortables
        }
    
        //Initialize based on 3-column layout
        currentPositions ? setSelected($('layout_'+currentPositions.get('layout'))) : setSelected($('layout_three'));       
        
    {/literal}
    -->
    </script>
{/if}





