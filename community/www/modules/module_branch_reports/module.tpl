{**}
{capture name = "branch_statistics"}
    <table class = "statisticsTools statisticsSelectList">
                 <tr><td class = "labelCell">{$smarty.const._CHOOSEBRANCH}:</td>
                     <td class = "elementCell">
                         <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                         <img id = "busy" src = "images/16x16/clock.png" style = "display:none;" alt = "{$smarty.const._LOADING}" title = "{$smarty.const._LOADING}"/>
                         <div id = "autocomplete_branches" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
{if $T_BRANCH_INFO}
                         <input type = "checkbox" style = "vertical-align:middle" {if $smarty.get.subbranches}checked onclick = "document.location=document.location.toString().replace('&subbranches=1','')"{else}onclick = "document.location=document.location.toString()+'&subbranches=1'"{/if} /><span style = "vertical-align:middle" >{$smarty.const._SUBBRANCHES}</span>
{/if}
                     </td>
                 </tr>
                 <tr><td></td>
                     <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
              </tr>
{if $T_BRANCH_INFO}
    <tr>
                 <td colspan = "2" id = "right">
                        {$smarty.const._EXPORTSTATS}
                        <a href = "{$T_MODULE_BASEURL}&sel_branch={$smarty.get.sel_branch}&from_year={$smarty.get.from_year}&from_month={$smarty.get.from_month}&from_day={$smarty.get.from_day}&to_year={$smarty.get.to_year}&to_month={$smarty.get.to_month}&to_day={$smarty.get.to_day}&subbranches={$smarty.get.subbranches}&excel=1">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                        </a>
                        <a href = "{$T_MODULE_BASEURL}&sel_branch={$smarty.get.sel_branch}&from_year={$smarty.get.from_year}&from_month={$smarty.get.from_month}&from_day={$smarty.get.from_day}&to_year={$smarty.get.to_year}&to_month={$smarty.get.to_month}&to_day={$smarty.get.to_day}&subbranches={$smarty.get.subbranches}&pdf=1">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                        </a>
                    </td></tr>
{/if}
             </table>
            {if $T_BRANCH_INFO}
                    <form name = "systemperiod">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="to_" time=$T_TO_TIMESTAMP start_year="-5" end_year="+0" field_order = $T_DATE_FORMATGENERAL}</td></tr>
                        <tr><td class = "labelCell"></td>
                            <td class = "elementCell"><a href = "javascript:void(0)" onclick = "showSystemStats('day')">{$smarty.const._LAST24HOURS}</a> - <a href = "javascript:void(0)" onclick = "showSystemStats('week')">{$smarty.const._LASTWEEK}</a> - <a href = "javascript:void(0)" onclick = "showSystemStats('month')">{$smarty.const._LASTMONTH}</a></td></tr>
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" value = "{$smarty.const._SHOW}" class = "flatButton" onclick = "document.location='{$T_MODULE_BASEURL}&sel_branch={$smarty.get.sel_branch}&from_year='+document.systemperiod.from_Year.value+'&from_month='+document.systemperiod.from_Month.value+'&from_day='+document.systemperiod.from_Day.value+'&to_year='+document.systemperiod.to_Year.value+'&to_month='+document.systemperiod.to_Month.value+'&to_day='+document.systemperiod.to_Day.value+'&subbranches={$smarty.get.subbranches}'"></td>
                        </tr>
                    </table>
                    </form>

             <table class = "statisticsGeneralInfo">
              <tr>
               <td>
                         <table>
                             <tr class = "{cycle name = 'common_branch_info' values = 'oddRowColor, evenRowColor'}">
                                 <td class = "labelCell">{$smarty.const._BRANCHNAME}:</td>
                                 <td class = "elementCell">{$T_BRANCH_PATH} {if $T_BRANCH_INFO.is_default}({$smarty.const._DEFAULT}){/if}</td>
                             </tr>
        {if $T_BRANCH_INFO.father_name}
           <tr class = "{cycle name = 'common_branch_info' values = 'oddRowColor, evenRowColor'}">
                                 <td class = "labelCell">Parent branch:</td>
                                 <td class = "elementCell">{$T_BRANCH_INFO.father_name}</td>
                             </tr>
        {/if}
           <tr class = "{cycle name = 'common_branch_info' values = 'oddRowColor, evenRowColor'}">
                                 <td class = "labelCell">{$smarty.const._BRANCHUSERS}:</td>
                                 <td class = "elementCell">{$T_BRANCH_INFO.users_count}</td>
                             </tr>
           <tr class = "{cycle name = 'common_branch_info' values = 'oddRowColor, evenRowColor'}">
                                 <td class = "labelCell">{$smarty.const._JOBDESCRIPTIONS}:</td>
                                 <td class = "elementCell">{$T_BRANCH_INFO.jobs_count}</td>
                             </tr>

           <tr class = "{cycle name = 'common_branch_info' values = 'oddRowColor, evenRowColor'}">
                                 <td class = "labelCell">{$smarty.const._SUBBRANCHES}:</td>
                                 <td class = "elementCell">{$T_BRANCH_INFO.subbranches_count}</td>
                             </tr>



                         </table>
              </td></tr>
             </table>

             {foreach name = "users_list" item = "user" key = "key" from = $T_USERS}
             <div style = "margin-top:30px">
              <table>
               <tr><td style = "font-weight:bold">User name:&nbsp;</td><td><a href = "{$smarty.server.PHP_SELF}?ctg=personal&user={$user.login}&op=user_courses" class = "editLink">#filter:login-{$user.login}#</a></td></tr>
               <tr><td style = "font-weight:bold">User type:&nbsp;</td><td>{$user.user_type}</td></tr>
               <tr><td style = "font-weight:bold">Placement:&nbsp;</td><td>{$user.description} in {$user.branch_path}</td></tr>
               <tr><td style = "font-weight:bold">Last login:&nbsp;</td><td>#filter:timestamp_time-{$user.last_login}#</td></tr>
               <tr><td style = "font-weight:bold">Total time in system:&nbsp;</td><td>
        {if $user.time.hours || $user.time.minutes || $user.time.seconds}
         {if $user.time.hours}{$user.time.hours}{$smarty.const._HOURSSHORTHAND} {/if}
         {if $user.time.minutes}{$user.time.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}
         {if $user.time.seconds}{$user.time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
        {else}
         {$smarty.const._NOACCESSDATA}
        {/if}
       </td></tr>
              </table>

              <table style = "width:100%" class = "sortedTable" size = "{$T_TABLE_SIZE}" sortBy = "2" id = "userTable{$user.login}" rowsPerPage = "{$smarty.const.G_DEFAULT_TABLE_SIZE}" url = "{$T_MODULE_BASEURL}&user={$user.login}&sel_branch={$smarty.get.sel_branch}&from_year={$smarty.get.from_year}&from_month={$smarty.get.from_month}&from_day={$smarty.get.from_day}&to_year={$smarty.get.to_year}&to_month={$smarty.get.to_month}&to_day={$smarty.get.to_day}&subbranches={$smarty.get.subbranches}&">
               <tr><td class = "topTitle" name = "name">{$smarty.const._COURSENAME}</td>
                <td class = "topTitle centerAlign" name = "active_in_course">{$smarty.const._ENROLLEDON}</td>
                <td class = "topTitle centerAlign" name = "start_date">{$smarty.const._STARTDATE}</td>
                <td class = "topTitle centerAlign" name = "end_date">{$smarty.const._ENDDATE}</td>
                <td class = "topTitle centerAlign" name = "completed">{$smarty.const._COMPLETED}</td>
               </tr>
   {foreach name = 'courses_list' item = "course" from = $T_DATA_SOURCE[$user.login]}
    <tr class = "{cycle name = 'courses_cycle_'|cat:$smarty.foreach.users_list.iteration values = 'oddRowColor, evenRowColor'} {if !$course.active}deactivatedTableElement{/if}">
     <td>{$course.name}</td>
     <td class = "centerAlign">
      <span style="display:none">{$course.active_in_course}</span>
      #filter:timestamp_time_nosec-{$course.active_in_course}#
     </td>
     <td class = "centerAlign">
      <span style="display:none">{$course.start_date}</span>
      #filter:timestamp_time_nosec-{$course.start_date}#
     </td>
     <td class = "centerAlign">
      <span style="display:none">{$course.end_date}</span>
      #filter:timestamp_time_nosec-{$course.end_date}#
     </td>
     <td class = "centerAlign">
      <span style="display:none">{$course.completed}</span>
      {if $course.completed}#filter:timestamp_time_nosec-{$course.to_timestamp}#{else}<img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}">{/if}
     </td>
    </tr>
   {foreachelse}
    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
   {/foreach}
              </table>


             </div>
             {foreachelse}
             <div style = "font-size:18px;text-align:center;margin-top:50px;margin-bottom:50px">The branch "{$T_BRANCH_NAME}" does not contain any users</div>
             {/foreach}
           {/if}
{/capture}
{eF_template_printBlock title = $smarty.const._MODULE_WENTWORTH_MODULEWENTWORTH data = $smarty.capture.branch_statistics image = '32x32/users.png' help = 'Reports'}
