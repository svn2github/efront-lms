{capture name="t_reports_edit"}
    {$T_TRAININGREPORTS_FORM.javascript}
    <form {$T_TRAININGREPORTS_FORM.attributes}>
        {$T_TRAININGREPORTS_FORM.hidden}

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._TRAININGREPORTS_GENERAL}</legend>
            <table>
                <tbody>
                    <tr>
                        <td>{$T_TRAININGREPORTS_FORM.name.label}:</td>
                        <td class="elementCell">{$T_TRAININGREPORTS_FORM.name.html}</td>
                    </tr>
                </tbody>
            </table>
        </fieldset>

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._TRAININGREPORTS_FIELDS}</legend>
            <table id="module-reports-fields">
                <tbody>
                {foreach from=$T_TRAININGREPORTS_SELECTEDFIELDS item=T_TRAININGREPORTS_SELECTEDFIELD}
                    <tr>
                        <td class="labelCell">
                            {html_options name="fields[]" options=$T_TRAININGREPORTS_FIELDS selected=$T_TRAININGREPORTS_SELECTEDFIELD}
                        </td>
                        <td class="elementCell">&nbsp;</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            <table>
                <tr>
                    <td><a id="module-reports-add-field" href="javascript:void(0)">{$smarty.const._TRAININGREPORTS_ADDFIELD}</a></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </fieldset>

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._COURSES}</legend>
            <table id="module-reports-courses">
                <tbody>
                {foreach from=$T_TRAININGREPORTS_SELECTEDCOURSES item=T_TRAININGREPORTS_SELECTEDCOURSE}
                    <tr>
                        <td class="labelCell">
                            {html_options name="courses[]" options=$T_TRAININGREPORTS_COURSES selected=$T_TRAININGREPORTS_SELECTEDCOURSE}
                        </td>
                        <td class="elementCell">&nbsp;</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>

            <table>
                <tr>
                    <td><a id="module-reports-add-course" href="javascript:void(0)">{$smarty.const._TRAININGREPORTS_ADDCOURSE}</a></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </fieldset>

        <fieldset class="fieldsetSeparator">
            <legend>{$smarty.const._TRAININGREPORTS_DATES}</legend>
            <table>
                <tr>
                    <td>{$T_TRAININGREPORTS_FORM.start_date.label}:</td>
                    <td class = "elementCell">{$T_TRAININGREPORTS_FORM.start_date.html}</td>
                </tr>
                <tr>
                    <td>{$T_TRAININGREPORTS_FORM.end_date.label}:</td>
                    <td class = "elementCell">{$T_TRAININGREPORTS_FORM.end_date.html}</td>
                </tr>
                <tr>
                    <td>{$T_TRAININGREPORTS_FORM.separate_by.label}:</td>
                    <td class = "elementCell">
                        {$T_TRAININGREPORTS_FORM.separate_by.html}
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class = "elementCell">
                        {$T_TRAININGREPORTS_FORM.submit.html}
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <img id="module-reports-transparent" src="{$T_MODULE_BASELINK|cat:'assets/images/transparent.png'}" style="display:none;" />
    <select id="module-reports-fields-select" name="fields[]" style="display:none;">
        {html_options options=$T_TRAININGREPORTS_FIELDS}
    </select>
    <select id="module-reports-courses-select" name="courses[]" style="display:none;">
        {html_options options=$T_TRAININGREPORTS_COURSES}
    </select>
{/capture}

{eF_template_printInnerTable
    title=$smarty.const._TRAININGREPORTS
    data=$smarty.capture.t_reports_edit
    absoluteImagePath=1
    image=$T_REPORTS_BASELINK|cat:'assets/images/logo32.png'}
