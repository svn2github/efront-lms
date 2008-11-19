{include file = "includes/header.tpl"}
{if $T_MESSAGE}
        {eF_template_printMessage message = $T_MESSAGE type = $T_MESSAGE_TYPE}    
{/if}
<table>
    <tr>
        <td colspan="100%">
            <table align = "left" width="100%" valign = "top">
            <tr>
                <td width = "100%" valign = "top">
                    <table width="100%" style = "text-align:left"> 
                        <tr>                            
                            <td colspan = "2" align = "left" width="100%" class="topSubtitle"><b>{$smarty.const._GENERICLESSONINFO}</b></td>
                        </tr>
                        <tr>                            
                            <td>{$smarty.const._TIMEINLESSON}:</td>
                            <td width="80%" align="left">{$T_USER_TIMES.hours}h {$T_USER_TIMES.minutes}&#039; {$T_USER_TIMES.seconds}&#039;&#039;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width = "100%" valign = "top">
                    <table width = "100%" border = "0px">
                         <tr>                            
                            <td colspan = "2" align = "left" width="100%" class="topSubtitle"><b>{$smarty.const._CONTENT}</b></td>
                        </tr>
                        <tr>                            
                            <td>{$smarty.const._CONTENT}:</td>
                            <td class = "progressCell" style = "vertical-align:top;width:80%">
                                <span class = "progressNumber">#filter:score-{$T_USER_STATUS.overall_progress}#%</span>
                                <span class = "progressBar" style = "width:{$T_USER_STATUS.overall_progress}px;">&nbsp;</span>                              
                            </td>
                        </tr>                                          
                    </table>
                </td>
            </tr>
            <tr>
                <td width = "100%" valign = "top">
                    <table width = "100%" border = "0px">
                        <tr>
                            <td colspan = "100%" align = "left" width="100%" class="topSubtitle"><b>{$smarty.const._TESTS}</b></td>
                        </tr>
                        {if $T_USER_DONE_TESTS}                                                            
                            <tr>                            
                                <td>{$smarty.const._USERAVERAGESCOREFORTESTS}:</td>
                                <td class = "progressCell" style = "vertical-align:top;width:80%">                       
                                    <span class = "progressNumber">#filter:score-{$T_USER_STATUS.tests_avg_score}#%</span>
                                    <span class = "progressBar" style = "width:{$T_USER_STATUS.tests_avg_score}px;">&nbsp;</span>
                                </td>                  
                            </tr>
                            {foreach name = 'done_tests_list' key = "test_id" item = "test" from =  $T_USER_DONE_TESTS}
                                <tr>                                    
                                    <td {if !$test.active}class = "deactivatedElement"{/if}>{$test.name}:</td>
                                    <td class = "progressCell" style = "vertical-align:top;width:80%">
                                        <span class = "progressNumber">#filter:score-{$test.score}#%</span>
                                        <span class = "progressBar" style = "width:{$test.score}px;">&nbsp;</span>
                                        <span style = "margin-left:120px">(#filter:timestamp_time-{$test.timestamp}#)</span>
                                        {if $test.active}<a href = "view_test.php?test_id={$test.tests_ID}&user={$smarty.get.user}"><img src="images/12x12/zoom_in.png" title="{$smarty.const._VIEWTEST}" alt="{$smarty.const._VIEWTEST}" border="0px"/></a>{else}<span class = "emptyCategory">({$smarty.const._INACTIVE})</span>{/if}
                                    </td>
                                </tr>    
                            {/foreach}                                   
                        {else}
                            <tr>                                        
                                <td style = "text-align:left" class = "emptyCategory" width="100%">{$smarty.const._THEUSERHASNOTDONEANYTESTSINTHISLESSON}</td>
                            </tr>
                        {/if}                                     
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width = "100%" valign = "top">
                    <table width = "100%" border = "0px">
                        <tr>                            
                            <td colspan = "100%" align = "left" width="100%" class="topSubtitle"><b>{$smarty.const._PROJECTS}</b></td>
                        </tr>
                        {if !empty($T_USER_STATUS.assigned_projects)}
                        <tr>                            
                            <td>{$smarty.const._PROJECTAVERAGESCOREFORLESSON}:</td> 
                            <td class = "progressCell" style = "vertical-align:top;width:80%">                       
                                <span class = "progressNumber">{$T_USER_STATUS.avg_grade_projects}%</span>
                                <span class = "progressBar" style = "width:{$T_USER_STATUS.avg_grade_projects}px;">&nbsp;</span>
                           </td>  
                        </tr>
                        {/if}
                        {if ($T_USER_STATUS.assigned_projects|@sizeof)}
                            {foreach name = 'done_projects_list' item = project from = $T_USER_STATUS.assigned_projects}
                                <tr>                                
                                    <td>{$project.title}:</td>
                                    {if $project.grade != ''}
                                        <td class = "progressCell" style = "vertical-align:top;width:80%">
                                            <span class = "progressNumber">{$project.grade}%</span>
                                            <span class = "progressBar" style = "width:{$project.grade}px;">&nbsp;</span>
                                            {if $project.upload_timestamp > 0}
                                                <span style = "margin-left:120px">{$smarty.const._FILEUPLOADEDON}: #filter:timestamp_time-{$project.upload_timestamp}#</span>                                                        
                                            {else}
                                                <span style = "margin-left:120px">{$smarty.const._NOFILEUPLOADED}</span>
                                            {/if}
                                        </td>
                                    {else}
                                        <td width="80%" align="left">----</td>
                                    {/if}
                                </tr>
                            {/foreach}                                                
                        {else}
                            <tr>                                    
                                <td style = "text-align:left" class = "emptyCategory" width="100%">{$smarty.const._THEUSERHASNOTBEENASSIGNEDANYPROJECT}</td>
                            </tr>
                        {/if}        
                    </table>
                </td>
            </tr>            
        </table>
        </td>
    </tr>
</table>
{include file = "includes/closing.tpl"}