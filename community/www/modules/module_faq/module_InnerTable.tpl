{* template functions for inner table *}
{capture name = 't_inner_table_code'}
    <table>
        {section name = 'faq_list' loop = $T_FAQ_INNERTABLE max = 10}
            <tr><td>{$smarty.section.faq_list.iteration}.</td><td><a href = "{$T_FAQ_MODULE_BASEURL}#{$smarty.section.faq_list.iteration}">{$T_FAQ_INNERTABLE[faq_list].question|eF_truncate:50}</a></td></tr>
            <tr><td></td><td><i>{$T_FAQ_INNERTABLE[faq_list].answer|eF_truncate:50}</i></td></tr>
        {sectionelse}
            <tr><td class = "emptyCategory">{$smarty.const._FAQ_NOFAQFOUND}</td></tr>
        {/section}
    </table>
{/capture}

{if $T_FAQ_IN_UNIT_CONTENT}
	{$smarty.capture.t_inner_table_code}
{else}
	{eF_template_printBlock title = $smarty.const._FAQ_FAQPAGE data = $smarty.capture.t_inner_table_code image = $T_FAQ_MODULE_BASELINK|cat:'images/unknown32.png' absoluteImagePath = 1 options = $T_FAQ_INNERTABLE_OPTIONS}
{/if}
