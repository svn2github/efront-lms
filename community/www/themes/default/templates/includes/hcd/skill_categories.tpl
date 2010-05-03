 {if $smarty.get.add_skill_cat || $smarty.get.edit_skill_cat}

 {* **************************************************************
 This is the form that contains the skill's data
 ************************************************************** *}
 {capture name = 't_skill_cat_code'}
 {$T_SKILL_CAT_FORM.javascript}
     <table width = "75%">
      <tr>
       <td width="70%">
         <form {$T_SKILL_CAT_FORM.attributes}>
         {$T_SKILL_CAT_FORM.hidden}
          <table class = "formElements">
           <tr>
            <td class = "labelCell">{$T_SKILL_CAT_FORM.skill_cat_description.label}:&nbsp;</td>
            <td>{$T_SKILL_CAT_FORM.skill_cat_description.html}</td>
           </tr>
           {if $T_SKILL_CAT_FORM.skill_cat_description.error}<tr><td></td><td class = "formError">{$T_SKILLS_FORM.skill_description.error}</td></tr>{/if}

           <tr><td colspan = "2">&nbsp;</td></tr>

           <tr><td></td><td class = "submitCell" style = "text-align:left">
            {$T_SKILL_CAT_FORM.submit_skill_details.html}</td>
           </tr>

         </table>
       </form>
      </td>
     </tr>
    </table>

 {/capture}
 {/if}
{eF_template_printBlock title = $smarty.const._UPDATESKILLSCATEGORY data = $smarty.capture.t_skill_cat_code image = '32x32/tools.png'}
