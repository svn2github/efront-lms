{*smarty template for stress_tool.php*}

{include file = "includes/header.tpl"}

{if $T_MESSAGE}
                <tr class = "messageRow">
                    <td colspan = "2">{eF_template_printMessage message=$T_MESSAGE type=$T_MESSAGE_TYPE}</td>        {*Display Message, if any*}
                </tr>
{/if}

<table width = "100%" style = "text-align:center;font-size:14px;font-weight:bold;height:2em;"><tr><td>EFRONT STRESS TOOL</td></tr></table>

{$T_STRESS_FORM.javascript}
<form {$T_STRESS_FORM.attributes}>
    {$T_STRESS_FORM.hidden}
<table align = "center" style = "border:1px solid black">
    <tr><td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Users</td></tr>
            <tr><td>Number of users:</td><td>{$T_STRESS_FORM.num_users.html}</td></tr>
            <tr><td>Type of users:</td><td>{$T_STRESS_FORM.users_type.html}</td></tr>
            <tr><td>Users language:</td><td>{$T_STRESS_FORM.users_language.html}</td></tr>
            <tr><td>Activate users:</td><td>{$T_STRESS_FORM.activate_users.html}</td></tr>
            <tr><td>Insert log entries:</td><td>{$T_STRESS_FORM.insert_users_log.html}</td></tr>
            <tr><td>Enroll to lesson:</td><td>{$T_STRESS_FORM.enroll_lesson.html}</td></tr>
            <tr><td class = "emptyCategory">Do tests:</td><td></td></tr>
            <tr><td class = "emptyCategory">Post comments:</td><td></td></tr>
            <tr><td class = "emptyCategory">Post forum messages:</td><td></td></tr>
            <tr><td class = "emptyCategory">Add bookmarks:</td><td></td></tr>
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_users.html}</td></tr>
        </table>

    </td><td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Directions</td></tr>
            <tr><td>Number of directions:</td><td>{$T_STRESS_FORM.num_directions.html}</td></tr>
            <tr><td>Parent direction:</td><td>{$T_STRESS_FORM.parent_direction.html}</td></tr>
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_directions.html}</td></tr>
        </table>

    </td><td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Lessons</td></tr>
            <tr><td>Number of lessons:</td><td>{$T_STRESS_FORM.num_lessons.html}</td></tr>
            <tr><td>Language:</td><td>{$T_STRESS_FORM.lesson_language.html}</td></tr>
            <tr><td>Direction:</td><td>{$T_STRESS_FORM.lesson_direction.html}</td></tr>
            <tr><td>Price:</td><td>{$T_STRESS_FORM.lesson_price.html}</td></tr>
            {*<tr><td>Insert log entries:</td><td>{$T_STRESS_FORM.insert_lessons_log.html}</td></tr>*}
            <tr><td class = "emptyCategory">Import lesson:</td><td></td></tr>
            <tr><td class = "emptyCategory">Add random units:</td><td></td></tr>
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_lessons.html}</td></tr>
        </table>

    </td></tr>
    <tr><td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Content</td></tr>
            <tr><td>Number of units:</td><td>{$T_STRESS_FORM.num_units.html}</td></tr>
            <tr><td>lesson:</td><td>{$T_STRESS_FORM.unit_lesson.html}*</td></tr>
            <tr><td>type:</td><td>{$T_STRESS_FORM.unit_type.html}</td></tr>
            <tr><td>Copy content from unit:</td><td>{$T_STRESS_FORM.copy_unit.html}</td></tr>
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_units.html}</td></tr>
            <tr><td colspan = "100%" class = "infoCell">*The lesson must have at least 1 unit already!</td></tr>
        </table>

    </td><td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Logs</td></tr>
            <tr><td>Number of log entries:</td><td>{$T_STRESS_FORM.num_logs.html}</td></tr>
            <tr><td>For user:</td><td>{$T_STRESS_FORM.logs_user.html}</td></tr>
            {*<tr><td>Add a specific entry:</td><td>{$T_STRESS_FORM.log_entry.html}</td></tr>*}
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_logs.html}</td></tr>
        </table>

    </td>
{*    <td style = "vertical-align:top">
    
        <table  width = "100%">
            <tr><td class = "topTitle" colspan = "100%">Forum Messages</td></tr>
            <tr><td>Number of topics:</td><td>{$T_STRESS_FORM.num_topics.html}</td></tr>
            <tr><td>Number of messages/topic:</td><td>{$T_STRESS_FORM.num_messages.html}</td></tr>
            <tr><td>Use a specific message title:</td><td>{$T_STRESS_FORM.message_title.html}</td></tr>
            <tr><td>Use a specific message body:</td><td>{$T_STRESS_FORM.message_body.html}</td></tr>
            <tr><td>Prefix:</td><td>{$T_STRESS_FORM.prefix_forum.html}</td></tr>
        </table>

    </td>
*}
    </tr>
    <tr><td colspan = "100%" class = "horizontalSeparatorAbove">&nbsp;</td></tr>
    <tr><td colspan = "100%" align = "Center">{$T_STRESS_FORM.submit_stress.html}</td></tr>
</table>
</form>



{$T_DELETE_STRESS_FORM.javascript}
<form {$T_DELETE_STRESS_FORM.attributes}>
    {$T_DELETE_STRESS_FORM.hidden}
<table align = "center">
    <tr><td>Delete stress data for:&nbsp;
            {$T_DELETE_STRESS_FORM.delete_stress_data.html}&nbsp;
            Prefixed with:&nbsp;
            {$T_DELETE_STRESS_FORM.delete_stress_prefix.html}&nbsp;
            {$T_DELETE_STRESS_FORM.submit_delete_stress.html}
    </td></tr>
</table>
</form>

