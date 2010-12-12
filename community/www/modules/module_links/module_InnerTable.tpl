{* template functions for inner table *}
{capture name = 't_inner_table_code}
    <table>
        {section name = 'links_list' loop = $T_LINKS_INNERTABLE max = 5}
            <tr><td>{$smarty.section.links_list.iteration}.
                <a target="_blank" href = "{$T_LINKS_INNERTABLE[links_list].link}">{$T_LINKS_INNERTABLE[links_list].display}</a>
                </td>
            </tr>
        {sectionelse}
            <tr><td class = "emptyCategory">{$smarty.const._LINKS_NOLINKFOUND}</td></tr>
        {/section}
    </table>
{/capture}

{eF_template_printBlock title = $smarty.const._LINKS_LINKSPAGE data = $smarty.capture.t_inner_table_code absoluteImagePath = 1 image = $T_LINKS_BASELINK|cat:'images/link.png' options = $T_LINKS_INNERTABLE_OPTIONS}
