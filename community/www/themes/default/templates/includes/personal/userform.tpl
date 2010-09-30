{*This is the form that contains the user personal data - to be shown in educational (T_SHOW_USER_FORM==1) and enterprise (T_ENTERPRISE) *}
{if $smarty.const.G_VERSIONTYPE == 'enterprise' || $T_SHOW_USER_FORM}


{capture name = 't_personal_form_data_code'}
 <table class = "statisticsTools statisticsSelectList">
  <tr>
   <td class = "labelCell">{$smarty.const._SORTBY}:</td>
   <td class = "filter">
    <select onchange = "reloadAjaxTab('{$T_TABBERAJAX.form}');setCookie('form_cookie', this.options[this.options.selectedIndex].value);">
     <option {if $smarty.cookies.form_cookie == ''}selected{/if} value = ""></option>
     <option {if $smarty.cookies.form_cookie == 'name'}selected{/if} value = "name">{$smarty.const._COURSENAME}</option>
     <option {if $smarty.cookies.form_cookie == 'completion_date'}selected{/if} value = "completion_date">{$smarty.const._COMPLETIONDATE}</option>
     <option {if $smarty.cookies.form_cookie == 'category'}selected{/if} value = "category">{$smarty.const._CATEGORY}</option>
     <option {if $smarty.cookies.form_cookie == 'score'}selected{/if} value = "score">{$smarty.const._SCORE}</option>
    </select>
   </td>
   <td id = "right">{$smarty.const._TOOLS}:&nbsp;
                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&op=status&print=1&popup=1" onclick = "eF_js_showDivPopup('{$smarty.const._PRINTEMPLOYEEFORM}', 2)" target = "POPUP_FRAME">
                            <img src = "images/16x16/printer.png" title = "{$smarty.const._PRINTEMPLOYEEFORM}" alt = "{$smarty.const._PRINTEMPLOYEEFORM}" />
                        </a>
                        <a href = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$T_EDITEDUSER->user.login}&pdf=1&op=status">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                        </a>
   </td></tr>

 </table>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._GENERALUSERINFO}</legend>
  <table style = "width:100%">
   <tr><td rowspan = "6" style = "padding-right:5px;width:1px;">
     <img src = "{if ($T_AVATAR)}view_file.php?file={$T_AVATAR}{else}{$smarty.const.G_SYSTEMAVATARSPATH}unknown_small.{$globalImageExtension}{/if}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" />
    </td></tr>
   <tr><td class = "labelCell">{$smarty.const._NAME}:&nbsp;</td>
    <td class = "elementCell">#filter:login-{$T_EDITEDUSER->user.login}#</td></tr>
   {if $T_EMPLOYEE.birthday}
   <tr><td class = "labelCell">{$smarty.const._BIRTHDAY}:&nbsp;</td>
    <td class = "elementCell">{$T_EMPLOYEE.birthday}</td></tr>
   {/if}
   {if $T_EMPLOYEE.address}
   <tr><td class = "labelCell">{$smarty.const._ADDRESS}:&nbsp;</td>
    <td class = "elementCell">{$T_EMPLOYEE.address}</td></tr>
   {/if}
   {if $T_EMPLOYEE.city}
   <tr><td class = "labelCell">{$smarty.const._CITY}:&nbsp;</td>
    <td class = "elementCell">{$T_EMPLOYEE.city}</td></tr>
   {/if}
   {if $T_EMPLOYEE.hired_on}
   <tr><td class = "labelCell">{$smarty.const._HIREDON}:&nbsp;</td>
    <td class = "elementCell">#filter:timestamp-{$T_EMPLOYEE.hired_on}#</td></tr>
   {/if}
   {if $T_EMPLOYEE.left_on}
   <tr><td class = "labelCell">{$smarty.const._LEFTON}:&nbsp;</td>
    <td class = "elementCell">#filter:timestamp-{$T_EMPLOYEE.left_on}#</td></tr>
   {/if}
  </table>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._TRAINING}</legend>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._OVERALL}</legend>
    <table style = "width:100%">
     {foreach name = 'averages_list' item = 'average' from = $T_AVERAGES}
     <tr>
      <td class = "labelCell">{$average.title}:&nbsp;<td>
      <td class = "elementCell">#filter:score-{$average.avg}#%</td>
     </tr>
     {/foreach}
    </table>
   </fieldset>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._COURSES}</legend>
    <table style = "width:100%">
     {foreach name = 'courses_list' item = 'course' from = $T_COURSES}
     <tr>
      <td style = "width:50%;{if !$course.active}color:red{/if}" >{$course.name}</td>
      <td style = "width:20%">#filter:timestamp-{$course.active_in_course}#</td>
      <td style = "width:20%">{if $course.completed}#filter:timestamp-{$course.to_timestamp}#{else}{$smarty.const._NOTCOMPLETED}{/if}</td>
      <td style = "width:10%">{if $course.completed}#filter:score-{$course.score}#%{/if}</td>
     </tr>
{*
     <tr><td>
       <span {if !$course.active}style = "color:red"{/if}>{$course.name}</span>
       <span>(#filter:timestamp-{$course.active_in_course}#
       {if $course.completed}
        <span class = "success">#filter:timestamp-{$course.to_timestamp}# - {$course.score}%</span>
       {else}
        <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>
       {/if})
       </span>
      </td></tr>
*}
{*
     <tr><td colspan = "2">
       <table width="100%">
      {foreach name = 'lessons_list' item = 'lesson' key = "key" from = $course.lessons}
        <tr><td class="labelForm" style="font-weight:bold;">
          {$lesson.name}<span style = "font-size:12px;font-weight:normal"> (#filter:timestamp-{$lesson.from_timestamp}# {if $lesson.completed}- <span class = "success">#filter:timestamp-{$lesson.to_timestamp}# - {$lesson.score}%</span>{else} - <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>{/if})</span>
         </td></tr>
       {if $lesson.tests}
        <tr><td><table width="100%">
           {foreach name = 'tests_list' item = 'test' from = $lesson.tests}
           <tr><td class = "labelFormCell">{$test.name}:</td><td><table><tr><td><table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$test.score}%</td></tr></table></td></tr></table></td><td>(#filter:timestamp-{$test.timestamp}#)</td><td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
           {/foreach}
           {if $lesson.tests_count > 0}
           <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td><td><table><tr><td><table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$lesson.tests_average}%</td></tr></table></td></tr></table></td></tr>
           {/if}
          </table>
         </td>
        </tr>
       {/if}
      {/foreach}
       </table>
      </td></tr>
*}
     {/foreach}
    </table>
   </fieldset>
   <fieldset class = "fieldsetSeparator">
    <legend>{$smarty.const._LESSONS}</legend>
    <table style = "width:100%">
     {foreach name = 'lessons_list' item = 'lesson' from = $T_LESSONS}
     <tr>
      <td style = "width:50%;{if !$lesson.active}color:red{/if}" >{$lesson.name}</td>
      <td style = "width:20%">#filter:timestamp-{$lesson.from_timestamp}#</td>
      <td style = "width:20%">{if $lesson.completed}#filter:timestamp-{$lesson.to_timestamp}#{else}{$smarty.const._NOTCOMPLETED}{/if}</td>
      <td style = "width:10%">{if $lesson.completed}#filter:score-{$lesson.score}#%{/if}</td>
     </tr>
{*
     <tr><td>
       <span {if !$lesson.active}style = "color:red"{/if}>{$lesson.name}</span>
       <span>(#filter:timestamp-{$lesson.from_timestamp}#
       {if $lesson.completed}
        <span class = "success">#filter:timestamp-{$lesson.to_timestamp}# - {$lesson.score}%</span>
       {else}
        <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>
       {/if})
       </span>
      </td></tr>
*}
{*
     <tr><td>
       <table>
        {foreach name = 'tests_list' key = 'key' item = 'test' from = $lesson.tests}
        <tr><td class = "labelFormCell">{$test.name}:</td>
         <td>
          <table>
           <tr><td>
             <table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
              <tr><td>{$test.score}%</td></tr>
             </table>
            </td></tr>
          </table>
         </td><td>(#filter:timestamp-{$test.timestamp}#)</td>
           <td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
        {/foreach}
        {if $lesson.tests_count > 0}
        <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td>
         <td>
          <table>
           <tr><td>
             <table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
              <tr><td>{$lesson.tests_average}%</td></tr>
             </table>
            </td></tr>
          </table>
         </td></tr>
        {/if}
       </table>
      </td>
     </tr>
*}
     {/foreach}
   </fieldset>
 </fieldset>
{*
  <table style="white-space:nowrap;">
   <tr>
    <td width = "30px">&nbsp</td>
    <td width = "*">
     <table width="100%">
      <tr><td>
       <table width = "100%">
        <tr><td colspan=2 width="300px">&nbsp;</td></tr>
        <tr><td width="35%" align = "center" style="min-width:100px;"><img src = "{if ($T_AVATAR)}view_file.php?file={$T_AVATAR}{else}{$smarty.const.G_SYSTEMAVATARSPATH}unknown_small.{$globalImageExtension}{/if}" title="{$smarty.const._CURRENTAVATAR}" alt="{$smarty.const._CURRENTAVATAR}" /></td>
         <td width="*">
          <table>
           <tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.name.label}:&nbsp;</td><td class="elementCell">{$T_USERNAME}</td></tr>
           {if $T_EMPLOYEE.birthday}<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.birthday.label}:&nbsp;</td><td class="elementCell">{$T_EMPLOYEE.birthday}</td></tr>{/if}
           {if $T_EMPLOYEE.address}<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.address.label}:&nbsp;</td><td class="elementCell">{$T_EMPLOYEE.address}</td></tr>{/if}
           {if $T_EMPLOYEE.city}<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.city.label}:&nbsp;</td><td class="elementCell">{$T_EMPLOYEE.city}</td></tr>{/if}
           {if $T_EMPLOYEE.hired_on}<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.hired_on.label}:&nbsp;</td><td class="elementCell">#filter:timestamp-{$T_EMPLOYEE.hired_on}#</td></tr>{/if}
           {if $T_EMPLOYEE.left_on}<tr><td class = "labelCell">{$T_PERSONAL_DATA_FORM.left_on.label}:&nbsp;</td><td class="elementCell">#filter:timestamp-{$T_EMPLOYEE.left_on}#</td></tr>{/if}
          </table>
         </td>
        </tr>
       </table>
       </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      {if $smarty.const.G_VERSIONTYPE == 'enterprise'}
      <tr><td class="labelFormCellTitle">{$smarty.const._PLACEMENTS}</td><td></td></tr>
      <tr><td>
      <table width="100%" id = "JobsFormTable" class = "sortedTable" noFooter="true">
      <tr display="style:none"><td class = "labelFormCell noSort" name="name"></td><td class = "elementCell noSort" name="description"></td><td class = "elementCell noSort" name="supervisor"></td></tr>
      {foreach name = 'placements' item = 'placement' from = $T_FORM_PLACEMENTS}
      <tr><td class = "userFormCellLabel" name="name">{$placement.name}:&nbsp;</td><td name="description">{$placement.description}&nbsp;{if $placement.supervisor}({$smarty.const._SUPERVISOR}){/if}</td><td class="elementCell" name="description" width="1%">&nbsp;</td></tr>
      {foreachelse}
      <tr><td colspan=3>{$smarty.const._NOPLACEMENTSASSIGNEDYET}</td></tr>
      {/foreach}
      </table>
       </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr><td class="labelFormCellTitle">{$smarty.const._EVALUATIONS}</td></tr>
      <tr><td><table width="100%">
       {foreach name = 'evaluation' item = 'evaluation' from = $T_EVALUATIONS}
         <tr><td class = "userFormCellLabel">#filter:timestamp-{$evaluation.timestamp}#:&nbsp;</td>
          <td class = "elementCell">{$evaluation.specification}&nbsp;[#filter:login-{$evaluation.login}#]</td></tr>
       {foreachelse}
         <tr><td colspan=3>{$smarty.const._NOEVALUATIONSASSIGNEDYET}</td></tr>
       {/foreach}
        </table>
       </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr><td class="labelFormCellTitle">{$smarty.const._SKILLS}</td></tr>
      {foreach name = 'skill_categories' item = 'skill_category' from = $T_SKILL_CATEGORIES}
      <tr><td>
<!--ajax:{$skill_category.id}skillFormTable-->
      <table {if $skill_category.size == 0}style="display:none"{/if} width="100%" size = "{$skill_category.size}" id = "{$skill_category.id}skillFormTable" class = "sortedTable" noFooter="true" {if $smarty.get.print != 1}useAjax = "1" url = "{$smarty.server.PHP_SELF}?ctg=users&edit_user={$smarty.get.edit_user}&op={$smarty.get.op}&skills=1&op={$smarty.get.op}&tabberajax={$T_TABBERAJAX.form}&"{/if}>
       <tr {if $skill_category.size == 0}style="display:none"{/if} ><td class = "labelFormCell noSort" style="font-weight:bold;">{$skill_category.description}</td><td></td></tr>
       <tr {if $skill_category.size == 0}style="display:none"{/if} ><td>
        <tr height="1px"><td class = "labelFormCell noSort" name="description"></td><td class = "elementCell noSort" name="specification"></td></tr>
        {foreach name = 'skill_$skill_category.id' item = 'skill' from = $skill_category.skills}
        <tr><td class = "userFormCellLabel" name="description">{$skill.description}:</td><td class="elementCell" name="specification">&nbsp;{$skill.specification}&nbsp;[{$skill.surname}&nbsp;{$skill.name}]</td></tr>
        {foreachelse}
        <tr><td>{$smarty.const._NOSKILLSASSIGNED}</td></tr>
        {/foreach}
       </td>
      </tr>
      <tr {if $skill_category.size == 0}style="display:none"{/if} ><td>&nbsp;</td></tr>
      </table>
      </td></tr>
<!--/ajax:{$skill_category.id}skillFormTable-->
      {foreachelse}
      <tr><td>{$smarty.const._NOSKILLSHAVEBEENREGISTERED}</td></tr>
      {/foreach}
      {/if}
      {if !isset($T_NOTRAINING)}
      <tr><td class="labelFormCellTitle">{$smarty.const._TRAININGCAP}</td></tr>
      <tr><td>{$smarty.const._SORTBY}:
        <select onchange = "reloadAjaxTab('{$T_TABBERAJAX.form}');setCookie('form_cookie', this.options[this.options.selectedIndex].value);">
         <option {if $smarty.cookies.form_cookie == ''}selected{/if} value = ""></option>
         <option {if $smarty.cookies.form_cookie == 'name'}selected{/if} value = "name">{$smarty.const._COURSENAME}</option>
         <option {if $smarty.cookies.form_cookie == 'completion_date'}selected{/if} value = "completion_date">{$smarty.const._COMPLETIONDATE}</option>
         <option {if $smarty.cookies.form_cookie == 'category'}selected{/if} value = "category">{$smarty.const._CATEGORY}</option>
         <option {if $smarty.cookies.form_cookie == 'score'}selected{/if} value = "score">{$smarty.const._SCORE}</option>
        </select>
       </td></tr>
      <tr><td>
        <table>
         <tr><td colspan = "2">&nbsp;</td></tr>
         {foreach name = 'courses_list' item = 'course' from = $T_COURSES}
         <tr><td class="labelFormCellTitle">
           {if !$course.active}<span style = "color:red">{$course.name}</span>{else}<span>{$course.name}</span>{/if} <span style = "font-size:12px;font-weight:normal">(#filter:timestamp-{$course.active_in_course}# {if $course.completed}- <span class = "success">#filter:timestamp-{$course.to_timestamp}# - {$course.score}%</span>{else} - <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>{/if})</span>
          </td></tr>
         <tr><td colspan = "2">
           <table width="100%">
          {foreach name = 'lessons_list' item = 'lesson' key = "key" from = $course.lessons}
            <tr><td class="labelForm" style="font-weight:bold;">
              {$lesson.name}<span style = "font-size:12px;font-weight:normal"> (#filter:timestamp-{$lesson.from_timestamp}# {if $lesson.completed}- <span class = "success">#filter:timestamp-{$lesson.to_timestamp}# - {$lesson.score}%</span>{else} - <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>{/if})</span>
             </td></tr>
           {if $lesson.tests}
            <tr><td><table width="100%">
               {foreach name = 'tests_list' item = 'test' from = $lesson.tests}
               <tr><td class = "labelFormCell">{$test.name}:</td><td><table><tr><td><table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$test.score}%</td></tr></table></td></tr></table></td><td>(#filter:timestamp-{$test.timestamp}#)</td><td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
               {/foreach}
               {if $lesson.tests_count > 0}
               <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td><td><table><tr><td><table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$lesson.tests_average}%</td></tr></table></td></tr></table></td></tr>
               {/if}
              </table>
             </td>
            </tr>
           {/if}
          {/foreach}
           </table>
          </td></tr>
          <tr><td>&nbsp;</td></tr>
         {/foreach}
          </table>
       </td>
      </tr>
      <tr><td><table width="100%">
       {foreach name = 'lessons_list' item = 'lesson' from = $T_LESSONS}
         <tr><td class="labelFormCellTitle">
           {$lesson.name}<span style = "font-size:12px;font-weight:normal"> (#filter:timestamp-{$lesson.from_timestamp}# {if $lesson.completed}- <span class = "success">#filter:timestamp-{$lesson.to_timestamp}# - {$lesson.score}%</span>{else} - <span class = "failure">{$smarty.const._NOTCOMPLETED}</span>{/if})</span>
          </td></tr>
         <tr><td>
           <table>
            {foreach name = 'tests_list' key = 'key' item = 'test' from = $lesson.tests}
            <tr><td class = "labelFormCell">{$test.name}:</td>
             <td>
              <table>
               <tr><td>
                 <table bgcolor = {if $test.score > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
                  <tr><td>{$test.score}%</td></tr>
                 </table>
                </td></tr>
              </table>
             </td><td>(#filter:timestamp-{$test.timestamp}#)</td>
               <td>{if $test.comments != ''}({$test.comments}){/if}</td></tr>
            {/foreach}
            {if $lesson.tests_count > 0}
            <tr><td><b>{$smarty.const._AVERAGESCORE}:</b></td>
             <td>
              <table>
               <tr><td>
                 <table bgcolor = {if $lesson.tests_average > 60} "#00FF00" {else}"#FF0000"{/if} border="1">
                  <tr><td>{$lesson.tests_average}%</td></tr>
                 </table>
                </td></tr>
              </table>
             </td></tr>
            {/if}
           </table>
          </td>
         </tr>
         <tr><td>&nbsp;</td></tr>
       {/foreach}
        </table>
       </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
       <td>
        <table>
         {foreach name = 'averages_list' item = 'average' from = $T_AVERAGES}
         <tr><td class="labelForm" style="font-weight:bold;">{$average.title}:&nbsp;<td><table bgcolor = {if $average.avg > 60} "#00FF00" {else}"#FF0000"{/if} border="1"><tr><td>{$average.avg}%</td></tr></table></td></tr>
         {/foreach}
        </table>
       </td>
      </tr>
      {/if}
     </table>
    </td>
    <td width="30px">&nbsp;</td>
   </tr>
  </table>
 </td>
</tr>
*}
{/capture}
{/if}
