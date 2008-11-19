{if !isset($T_OPTION)}
        <table class = "statisticsPanel">
            <tr><td>
                {if $T_MODULE_HCD_INTERFACE}
                    {eF_template_printIconTable title = $smarty.const._STATISTICS columns = 3 links  =$T_STATISTICS_OPTIONS image = '32x32/gears.png'}
                {else}    
                    {eF_template_printIconTable title = $smarty.const._STATISTICS columns = 4 links = $T_STATISTICS_OPTIONS image = '32x32/gears.png'}
                {/if}    
            </td></tr>        
        </table>
{elseif $T_OPTION == 'user'}
    {capture name = 'user_statistics'}
    	{if !$T_SINGLE_USER}
            <table class = "statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._CHOOSEUSER}:</td>
                    <td class = "elementCell">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                        <img id = "busy" src = "images/12x12/hourglass.png" style = "display:none;" alt = "working ..."/> 
                        <div id = "autocomplete_choices" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                    {literal}
                            <script language = "JavaScript" type = "text/javascript">
                                <!--        
                                new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ask_users.php?type=1", {paramName: "preffix", afterUpdateElement : getSelectionId, indicator : "busy"}); 
                                function getSelectionId(text, li) {
                                        document.location='{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=statistics&tab=lessons&option=user&sel_user='+li.id;
                                }
                                //-->
                            </script>
                    {/literal}      
                    </td>    
                </tr>
                <tr><td></td>
                    <td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td>
            	</tr>
            </table>
            <br />
        {/if}
        
        {if (isset($T_USER_INFO))}
            <br/><br/>
            <table class = "statisticsTools">
                <tr><td id = "right">
                        {$smarty.const._EXPORTSTATS}
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&excel=user">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}"/>
                        </a>
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&pdf=user">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt="{$smarty.const._PDFFORMAT}"/>
                        </a>
                    </td></tr>
            </table>

            <br/>
            <table class = "statisticsGeneralInfo">
            	<tr><td id = "userAvatar"><img src = "view_file.php?file={$T_AVATAR}" title = "{$smarty.const._USERAVATAR}" alt = "{$smarty.const._USERAVATAR}"></td>
            		<td>
                        <table>                        
                            <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._USERNAME}:</td>
                                <td class = "elementCell">{$T_USER_INFO.general.fullname}</td>
                            </tr>                                        
                            <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._USERTYPE}:</td>
                                <td class = "elementCell">{if $T_USER_INFO.general.user_type == 'administrator'}{$smarty.const._ADMINISTRATOR}{elseif $T_USER_INFO.general.user_type == 'professor'}{$smarty.const._PROFESSOR}{else}{$smarty.const._STUDENT}{/if}</td>
                            </tr>     
                        {if $T_USER_INFO.general.user_types_ID}
                            <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._USERROLE}:</td>
                                <td class = "elementCell">{$T_ROLES[$T_USER_INFO.general.user_types_ID]}</td>
                            </tr>                             
                        {/if}           
                            <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._LESSONS}:</td>
                                <td class = "elementCell">{$T_USER_INFO.general.total_lessons}</td>
                            </tr>
                            <tr class = "{cycle name = 'common_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._TOTALLOGINTIME}:</td>
                                <td class = "elementCell">
                                    {if $T_USER_INFO.general.total_login_time.hours || $T_USER_INFO.general.total_login_time.minutes || $T_USER_INFO.general.total_login_time.seconds}
                                    	{if $T_USER_INFO.general.total_login_time.hours}{$T_USER_INFO.general.total_login_time.hours}h{/if}
                                    	{if $T_USER_INFO.general.total_login_time.minutes}{$T_USER_INFO.general.total_login_time.minutes}'{/if}
                                    	{if $T_USER_INFO.general.total_login_time.seconds}{$T_USER_INFO.general.total_login_time.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if} 
                                </td>
                            </tr>        
                        </table>
            	</td></tr>
            </table>
                        
            <div class = "tabber">
                {if !empty($T_USER_LESSON_INFO)}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'lessons')} tabbertabdefault{/if}" title = "{$smarty.const._LESSONS}">
                    {if !empty($T_USER_LESSON_INFO.student)}
                        <table class = "statisticsTools">
							<tr><td>{$smarty.const._STUDENTROLE}:</td></tr>
						</table>
                        <table class = "sortedTable">
                            <tr>
                                <td class = "topTitle">{$smarty.const._LESSON}</td>
                                <td class = "topTitle">{$smarty.const._LESSONROLE}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._TIMEINLESSON}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._CONTENT}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._TESTS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._PROJECTS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
                            </tr>
                            {foreach name = 'lesson_list' key = 'lesson_id' item = 'lesson' from = $T_USER_LESSON_INFO.student}
                            <tr class = "{cycle name = 'user_lessons_list' values = 'oddRowColor, evenRowColor'} {if !$lesson.active}deactivatedTableElement{/if}">
                                <td>{$lesson.name}</td>
                                <td>{$T_ROLES[$lesson.role]}</td>
                                <td class = "centerAlign">
                                    <span style = "display:none">{$lesson.time.total_seconds}</span>
                                    {if $lesson.time.total_seconds}
                                    	{if $lesson.time.hours}{$lesson.time.hours}h{/if}
                                    	{if $lesson.time.minutes}{$lesson.time.minutes}'{/if}
                                    	{if $lesson.time.seconds}{$lesson.time.seconds}''{/if}
                                    {else}
                                    	-
                                    {/if}
                                </td>
                                <td class = "progressCell">
                                    <span style = "display:none">{$lesson.content+1000}</span>
                                    <span class = "progressNumber">#filter:score-{$lesson.content}#%</span>
                                    <span class = "progressBar" style = "width:{$lesson.content}px;">&nbsp;</span>&nbsp;&nbsp;
                                </td>     
                                <td class = "progressCell">                       
                                {if $lesson.total_tests && $lesson.tests_progress}
                                    <span style = "display:none">{$lesson.tests+1000}</span>
                                    <span class = "progressNumber">#filter:score-{$lesson.tests}#%</span>
                                    <span class = "progressBar" style = "width:{$lesson.tests}px;">&nbsp;</span>&nbsp;&nbsp;
                                {else}<div class = "centerAlign">-</div>{/if}
                                </td>  
                                <td class = "progressCell">      
                            	{if $lesson.total_projects && $lesson.projects_progress}
                                    <span style = "display:none">{$lesson.projects+1000}</span>
                                    <span class = "progressNumber">#filter:score-{$lesson.projects}#%</span>
                                    <span class = "progressBar" style = "width:{$lesson.projects}px;">&nbsp;</span>&nbsp;&nbsp;
                                {else}<div class = "centerAlign">-</div>{/if}
                                </td>  
                                <td class = "centerAlign">
                            	{if $lesson.completed}
                                    <img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>
                                {else}
                                    <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>
                            	{/if}
                            	</td>
                            	<td class = "centerAlign">#filter:score-{$lesson.score}#%</td>
                                <td class = "centerAlign">
                                    <a href = "user_lesson.php?user={$T_USER_LOGIN}&lesson={$lesson_id}" onclick = "eF_js_showDivPopup('{$lesson_name}', 2)" target = "POPUP_FRAME">
                                    	<img src="images/16x16/about.png" title = "{$smarty.const._MOREINFO}" alt = "{$smarty.const._MOREINFO}"></a>
                                </td>
                            </tr>
                            {/foreach}    
                        </table>
	                    <br/>
                    {/if}
                    {if !empty($T_USER_LESSON_INFO.professor)}
                        <table class = "statisticsTools">
							<tr><td>{$smarty.const._PROFESSORROLE}:</td></tr>
						</table>                    
                        <table class = "sortedTable">                        
                            <tr>
                                <td class = "topTitle">{$smarty.const._LESSON}</td>
                                <td class = "topTitle">{$smarty.const._LESSONROLE}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._TIMEINLESSON}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._CONTENT}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._TESTS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._PROJECTS}</td>
                            </tr>
                            {foreach name = 'lesson_list' key = 'lesson_id' item = 'lesson' from=$T_USER_LESSON_INFO.professor}
                            <tr class = "{cycle name = 'professor_lessons_list' values = 'oddRowColor, evenRowColor'} {if !$lesson.active}deactivatedTableElement{/if}">
                                <td>{$lesson.name}</td>
                                <td>{$T_ROLES[$lesson.role]}</td>
                                <td class = "centerAlign">
                                    <span style = "display:none">{$lesson.time.total_seconds}</span>
                                    {if $lesson.time.total_seconds}
                                    	{if $lesson.time.hours}{$lesson.time.hours}h{/if}
                                    	{if $lesson.time.minutes}{$lesson.time.minutes}'{/if}
                                    	{if $lesson.time.seconds}{$lesson.time.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if}
                                </td>
                                <td class = "centerAlign">{$lesson.content}</td>     
                                <td class = "centerAlign">{$lesson.tests}</td>  
                                <td class = "centerAlign">{$lesson.projects}</td>  
                            </tr>
                            {/foreach}    
                        </table>
                    {/if}
                </div>
                {/if}          
                
                {if !empty($T_USER_COURSE_INFO)}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'courses')} tabbertabdefault{/if}" title = "{$smarty.const._COURSES}">    
                    {if !empty($T_USER_COURSE_INFO.student)}
                        <table class = "statisticsTools">
							<tr><td>{$smarty.const._STUDENTROLE}:</td></tr>
						</table>                    
                        <table class = "sortedTable">
                            <tr>
                                <td class = "topTitle">{$smarty.const._COURSE}</td>
                                <td class = "topTitle">{$smarty.const._COURSEROLE}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._LESSONS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._OPTIONS}</td>
                            </tr>
                            {foreach name = 'course_list' key = 'course_id' item = 'course' from = $T_USER_COURSE_INFO.student}
                            <tr class = "{cycle name = 'user_courses_list' values = 'oddRowColor, evenRowColor'} {if !$course.active}deactivatedTableElement{/if}">
                                <td>{$course.name}</td>
                                <td>{$T_ROLES[$course.role]}</td>
                                <td class = "centerAlign">{$course.lessons}</td>
                                <td class = "progressCell">
                                    <span style = "display:none">{$course.score+1000}</span>
                                    <span class = "progressNumber">#filter:score-{$course.score}#%</span>
                                    <span class = "progressBar" style = "width:{$course.score}px;">&nbsp;</span>&nbsp;&nbsp;
                                </td>  
                                <td class = "centerAlign">
                                {if $course.completed}
                                    <img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}"/>
                                {else}
                                    <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}"/>
                                {/if}
                                </td>   
                                <td class = "centerAlign">
                                    <a href = "user_course.php?user={$T_USER_LOGIN}&course={$course_id}" onclick = "eF_js_showDivPopup('{$course.name}', 2)" target = "POPUP_FRAME">
                                    	<img src = "images/16x16/about.png" title = "{$smarty.const._MOREINFO}" alt = "{$smarty.const._MOREINFO}"></a>
                                </td>
                            </tr>
                            {/foreach}    
                        </table>
                        <br>
                    {/if}
                    {if !empty($T_USER_COURSE_INFO.professor)}
                        <table class = "statisticsTools">
							<tr><td>{$smarty.const._STUDENTROLE}:</td></tr>
						</table>                    
                        <table class = "sortedTable">
                            <tr>
                                <td class = "topTitle">{$smarty.const._COURSE}</td>
                                <td class = "topTitle">{$smarty.const._COURSEROLE}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._LESSONS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._USERS}</td>
                                <td class = "topTitle centerAlign">{$smarty.const._PROFESSORS}</td>
                            </tr>
                            {foreach name = 'p_course_list' key = 'course_id' item = 'course' from=$T_USER_COURSE_INFO.professor}
                            <tr class = "{cycle name = 'professor_courses_list' values = 'oddRowColor, evenRowColor'} {if !$course.active}deactivatedTableElement{/if}">
                                <td>{$course.name}</td>
                                <td>{$T_ROLES[$course.role]}</td>
                                <td class = "centerAlign">{$course.lessons}</td>
                                <td class = "centerAlign">{$course.students}</td>
                                <td class = "centerAlign">{$course.professors}</td>
                            </tr>
                            {/foreach}    
                        </table>
                    {/if}
                
                </div>
                {/if}
                
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'moreinfo')} tabbertabdefault{/if}" title = "{$smarty.const._MOREINFO}">
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._GENERALUSERINFO}</td></tr>
                        <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._LANGUAGE}:</td>
                                <td class = "elementCell">{$T_USER_INFO.general.language}</td>
                        </tr>
                        <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._ACTIVE}:</td>
                                <td class = "elementCell">{$T_USER_INFO.general.active_str}</td>
                        </tr>
                        <tr class = "{cycle name = 'general_user_info' values = 'oddRowColor, evenRowColor'}">
                                <td class = "labelCell">{$smarty.const._JOINED}:</td>
                                <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.general.joined}#</td>
                        </tr>
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERCOMMUNICATIONINFO}</td></tr>                                 
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._FORUMPOSTS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.forum_messages|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._FORUMLASTMESSAGE}:</td>
                            <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.communication.last_message.timestamp}#</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._PERSONALMESSAGES}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.personal_messages|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._MESSAGESFOLDERS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.personal_folders|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell" >{$smarty.const._FILES}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.files|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._FOLDERS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.folders|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALSIZE}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.total_size}KB</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._CHATMESSAGES}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.chat_messages|@sizeof}</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._CHATLASTMESSAGE}:</td>
                            <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.communication.last_caht.timestamp}#</td>
                        </tr>
                        <tr class = "{cycle name = 'communication_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._COMMENTS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.communication.comments|@sizeof}</td>
                        </tr>
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERUSAGEINFO}</td></tr>                                    
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._LASTLOGIN}:</td>
                            <td class = "elementCell">#filter:timestamp-{$T_USER_INFO.usage.last_login.timestamp}#</td>
                        </tr>
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALLOGINS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.logins|@sizeof}</td>    
                        </tr>
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._MONTHLOGINS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.month_logins|@sizeof}</td>    
                        </tr>
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._WEEKLOGINS}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.week_logins|@sizeof}</td>    
                        </tr>
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._MEANDURATION}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.mean_duration}&#039;</td>    
                        </tr>
                        <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._MONTHMEANDURATION}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.month_mean_duration}&#039;</td>    
                        </tr>
                         <tr class = "{cycle name = 'user_usage' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._WEEKMEANDURATION}:</td>
                            <td class = "elementCell">{$T_USER_INFO.usage.week_mean_duration}&#039;</td>    
                        </tr>
                    </table>
                </div>
                {if $T_BASIC_TYPE == 'administrator'}     
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'usertraffic')} tabbertabdefault{/if}" title = "{$smarty.const._TRAFFIC}">
                    <form name = "period">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix = "from_" time = $T_FROM_TIMESTAMP start_year = "-2" end_year = "+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix = "to_"   time = $T_TO_TIMESTAMP   start_year = "-2" end_year = "+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $T_TO_TIMESTAMP   display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._ANALYTICLOG}:</td>
                        	<td class = "elementCell"><input class = "inputCheckbox" type = checkbox id = "showLog" {if (isset($T_USER_LOG))}checked{/if}></td></tr>                            
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" value = "{$smarty.const._SHOW}" onclick = "document.location='administrator.php?ctg=statistics&option=user&sel_user={$T_USER_LOGIN}&tab=usertraffic&from_year='+document.period.from_Year.value+'&from_month='+document.period.from_Month.value+'&from_day='+document.period.from_Day.value+'&from_hour='+document.period.from_Hour.value+'&from_min='+document.period.from_Minute.value+'&to_year='+document.period.to_Year.value+'&to_month='+document.period.to_Month.value+'&to_day='+document.period.to_Day.value+'&to_hour='+document.period.to_Hour.value+'&to_min='+document.period.to_Minute.value+'&showlog='+document.period.showLog.checked"></td>
                        </tr>
	                </table>
                    </form>
					
                    <table class = "statisticsTools">
                        <tr><td id = "right">
                        		{$smarty.const._ACCESSSTATISTICS}:
                                <a href = "display_chart.php?id=11&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}&login={$T_USER_LOGIN}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)" target = "POPUP_FRAME">
                                	<img src = "images/16x16/chart.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}"/></a>     
                            </td></tr>
                    </table>
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._USERTRAFFIC}</td></tr>
                        <tr class = "oddRowColor">
                        	<td class = "labelCell">{$smarty.const._TOTALLOGINS}: </td>
                        	<td class = "elementCell">{$T_USER_TRAFFIC.total_logins}</td></tr>
                        <tr class = "evenRowColor">
                        	<td class = "labelCell">{$smarty.const._LESSONACCESS}: </td>
                        	<td class = "elementCell">{$T_USER_TRAFFIC.total_access}</td></tr>
                    </table>

					<br/>					
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ACCESSPERLESSON}</td></tr>
                    </table>   
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle">{$smarty.const._LESSON}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                            <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
                        </tr>                                   
                        {foreach name = 'lesson_traffic_list' key = "id" item = "lesson" from = $T_USER_TRAFFIC.lessons}
                            <tr class = "{cycle name = 'lessontraffic' values = 'oddRowColor, evenRowColor'} {if !$lesson.active}deactivatedTableElement{/if}">
                                <td>{$lesson.name}</td>
                                <td class = "centerAlign">{$lesson.accesses}</td>   
                                <td class = "centerAlign">
                                    <span style="display:block">{$lesson.total_seconds}</span>
                                    {if $lesson.total_seconds}
                                    	{if $lesson.hours}{$lesson.hours}h{/if}
                                    	{if $lesson.minutes}{$lesson.minutes}'{/if}
                                    	{if $lesson.seconds}{$lesson.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if} 
                                </td>  
                                <td class = "centerAlign">
                                    <a href = "display_chart.php?id=10&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}&login={$T_USER_LOGIN}&lesson_id={$id}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)" target = "POPUP_FRAME">
                                    	<img src = "images/16x16/chart.png" title = "{$smarty.const._ACCESSSTATISTICS}" alt = "{$smarty.const._ACCESSSTATISTICS}"/></a>     
                                </td>
                            </tr>                 
                        {foreachelse}
                        	<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}                              
                    </table>                        
                    <br/>
                  {if isset($T_USER_LOG)}
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
                    </table>   
                    <table>
                    	<tr>
                            <td class = "topTitle">{$smarty.const._LESSON}</td>
                            <td class = "topTitle">{$smarty.const._UNIT}</td>
                            <td class = "topTitle">{$smarty.const._ACTION}</td>
                            <td class = "topTitle">{$smarty.const._TIME}</td>    
                            <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
                        </tr>                      
                    {foreach name = 'user_log_loop' key = "key" item = "info" from = $T_USER_LOG}
                        <tr class = "{cycle name = 'user_log_list' values = 'oddRowColor, evenRowColor'}">
                            <td>{$info.lesson_name}</td>           
                            <td>{$info.content_name}</td>
                            <td>{$T_ACTIONS[$info.action]}</td>           
                            <td>#filter:timestamp_time-{$info.timestamp}#</td>
                            <td>{$info.session_ip|eF_decodeIp}</td>           
                        </tr>
					{foreachelse}
						<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                    </table>
                {/if}                
                </div>
            {/if}
            </div>
        {/if}
    {/capture}
    {if $T_USER_LOGIN != ""}
    	{eF_template_printInnerTable title = "`$smarty.const._REPORTSFORUSER` <span class='innerTableName'>&quot;`$T_USER_LOGIN`&quot;</span>" data = $smarty.capture.user_statistics image = '32x32/user1.png'}   
    {else}
    	{eF_template_printInnerTable title = $smarty.const._USERSTATISTICS data = $smarty.capture.user_statistics image = '32x32/user1.png'}  
	{/if}
    
	 
{elseif $T_OPTION == 'lesson'}
        {capture name = 'lesson_statistics'}
            <table class = "statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._CHOOSELESSON}:</td>
                    <td class = "elementCell">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                        <img id = "busy" src = "images/12x12/hourglass.png" style="display:none;" alt="working ..."/> 
                        <div id = "autocomplete_choices" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                            {literal}
                                <script language = "JavaScript" type = "text/javascript">
                                    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ask_lessons.php", {paramName: "preffix", afterUpdateElement : getSelectionId, indicator : "busy"}); 
                                    function getSelectionId(text, li) {
                                    	document.location='{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=statistics&option=lesson&tab=users&sel_lesson='+li.id;
                                    }
                                </script>
                            {/literal}          
                    </td>
                </tr>
                <tr><td></td>
                	<td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td></tr>     
            </table>       
        
        {if isset($T_LESSON_ID)}           
            <br/><br/>
            <table class = "statisticsTools">
                <tr>
                	<td>{$smarty.const._GROUPFILTER}:
                        <select name = "group_filter" onchange = "if(this.options[this.selectedIndex].value != '') document.location='professor.php?ctg=statistics&option=lesson&{if (isset($smarty.get.tab)) }&tab={$smarty.get.tab} {else}&tab=overall {/if}&sel_lesson={$T_LESSON_ID}&group_filter='+this.options[this.selectedIndex].value;">                        
                                <option value = "-1" class = "inactiveElement" {if !$smarty.get.group_filter}selected{/if}>{$smarty.const._SELECTGROUP}</option>
                            {foreach name = "group_options" from = $T_GROUPS item = 'group' key='id'}                       
                                <option value = "{$group.id}" {if $smarty.get.group_filter == $group.id}selected{/if}>{$group.name}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td id = "right">
                        {$smarty.const._EXPORTSTATS}
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=lesson&sel_lesson={$T_LESSON_ID}&group_filter={$smarty.get.group_filter}&excel=lesson">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}"/>
                        </a>
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=lesson&sel_lesson={$T_LESSON_ID}&group_filter={$smarty.get.group_filter}&pdf=lesson">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}"/>
                        </a>
                    </td></tr>
            </table>            

            <br />            
            <table class = "statisticsGeneralInfo">           
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._NAME}:</td>
                    <td class = "elementCell"><b>{$T_LESSON_NAME}</b></td>
                </tr>
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._DIRECTION}:</td>
                    <td class = "elementCell"><b>{$T_LESSON_INFO.direction}</b></td>
                </tr>
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._STUDENTS}:</td>
                    <td class = "elementCell"><b>{$T_LESSON_INFO.students|@sizeof}</b></td>
                </tr>
                <tr class = "{cycle name = 'common_lesson_info' values = 'oddRowColor, evenRowColor'}">
                    <td class = "labelCell">{$smarty.const._PROFESSORS}:</td>
                    <td class = "elementCell"><b>{$T_LESSON_INFO.professors|@sizeof}</b></td>
                </tr>
            </table>
            
            <div class = "tabber">                        
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'users')} tabbertabdefault{/if}" title = "{$smarty.const._USERS}">
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._STUDENTS}:</td></tr>
                    </table>   
                    <table class = "sortedTable" sortBy = "0">    
                        <tr>
                            <td class = "topTitle">{$smarty.const._USERNAME}</td>
                            <td class = "topTitle">{$smarty.const._FULLNAME}</td>
                            <td class = "topTitle">{$smarty.const._LESSONROLE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TIMEINLESSON}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._CONTENT}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TESTS}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._PROJECTS}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._MESSAGES}</td>
                        </tr>
                    {foreach name = 'student_list' key = 'login' item = "info" from = $T_STUDENTS_INFO}   
                        <tr class = "{cycle name = 'student_list' values = 'oddRowColor, evenRowColor'} {if !$info.active[$lesson_id]}deactivatedTableElement{/if}">
                            <td>#filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">{$login}</a></td>
                            <td>{$info.surname} {$info.name.0}.</td>
                            <td>{$T_ROLES[$info.role]}</td>
                            <td class = "centerAlign">
                                {if $info.completed}
                                    <img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}" border = "0" />
                                {else}
                                    <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}" border = "0" />
                                {/if}
                            </td>
                            <td class = "centerAlign">
                                <span style = "display:none">{$info.seconds}</span>
                                {if $info.seconds}
                                	{if $info.time.hours}{$info.time.hours}{$smarty.const._HOURSSHORTHAND}{/if}
                                	{if $info.time.minutes}{$info.time.minutes}{$smarty.const._MINUTESSHORTHAND}{/if}
                                	{if $info.time.seconds}{$info.time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                {else}-{/if}
                            </td>
                            <td class = "progressCell">
                                <span style = "display:none">{$info.content+1000}</span>
                                <span class = "progressNumber" >#filter:score-{$info.content}#%</span>
                                <span class = "progressBar" style = "width:{$info.content}px;">&nbsp;</span>&nbsp;
                            </td>
                            <td class = "progressCell">
                            {if $info.total_tests && $info.tests_progress}
                                <span style = "display:none">{$info.tests+1000}</span>
                                <span class = "progressNumber">#filter:score-{$info.tests}#%</span>
                                <span class = "progressBar" style = "width:{$info.tests}px;">&nbsp;</span>&nbsp;
                            {else}<div class = "centerAlign">-</div>{/if}
                            </td>
                            <td class = "progressCell">
                            {if $info.total_projects && $info.projects_progress}
                                <span style = "display:none">{$info.projects+1000}</span>
                                <span class = "progressNumber">#filter:score-{$info.projects}#%</span>
                                <span class = "progressBar" style = "width:{$info.projects}px;">&nbsp;</span>&nbsp;
                            {else}<div class = "centerAlign">-</div>{/if}
                            </td>
                            <td class = "centerAlign">{$info.posts}</td>
                        </tr>                   
                    {foreachelse}
                    	<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
                    </table>      
                    <br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._PROFESSORS}:</td></tr>
                    </table>   
                    <table class = "sortedTable" sortBy = "0">
                        <tr>
                            <td class = "topTitle">{$smarty.const._USERNAME}</td>
                            <td class = "topTitle">{$smarty.const._FULLNAME}</td>
                            <td class = "topTitle">{$smarty.const._LESSONROLE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TIMEINLESSON}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._MESSAGES}</td>
                        </tr>
                    {foreach name = 'professor_list' key = 'login' item = "info" from = $T_PROFESSORS_INFO}
                        <tr class = "{cycle name = 'professor_list' values = 'oddRowColor, evenRowColor'} {if !$info.active[$lesson_id]}deactivatedTableElement{/if}">
                            <td>#filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">{$login}</a></td>
                            <td>{$info.surname} {$info.name.0}.</td>
                            <td>{$T_ROLES[$info.role]}</td>
                            <td class = "centerAlign">
                                <span style = "display:none">{$info.seconds}</span>
                                {if $info.seconds}
                                	{if $info.time.hours}{$info.time.hours}{$smarty.const._HOURSSHORTHAND}{/if}
                                	{if $info.time.minutes}{$info.time.minutes}{$smarty.const._MINUTESSHORTHAND}{/if}
                                	{if $info.time.seconds}{$info.time.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}
                                {else}-{/if}
                            </td>
                            <td class = "centerAlign">{$info.posts}</td>
                        </tr>                   
                    {foreachelse}
                    	<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}
    				</table>      
                </div>
                
                {if !empty($T_TESTS_INFO)}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'tests')} tabbertabdefault{/if}" title = "{$smarty.const._TESTS}">
                {foreach key = "test_id" item = "test_info" from = $T_TESTS_INFO}
                    {if !$test_info.general.scorm}
					<table class = "statisticsTools">
                        <tr><td>
                                <a href = "javascript:void(0)" onclick = "toggleVisibility(document.getElementById('tinfo{$test_id}'),this)">
									<img src = "images/others/blank.gif"  class = "plus" title = "{$smarty.const._SHOWHIDE}" alt = "{$smarty.const._SHOWHIDE}"/>
									{$smarty.const._TEST}: {$test_info.general.name}</a>
                            </td>                                            
                            <td id = "right">
                                <a href = "display_chart.php?id=2&lesson_id={$T_LESSON_ID}&test_id={$test_info.general.id}" onclick = "eF_js_showDivPopup('{$smarty.const._QUESTIONSKIND}', 2)" target = "POPUP_FRAME">
                                	{$smarty.const._QUESTIONSKIND}: <img src = "images/16x16/pie-chart.png" alt = "{$smarty.const._QUESTIONSKIND}" title = "{$smarty.const._QUESTIONSKIND}"/></a> 
                            </td>                       
                    </table>   
                    <table class = "statisticsSubInfo" id = "tinfo{$test_id}" style = "display:none">
                        <tr>
                            <td class = "topTitle leftAlign">{$smarty.const._TESTINFO}</td>
                            <td>&nbsp;</td>
                            <td class = "topTitle leftAlign">{$smarty.const._QUESTIONINFO}</td>
                        </tr>
                        <tr><td>
                                <table>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TESTDURATION}:</td><td>{if $test_info.general.duration_str.hours}{$test_info.general.duration_str.hours}{$smarty.const._HOURSSHORTHAND} {/if}{if $test_info.general.duration_str.minutes}{$test_info.general.duration_str.minutes}{$smarty.const._MINUTESSHORTHAND} {/if}{if $test_info.general.duration_str.seconds}{$test_info.general.duration_str.seconds}{$smarty.const._SECONDSSHORTHAND}{/if}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._REDOABLE}:</td><td>{$test_info.general.redoable_str}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._ONEBYONE}:</td><td>{$test_info.general.onebyone_str}</td></tr>
                                    <tr class = "{cycle name = 'test_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._CREATED}:</td><td>#filter:timestamp-{$test_info.general.timestamp}#</td></tr>
                                </table>
                            </td>
                            <td>&nbsp;</td>
                            <td>
                                <table>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TOTALQUESTIONS}:</td><td>{$test_info.questions.total}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DEVELOPMENT}:</td><td>{$test_info.questions.raw_text}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MULTIPLEONE}:</td><td>{$test_info.questions.multiple_one}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MULTIPLEMANY}:</td><td>{$test_info.questions.multiple_many}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._TRUEFALSE}:</td><td>{$test_info.questions.true_false}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MATCH}:</td><td>{$test_info.questions.match}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._EMPTYSPACES}:</td><td>{$test_info.questions.empty_spaces}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._LOWDIFFICULTY}:</td><td>{$test_info.questions.low}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._MEDIUMDIFFICULTY}:</td><td>{$test_info.questions.medium}</td></tr>
                                    <tr class = "{cycle name = 'question_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._HIGHDIFFICULTY}:</td><td>{$test_info.questions.high}</td></tr> 
                                </table>
                            </td></tr>
                    {else}
					<table class = "statisticsTools">
                        <tr><td>{$smarty.const._TEST}: {$test_info.general.name} (SCORM)</td></tr>
					</table>                    
                    {/if}
                    <table class = "sortedTable" sortBy = "0">
                        <tr>
                            <td class = "topTitle">{$smarty.const._USERNAME}</td>
                            <td class = "topTitle">{$smarty.const._FULLNAME}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._MASTERYSCORE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                            <td class = "topTitle">{$smarty.const._DATE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._OPERATIONS}</td>							
                        </tr>
                        {foreach name = 'done_tests_list' key = "id" item = "done_test" from = $T_TESTS_INFO[$test_id].done}
                        <tr class = "{cycle name = $test_id values = "oddRowColor, evenRowColor"}">
                        	<td>#filter:user_login-{$done_test.users_LOGIN}#<a href = "{$smarty.server.PHP_SELF}.php?ctg=statistics&action=user_tab&user={$done_test.users_LOGIN}">{$done_test.users_LOGIN}</td>
                        	<td>{$done_test.surname} {$done_test.name.0}.</td>
                        	<td class = "progressCell">
                                <span style = "display:none">{$done_test.score}</span>
                                <span class = "progressNumber">#filter:score-{$done_test.score}#%</span>
                                <span class = "progressBar" style = "width:{$done_test.score}px;">&nbsp;</span>&nbsp;
                        	</td>
                        	<td class = "centerAlign">#filter:score-{$done_test.mastery_score}#%</td>
                        	<td class = "centerAlign">{if $done_test.status == 'failed'}<img src = "images/16x16/error.png" alt = "{$smarty.const._FAILED}" title = "{$smarty.const._FAILED}" style = "vertical-align:middle">{else}<img src = "images/16x16/checks.png" alt = "{$smarty.const._PASSED}" title = "{$smarty.const._PASSED}" style = "vertical-align:middle">{/if}</td>
                        	<td>#filter:timestamp_time-{$done_test.timestamp}#</td>
                        	<td class = "centerAlign">
                        		{if !$test_info.general.scorm}
                                <a href = "view_test.php?done_test_id={$done_test.id}" onclick = "eF_js_showDivPopup('{$smarty.const._VIEWTEST}', 2)" target = "POPUP_FRAME">
                                	<img src = "images/16x16/view.png" alt = "{$smarty.const._VIEWTEST}" title = "{$smarty.const._VIEWTEST}" /></a>
                            	{/if} 
                        	</td>
						</tr>                            
                        {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}
                    </table>
                    <br/>
                    {/foreach}
                    <script language = "JavaScript" type = "text/javascript">
                    {literal}
                    function resetTest(el, id) {
                    	Element.extend(el);
                    	url = 'view_test.php?done_test_id='+id+'&reset=1';
                        el.down().src = 'images/others/progress1.gif';
                        new Ajax.Request(url, {
                                method:'get',
                                asynchronous:true,
                                encoding: 'UTF-8',
                                onFailure: function (transport) {
                                    el.down().writeAttribute({src:'images/16x16/delete.png', title:transport.responseText}).hide();
                                    new Effect.Appear(el.down().identify());
                                    window.setTimeout('Effect.Fade("'+el.down().identify()+'")', 10000);
                                },
                                onSuccess: function (transport) {
                                	new Effect.Fade(el.up().up());
                                }
                        });                    	
                    }
                    {/literal}
                    </script>
                </div>
                {/if}
                
                {if !empty($T_QUESTIONS_INFORMATION)}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'questions')} tabbertabdefault{/if}" title = "{$smarty.const._QUESTIONS}">
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle leftAlign nowrap">{$smarty.const._QUESTIONTEXT}</td>                            	
                            <td class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._DIFFICULTY}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._TIMESDONE}</td>
                            <td class = "topTitle centerAlign nowrap">{$smarty.const._AVERAGESCORE}</td>
                        </tr>
                        {foreach name = 'questions_list' item = "question" key = "id" from = $T_QUESTIONS_INFORMATION}
                        <tr class = "{cycle values = "oddRowColor,evenRowColor"}">
                            <td>{$question.text}</td>
                            <td class = "centerAlign">
                                {if $question.type == 'match'}             <img src = "images/16x16/component.png"      title = "{$smarty.const._MATCH}"        alt = "{$smarty.const._MATCH}" />
                                {elseif $question.type == 'raw_text'}      <img src = "images/16x16/pens.png"           title = "{$smarty.const._RAWTEXT}"      alt = "{$smarty.const._RAWTEXT}" />
                                {elseif $question.type == 'multiple_one'}  <img src = "images/16x16/branch_element.png" title = "{$smarty.const._MULTIPLEONE}"  alt = "{$smarty.const._MULTIPLEONE}" />
                                {elseif $question.type == 'multiple_many'} <img src = "images/16x16/branch.png"         title = "{$smarty.const._MULTIPLEMANY}" alt = "{$smarty.const._MULTIPLEMANY}" />
                                {elseif $question.type == 'true_false'}    <img src = "images/16x16/yinyang.png"        title = "{$smarty.const._TRUEFALSE}"    alt = "{$smarty.const._TRUEFALSE}" />
                                {elseif $question.type == 'empty_spaces'}  <img src = "images/16x16/dot-chart.png"      title = "{$smarty.const._EMPTYSPACES}"  alt = "{$smarty.const._EMPTYSPACES}" />
                                {/if}
                            </td>
                            <td class = "centerAlign">
                                {if $question.difficulty     == 'low'}        <img src = "images/16x16/flag_green.png" title = "{$smarty.const._LOW}"    alt = "{$smarty.const._LOW}" />
                                {elseif $question.difficulty == 'medium'}     <img src = "images/16x16/flag_blue.png"  title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                                {elseif $question.difficulty == 'high'}       <img src = "images/16x16/flag_red.png"   title = "{$smarty.const._HIGH}"   alt = "{$smarty.const._HIGH}" />
                                {/if}
                            </td>
                            <td class = "centerAlign">{$question.times_done}</td>
                            <td class = "centerAlign">#filter:score-{$question.avg_score}#%</td>
                        </tr>
                        {/foreach}
                    </table>
                </div>
                {/if}
                
                {if !empty($T_PROJECTS_INFORMATION)}
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'projects')} tabbertabdefault{/if}" title = "{$smarty.const._PROJECTS}">
                    {foreach key = "project_id" item = "project_info" from = $T_PROJECTS_INFORMATION}
					<table class = "statisticsTools">
						<tr><td>                                        
                                <a href = "javascript:void(0)" onclick = "toggleVisibility(document.getElementById('projects_info{$project_id}'),this)">
									<img src = "images/others/blank.gif"  class = "plus" title = "{$smarty.const._SHOWHIDE}" alt = "{$smarty.const._SHOWHIDE}"/>{$smarty.const._PROJECT}: {$project_info.general.title}</a>
                            </td>                     
                        </tr>
					</table>
							
                    <table class = "statisticsSubInfo" id = "projects_info{$project_id}" style = "display:none;">                            
                        <tr><td>
                                <table>                                        
                                    <tr><td class = "topTitle" colspan = "3">{$smarty.const._PROJECTINFO}</td></tr>
                                    <tr class = "{cycle name = 'project_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DESCRIPTION}:</td><td>{$project_info.general.data}</td></tr>
                                    <tr class = "{cycle name = 'project_info' values = 'oddRowColor, evenRowColor'}"><td>{$smarty.const._DEADLINE}:</td><td>#filter:timestamp_time-{$project_info.general.deadline}#</td></tr>                                               
                                </table>
                            </td></tr>                      
                    </table>
                    <table class = "sortedTable" sortBy = "0">
                        <tr>
                        	<td class = "topTitle">{$smarty.const._USERNAME}</td>
                        	<td class = "topTitle">{$smarty.const._NAME}</td>
                        	<td class = "topTitle">{$smarty.const._SURNAME}</td>
                        	<td class = "topTitle centerAlign">{$smarty.const._GRADE}</td>
                        	<td class = "topTitle">{$smarty.const._DATE}</td>
                        </tr>
                    	{foreach name = 'done_projects_list' key = "key" item = "info" from = $project_info.done}
                        <tr class = "{cycle name = 'done_tests' values = 'oddRowColor, evenRowColor'}">
                            <td>#filter:user_login-{$info.users_LOGIN}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&action=user_tab&user={$info.users_LOGIN}">{$info.users_LOGIN}</a></td>
                            <td>{$info.surname}</td>
                            <td>{$info.name}</td>
                            <td class = "progressCell">
                                <span style = "display:none">{$info.grade}</span>
                                <span class = "progressNumber">#filter:score-{$info.grade}#%</span>
                                <span class = "progressBar" style = "width:{$info.grade}px;">&nbsp;</span>&nbsp;&nbsp;
                            </td>
                            <td>#filter:timestamp_time-{$info.upload_timestamp}#</td>                                
                        </tr>
                    	{foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NOONEHASBEENASSIGNEDTHISPROJECT}</td></tr>                        
                    	{/foreach}
               		</table>     
                	<br/>            
                	{/foreach}
                </div>    
                {/if} 
                
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'overall')} tabbertabdefault{/if}" title = "{$smarty.const._MOREINFO}">
                    <table class = "statisticsGeneralInfo">
                        <tr class = "defaultRowHeight">
                            <td class = "topTitle" colspan = "3">{$smarty.const._GENERALLESSONINFO}</td>
                        </tr>
                        <tr class = "{cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._PRICE}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.price_string}</td>
                        </tr>
                        <tr class = "{cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._ACTIVENEUTRAL}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.active_string}</td>
                        </tr>
                        <tr class = "{cycle name = 'general_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._LANGUAGE}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.language}</td>
                        </tr>
                        <tr>
                            <td class = "topTitle leftAlign" colspan = "3">{$smarty.const._LESSONPARTICIPATIONINFO}</td>
                        </tr>
                        <tr class = "{cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._COMMENTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.comments}</td>
                        </tr>
                        <tr class = "{cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td  class = "labelCell">{$smarty.const._FORUMPOSTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.messages}</td>
                        </tr>
                        <tr class = "{cycle name = 'participation_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._CHATMESSAGES}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.chatmessages}</td>
                        </tr>
                        <tr>
                            <td class = "topTitle leftAlign" colspan = "3">{$smarty.const._LESSONCONTENTINFO}</td>
                        </tr>
                        <tr class = "{cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._THEORY}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.theory}</td>
                        </tr>
                        <tr class = "{cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._EXAMPLES}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.examples}</td>
                        </tr>
                        <tr class = "{cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._PROJECTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.projects}</td>
                        </tr>
                        <tr class = "{cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TESTS}:</td>
                            <td class = "elementCell">{$T_LESSON_INFO.tests}</td>
                        </tr>
                        <tr class = "{cycle name = 'content_lesson_info' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTAL}:</td>
                            <td>{math equation="x + y + z + k" x = $T_LESSON_INFO.theory y = $T_LESSON_INFO.examples z = $T_LESSON_INFO.projects k = $T_LESSON_INFO.tests}</td>
                        </tr>
                    </table>
                </div>
                
                {if ($T_BASIC_TYPE == 'administrator' || $T_ISPROFESSOR == true) }
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'traffic')} tabbertabdefault{/if}" title = "{$smarty.const._TRAFFIC}">
                    <form name = "period">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="to_"   time=$T_TO_TIMESTAMP   start_year="-2" end_year="+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $T_TO_TIMESTAMP   display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._ANALYTICLOG}:</td>
                        	<td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLog" {if ( isset($T_LESSON_LOG))} checked="true" {/if}></td></tr>                            
                        <tr><td colspan = "2">&nbsp;</td></tr>
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" value = "{$smarty.const._SHOW}" onclick = "document.location='{$smarty.session.s_type}.php?ctg=statistics&option=lesson&sel_lesson={$T_LESSON_ID}&tab=traffic&from_year='+document.period.from_Year.value+'&from_month='+document.period.from_Month.value+'&from_day='+document.period.from_Day.value+'&from_hour='+document.period.from_Hour.value+'&from_min='+document.period.from_Minute.value+'&to_year='+document.period.to_Year.value+'&to_month='+document.period.to_Month.value+'&to_day='+document.period.to_Day.value+'&to_hour='+document.period.to_Hour.value+'&to_min='+document.period.to_Minute.value+'&showlog='+document.period.showLog.checked"></td>
                        </tr>
	                </table>
	                </form>

                    {if $T_LESSON_TRAFFIC.total_access > 0}
                    <table class = "statisticsTools">
                        <tr><td id = "right"> 
                                <a href = "display_chart.php?id=8&lesson_id={$T_LESSON_ID}&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)", target = "POPUP_FRAME">
                                	{$smarty.const._ACCESSSTATISTICS}: <img src = "images/16x16/chart.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" /></a> 
                            </td>                            
                        </tr>
                    </table>
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._LESSONTRAFFIC}</td></tr> 
                        <tr class = "oddRowColor">
                            <td class = "labelCell">{$smarty.const._TOTALACCESS}:</td>
                            <td class = "elementCell">{$T_LESSON_TRAFFIC.total_access}</td>                                    
                        </tr> 
                        <tr class = "evenRowColor">
                            <td class = "labelCell">{$smarty.const._TOTALACCESSTIME}: </td>
                            <td class = "elementCell">
                                {if $T_LESSON_TRAFFIC.total_seconds}
                                	{if $T_LESSON_TRAFFIC.total_time.hours}{$T_LESSON_TRAFFIC.total_time.hours}h{/if}
                                	{if $T_LESSON_TRAFFIC.total_time.minutes}{$T_LESSON_TRAFFIC.total_time.minutes}'{/if}
                                	{if $T_LESSON_TRAFFIC.total_time.seconds}{$T_LESSON_TRAFFIC.total_time.seconds}''{/if}
                                {else}
                                	{$smarty.const._NOACCESSDATA}
                                {/if}
                            </td>
                        </tr>
                    </table>
                    {/if}      
                                 
					<br/>					
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ACCESSPERLESSON}</td>
                    {if $T_LESSON_TRAFFIC.total_seconds > 0 }                                 
                            <td id = "right">
                                <a href = "display_chart.php?id=5&lesson_id={$T_LESSON_ID}&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}" onclick = "eF_js_showDivPopup('{$smarty.const._MOSTACTIVEUSERS}', 2)", target = "POPUP_FRAME" style = "vertical-align:middle">
                                	{$smarty.const._MOSTACTIVEUSERS}: <img src = "images/16x16/chart.png" alt = "{$smarty.const._MOSTACTIVEUSERS}" title = "{$smarty.const._MOSTACTIVEUSERS}"/></a> 
                            </td>
                    {/if} 
                    	</tr>
                    </table>   
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                            <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
                        </tr>                                   
                        {foreach name = 'user_traffic_list' key = "login" item = "info" from = $T_LESSON_TRAFFIC.users}
                            <tr class = "{cycle name = 'usertraffic' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td>#filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&action=user_tab&user={$login}">{$login}</a></td>
                                <td class = "centerAlign">{$info.accesses}</td>
                                <td class = "centerAlign"><span style = "display:none">{$info.total_seconds}</span>
                                    {if $info.total_seconds}
                                    	{if $info.hours}{$info.hours}h{/if}
                                    	{if $info.minutes}{$info.minutes}'{/if}
                                    	{if $info.seconds}{$info.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if} 
                                </td>
                                <td class = "centerAlign">
                                    <a href = "display_chart.php?id=10&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}&login={$login}&lesson_id={$T_LESSON_ID}" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)" target = "POPUP_FRAME">
                                    	<img src = "images/16x16/chart.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}"/></a>     
                                </td>
                            </tr>          
                        {foreachelse}
                        	<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}                              
                    </table>                        
                    {if isset($T_LESSON_LOG)}
                    <br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
                    </table>   
                    <table>
                		<tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle">{$smarty.const._UNIT}</td>
                            <td class = "topTitle">{$smarty.const._ACTION}</td>
                            <td class = "topTitle">{$smarty.const._TIME}</td>    
                            <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
                        </tr>
	                    {foreach name = 'lesson_log_loop' key = "key" item = "info" from = $T_LESSON_LOG}
                        <tr class = "{cycle name = 'lesson_log_list' values = 'oddRowColor, evenRowColor'}">
                            <td>{$info.users_LOGIN}</td>           
                            <td>{$info.content_name}</td>
                            <td>{$T_ACTIONS[$info.action]}</td>           
                            <td>#filter:timestamp_time-{$info.timestamp}#</td>
                            <td>{$info.session_ip|eF_decodeIp}</td>           
                        </tr>
                        {foreachelse}
                        <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
    	                {/foreach}
                    </table>
					{/if}
                </div>
                {/if}                
            </div>
        {/if}      
    {/capture}
    {if $T_LESSON_NAME != ""}
    	{eF_template_printInnerTable title = "`$smarty.const._STATISTICSFORLESSON` <span class='innerTableName'>&quot;`$T_LESSON_NAME`&quot;</span>" data = $smarty.capture.lesson_statistics image = '32x32/chart.png'}
    {else}
    	{eF_template_printInnerTable title = "`$smarty.const._STATISTICSFORLESSON`" data = $smarty.capture.lesson_statistics image = '32x32/chart.png'}
    {/if}
   

