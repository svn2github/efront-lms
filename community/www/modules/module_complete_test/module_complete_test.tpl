{* smarty template for complete_test *}
{if $smarty.get.login && $smarty.get.test}
   {capture name = 't_test_code'}

      {if $T_SHOW_CONFIRMATION}
         {assign var = 't_show_side_menu' value = true}
          {capture name = "sideContentTree"}
           {eF_template_printSide title = $smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE id = 'current_content'}
          {/capture}
          <table class = "navigationHandles">
           <tr><td>{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
          </table>
          <table class = "testHeader">
           <tr><td id = "testName">{$T_TEST_DATA->test.name}</td></tr>
           <tr><td id = "testDescription">{$T_TEST_DATA->test.description}</td></tr>
           <tr><td>
             <table class = "testInfo">
              <tr><td rowspan = "6" id = "testInfoImage"><img src = {$T_COMPLETETEST_BASELINK|cat:"images/tests.png"} alt = "{$T_TEST_DATA->test.name}" title = "{$T_TEST_DATA->test.name}"/></td>
               <td id = "testInfoLabels"></td>
               <td></td></tr>
              <tr><td>{$smarty.const._TESTDURATION}:&nbsp;</td>
               <td>
               {if $T_TEST_DATA->options.duration}
                {if $T_TEST_DATA->convertedDuration.hours}{$T_TEST_DATA->convertedDuration.hours} {$smarty.const._HOURS}&nbsp;{/if}
                {if $T_TEST_DATA->convertedDuration.minutes}{$T_TEST_DATA->convertedDuration.minutes} {$smarty.const._MINUTES}&nbsp;{/if}
                {if $T_TEST_DATA->convertedDuration.seconds}{$T_TEST_DATA->convertedDuration.seconds} {$smarty.const._SECONDS}{/if}
               {else}
                {$smarty.const._UNLIMITED}
               {/if}
               </td></tr>
              <tr><td>{$smarty.const._NUMOFQUESTIONS}:&nbsp;</td>
               <td>{$T_TEST_QUESTIONS_NUM}</td></tr>
              <tr><td>{$smarty.const._QUESTIONSARESHOWN}:&nbsp;</td>
               <td>{if $T_TEST_DATA->options.onebyone}{$smarty.const._ONEBYONEQUESTIONS}{else}{$smarty.const._ALLTOGETHER}{/if}</td></tr>
             {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
              <tr><td>{$smarty.const._YOUPAUSEDTHISTESTON}:&nbsp;</td>
               <td>#filter:timestamp_time-{$T_TEST_DATA->time.pause}#</td></tr>
             {else}
              <tr><td>{$smarty.const._DONETIMESSOFAR}:&nbsp;</td>
               <td>{if $T_TEST_STATUS.timesDone}{$T_TEST_STATUS.timesDone}{else}0{/if}&nbsp;{$smarty.const._TIMES}</td></tr>
              <tr><td>{if $T_TEST_STATUS.timesLeft !== false }{$smarty.const._YOUCANDOTHETEST}:&nbsp;</td>
               <td>{$T_TEST_STATUS.timesLeft}&nbsp;{$smarty.const._TIMESMORE}{/if}</td></tr>
             {/if}
             </table>
            </td>
           <tr><td id = "testProceed">
           {if $T_TEST_STATUS.status == 'incomplete' && $T_TEST_DATA->time.pause}
            <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._RESUMETEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&resume=1'" />
           {else}
            <input class = "flatButton" type = "button" name = "submit_sure" value = "{$smarty.const._PROCEEDTOTEST}&nbsp;&raquo;" onclick = "javascript:location=location+'&confirm=1'" />
           {/if}
           </td></tr>
          </table>
      {elseif $smarty.get.test_analysis}
         {assign var = 'title' value = "`$title`&nbsp;&raquo;&nbsp;<a class = 'titleLink' href = '`$smarty.server.PHP_SELF`?ctg=content&view_unit=`$smarty.get.view_unit`&test_analysis=1'>`$smarty.const._TESTANALYSISFORTEST` &quot;`$T_TEST_DATA->test.name`&quot;</a>"}

         {capture name = "t_test_analysis_code"}
          <div class = "headerTools">
           <span>
            <img src = {$T_COMPLETETEST_BASELINK|cat:"images/arrow_left.png"} alt = "{$smarty.const._VIEWSOLVEDTEST}" title = "{$smarty.const._VIEWSOLVEDTEST}">
            <a href = "{$smarty.server.PHP_SELF}?ctg=tests&view_unit={$smarty.get.view_unit}&show_solved_test={$smarty.get.show_solved_test}">{$smarty.const._VIEWSOLVEDTEST}</a>
           </span>
           {if $T_TEST_STATUS.testIds|@sizeof > 1}
           <span>
            <img src = {$T_COMPLETETEST_BASELINK|cat:"images/go_into.png"} alt = "{$smarty.const._JUMPTOEXECUTION}" title = "{$smarty.const._JUMPTOEXECUTION}">
            &nbsp;{$smarty.const._JUMPTOEXECUTION}
            <select style = "vertical-align:middle" onchange = "location.toString().match(/show_solved_test/) ? location = location.toString().replace(/show_solved_test=\d+/, 'show_solved_test='+this.options[this.selectedIndex].value) : location = location + '&show_solved_test='+this.options[this.selectedIndex].value">
             {if $smarty.get.show_solved_test}{assign var = "selected_test" value = $smarty.get.show_solved_test}{else}{assign var = "selected_test" value = $T_TEST_STATUS.lastTest}{/if}
             {foreach name = "test_analysis_list" item = "item" key = "key" from = $T_TEST_STATUS.testIds}
              <option value = "{$item}" {if $selected_test == $item}selected{/if}>#{$smarty.foreach.test_analysis_list.iteration} - #filter:timestamp_time-{$T_TEST_STATUS.timestamps[$key]}#</option>
             {/foreach}
            </select>
           </span>
           {/if}
          </div>
          <table style = "width:100%">
           <tr><td style = "vertical-align:top">{$T_CONTENT_ANALYSIS}</td></tr>
                          <tr><td>
                           <div id = "graph_table"><div id = "proto_chart" class = "proto_graph"></div></div>
                           <script>var show_test_graph = true;</script>
                          </td></tr>
          </table>
         {/capture}

         {eF_template_printBlock title = "`$smarty.const._TESTANALYSIS` `$smarty.const._FORTEST` &quot;`$T_TEST_DATA->test.name`&quot; `$smarty.const._ANDUSER` &quot;`$T_TEST_DATA->completedTest.login`&quot;" data = $smarty.capture.t_test_analysis_code image= $T_COMPLETETEST_BASELINK|cat:'images/tests.png' absoluteImagePath = 1}
      {else}
       {if $T_TEST_STATUS.status == '' || $T_TEST_STATUS.status == 'incomplete'}
         {capture name = "test_footer"}
         <table class = "formElements" style = "width:100%">
          <tr><td colspan = "2">&nbsp;</td></tr>
          <tr><td colspan = "2" class = "submitCell" style = "text-align:center">{$T_TEST_FORM.submit_test.html}&nbsp;{$T_TEST_FORM.pause_test.html}</td></tr>
         </table>
         {/capture}
        {else}
         {assign var = 't_show_side_menu' value = true}
         {capture name = "sideContentTree"}
          {eF_template_printSide title=$smarty.const._LESSONMATERIAL data = $T_CONTENT_TREE}
         {/capture}
         <table class = "navigationHandles">
          <tr><td>{eF_template_printPreviousNext previous = $T_PREVIOUS_UNIT next = $T_NEXT_UNIT}</td></tr>
         </table>
        {/if}
        {if !$T_NO_TEST}
         {$T_TEST_FORM.javascript}
         <form {$T_TEST_FORM.attributes}>
          {$T_TEST_FORM.hidden}
          {$T_TEST}
          {$smarty.capture.test_footer}
         </form>
        {/if}
      {/if}
   {/capture}
   {eF_template_printBlock title = "`$smarty.const._COMPLETE_TEST_SETVALUES`" data = $smarty.capture.t_test_code image = $T_COMPLETETEST_BASELINK|cat:'images/theory.png' absoluteImagePath = 1 main_options = $T_TABLE_OPTIONS}
{else}
         {capture name = 't_users_code'}
 {capture name = 't_upload_form_code'}
 {$T_UPLOAD_FORM.javascript}
  <style>
  {literal}
  td.ct_hoverCell{background:#C7D7F3;}
  {/literal}
  </style>
  <script>
  var dataSources = new Array('row', 'date', 'user', 'score');
  var dataSourcesColors = new Array('blue', 'red', 'green', 'orange');
  {foreach name = 'test_questions_loop' item = "question" key = "id" from = $T_TEST_QUESTIONS}
   dataSources.push('{$id}_answer');
   dataSourcesColors.push('#'+Math.round(Math.random()*16777215).toString(16));
   dataSources.push('{$id}_score');
   dataSourcesColors.push('#'+Math.round(Math.random()*16777215).toString(16));
  {/foreach}

  {literal}
  var currentDataSource = 0;
  var hasSelectedRow = false;
  function selectRow(idx) {
   if (!hasSelectedRow) {
    hasSelectedRow = true;
    $('import_results_table').select('tr').each(function (s) {
     if (s.hasClassName('oddRowColor')) {
      s.removeClassName('oddRowColor').addClassName('oddRowColorNoHover');
     } else if (s.hasClassName('evenRowColor')) {
      s.removeClassName('evenRowColor').addClassName('evenRowColorNoHover');
     }
    });
    dataSourceColor = dataSourcesColors[currentDataSource];
    $('start_row_result').update(':&nbsp;#'+idx).setStyle({color:dataSourceColor});
    $('start_data_row').value = idx;
    setRowAsStartData(idx, dataSourceColor);
    currentDataSource++;
    $(dataSources[currentDataSource]+'_source').show();
   }
  }
  function setRowAsStartData(idx, color) {
   $('import_results_table').select('tr.defaultRowHeight').each(function (s, i) {if (i==idx) {s.setStyle({color:color});}});
  }
  function highlightColumn(idx) {
   $('import_results_table').select('tr').each(function (s) {if (s.hasClassName('oddRowColorNoHover') || s.hasClassName('evenRowColorNoHover')) {s.select('td').each(function (p, i) {if (i == idx) {p.addClassName('ct_hoverCell');};});}});
  }
  function unHighlightColumn(idx) {
   $('import_results_table').select('tr').each(function (s) {if (s.hasClassName('oddRowColorNoHover') || s.hasClassName('evenRowColorNoHover')) {s.select('td').each(function (p, i) {if (i == idx) {p.removeClassName('ct_hoverCell');};});}});
  }
  function setColumnAsDataSource(idx, color) {
   $('import_results_table').select('tr').each(function (s) {if (s.hasClassName('oddRowColorNoHover') || s.hasClassName('evenRowColorNoHover')) {s.select('td').each(function (p, i) {if (i == idx) {p.setStyle({color:color});};});}});
  }
  function unsetColumnAsDataSource(idx, color) {
   $('import_results_table').select('tr').each(function (s) {if (s.hasClassName('oddRowColorNoHover') || s.hasClassName('evenRowColorNoHover')) {s.select('td').each(function (p, i) {if (i == idx) {p.setStyle({color:''});};});}});
  }
  function setDataSource(idx) {
   dataSource = dataSources[currentDataSource];
   if (dataSource) {
    dataSourceColor = dataSourcesColors[currentDataSource];

    $(dataSource+'_source_result').update(':&nbsp;#'+idx).setStyle({color:dataSourceColor});
    $(dataSource+'_source_hidden').value = idx;
    setColumnAsDataSource(idx, dataSourceColor);
    currentDataSource++;
    if (dataSources[currentDataSource]) {
     $(dataSources[currentDataSource]+'_source').show();
    } else {
     $('continue_button').show();
    }
   }
  }
  function unsetDataSource(idx) {

  }
  {/literal}
  </script>
  {if 0}

  {elseif $T_COMPLETED_TEST_PARSED_CONTENTS}
   <div class = "mediumHeader" style = "margin-bottom:30px;">Step 2: Correlate columns to data</div>
   {$T_CORRELATE_FORM.javascript}
   <form {$T_CORRELATE_FORM.attributes}>
   {$T_CORRELATE_FORM.hidden}
    <table id = "import_results_table" style = "width:100%;">
     <tr><td colspan = "100%">
       <div id = "start_row_source" >Click on the row where the data starts<span id = "start_row_result">: </span></div>
       <div id = "date_source" style = "display:none">Click on the column that will serve as <b>Date</b><span id = "date_source_result">: </span></div>
       <div id = "user_source" style = "display:none">Click on the column that will serve as <b>User name</b><span id = "user_source_result">: </span></div>
       <div id = "score_source" style = "display:none">Click on the column that will serve as <b>Test score</b><span id = "score_source_result">: </span></div>
      {foreach name = 'test_questions_loop' item = "question" key = "id" from = $T_TEST_QUESTIONS}
       <div id = "{$id}_answer_source" style = "display:none">Click on the column that will serve as answer for question <b>{$question.text|@strip_tags|eF_truncate:30}</b><span id = "{$id}_answer_source_result">: </span></div>
       <div id = "{$id}_score_source" style = "display:none">Click on the column that will serve as score for question <b>{$question.text|@strip_tags|eF_truncate:30}</b><span id = "{$id}_score_source_result">: </span></div>
      {/foreach}
       <div id = "continue_button" style = "display:none">{$T_CORRELATE_FORM.complete_course.label}: {$T_CORRELATE_FORM.complete_course.html}<br/>{$T_CORRELATE_FORM.submit.html}</div>
     </td></tr>
     <tr class = "topTitle"><td class = "topTitle centerAlign" colspan = "100%">{$smarty.const._RESULTS}</td></tr>
    {foreach name = 'parsed_contents_loop' key = "key" item = "row" from = $T_COMPLETED_TEST_PARSED_CONTENTS}
     <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"}" onclick = "selectRow('{$key}');">
     {foreach name = 'parsed_contents_inner_loop' key = "key2" item = "cell" from = $row}
      <td style = "padding:2px 4px 2px 4px;" onmouseover = "if (hasSelectedRow) highlightColumn('{$key2}');" onmouseout = "if (hasSelectedRow) unHighlightColumn('{$key2}');" onclick = "if (hasSelectedRow) setDataSource('{$key2}');">{$cell}</td>
     {/foreach}
     </tr>
    {/foreach}
    </table>
   </form>

  {else}
   <div class = "mediumHeader">Step 1: Import csv file and select test</div>
   {$T_UPLOAD_FORM.javascript}
   <form {$T_UPLOAD_FORM.attributes}>
   {$T_UPLOAD_FORM.hidden}
   <table class = "formElements">
    <tr><td class = "labelCell">{$T_UPLOAD_FORM.upload_file.label}</td>
     <td class = "elementCell">{$T_UPLOAD_FORM.upload_file.html}</td></tr>
    <tr><td class = "labelCell">{$T_UPLOAD_FORM.select_test.label}</td>
     <td class = "elementCell">{$T_UPLOAD_FORM.select_test.html}</td></tr>
    <tr><td class = ""></td>
     <td class = "submitCell">{$T_UPLOAD_FORM.submit.html}</td></tr>
   </table>
   </form>
  {/if}
 {/capture}


<!--ajax:usersTable-->

  <table style = "width:100%" class = "sortedTable" size = "{$T_USERS_SIZE}" sortBy = "0" id = "usersTable" useAjax = "1" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_COMPLETETEST_BASEURL}&">
   <tr class = "topTitle">
    <td class = "topTitle" name = "login">{$smarty.const._LOGIN}</td>
    <td class = "topTitle" name = "name">{$smarty.const._NAME}</td>
    <td class = "topTitle" name = "surname">{$smarty.const._SURNAME}</td>
    <td class = "topTitle">{$smarty.const._TEST}</td>
    <td class = "topTitle centerAlign">{$smarty.const._COMPLETE_TEST_SETVALUES}</td>
   </tr>
 {foreach name = 'users_to_lessons_list' key = 'key' item = 'user' from = $T_ALL_USERS}
   <tr class = "defaultRowHeight {cycle values = "oddRowColor, evenRowColor"} {if !$user.active}deactivatedTableElement{/if}">
    <td>{$user.login}</td>
    <td>{$user.name}</td>
    <td>{$user.surname}</td>
    <td>{$T_TESTS_SELECT}</td>
    <td class = "centerAlign"><a href = "javascript:void(0)" onclick = "location = ('{$T_CURRENT_TEST_MODULE_BASEURL}&login={$user.login}&test='+Element.extend(this).up().previous().down().options[this.up().previous().down().options.selectedIndex].value)"><img src = {$T_COMPLETETEST_BASELINK|cat:"images/arrow_right.png"} alt = "{$smarty.const._COMPLETE_TEST_SETVALUES}" title = "{$smarty.const._COMPLETE_TEST_SETVALUES}"></a></td>
   </tr>
 {/foreach}
  </table>
<!--/ajax:usersTable-->

{/capture}


{capture name = "t_complete_test_code"}
<div class = "tabber">
 {eF_template_printBlock tabber = "main" title = "`$smarty.const._LESSONUSERS`" data = $smarty.capture.t_users_code image = $T_COMPLETETEST_BASELINK|cat:'images/theory.png' absoluteImagePath = 1 main_options = $T_TABLE_OPTIONS}
 {eF_template_printBlock tabber = "import" title = "`$smarty.const._COMPLETE_TEST_IMPORTFILE`" data = $smarty.capture.t_upload_form_code image = "32x32/import.png"}
</div>
{/capture}
{eF_template_printBlock title = "`$smarty.const._COMPLETE_TEST`" data = $smarty.capture.t_complete_test_code image = "32x32/tests.png"}

{/if}
