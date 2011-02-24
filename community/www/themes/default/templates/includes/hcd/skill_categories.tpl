{if $smarty.get.add_skill_cat || $smarty.get.edit_skill_cat}
 {capture name = 't_skill_cat_code'}
  {eF_template_printForm form = $T_SKILL_CAT_FORM}
 {/capture}
 {eF_template_printBlock title = $smarty.const._UPDATESKILLSCATEGORY data = $smarty.capture.t_skill_cat_code image = '32x32/tools.png'}
 {if $T_MESSAGE_TYPE == 'success'}
     <script>parent.location = parent.location</script>
 {/if}
{/if}