{elseif $T_OPTION == 'course'}
    {capture name='course_statistics'}    
            <table class = "statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._CHOOSECOURSE}:</td>
                    <td class = "elementCell">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                        <img id = "busy" src = "images/12x12/hourglass.png" style="display:none;" alt="working ..."/> 
                        <div id = "autocomplete_choices" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                            {literal}
                                <script language = "JavaScript" type = "text/javascript">
                                    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ask_courses.php", {paramName: "preffix",afterUpdateElement : getSelectionId, indicator : "busy"}); 
                                    function getSelectionId(text, li) {
                                            document.location='{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=statistics&option=course&sel_course='+li.id;
                                    }                    
                                </script>
                            {/literal}          
                    </td>
                </tr>
                <tr><td></td>
                	<td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td></tr>     
            </table>       
            
    {if isset($T_COURSE_INFO)} 
            <br/><br/>
            <table class = "statisticsTools">
                <tr><td id = "right">
                        {$smarty.const._EXPORTSTATS}
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=course&sel_course={$T_COURSE_ID}&excel=1">
                            <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                        </a>
                        <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=course&sel_course={$T_COURSE_ID}&pdf=1">
                            <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                        </a>
                    </td></tr>
            </table>            
    		<br/>       
            <table class = "statisticsGeneralInfo">
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._NAME}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.name}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._DIRECTION}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.direction}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._LESSONS}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.lessons}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._STUDENTS}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.students}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._PROFESSORS}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.professors}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._PRICE}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.price}</td></tr>
                </tr>
                <tr class = "{cycle name = 'course_common_info' values = 'oddRowColor, evenRowColor'}">                
                    <td class = "labelCell">{$smarty.const._LANGUAGE}:</td>
                    <td class = "elementCell">{$T_COURSE_INFO.language}</td></tr>
                </tr>
			</table>
    {/if}
    
    {if $T_COURSE_USERS_STATS|@sizeof > 0 || $T_COURSE_PROFESSORS_STATS|@sizeof > 0 || $T_COURSE_LESSON_STATS|@sizeof > 0}
        <div class = "tabber">                        
        {if $T_COURSE_USERS_STATS|@sizeof > 0 || $T_COURSE_PROFESSORS_STATS|@sizeof > 0}
            <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'users')} tabbertabdefault{/if}" title = "{$smarty.const._USERS}">
			{if $T_COURSE_USERS_STATS|@sizeof > 0}
                <table class = "statisticsTools">
                    <tr><td>{$smarty.const._STUDENTS}:</td></tr>
                </table>   
                <table class = "sortedTable">
                    <tr>
                        <td class = "topTitle">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle">{$smarty.const._NAME}</td>
                        <td class = "topTitle">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle">{$smarty.const._COURSEROLE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._COMPLETED}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
                        <td class = "topTitle noSort centerAlign">{$smarty.const._OPTIONS}</td>
                    </tr>                                   
                {foreach name = 'student_list' key = "login" item = "info" from = $T_COURSE_USERS_STATS}   
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$info.active}deactivatedTableElement{/if}">
                        <td>#filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">{$login}</a></td>
                        <td>{$info.name}</td>
                        <td>{$info.surname}</td>
                        <td>{$T_ROLES[$info.role]}</td>
                        <td class = "centerAlign">
                            {if $info.completed}
                                <img src = "images/16x16/check.png" alt = "{$smarty.const._YES}" title = "{$smarty.const._YES}" border = "0" />
                            {else}
                                <img src = "images/16x16/forbidden.png" alt = "{$smarty.const._NO}" title = "{$smarty.const._NO}" border = "0" />
                            {/if}
                        </td>
                        <td class = "progressCell">
                            <span style = "display:none">{$info.score * 1000}</span>
                            <span class = "progressNumber">#filter:score-{$info.score}#%</span>
                            <span class = "progressBar" style = "width:{$info.score}px;">&nbsp;</span>&nbsp;
                        </td>
                        <td class = "centerAlign">
                            <a href = "user_course.php?user={$login}&course={$T_COURSE_ID}" onclick = "eF_js_showDivPopup('{$T_COURSE_NAME}', new Array('700px', '400px'))" target = "POPUP_FRAME"><img src="images/16x16/about.png" border="0px" title="{$smarty.const._MOREINFO}" alt="{$smarty.const._MOREINFO}"></a>
                        </td>
                    </tr>                   
                {/foreach}
        		</table>
	            <br/>
            {/if}      
			{if $T_COURSE_PROFESSORS_STATS|@sizeof > 0}
                <table class = "statisticsTools">
                    <tr><td>{$smarty.const._PROFESSORS}:</td></tr>
                </table>   
				<table class = "sortedTable">
                    <tr>
                        <td class = "topTitle">{$smarty.const._LOGIN}</td>
                        <td class = "topTitle">{$smarty.const._NAME}</td>
                        <td class = "topTitle">{$smarty.const._SURNAME}</td>
                        <td class = "topTitle">{$smarty.const._COURSEROLE}</td>
                    </tr>
                    {foreach name = 'professor_list' item = 'info' key = "login" from = $T_COURSE_PROFESSORS_STATS}   
                        <tr class = "{cycle name = 'cprofessor_list' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                            <td> #filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">{$login}</a></td>
                            <td>{$info.name}</td>
                            <td>{$info.surname}</td>
                            <td>{$T_ROLES[$info.role]}</td>
                        </tr>                   
                    {/foreach}
				</table>      
			{/if}
            </div>
        {/if}
        
        {if $T_COURSE_LESSON_STATS|@sizeof > 0}
            <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'lessons')} tabbertabdefault{/if}" title = "{$smarty.const._LESSONS}">
                <table class = "sortedTable">
                    <tr>
                        <td class = "topTitle">{$smarty.const._LESSON}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._CONTENT}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._TESTS}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._PROJECTS}</td>
                    </tr>
                {foreach name = 'lesson_list' key ="id" item = "info" from = $T_COURSE_LESSON_STATS}   
                    <tr class = "{cycle values = "oddRowColor, evenRowColor"} {if !$info.active}deactivatedTableElement{/if}" >
                        <td><a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=lesson&sel_lesson={$id}">{$info.name}</a></td>
                        <td class = "centerAlign">{$info.content}</td>
                        <td class = "centerAlign">{$info.tests}</td>
                        <td class = "centerAlign">{$info.projects}</td>
                    </tr>                   
                {/foreach}
				</table>      
			</div>
        {/if}
        </div>
    {/if}
    {/capture}
    
    {if $T_COURSE_NAME != ""}
		{eF_template_printInnerTable title = "`$smarty.const._REPORTSFORCOURSE` <span class='innerTableName'>&quot;`$T_COURSE_NAME`&quot;</span>" data = $smarty.capture.course_statistics image = '32x32/books.png'}     
    {else}
    	{eF_template_printInnerTable title = $smarty.const._COURSESTATISTICS data = $smarty.capture.course_statistics image = '32x32/books.png'}
	{/if}
    
    

