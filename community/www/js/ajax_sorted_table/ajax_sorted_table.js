 var minimumRows = 3; //Below minimumRows, the table does not display the status/tool bar below it
    var defaultRowsPerPage = 20; //This will change    

    var allTables = document.getElementsByTagName('table'); //Get all the tables in this document
    var sortedTables = new Array(); //This global array will hold all the tables that need to have sorting/paging capabilities
    var useAjax = new Array();
    var autoAjax = new Array();
    var rowsPerPage = new Array();
    var tableSize = new Array();
    var ajaxUrl = new Array();
    var noFooter = new Array();

    var currentOffset = new Array();
    var currentSort = new Array();
    var currentOrder = new Array();
    var currentOther = new Array();
    var currentFilter = new Array();

    var branchFilter = new Array();
    var jobFilter = new Array();
    var currentBranchFilter = new Array();
    var currentJobFilter = new Array();

    var checkedEntries = new Array();

    for (var k = 0; k < allTables.length; k++) { //Walk through all document tables
        if (allTables[k].className.match('sortedTable')) { //Get all tables that have 'sortedTable' as part of their class definition. These will be paging/sorting enabled
            //sortedTables.push(allTables[k]);                                //Add this table to the sorted tables array
            table = allTables[k]; //For this table, create the sorting table headers            
            init(table, true);
        }
    }

    function init(table, isFirst, idx) {

     var count = 0;
        if (isFirst) {
            sortedTables.push(table);
            tableIndex = sortedTables.length-1;
        } else {
            sortedTables[idx] = table;
            tableIndex = idx;
        }

        other = '';
  if (table.getAttribute('other')) {
   other = table.getAttribute('other');
  }

        for (i = 0; i < table.rows[0].cells.length; i++) { //the first table row, table.row[0] should hold the cell headlines.
            if (!table.rows[0].cells[i].className.match('noSort')) { //If a column has the class \"noSort\" defined, make it non-sortable. Furthermore, if the table has only 1 or 2 rows, it is empty so disabled sorting features
                var anchor = document.createElement('a'); //create the link that will be used to sort the table on this field
                anchor.setAttribute('href', 'javascript:void(0)'); //Inactive link
                anchor.setAttribute('id', tableIndex + '_' + table.rows[0].cells[i].getAttribute('name')); //The id corresponds to <current table>_<current link>, so that we know which table is sorted, if there are multiple paged tables, and which link was pressed
                anchor.setAttribute('tableIndex', tableIndex);
                anchor.setAttribute('order', 'asc');
                //anchor.style.paddingRight = '15px';
                if (Element.hasClassName(table.rows[0].cells[i], 'centerAlign')) { //For proper alignment, otherwise center-aligned elements display a little to the right. For some strange reason, obj.hasClassName() does not work in IE, and we must use Element.hasclassName() notation
                 //anchor.style.paddingLeft = '15px';
                }
                anchor.style.verticalAlign = 'middle';
                anchor.setAttribute('column_name', table.rows[0].cells[i].getAttribute('name'));
                anchor.onclick = function () {eF_js_sortTable(this, other);}; //Add the sorting function to the onclick event
                anchor.innerHTML = table.rows[0].cells[i].innerHTML; //Copy the cells content inside the link
                table.rows[0].cells[i].innerHTML = ''; //Remove the cell content, since it was copied to the link
                table.rows[0].cells[i].appendChild(anchor); //Append the link to the cell

                if (table.getAttribute('sortBy') && table.getAttribute('sortBy') == i) {
                    var sortBy = anchor; //Assign the element that will be initially sorted for
                    if (table.getAttribute('order') && table.getAttribute('order') == 'desc') {
                     anchor.setAttribute('order', 'desc');
                    }
                }
            }
        }

  if (!$('loading_'+table.id)) {
      loadingDiv = new Element('div', {id:'loading_'+table.id}).addClassName('loading');
      loadingDiv.setOpacity(0.9);
      loadingDiv.insert(new Element('div').insert(new Element('img', {src:'js/ajax_sorted_table/images/progress1.gif'}).setStyle({verticalAlign:'middle'}))
                 .insert(new Element('span').setStyle({verticalAlign:'middle'}).update('&nbsp;'+sorted_translations["loadingdata"]))
                 .setStyle({top:'50%',left:'45%',position:'absolute'}));
  }

        if (isFirst) {

            checkedEntries[tableIndex] = new Array();
            if (getCookie('cookieTableRows')) {
             rowsPerPage[tableIndex] = getCookie('cookieTableRows');
            } else if (table.getAttribute('rowsPerPage')) {
                rowsPerPage[tableIndex] = parseInt(table.getAttribute('rowsPerPage'));
            } else {
                rowsPerPage[tableIndex] = defaultRowsPerPage;
            }

            if (table.getAttribute('useAjax')) {
          document.body.appendChild(loadingDiv);
          loadingDiv.clonePosition(table);
         //alert(loadingDiv.getDimensions().width +'x'+loadingDiv.getDimensions().height);
    if (loadingDiv.getDimensions().height == 0 && loadingDiv.getDimensions().width == 0) {
     loadingDiv.setStyle({display:'none'});
    }

                useAjax[tableIndex] = true;
                ajaxUrl[tableIndex] = table.getAttribute('url');
                autoAjax[tableIndex] = true;
                if (table.getAttribute('no_auto')) {
     loadingDiv.hide();
     loadingDiv.writeAttribute({loaded:true});
                 autoAjax[tableIndex] = false;
                }
            }
            if (table.getAttribute('branchFilter')) {
             branchFilter[tableIndex] = table.getAttribute('branchFilter');
            } else {
             branchFilter[tableIndex] = false;
            }
            if (table.getAttribute('jobFilter')) {
             jobFilter[tableIndex] = table.getAttribute('jobFilter');
            } else {
             jobFilter[tableIndex] = false;
            }


            if (table.getAttribute('noFooter') == 'true') {
                noFooter[tableIndex] = true;
            }
            tableSize[tableIndex] = Math.ceil(table.getAttribute('size') / rowsPerPage[tableIndex]);

            eF_js_pageTable(tableIndex); //Convert this table to paged table.

            if (sortBy && autoAjax[tableIndex]) {
                eF_js_sortTable(sortBy);
            } else if (useAjax[tableIndex] && autoAjax[tableIndex]) {
                eF_js_sortTable(anchor, other); //Ajax must be initialized some way, and sortBy is a convenient one.
            }
            sortBy = false;


        } else {
            tableSize[tableIndex] = Math.ceil(table.getAttribute('size') / rowsPerPage[tableIndex]);
            eF_js_pageTable(tableIndex); //Convert this table to paged table.
        }

    }

    function eF_js_rebuildTable(tableIndex, offset, column_name, order, other, noDiv) {
     try {
      if (window.onBeforeSortedTable) {
       window.onBeforeSortedTable(sortedTables[tableIndex]);
      }

      //eF_js_getChecked (tableIndex);
      if (!column_name) {
       column_name = '';
      }
      if (!order) {
       order = '';
      }

      currentOffset[tableIndex] = offset;
      currentSort[tableIndex] = column_name;
      currentOrder[tableIndex] = order;
      if (Object.isUndefined(other)) {
       other = '';
      }
      currentOther[tableIndex] = other;

      var el = document.getElementById(tableIndex+ '_' + column_name);
      url = ajaxUrl[tableIndex]+'ajax='+sortedTables[tableIndex].id+'&limit='+rowsPerPage[tableIndex]+'&offset='+offset+'&sort='+column_name+'&order='+order+'&other='+other;

      if (currentFilter[tableIndex] || currentBranchFilter[tableIndex] || currentJobFilter[tableIndex]) {
       //url = url + '&filter='+currentFilter[tableIndex]+((currentBranchFilter[tableIndex])?currentBranchFilter[tableIndex]:'')+'||||'+((currentJobFilter[tableIndex])?currentJobFilter[tableIndex]:'');
       url = url + '&filter='+currentFilter[tableIndex]+'||||'+((currentBranchFilter[tableIndex])?currentBranchFilter[tableIndex]:'')+'||||'+((currentJobFilter[tableIndex])?currentJobFilter[tableIndex]:'');
      }

      var loadingDiv = $('loading_'+sortedTables[tableIndex].id);
      loadingDiv.clonePosition(sortedTables[tableIndex]);
      //sortedTables[tableIndex].ancestors().each(function (s) {alert(s.getDimensions().height + ' ' + s.tagName + ' ' + s.id);});
      //alert(sortedTables[tableIndex].up().getDimensions().up().up().up().height);
      if (!noDiv && (loadingDiv.getDimensions().height > 0 || loadingDiv.getDimensions().width > 0)) {
       loadingDiv.show();
      }

      new Ajax.Request(url, {
       method:'get',
       asynchronous:true,
       onFailure: function (transport) {
       var tableId = sortedTables[tableIndex].id;
       var spanElement = document.createElement('span');
       spanElement.innerHTML += transport.responseText;
       sortedTables[tableIndex].parentNode.replaceChild(spanElement, sortedTables[tableIndex]);
       loadingDiv.hide();
       loadingDiv.writeAttribute({loaded:true});
       //alert(transport.responseText);
      },
      onSuccess: function (transport) {
       var tableId = sortedTables[tableIndex].id;
       var spanElement = document.createElement('span');

       var re2 = new RegExp("<!--ajax:"+tableId+"-->((.*[\n])*)<!--\/ajax:"+tableId+"-->"); //Does not work with smarty {strip} tags!
       var tableText = re2.exec(transport.responseText);
       if (!tableText) {
        var re = new RegExp("<!--ajax:"+tableId+"-->((.*[\r\n\u2028\u2029])*)<!--\/ajax:"+tableId+"-->"); //Does not work with smarty {strip} tags!
        tableText = re.exec(transport.responseText);
       }

       spanElement.innerHTML += tableText[1];
       //spanElement.innerHTML += transport.responseText;

       sortedTables[tableIndex].parentNode.replaceChild(spanElement, sortedTables[tableIndex]);

       loadingDiv.hide();
       //loadingDiv.setStyle({width:'0px', height:'0px'});
       loadingDiv.writeAttribute({loaded:true});

       init(document.getElementById(tableId), false, tableIndex);

       document.getElementById(tableIndex+'_sortedTable_currentPage').selectedIndex = Math.ceil(currentOffset[tableIndex]/rowsPerPage[tableIndex]);

       sortedTables[tableIndex].style.visibility = 'visible';
       //loadingDiv.clonePosition(sortedTables[tableIndex]);

       if (el) {
        if (currentOrder[tableIndex] == 'desc') { //Set the icons through the class to reflect the order, ascending or descending
         document.getElementById(el.id).className = 'sortDescending';
         document.getElementById(el.id).setAttribute('order', 'asc');
         if (document.getElementById(el.id).up().select('img').length == 0) {
          document.getElementById(el.id).up().insert(new Element('img', {src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').addClassName('sprite16-navigate_down').setStyle({verticalAlign:'middle'}));
         } else {
          document.getElementById(el.id).up().select('img')[0].src = 'themes/default/images/others/transparent.gif';
          document.getElementById(el.id).up().select('img')[0].addClassName('sprite16').addClassName('sprite16-navigate_down');
         }
        } else {
         document.getElementById(el.id).className = 'sortAscending';
         document.getElementById(el.id).setAttribute('order', 'desc');
         if (document.getElementById(el.id).up().select('img').length == 0) {
          document.getElementById(el.id).up().insert(new Element('img', {src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').addClassName('sprite16-navigate_up').setStyle({verticalAlign:'middle'}));
         } else {
          document.getElementById(el.id).up().select('img')[0].src = 'themes/default/images/others/transparent.gif';
          document.getElementById(el.id).up().select('img')[0].addClassName('sprite16').addClassName('sprite16-navigate_up');
         }
        }
       }

       //eF_js_setChecked(tableIndex);
       //table.rows[0].style.visibility = 'visible';

       if (window.onSortedTableComplete) {
        window.onSortedTableComplete();
       }
       if (sortedTables[tableIndex].hasClassName('subSection')) {
        onLoadSubSection(sortedTables[tableIndex]);
       }
      }
      });
     } catch (e) {
      handleException(e);
     }

    }



function eF_js_sortTable(el, other) {
 Element.extend(el);
 if (el) {
         tableIndex = el.getAttribute('tableIndex'); //Get the id of the element
         column_name = el.getAttribute('column_name');
         order = el.getAttribute('order');
 } else {
  column_name = "null";
  order = "desc";
 }

        if (useAjax[tableIndex]) {
   if (el) {
    //Element.extend(el).insert(new Element('img', {id: 'img_', src:'js/ajax_sorted_table/images/progress1.gif'}).setStyle({borderWidth:'0px', position:'absolute'}));
    Element.extend(el).addClassName('loadingImg').setStyle({background:'url("js/ajax_sorted_table/images/progress1.gif") center right no-repeat'});
   }
            eF_js_rebuildTable(tableIndex, 0, column_name, order, other, true);
        } else {

            parentTable = sortedTables[tableIndex]; //Get the table object, depending on the array offset that el.getAttribute('tableIndex') represents

            var counter = 0; //counter is used in the search for the clicked column
            for (i = 0; i < parentTable.rows[0].cells.length; i++) { //Traverse through all table header cells
                //if (parentTable.rows[0].childNodes[i].tagName == 'TD' || parentTable.rows[0].childNodes[i].tagName == 'TH') {   //filter out any non cell elements that are children of the top table row
                    if (parentTable.rows[0].cells[i] == el.parentNode) { //If this node is the same as el (the clicked one), hold its index at 'pressed'
                        var pressed = counter;
                    } else {
                        if (parentTable.rows[0].cells[i].getElementsByTagName('A').length > 0){
                            parentTable.rows[0].cells[i].getElementsByTagName('A')[0].className = ''; //For every other cell, eliminate its class name, thus making the ascending or descending icon disspear
                        }
                    }
                    counter++;
                //}
            }

            var tableRowIndex = new Array(); //tableRowIndex holds the table rows and their index in the table
            var tableRows = new Array(); //tableRows holds a copy of the table rows

            for (i = 0; i < parentTable.rows.length - 2; i++) {

                tableRowIndex[i] = new Array(getText(parentTable.rows[i+1].cells[pressed]).toLowerCase(), i);
                //alert(getText(parentTable.rows[i+1].cells[pressed]).toLowerCase());
                tableRows[i] = parentTable.rows[i+1].cloneNode(true);

                selects_source = parentTable.rows[i+1].getElementsByTagName('select'); //Microsoft IE has a bug, and does not copy selected and checked attributes when cloning! See http://channel9.msdn.com/wiki/default.aspx/Channel9.InternetExplorerProgrammingBugs
                selects_target = tableRows[i].getElementsByTagName('select');
                for (var k = 0; k < selects_source.length; k++) {
                    selects_target[k].options.selectedIndex = selects_source[k].options.selectedIndex;
                }

                checkboxes_source = parentTable.rows[i+1].getElementsByTagName('input'); //Microsoft IE has a bug, and does not copy selected and checked attributes when cloning! See http://channel9.msdn.com/wiki/default.aspx/Channel9.InternetExplorerProgrammingBugs
                checkboxes_target = tableRows[i].getElementsByTagName('input');
                for (var k = 0; k < checkboxes_source.length; k++) {
                    if (checkboxes_target[k].type == 'checkbox') {
                        checkboxes_target[k].checked = checkboxes_source[k].checked;
                    }
                }
            }

            //alert(tableRowIndex[0][0].match(/^(-?\d\d*)/));
            if (tableRowIndex[0][0].match(/^(-?\d\d*)/)) { //If it's a number, use other sorting function 
                tableRowIndex.sort(sortNumber);
            } else {
             tableRowIndex.sort();
            }
         el.up().up().select('img').each(function(s) {if (s.hasClassName('sprite16-navigate_down') || s.hasClassName('sprite16-navigate_up')) {s.remove();}});
            if (parseInt(document.getElementById(tableIndex+'_sortedTable_sortBy').value) == pressed) { //parseInt is needed here, since if inputs[counter].value is empty and pressed is 0, the clause evaluates to true! We need to make sure that if inputs[counter].value is empty is not converted implicitly to 0
                if (el.className == 'sortAscending') { //Set the icons through the class to reflect the order, ascending or descending
                    el.className = 'sortDescending';
                    if (el.up().select('img').length == 0) {
                     el.up().insert(new Element('img', {src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').addClassName('sprite16-navigate_down').setStyle({verticalAlign:'middle',marginLeft:'6px'}));
                    } else {
                     //el.up().select('img')[0].src = 'themes/default/images/others/transparent.gif';
                     el.up().select('img')[0].removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up');
                    }
                } else {
                 tableRowIndex.reverse(); //If the column clicked is already sorted, we need to reverse the elements order
                    el.className = 'sortAscending';
                    if (el.up().select('img').length == 0) {
                     el.up().insert(new Element('img', {src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').addClassName('sprite16-navigate_up').setStyle({verticalAlign:'middle',marginLeft:'6px'}));
                    } else {
                     //el.up().select('img')[0].src = 'themes/default/images/others/transparent.gif';
                     el.up().select('img')[0].removeClassName('sprite16-navigate_up').addClassName('sprite16-navigate_down');

                    }
                }
            } else {
                document.getElementById(tableIndex+'_sortedTable_sortBy').value = pressed; //Set the hidden element value to the current sorted field
                el.className = 'sortDescending'; //Update the sorting icon
                //alert(el.up().select('img'));
                if (el.up().select('img').length == 0) {
                 el.insert(new Element('img', {src:'themes/default/images/others/transparent.gif'}).addClassName('sprite16').addClassName('sprite16-navigate_down').setStyle({verticalAlign:'middle',marginLeft:'6px'}));
                } else {
                 //el.up().select('img')[0].src = 'themes/default/images/others/transparent.gif';
                 el.up().select('img')[0].removeClassName('sprite16-navigate_down').addClassName('sprite16-navigate_up');
                }
            }
      //el.insert(new Element)
      //alert('a');

            counter = parentTable.rows.length - 2;
            while (tableRowIndex.length > 0) { //Replace the table rows with the copies corresponding to the sorted order
                val = tableRowIndex.pop();
                parentTable.rows[counter].parentNode.replaceChild(tableRows[val[1]], parentTable.rows[counter]);
                counter--;
            }

            eF_js_refreshPage(el.getAttribute('tableIndex')); //Refresh the current page so that it holds the correct data
        }
    }


    function eF_js_refreshPage(tableIndex) {

        var parentTable = sortedTables[tableIndex]; //Get the current table

        var page = document.getElementById(tableIndex+'_sortedTable_currentPage').options[document.getElementById(tableIndex+'_sortedTable_currentPage').selectedIndex].value; //Get the current page from the select box
        var count = 0;
        for (var j = 1; j < parentTable.rows.length - 1; j++) { //Update the contents of this page, by hiding each row that should not be part of it
            if (j < page * rowsPerPage[tableIndex] + 1 || j > parseInt(rowsPerPage[tableIndex]) + page * rowsPerPage[tableIndex]) {
                parentTable.rows[j].style.display = 'none';
            } else {
                parentTable.rows[j].style.display = '';
                count++ % 2 ? newClass = 'evenRowColor' : newClass = 'oddRowColor';
                parentTable.rows[j].className = parentTable.rows[j].className.replace(/evenRowColor|oddRowColor/i, newClass);
            }
        }
    }

    function eF_js_changePage(tableIndex, page) {

        var parentTable = sortedTables[tableIndex]; //Get the current table

        if (page == 'next') {
            page = parseInt(document.getElementById(tableIndex+'_sortedTable_currentPage').options[document.getElementById(tableIndex+'_sortedTable_currentPage').selectedIndex].value) + 1; //Get the current page from the select box
            if (page > document.getElementById(tableIndex+'_sortedTable_currentPage').options.length - 1) {
                page = document.getElementById(tableIndex+'_sortedTable_currentPage').options.length - 1;
            }
        } else if (page == 'previous') {
            page = parseInt(document.getElementById(tableIndex+'_sortedTable_currentPage').options[document.getElementById(tableIndex+'_sortedTable_currentPage').selectedIndex].value) - 1; //Get the current page from the select box
            if (page < 0 ) {
                page = 0;
            }
        }

        if (useAjax[tableIndex] && !isNaN(currentOffset[tableIndex])) {
            eF_js_rebuildTable(tableIndex, page * rowsPerPage[tableIndex], currentSort[tableIndex], currentOrder[tableIndex], currentOther[tableIndex]);
        } else {
        //alert(rowsPerPage[tableIndex]);
            for (var j = 1; j < parentTable.rows.length - 1; j++) { //Update the contents of the current page, by hiding each row that should not be part of it
                if (j < page*rowsPerPage[tableIndex] + 1 || j > parseInt(rowsPerPage[tableIndex]) + page * rowsPerPage[tableIndex]) {
                    parentTable.rows[j].style.display = 'none';
                } else {
                    parentTable.rows[j].style.display = '';
                }
            }
            var startResult = parseInt(page*rowsPerPage[tableIndex]) + 1;
            var endResult = parseInt(rowsPerPage[tableIndex]) + page * rowsPerPage[tableIndex];
            if (endResult > parentTable.rows.length - 2) {
             endResult = parentTable.rows.length - 2;
            }
            //$(tableIndex+'_displaying_results').innerHTML = '<?php echo _DISPLAYINGRESULTS?> '+startResult+'-'+endResult+' <?php echo _OUTOF?> '+(parentTable.rows.length - 2);
        }

        $(tableIndex+'_sortedTable_currentPage').selectedIndex = page;

    }


    function eF_js_selectAll (el, tableIndex) {
     try {
      var table = sortedTables[tableIndex]; //Get the table to perform paging on
      if (window.ajaxPost && !table.getAttribute('nomass')) {
       ajaxPost('', el, sortedTables[tableIndex].id);
      }
      var inputs = table.getElementsByTagName('input'); //Get all the \"input\" elements on the table

      for (var i = 0; i < inputs.length; i++) {

       if (inputs[i].type == 'checkbox') { //for each checkbox, set its \"checked\" state to match the global checkbox
        inputs[i].checked = el.checked;

        // MODULE HCD INTERVENTION FOR APPEARANCES OF HIDDEN BOXES -- The following should leave from here (by mpaltas)
        if (typeof(myform) != 'undefined' && myform == "branch_to_employees") {
         if (document.getElementById("job_selection_row" + i) && document.getElementById("position_select_row" + i)) {
          if (!inputs[i].checked) { //for each job descriptions select, make it appear/disappear
           document.getElementById("job_selection_row" + i).style.visibility = "hidden";
           document.getElementById("position_select_row" + i).style.visibility = "hidden";
           document.getElementById('position_select_row' + i).name = "_";
          } else {
           document.getElementById("job_selection_row" + i).style.visibility = "visible";
           document.getElementById("position_select_row" + i).style.visibility = "visible";
           document.getElementById("position_select_row" + i).name = "visible";
           document.getElementById('position_select_row' + i).name = document.getElementById('position_select_row'+i).value + "_" + document.getElementById('job_selection_row'+i).value;

          }
         }
        }

        if (typeof(myform) != 'undefined' && (myform == "employee_to_skills" || myform == "employees_to_skill" )) {
         skill_id = inputs[i].name;
         spec_text = document.getElementById('spec_skill_'+skill_id);
         if (spec_text) {
          if (!inputs[i].checked) {
           spec_text.style.visibility = "hidden";
          } else {
           spec_text.style.visibility = "visible";
          }
         }
        }

        if (typeof(myform) != 'undefined' && myform == "skills_to_lesson") {

         skill_id = inputs[i].name;
         spec_text = document.getElementById('spec_skill_'+skill_id);
         if (spec_text) {
          if (!inputs[i].checked) {
           spec_text.style.visibility = "hidden";
          } else {
           spec_text.style.visibility = "visible";
          }
         }
        }

        if (typeof(myform) != 'undefined' && myform == "skills_to_course") {

         skill_id = inputs[i].name;
         spec_text = document.getElementById('spec_skill_'+skill_id);
         if (spec_text) {
          if (!inputs[i].checked) {
           spec_text.style.visibility = "hidden";
          } else {
           spec_text.style.visibility = "visible";
          }
         }
        }
       }
      }
     } catch (e) {alert(e);}

    }
/*

    function eF_js_getChecked (tableIndex) {

        var table = sortedTables[tableIndex];                                 //Get the table to perform paging on

        var inputs = table.getElementsByTagName('input');                   //Get all the \"input\" elements on the table

        for (var i = 0; i < inputs.length; i++) {

            if (inputs[i].type == 'checkbox') {

                if (inputs[i].checked) {

                    checkedEntries[tableIndex][inputs[i].id] = 1;

                } else {

                    checkedEntries[tableIndex][inputs[i].id] = 0;

                }

            }

        }

    }



    function eF_js_setChecked (tableIndex) {

        var table = sortedTables[tableIndex];                                 //Get the table to perform paging on



        var inputs = table.getElementsByTagName('input');                   //Get all the \"input\" elements on the table



        for (var i = 0; i < inputs.length; i++) {

            if (inputs[i].type == 'checkbox') {

                if (checkedEntries[tableIndex][inputs[i].id] == 1) {

                    inputs[i].checked = 'checked';

                } else if (checkedEntries[tableIndex][inputs[i].id] == 0) {

                    inputs[i].checked = '';

                }

            }

        }

    }

*/
    function eF_js_pageTable(tableIndex) {
        var table = sortedTables[tableIndex]; //Get the table to perform paging on
        var checkboxesPositions = new Array(); //This array will hold the columns containing checkboxes, to later implement \"check all\" function
        if (table.rows.length > 1) {
            for (var i = 0; i < table.rows[table.rows.length-1].cells.length; i++) { //Traverse through the cells of the *first* data row and search for checkboxes
                if (table.rows[table.rows.length-1].cells[i].getElementsByTagName('input').length > 0 && table.rows[table.rows.length-1].cells[i].getElementsByTagName('input')[0].type == 'checkbox') {
                    checkboxesPositions.push(i); //Put the column numbers to this array
                }
            }
        }
        if (useAjax[tableIndex] && tableSize[tableIndex]) {
            pages = tableSize[tableIndex];
        } else {
            pages = Math.ceil((table.rows.length - 1) / rowsPerPage[tableIndex]); //Calculate the total of pages required
        }
        var td = document.createElement('td'); //Create the table cell element that will be the footer holding all the sorting / paging handles
        if (checkboxesPositions.length > 0) {
            td.colSpan = table.rows[0].cells.length - 1;
            var td_checkbox = document.createElement('td');
            td_checkbox.className = 'sortedTableFooter'; //Assign it its special class
            td_checkbox.style.textAlign = 'center';
            var checkbox = document.createElement('input');
            checkbox.setAttribute('type', 'checkbox');
            //mpaltas
            //checkbox.setAttribute('onclick', 'eF_js_selectAll(this, '+tableIndex+')');                  //Only workds in FF, not IE. the code below works in both browsers
            checkbox.onclick = function () {
             if (!useAjax[tableIndex] || confirm(sorted_translations["operationaffectmany"])) {
              eF_js_selectAll(this, tableIndex);
             }
            };
            td_checkbox.appendChild(checkbox);
        } else {
         if (table.rows[0].cells.length) {
          td.colSpan = table.rows[0].cells.length; //Spread it to span over all columns
         }
        }
        td.className = 'sortedTableFooter'; //Assign it its special class
//Prototype implementation --not ready yet
//		td_checkBox = new Element('td', {colSpan:table.rows[0].cells.length - 1})
//							.setStyle({textAlign:'center'})
//							.addClassName('sortedTableFooter')
//							.insert(new Element('input', {type:'checkbox'}))
//											.observe('onclick', function () {if (!useAjax[tableIndex] || confirm('<?php echo _OPERATIONWILLAFFECTMANYAREYOUSURE?>')) eF_js_selectAll(this, tableIndex)});
        var div = document.createElement('div');
        var input = document.createElement('input'); //Create a text box that will be used for the filtering function
        input.setAttribute('type', 'text');
        input.setAttribute('id', tableIndex+'_sortedTable_filter'); //Set its id to retrieve it easily
        //input.setAttribute('size', '10');           //Added by mpaltas **But removed from venakis due to IE incompatibility (sic)** to avoid overlapping - using a new table inside the td might be a better idea
        //input.setAttribute('onkeypress', 'if (event.which == 13) eF_js_filterData('+tableIndex+')');
        input.setAttribute('onkeypress', 'if (event.which == 13 || event.keyCode == 13) {eF_js_filterData('+tableIndex+'); return false;}'); //Set an onkeypress event, so that pressing \"enter\" fires the function. We put the return false here, so that if the table is inside a form, enter will not submit it 
        if (currentFilter[tableIndex] || currentBranchFilter[tableIndex] || currentJobFilter[tableIndex]) {
         input.setAttribute("value", currentFilter[tableIndex]);
         if (currentBranchFilter[tableIndex] || currentJobFilter[tableIndex]) {
          div.innerHTML += '<span style = "display:none" id = "'+table.id+'_currentFilter">' + currentFilter[tableIndex]+'||||'+((currentBranchFilter[tableIndex])?currentBranchFilter[tableIndex]:'')+'||||'+((currentJobFilter[tableIndex])?currentJobFilter[tableIndex]:'')+'</span>';
         } else {
          div.innerHTML += '<span style = "display:none" id = "'+table.id+'_currentFilter">' + currentFilter[tableIndex]+'</span>';
         }
        }
        //input.className = 'inputSearchText';
        div.innerHTML += '<span style = "vertical-align:middle">&nbsp;'+sorted_translations["filter"]+':&nbsp;</span>';
        div.appendChild(input); //Append it to the footer cell
  // Enterprise filters
  if (branchFilter[tableIndex]) {
         var selectBranch = document.createElement('select'); //Create a select element that will hold the rows per page
            var temp = branchFilter[tableIndex].split('||||');
            var elOptNew;
            var i;
            for (i = 0; i < temp.length-1; i = i + 2) {
                elOptNew = document.createElement('option');
                elOptNew.value = temp[i];
                elOptNew.text = temp[i+1];
                try {
                    selectBranch.add(elOptNew,null);
                } catch(ex) {
                 selectBranch.add(elOptNew); // IE only
                }
                if (currentBranchFilter[tableIndex] && temp[i] == currentBranchFilter[tableIndex]) {
                 elOptNew.setAttribute('selected', 'selected');
                }
            }
            selectBranch.setAttribute('class', 'inputSelectMed');
         selectBranch.setAttribute('onchange', 'eF_js_filterData('+tableIndex+'); return false;'); //If we ommit parseInt, then rowsPerPage becomes string. So, if for example rowsPerPage is 10 and we add 5, it becoomes 105 instead of 15
         selectBranch.setAttribute('id', tableIndex+'_sortedTable_branchFilter'); //Set its id so we can retrieve its data easily
         if (currentBranchFilter[tableIndex]) {
          div.innerHTML += '<span style = "display:none" id = "'+table.id+'_currentBranchFilter">'+currentBranchFilter[tableIndex]+'</span>';
         }
         div.appendChild(selectBranch);
  }
  if (jobFilter[tableIndex]) {
         var selectJob = document.createElement('select'); //Create a select element that will hold the rows per page
            var temp = jobFilter[tableIndex].split('||||');
            var elOptNew;
            var i;
            var selectedValueIndex = 0;
            for (i = 0; i < temp.length; i++) {
                elOptNew = document.createElement('option');
                elOptNew.value = temp[i];
                elOptNew.text = temp[i];
                try {
                 selectJob.add(elOptNew,null);
                } catch(ex) {
                 selectJob.add(elOptNew); // IE only
                }
                if (currentJobFilter[tableIndex] && temp[i] == currentJobFilter[tableIndex]) {
                 elOptNew.setAttribute('selected', 'selected');
                }
            }
            selectJob.setAttribute('class', 'inputSelectMed');
            selectJob.setAttribute('onchange', 'eF_js_filterData('+tableIndex+'); return false;'); //If we ommit parseInt, then rowsPerPage becomes string. So, if for example rowsPerPage is 10 and we add 5, it becoomes 105 instead of 15
         selectJob.setAttribute('id', tableIndex+'_sortedTable_jobFilter'); //Set its id so we can retrieve its data easily						
         if (currentJobFilter[tableIndex]) {
          div.innerHTML += '<span style = "display:none" id = "'+table.id+'_currentJobFilter">'+currentJobFilter[tableIndex]+'</span>';
         }
         div.appendChild(selectJob);
         selectJob.selectedIndex = selectedValueIndex;
  }
        div.className = 'sortTablefilter';
        td.appendChild(div);
  if (useAjax[tableIndex]) {
   var startResult = 0;
   var endResult = 0;
   if (currentOffset[tableIndex] === 0) {
    startResult = 1;
    endResult = rowsPerPage[tableIndex];
   } else if (currentOffset[tableIndex]) {
    startResult = parseInt(currentOffset[tableIndex]) + 1;
    endResult = parseInt(currentOffset[tableIndex]) + parseInt(rowsPerPage[tableIndex]);
   }
   if (endResult > parseInt(table.getAttribute('size'))) {
    endResult = table.getAttribute('size');
   }
        }


        var select = document.createElement('select'); //Create a select element that will hold the rows per page
        //select.setAttribute('type', 'text');
        //var option = document.createElement('option');                      //Add the first option, which is the current setting
        //option.setAttribute('value', rowsPerPage[tableIndex]);
        //option.innerHTML = rowsPerPage[tableIndex];
        //select.appendChild(option);
        rowsPerPageArray = new Array('10', '15', '20', '50', '200');
        for (var i = 0; i < rowsPerPageArray.length; i++) { //Append 10 values, 5,10,15, ..., 45 rows per page
            var option = document.createElement('option');
            option.setAttribute('value', rowsPerPageArray[i]);
            option.innerHTML = rowsPerPageArray[i];
            select.appendChild(option);
            if (rowsPerPage[tableIndex] == rowsPerPageArray[i]) {
             option.setAttribute('selected', 'selected');
            }
        }
        select.setAttribute('onchange', 'numRows = parseInt(this.options[this.selectedIndex].value);eF_js_changeRowsPerPage('+tableIndex+', numRows)'); //If we ommit parseInt, then rowsPerPage becomes string. So, if for example rowsPerPage is 10 and we add 5, it becoomes 105 instead of 15
        select.setAttribute('id', tableIndex+'_sortedTable_rowsPerPage'); //Set its id so we can retrieve its data easily
        select.style.verticalAlign = 'middle';

        td.innerHTML += '<span style = "vertical-align:middle;">'+sorted_translations["rowsperpage"]+':&nbsp;</span>';
        td.appendChild(select); //Append it to the footer cell


        var input = document.createElement('input'); //Create a hidden element, that holds the current page.
        input.setAttribute('type', 'hidden');
        input.setAttribute('id', tableIndex+'_sortedTable_sortBy');
        td.appendChild(input); //Append it to the footer cell

        if (table.toolsCell) { //If we are repaginating table, then we will replace the previous footer cell with this one
            table.toolsCell.parentNode.replaceChild(td, table.toolsCell);
        } else { //If we are creating pagination for the first time, append this cell to the table
            var tr = document.createElement('tr');
            tr.appendChild(td);
            tr.setAttribute('class', 'defaultRowHeight');
            table.getElementsByTagName('tbody')[0].appendChild(tr);
            if (td_checkbox) {
                tr.appendChild(td_checkbox);
            }
        }

        var select = document.createElement('select'); //Create a select element, that lists the pages
        select.setAttribute('id', tableIndex+'_sortedTable_currentPage');
        select.setAttribute('onchange', 'eF_js_changePage('+tableIndex+', this.options[this.selectedIndex].value)'); //Set an onchange event, so that changing the value fires a change on the page
        for (var i = 0; i < pages; i++) { //Add an option for each page
            var option = document.createElement('option');
            option.setAttribute('value', i);
            option.innerHTML = (1 + i*rowsPerPage[tableIndex])+'-'+Math.min((i + 1)*rowsPerPage[tableIndex], table.getAttribute('size') ? table.getAttribute('size') : table.rows.length-2);
            select.appendChild(option);
        }

        select.style.verticalAlign = 'middle';

        td.innerHTML += '<span style = "vertical-align:middle">&nbsp;'+sorted_translations["displayingresults"]+':&nbsp;</span>';
        td.innerHTML += '<a href = \"javascript:void(0)\" onclick = \"eF_js_changePage('+tableIndex+',0)\"><img src = "js/ajax_sorted_table/images/navigate_left2.png" border = "0" style = "vertical-align:middle" /></a>&nbsp;'; //Add a \"first page\" handler
        td.innerHTML += '<a href = \"javascript:void(0)\" onclick = \"eF_js_changePage('+tableIndex+',\'previous\')\"><img src = "js/ajax_sorted_table/images/navigate_left.png" border = "0" style = "vertical-align:middle" /></a>&nbsp;'; //Add a \"previous page\" handler
        td.appendChild(select);
        td.innerHTML += '<span style = "vertical-align:middle">&nbsp;'+sorted_translations["outof"]+'&nbsp;' + (table.getAttribute('size') ? table.getAttribute('size') : table.rows.length-2) + '</span>';
        td.innerHTML += '&nbsp;<a href = \"javascript:void(0)\" onclick = \"eF_js_changePage('+tableIndex+',\'next\')\"><img src = "js/ajax_sorted_table/images/navigate_right.png" border = "0" style = "vertical-align:middle" /></a>'; //Add a \"next page\" handler
        td.innerHTML += '&nbsp;<a href = \"javascript:void(0)\" onclick = \"eF_js_changePage('+tableIndex+','+(pages - 1)+')\"><img src = "js/ajax_sorted_table/images/navigate_right2.png" border = "0" style = "vertical-align:middle" /></a>'; //Add a \"last page\" handler

        if (!Object.isUndefined(noFooter[tableIndex]) || ((table.rows.length < minimumRows + 2 || parseInt(table.getAttribute('size')) < minimumRows) && !currentFilter[tableIndex] && !currentOffset[tableIndex] && !currentBranchFilter[tableIndex] && !currentJobFilter[tableIndex])) {
            tr.style.display = 'none';
            if (!Object.isUndefined(noFooter[tableIndex])) {
             tr.setAttribute('id', 'noFooterRow'+tableIndex);
            }
        }

        table.toolsCell = td; //Assign the current cell to a global variable

        if (!useAjax[tableIndex]) {
            eF_js_changePage(tableIndex, 0); //Display the first page
         table.style.visibility = 'visible'; //The table is not visible by default (to avoid displaying effects). Make the table visible
        }

//        if (tr && table.rows.length <= minimumRows) {                              //Do not show footer table raw, if table rows are up to minimumRows (10)
//            tr.style.display = 'none';
//        }

    }

    function eF_js_filterData(tableIndex) {
//debugger;
        if (useAjax[tableIndex]) {
         var showing_image = false;
         if ($(tableIndex+'_sortedTable_jobFilter')) {
          Element.extend($(tableIndex+'_sortedTable_jobFilter')).addClassName('loadingImg').setStyle({background:'url("js/ajax_sorted_table/images/progress1.gif") center right no-repeat'});

          var jobStr = document.getElementById(tableIndex+'_sortedTable_jobFilter').value;
          currentJobFilter[tableIndex] = jobStr;
          showing_image = true;
         }

         if ($(tableIndex+'_sortedTable_branchFilter')) {
          if (!showing_image) {
           Element.extend($(tableIndex+'_sortedTable_branchFilter')).addClassName('loadingImg').setStyle({background:'url("js/ajax_sorted_table/images/progress1.gif") center right no-repeat'});
           showing_image = true;
          }

          var branchStr = document.getElementById(tableIndex+'_sortedTable_branchFilter').value;
          currentBranchFilter[tableIndex] = branchStr;
         }

         if (!showing_image) {
          Element.extend($(tableIndex+'_sortedTable_filter')).addClassName('loadingImg').setStyle({background:'url("js/ajax_sorted_table/images/progress1.gif") center right no-repeat'});
         }

            var str = document.getElementById(tableIndex+'_sortedTable_filter').value; //Get the filter value, from the corresponding text box
            currentFilter[tableIndex] = str;
            currentOffset[tableIndex] = 0;

            eF_js_rebuildTable(tableIndex, currentOffset[tableIndex], currentSort[tableIndex], currentOrder[tableIndex], currentOther[tableIndex], true);
        } else {
            var table = sortedTables[tableIndex]; //Get the current table
            var str = document.getElementById(tableIndex+'_sortedTable_filter').value; //Get the filter value, from the corresponding text box
            if (table.filteredRows) { //If there were any previously filtered rows, append them back to the table
                for (var i = 0; i < table.filteredRows.length; i++) {
                    table.rows[0].parentNode.insertBefore(table.filteredRows[i], table.rows[table.rows.length-1]); //Append the rows at the bottom of the table
                }
            }

            table.filteredRows = new Array(); //This array will hold the filtered rows

            var i = 0;
            while (i < table.rows.length - 2) {
                keepRow = false;
                j = 0;
                //tds = table.rows[i+1].getElementsByTagName('TD');
    /////Here maybe we can get the whole row text and check against the filter text, instead of checking cells one by one
                re = new RegExp(str, "i");
                while (table.rows[i+1].cells[j] != null && !(keepRow = (table.rows[i+1].cells[j++].innerHTML.toString().stripTags().strip()).match(re))) {} //Check if the current row contains the filter text                
                if (!keepRow) {
                    table.filteredRows.push(table.rows[i+1].parentNode.removeChild(table.rows[i+1])); //If the row doesn't contain the filter text, remove it fro mthe table and put it in the filteredRows array
                } else {
                    i++;
                }
            }

            newPages = (Math.ceil((table.rows.length - 2) / rowsPerPage[tableIndex])); //Recalculate the number of pages

            var select = document.getElementById(tableIndex+'_sortedTable_currentPage'); //Recreate the pages select box to match the new sum of pages
            for (var i = select.options.length - 1; i >= 0; i--) {
                select.removeChild(select.options[i]);
            }
            for (var i = 0; i < newPages; i++) {
                var option = document.createElement('option');
                option.setAttribute('value', i);
                option.innerHTML = i + 1;
                select.appendChild(option);
            }

            currentFilter[tableIndex] = str;
            eF_js_pageTable(tableIndex);
            document.getElementById(tableIndex+'_sortedTable_filter').value = str;

        }
                                                     //Repage the table
    }

function eF_js_changeRowsPerPage(tableIndex, numRows) {
    rowsPerPage[tableIndex] = numRows;
    if (useAjax[tableIndex]) {
        eF_js_rebuildTable(tableIndex, 0, currentSort[tableIndex], currentOrder[tableIndex], currentOther[tableIndex]);
    } else {
        eF_js_pageTable(tableIndex);
    }
    setCookie('cookieTableRows', numRows);
}

//Cross-browser function for getting element text
function getText(control) {
    if (document.all) {
        return control.innerText;
    } else {
        return control.textContent;
    }
}

//Sorting for numbers
function sortNumber(a, b) {
 var val1 = parseInt(a[0]);
 var val2 = parseInt(b[0]);
 if (isNaN(val1)) {
     val1 = 0;
 }
 if (isNaN(val2)) {
     val2 = 0;
 }
 return val1 - val2;
}

//In order to display the loading... div when in tab. onTabDisplay() is called automatically by the tabber library 
if (typeof(tabberObj) != 'undefined') {
 tabberObj.prototype.onTabDisplay = function(obj)
 {
  Element.extend(obj.tabber.tabs[obj.index].div);
  obj.tabber.tabs[obj.index].div.select('table').each(
   function (s) {
    if (s.hasClassName('sortedTable') && s.getDimensions().height > 0) {
     $$('div.loading').each(function (s) {s.hide();}); //Hide all
     if ($('loading_'+s.id) && $('loading_'+s.id).readAttribute('loaded') !== 'loaded') {
      $('loading_'+s.id).show().clonePosition(s); //Show this one
     }
    }
   });
 };
}

function toggleSubSection(el, id, sectionId, trailUrl) {
 try {
  Element.extend(el);
  var sectionTable = $(sectionId);
  var tr = el.up().up(); //The table row holding the clicked element

  if (el.hasClassName('sprite16-plus') || el.hasClassName('sprite16-plus2')) {

   tr.up().select('img.sprite16-minus').each(function (s) {setImageSrc(s, 16, 'plus');});
   tr.up().select('img.sprite16-minus2').each(function (s) {setImageSrc(s, 16, 'plus2');});
   el.hasClassName('sprite16-plus') ? setImageSrc(el, 16, 'minus') : setImageSrc(el, 16, 'minus2');
   newTr = new Element('tr', {id:'subsection_row_'+sectionId+id}).insert(new Element('td', {colspan:tr.childElements().length}).insert(sectionTable.show().remove()));
   tr.insert({after:newTr});

   sectionTable.writeAttribute({no_auto:0});
   for (var i = 0; i < sortedTables.size(); i++) {
    if (sortedTables[i].id.match(sectionId) && ajaxUrl[i]) {
     ajaxUrl[i] = ajaxUrl[i] + sectionId+'_source=' + id + '&' + trailUrl + '&';
     eF_js_rebuildTable(i, 0, column_name, order);
    }
   }
  } else {
   onCloseSubSection(sectionTable);
   el.hasClassName('sprite16-minus') ? setImageSrc(el, 16, 'plus') : setImageSrc(el, 16, 'plus2');
   document.body.insert({after:sectionTable.hide().remove()});
   $('subsection_row_'+sectionId+id).remove();
  }
 } catch (e) {alert(e);}
}
function onCloseSubSection(table) {
 containers = findContainers(table);
 activeRows = findActiveRows(table);

 containers[0].select('tr').each(function (s) {
  enableRow(s);
 });
 activeRows.each(function (s) {
  enableRow(s.previous());
  enableRow(s);
 });

}
function onLoadSubSection(table) {
 try {
  containers = findContainers(table);
  activeRows = findActiveRows(table);

  containers.each( function (c) {
   c.select('tr').each(function (s) {
    enableRow(s);
   });
   c.select('tr').each(function (s) {
    if (s.visible()) {
     disableRow(s);
    };
   });
  });
  activeRows.each(function (a) {
   enableRow(a);
  });
  if (activeRows[0]) {
   enableRow(activeRows[0].previous());
  }
  table.select('tr').each(function (s) {
   enableRow(s);
  });
 } catch (e) {
  handleException(e);
 }
}
function findActiveRows(el) {
 var activeRows = new Array();
 el.ancestors().each(function (s) {
  if (s.id.match('subsection_row')) {
   activeRows.push(s);
  }});
 return activeRows;
}
function findContainers(el) {
 var containers = new Array();
 el.ancestors().each(function (s) {
  if (s.hasClassName('sortedTable')) {
   containers.push(s);
  }});
 //containers.reverse();
 return containers;
}

function isDisabledRow(tr) {
 if ($('loading_'+tr.identify())) {
  return true;
 } else {
  return false;
 }
}
function disableRow(tr) {
    var loadingDiv = new Element('div', {id:'loading_'+tr.identify()}).addClassName('loading');//.setStyle({border:'2px solid green'});
    loadingDiv.setOpacity(0.4);
    document.body.appendChild(loadingDiv);
    //Unfortunately, IE doesn't like clonePosition(tr), so we have to go with this solution    
    loadingDiv.setStyle({width:tr.getDimensions().width+'px',
          height:tr.getDimensions().height+'px',
          left:tr.down().cumulativeOffset().left+'px',
          top:tr.down().cumulativeOffset().top+'px'});
}
function enableRow(tr) {
 if ($('loading_'+tr.identify())) {
  $('loading_'+tr.identify()).remove();
 }

}
function augmentUrl(table_id) {
 augmentedUrl = '';
 tables = sortedTables.size();
 for (var i = 0; i < tables; i++) {
  if (sortedTables[i].id.match(table_id) && ajaxUrl[i]) {
   tableIndex = i;
   augmentedUrl = ajaxUrl[tableIndex]+'ajax='+sortedTables[tableIndex].id+'&limit='+rowsPerPage[tableIndex]+'&offset='+currentOffset[tableIndex]+'&sort='+currentSort[tableIndex]+'&order='+currentOrder[tableIndex]+'&other='+currentOther[tableIndex];
  }
 }
 return augmentedUrl;
}
