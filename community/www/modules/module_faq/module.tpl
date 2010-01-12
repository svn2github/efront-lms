{*Smarty template*}

{if $smarty.get.add_faq || $smarty.get.edit_faq}
    {capture name = 't_insert_faq_code'}
                {$T_FAQ_FORM.javascript}
                <form {$T_FAQ_FORM.attributes}>
                    {$T_FAQ_FORM.hidden}
                    <table class = "formElements" style = "width:100%">
                        <tr><td class = "labelCell">{$smarty.const._FAQ_REGARDING}:&nbsp;</td>
                            <td class = "elementCell">{$T_FAQ_FORM.related_content.html}</td></tr>                    	
                        <tr><td class = "labelCell">{$smarty.const._FAQ_QUESTION}:&nbsp;</td>
                            <td class = "elementCell">{$T_FAQ_FORM.question.html}</td></tr>
                        <tr><td colspan = "2">&nbsp;</td></tr>
                        <tr><td class = "labelCell">{$smarty.const._FAQ_ANSWER}:&nbsp;</td>
                            <td class = "elementCell">{$T_FAQ_FORM.answer.html}</td></tr>
                        {if $T_FAQ_FORM.file_upload.0.error}<tr><td></td><td class = "formError">{$T_FAQ_FORM.file_upload.0.error}</td></tr>{assign var = 'div_error' value = 'upload_file_table'}{/if}
                        <tr><td></td><td >&nbsp;</td></tr>
                        <tr><td></td><td class = "submitCell">{$T_FAQ_FORM.submit_faq.html}</td></tr>
                    </table>
                </form>

    {/capture}

    {eF_template_printBlock title=$smarty.const._FAQ_INSERTFAQ data=$smarty.capture.t_insert_faq_code image = $T_FAQ_MODULE_BASELINK|cat:'images/unknown32.png' absoluteImagePath = 1}
{else}
    {capture name = 't_faq_list_code'}
        {if $T_FAQUSERLESSONROLE != 'student'}
                    <table>
                        <tr><td>
                            <a href = "{$T_FAQ_MODULE_BASEURL}&add_faq=1"><img src = {$T_FAQ_MODULE_BASELINK|cat:"images/add.png"} alt = "{$smarty.const._FAQ_ADDFAQ}" title = "{$smarty.const._FAQ_ADDFAQ}" border = "0" /></a>
                        </td><td>
                            <a href = "{$T_FAQ_MODULE_BASEURL}&add_faq=1" title = "{$smarty.const._FAQ_ADDFAQ}">{$smarty.const._FAQ_ADDFAQ}</a>
                        </td></tr>
                    </table>
        {/if}

        {if $T_QUESTIONS_FOUND}
                        <div class = "blockHeader" style = "margin-top:30px">{$smarty.const._FAQ_QUESTIONS}</div>
                        <table style = "margin-left:30px;padding:3px 3px 3px 3px;">
            {section name = 'questions_list' loop = $T_FAQ.question}
                            <tr><td>{$smarty.section.questions_list.iteration}. <a href = "{$smarty.server.REQUEST_URI}#{$smarty.section.questions_list.iteration}">{$T_FAQ.question[questions_list]}</a></td>
                {if $T_FAQUSERLESSONROLE != 'student'}
                                <td><a href = "{$T_FAQ_MODULE_BASEURL}&edit_faq={$T_FAQ.id[questions_list]}"><img src = {$T_FAQ_MODULE_BASELINK|cat:"images/edit.png"} alt = "{$smarty.const._FAQ_EDITFAQ}" title = "{$smarty.const._FAQ_EDITFAQ}" border = "0"/></a>
                                    <a href = "{$T_FAQ_MODULE_BASEURL}&delete_faq={$T_FAQ.id[questions_list]}" onclick = "return confirm ('{$smarty.const._IRREVERSIBLEACTIONAREYOUSURE}')"><img src = {$T_FAQ_MODULE_BASELINK|cat:"images/error_delete.png"} alt = "{$smarty.const._FAQ_EDITFAQ}" title = "{$smarty.const._FAQ_EDITFAQ}" border = "0"/></a>
                                </td>
                {/if}
                            </tr>
            {/section}
                        </table>
                        <div class = "blockHeader" style = "margin-top:50px">{$smarty.const._FAQ_ANSWERS}</div>
            {section name = 'questions_list' loop = $T_FAQ.question}
                        <div style = "margin-left:30px;padding:3px 3px 3px 3px;font-weight:bold"><a name = "{$smarty.section.questions_list.iteration}"></a>{$smarty.section.questions_list.iteration}. {$T_FAQ.question[questions_list]}</div>
                        <div style = "margin-left:60px;padding:3px 3px 3px 3px;margin-bottom:20px">{$T_FAQ.answer[questions_list]}</div>
            {/section}
        {else}
            <table>
                <tr><td class = "emptyCategory">{$smarty.const._FAQ_NOFAQFOUND}</td></tr>
            </table>
        {/if}
    {/capture}

    {eF_template_printBlock title=$smarty.const._FAQ_FAQLIST data=$smarty.capture.t_faq_list_code image = $T_FAQ_MODULE_BASELINK|cat:'images/unknown32.png' absoluteImagePath = 1}
{/if}