{elseif $T_OPTION == 'test'}
    {capture name='test_statistics'}
            <table class = "statisticsSelectList">
                <tr><td class = "labelCell">{$smarty.const._CHOOSETEST}:</td>
                    <td class = "elementCell">
                        <input type = "text" id = "autocomplete" class = "autoCompleteTextBox"/>
                        <img id = "busy" src = "images/12x12/hourglass.png" style="display:none;" alt="working ..."/> 
                        <div id = "autocomplete_choices" class = "autocomplete"></div>&nbsp;&nbsp;&nbsp;
                            {literal}
                                <script language = "JavaScript" type = "text/javascript">
                                    new Ajax.Autocompleter("autocomplete", "autocomplete_choices", "ask_tests.php", {paramName: "preffix",afterUpdateElement : getSelectionId, indicator : "busy"}); 
                                    function getSelectionId(text, li) {
                                            document.location='{/literal}{$smarty.server.PHP_SELF}{literal}?ctg=statistics&option=test&sel_test='+li.id;
                                    }
                                </script>
                            {/literal}          
                    </td>
                </tr>
                <tr><td></td>
                	<td class = "infoCell">{$smarty.const._STARTTYPINGFORRELEVENTMATCHES}</td></tr>     
            </table>       

    {if isset($T_TEST_INFO)}
            
            <br/><br/>
            <table class = "statisticsTools">
                <tr><td id = "right">
                    {$smarty.const._EXPORTSTATS}
                    <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=test&sel_test={$smarty.get.sel_test}&excel=1">
                        <img src = "images/file_types/xls.png" title = "{$smarty.const._XLSFORMAT}" alt = "{$smarty.const._XLSFORMAT}" />
                    </a>
                    <a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=test&sel_test={$smarty.get.sel_test}&pdf=1">
                        <img src = "images/file_types/pdf.png" title = "{$smarty.const._PDFFORMAT}" alt = "{$smarty.const._PDFFORMAT}" />
                    </a>
                </td></tr>
	        </table>            
        	<br/>
        
        <table class = "statisticsGeneralInfo">
            <tr class = "{cycle name = 'test_common_info' values = 'oddRowColor, evenRowColor'}">                
                <td class = "labelCell">{$smarty.const._NAME}:</td>
                <td class = "elementCell">{$T_TEST_INFO.general.name}</td></tr>
            </tr>
            <tr class = "{cycle name = 'test_common_info' values = 'oddRowColor, evenRowColor'}">                
                <td class = "labelCell" >{$smarty.const._LESSON}:</td>
                <td class = "elementCell">{$T_TEST_INFO.general.lesson_name}</td></tr>
            </tr>
            <tr class = "{cycle name = 'test_common_info' values = 'oddRowColor, evenRowColor'}">                
                <td class = "labelCell" >{$smarty.const._TESTDURATION}:</td>
                <td class = "elementCell">
                	{if $T_TEST_INFO.general.duration}
                    	{if $T_TEST_INFO.general.duration_str.hours > 1}{$T_TEST_INFO.general.duration_str.hours} {$smarty.const._HOURS}
                    	{elseif $T_TEST_INFO.general.duration_str.hours == 1}{$T_TEST_INFO.general.duration_str.hours} {$smarty.const._HOUR}{/if}
                    	{if $T_TEST_INFO.general.duration_str.minutes > 1}{$T_TEST_INFO.general.duration_str.minutes} {$smarty.const._MINUTES}
                    	{elseif $T_TEST_INFO.general.duration_str.minutes == 1}{$T_TEST_INFO.general.duration_str.minutes} {$smarty.const._MINUTE}{/if}
                    	{if $T_TEST_INFO.general.duration_str.seconds > 1}{$T_TEST_INFO.general.duration_str.seconds} {$smarty.const._SECONDS}
                    	{elseif $T_TEST_INFO.general.duration_str.seconds == 1}{$T_TEST_INFO.general.duration_str.seconds} {$smarty.const._SECOND}{/if}
                    {else}
                    	{$smarty.const._UNLIMITED}
                    {/if}
                	</td></tr>
            </tr>
            <tr class = "{cycle name = 'test_common_info' values = 'oddRowColor, evenRowColor'}">                
                <td class = "labelCell" >{$smarty.const._QUESTIONS}:</td>
                <td class = "elementCell">{$T_TEST_INFO.questions.total}</td></tr>
            </tr>
        </table>                

        <div class = "tabber">                        
            <div class = "statisticsDiv tabbertab" title = "{$smarty.const._QUESTIONS}">
                <table class = "sortedTable">
                    <tr>
                        <td class = "topTitle">{$smarty.const._QUESTIONTEXT}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._QUESTIONTYPE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._DIFFICULTY}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._WEIGHT}</td>   
                        <td class = "topTitle centerAlign">{$smarty.const._TIMESDONE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._MINSCORE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._MAXSCORE}</td>
                    </tr>
                    {foreach name = 'question_list' key = "key" item = "question" from = $T_TEST_QUESTIONS}
                        <tr class = "{cycle name = 'test_questions' values = 'oddRowColor, evenRowColor'}">                                                
                            <td>{$question->question.plain_text}</td>
                            <td class = "centerAlign">
                            	{assign var = "qtype" value = $question->question.type}
                            	<span style = "display:none">{$question->question.type}</span>
                            	<img src = "{$question->question.type_icon}" title = "{$T_TEST_QUESTIONS_TRANSLATIONS[$qtype]}" alt = "{$T_TEST_QUESTIONS_TRANSLATIONS[$qtype]}"></td>
                            <td class = "centerAlign">
                                <span style = "display:none">{$question->question.difficulty}</span>
                                {if $question->question.difficulty == 'low'}        <img src = "images/16x16/flag_green.png" title = "{$smarty.const._LOW}"    alt = "{$smarty.const._LOW}" />
                                {elseif $question->question.difficulty == 'medium'} <img src = "images/16x16/flag_blue.png"  title = "{$smarty.const._MEDIUM}" alt = "{$smarty.const._MEDIUM}" />
                                {elseif $question->question.difficulty == 'high'}   <img src = "images/16x16/flag_red.png"   title = "{$smarty.const._HIGH}"   alt = "{$smarty.const._HIGH}" />
                                {/if}
                            </td>
                            <td class = "centerAlign">{$question->question.weight}</td>
                            <td class = "centerAlign">{$T_TEST_QUESTIONS_STATS[$key].times_done}</td>
                            <td class = "centerAlign">#filter:score-{$T_TEST_QUESTIONS_STATS[$key].avg_score}#%</td>
                            <td class = "centerAlign">#filter:score-{$T_TEST_QUESTIONS_STATS[$key].min_score}#%</td>
                            <td class = "centerAlign">#filter:score-{$T_TEST_QUESTIONS_STATS[$key].max_score}#%</td>
                        </tr>                                                
                    {foreachelse}
                            <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                    {/foreach}      
                </table>
            </div>
            <div class = "statisticsDiv tabbertab {if $smarty.get.tab == 'users'}tabbertabdefault{/if}" title = "{$smarty.const._USERS}">
                <table class = "sortedTable">
                    <tr>
                        <td class = "topTitle">{$smarty.const._USER}</td>
                        <td class = "topTitle">{$smarty.const._FULLNAME}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._TIMESDONE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._AVERAGESCORE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._MINSCORE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._MAXSCORE}</td>
					</tr>
				{foreach name = 'question_list' key = "login" item = "test" from = $T_TEST_STATS}
					<tr class = "{cycle name = 'test_questions' values = 'oddRowColor, evenRowColor'}">
						<td>{$login}</td>
						<td>{$T_USER_NAMES[$login].surname} {$T_USER_NAMES[$login].name.0}.</td>
						<td class = "centerAlign">{$test.times_done}</td>
						<td class = "centerAlign">#filter:score-{$test.average_score}#%</td>
						<td class = "centerAlign">#filter:score-{$test.min_score}#%</td>
						<td class = "centerAlign">#filter:score-{$test.max_score}#%</td>
					</tr>
                {foreachelse}
                    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}      
				</table>
				<table class = "statisticsTable" style = "margin-top:20px;">
					<tr><td>            	
                            <a href = "javascript:void(0)" onclick = "toggleVisibility($('details'), Element.extend(this).down())">
            					<img src = "images/others/blank.gif"  class = "plus" title = "{$smarty.const._SHOWHIDE}" alt = "{$smarty.const._SHOWHIDE}"/>
            					{$smarty.const._SHOWDETAILS}</a>
					</td></tr>
				</table>
				<div id = "details" style = "display:none">
			{foreach name = 'question_list' key = "login" item = "test" from = $T_TEST_STATS}
                <table class = "sortedTable" style = "margin-bottom:10px">
                    <tr>
                        <td class = "topTitle">{$smarty.const._USER}</td>
                        <td class = "topTitle">{$smarty.const._FULLNAME}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._DATE}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._STATUS}</td>
                        <td class = "topTitle centerAlign">{$smarty.const._SCORE}</td>
					</tr>
				{foreach name = 'question_list' key = "id" item = "completed_test" from = $test}
					{if $id|@is_numeric}
					<tr class = "{cycle name = 'test_questions' values = 'oddRowColor, evenRowColor'}">
						<td>{$completed_test.users_LOGIN}</td>
						<td>{$T_USER_NAMES[$completed_test.users_LOGIN].surname} {$T_USER_NAMES[$completed_test.users_LOGIN].name.0}.</td>
						<td class = "centerAlign">#filter:timestamp_time-{$completed_test.timestamp}#</td>
						<td class = "centerAlign">{$completed_test.status}</td>
						<td class = "centerAlign">#filter:score-{$T_TEST_STATS.$login.scores[$id]}#%</td>
					</tr>
					{/if}
                {foreachelse}
                    <tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                {/foreach}      
				</table>            	
			{/foreach}
				</div>
            </div>
        </div>
    {/if}
        
    {/capture}
    {if $T_TEST_NAME != ""}
    	{eF_template_printInnerTable title="`$smarty.const._REPORTSFORTEST` <span class='innerTableName'>&quot;`$T_TEST_NAME`&quot;</span>" data=$smarty.capture.test_statistics image='32x32/edit.png'} 
	{else}
		{eF_template_printInnerTable title=$smarty.const._TESTSTATISTICS data=$smarty.capture.test_statistics image='32x32/edit.png'} 
	{/if}  

