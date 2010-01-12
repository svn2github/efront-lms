{* template functions for inner table *}
{capture name = 't_inner_table_code}
    <table>
        <tr><td>{$T_BILLBOARD_INNERTABLE}</td></tr>
    </table>
{/capture}

{eF_template_printBlock title = $smarty.const._BILLBOARD_BILLBOARDPAGE data = $smarty.capture.t_inner_table_code image = $T_BILLBOARD_MODULE_BASELINK|cat:'images/note_pinned32.png' absoluteImagePath=1 options = $T_BILLBOARD_INNERTABLE_OPTIONS}