{elseif $T_OPTION == 'system'}
        {capture name='display_system_statistics'}
             <div class = "tabber">   
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'system_traffic')} tabbertabdefault{/if}" title = "{$smarty.const._TRAFFIC}">
                    <form name = "systemperiod">
                    <table class = "statisticsSelectDate">
                        <tr><td class = "labelCell">{$smarty.const._FROM}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="from_" time=$T_FROM_TIMESTAMP start_year="-2" end_year="+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="from_" time = $T_FROM_TIMESTAMP display_seconds = false}</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._TO}:&nbsp;</td>
                            <td class = "elementCell">{eF_template_html_select_date prefix="to_"   time=$T_TO_TIMESTAMP   start_year="-2" end_year="+2" field_order = 'DMY'} {$smarty.const._TIME}: {html_select_time prefix="to_"   time = $T_TO_TIMESTAMP   display_seconds = false}</td></tr>
                        <tr><td></td>
                        	<td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLog" {if ( isset($T_SYSTEM_LOG))} "checked" {/if}>{$smarty.const._SHOWANALYTICLOG}</td></tr>                            
                        <tr><td></td>
                        	<td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showUsers" {if ( isset($smarty.get.showusers))} "checked"{/if}>{$smarty.const._SHOWALLUSERS}</td></tr>                            
                        <tr><td></td>
                        	<td class = "elementCell"><input class = "inputCheckbox" type = "checkbox" id = "showLessons" {if ( isset($smarty.get.showlessons))} "checked"{/if}>{$smarty.const._SHOWALLLESSONS}</td></tr>                            
                        <tr><td></td>
                            <td class = "elementCell"><input type = "button" value = "{$smarty.const._SHOW}" onclick = "document.location='administrator.php?ctg=statistics&option=system&tab=system_traffic&from_year='+document.systemperiod.from_Year.value+'&from_month='+document.systemperiod.from_Month.value+'&from_day='+document.systemperiod.from_Day.value+'&from_hour='+document.systemperiod.from_Hour.value+'&from_min='+document.systemperiod.from_Minute.value+'&to_year='+document.systemperiod.to_Year.value+'&to_month='+document.systemperiod.to_Month.value+'&to_day='+document.systemperiod.to_Day.value+'&to_hour='+document.systemperiod.to_Hour.value+'&to_min='+document.systemperiod.to_Minute.value+'&showlog='+document.systemperiod.showLog.checked+'&showusers='+document.systemperiod.showUsers.checked+'&showlessons='+document.systemperiod.showLessons.checked"></td>
                        </tr>
                    </table>
                    </form> 
                    <table class = "statisticsTools">
                       <tr><td id = "right">
                                <a href = "display_chart.php?id=9&from={$T_FROM_TIMESTAMP}&to={$T_TO_TIMESTAMP}" style = "vertical-align:middle" onclick = "eF_js_showDivPopup('{$smarty.const._ACCESSSTATISTICS}', 2)", target = "POPUP_FRAME"> 
	                                {$smarty.const._ACCESSSTATISTICS}:
	                                <img src = "images/16x16/chart.png" alt = "{$smarty.const._ACCESSSTATISTICS}" title = "{$smarty.const._ACCESSSTATISTICS}" /></a>
                            </td></tr>
                    </table>
                    <table class = "statisticsGeneralInfo">
                        <tr><td class = "topTitle" colspan = "2">{$smarty.const._TOTALSTATISTICS}</td></tr>
                        <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALLOGINS}:</td>
                            <td class = "elementCell">{$T_TOTAL_USER_ACCESSES}</td>
                        </tr>
                        <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'}">
                            <td class = "labelCell">{$smarty.const._TOTALACCESSTIME}:</td>
                            <td class = "elementCell">
                                {if $T_TOTAL_USER_TIME}
                                	{if $T_TOTAL_USER_TIME.hours}{$T_TOTAL_USER_TIME.hours}h{/if}
                                	{if $T_TOTAL_USER_TIME.minutes}{$T_TOTAL_USER_TIME.minutes}'{/if}
                                	{if $T_TOTAL_USER_TIME.seconds}{$T_TOTAL_USER_TIME.seconds}''{/if}
                                {else}
                                	{$smarty.const._NODATAFOUND}
                                {/if}                                            	                                             
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <table class = "statisticsTools">
                    	<tr><td>{if $smarty.get.showusers}{$smarty.const._USERSACTIVITY}{else}{$smarty.const._MOSTACTIVEUSERS}{/if}</td>   
                            <td id = "right">
                                <a href = "display_chart.php?id=6&logins={$T_USER_TIMES.logins}&seconds={$T_USER_TIMES.times}" onclick = "eF_js_showDivPopup('{$smarty.const._MOSTACTIVEUSERS}', 2)", target = "POPUP_FRAME"> 
                                    {$smarty.const._MOSTACTIVEUSERS}:
                                    <img src = "images/16x16/chart.png" alt = "{$smarty.const._MOSTACTIVEUSERS}" title = "{$smarty.const._MOSTACTIVEUSERS}" /></a>
                            </td></tr>                           
                    </table>
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle">{$smarty.const._NAME}</td>
                            <td class = "topTitle">{$smarty.const._SURNAME}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                         </tr>
                        {foreach name='active_users'  key = "login" item = "info" from=$T_ACTIVE_USERS}
                            <tr class = "{cycle name = 'active_users' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td>#filter:user_login-{$login}#<a href = "{$T_BASIC_TYPE}.php?ctg=statistics&option=user&sel_user={$login}">{$login}</a>
                                </td>
                                <td>{$info.name}</td>
                                <td>{$info.surname}</td>
                                <td class = "centerAlign">{$info.accesses}</td>
                                <td class = "centerAlign">
                                	<span style = "display:none">{$info.seconds}</span>
                                    {if $info.seconds}
                                    	{if $info.time.hours}{$info.time.hours}h {/if}
                                    	{if $info.time.minutes}{$info.time.minutes}'{/if}
                                    	{if $info.time.seconds}{$info.time.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if}                                            	 
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                    <br/>
                    <table class = "statisticsTools">
                        <tr><td>{if $smarty.get.showlessons}{$smarty.const._LESSONSACTIVITY}{else}{$smarty.const._MOSTACTIVELESSONS}{/if}</td></tr>
					</table>
                    <table class = "sortedTable">
                        <tr>
                            <td class = "topTitle">{$smarty.const._LESSON}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._ACCESSNUMBER}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._TOTALACCESSTIME}</td>
                         </tr>
                        {foreach name='active_lessons' key = "id" item = "info" from=$T_ACTIVE_LESSONS}
                            <tr class = "{cycle name = 'active_lessons' values = 'oddRowColor, evenRowColor'} {if !$info.active}deactivatedTableElement{/if}">
                                <td>{$info.name}</td>
                                <td class = "centerAlign">{$info.accesses}</td>
                                <td class = "centerAlign">
                                	<span style = "display:none">{$info.seconds}</span>
                                    {if $info.seconds}
                                    	{if $info.time.hours}{$info.time.hours}h {/if}
                                    	{if $info.time.minutes}{$info.time.minutes}' {/if}
                                    	{if $info.time.seconds}{$info.time.seconds}''{/if}
                                    {else}
                                    	{$smarty.const._NOACCESSDATA}
                                    {/if}                                            	 
                                </td>
                            </tr>
                        {foreachelse}
                        	<tr class = "oddRowColor defaultRowHeight"><td colspan = "100%" class = "centerAlign emptyCategory">{$smarty.const._NODATAFOUND}</td></tr>
                        {/foreach}
                    </table>
				{if isset($T_SYSTEM_LOG)}
					<br/>
                    <table class = "statisticsTools">
                        <tr><td>{$smarty.const._ANALYTICLOG}</td></tr>
                    </table>   
                    <table>
                		<tr>
                            <td class = "topTitle">{$smarty.const._LOGIN}</td>
                            <td class = "topTitle">{$smarty.const._LESSON}</td>
                            <td class = "topTitle">{$smarty.const._UNIT}</td>
                            <td class = "topTitle">{$smarty.const._ACTION}</td>
                            <td class = "topTitle">{$smarty.const._TIME}</td>    
                            <td class = "topTitle">{$smarty.const._IPADDRESS}</td>
                        </tr>
                    {foreach name = 'lesson_log_loop' key = "key" item = "info" from = $T_SYSTEM_LOG}
                        <tr class = "{cycle name = 'lesson_log_list' values = 'oddRowColor, evenRowColor'}">
                            <td>{$info.users_LOGIN}</td>           
                            <td>{$info.lesson_name}</td>
                            <td>{$info.content_name}</td>
                            <td>{$T_ACTIONS[$info.action]}</td>           
                            <td>#filter:timestamp_time-{$info.timestamp}#</td>
                            <td>{$info.session_ip|eF_decodeIp}</td>           
                        </tr>
                    {/foreach}
                    </table>
				{/if}
                </div>
                
                <div class = "statisticsDiv tabbertab {if (isset($smarty.get.tab) &&  $smarty.get.tab == 'user_types')} tabbertabdefault{/if}" title = "{$smarty.const._USERTYPES}">
                    <table class = "statisticsTools">
                        <tr><td id = "right">                                
                                <a href = "display_chart.php?id=4" onclick = "eF_js_showDivPopup('{$smarty.const._USERSKIND}', 2)" target = "POPUP_FRAME">
									{$smarty.const._USERSKIND}:
									<img src = "images/16x16/chart.png" alt = "{$smarty.const._USERSKIND}" title = "{$smarty.const._USERSKIND}"/></a> 
                            </td></tr>
                    </table>
                    <table>
                    	<tr>
                            <td class = "topTitle">{$smarty.const._USERTYPE}</td>
                            <td class = "topTitle centerAlign">{$smarty.const._OVERALL}</tD>
                    	</tr>                                        
                    {foreach name = 'user_types' key = 'key' item = 'usertype' from = $T_USER_TYPES}
                        <tr class = "{cycle name = 'userkinds_info' values = 'oddRowColor, evenRowColor'}">
                            <td>
                            	{if $usertype.user_type == 'administrator'}{$smarty.const._ADMINISTRATOR}
                            	{elseif $usertype.user_type == 'professor'}{$smarty.const._PROFESSOR}
                            	{elseif $usertype.user_type == 'student'}{$smarty.const._STUDENT}
                            	{/if}
                            </td>
                            <td class = "centerAlign">{$usertype.num}</td>
                        </tr>
                    {/foreach}
                    </table>
                </div>
            </div>
        {/capture}
        {eF_template_printInnerTable title = $smarty.const._SYSTEMSTATISTICS data = $smarty.capture.display_system_statistics image = '32x32/chart.png'}   
{/if}