        {$T_PERSONAL_INFO_FORM.javascript}
        <form {$T_PERSONAL_INFO_FORM.attributes}>
      {$T_PERSONAL_INFO_FORM.hidden}
      <div class = "formRow">
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.login.label}</div>
                    <div class = "explanation" {if $T_LDAP_USER}style = "display:none"{/if}>{$smarty.const._ONLYALLOWEDCHARACTERSLOGIN}</div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.login.html}</div>
              {if $T_PERSONAL_INFO_FORM.login.error}<div class = "error">{$T_PERSONAL_INFO_FORM.login.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow" {if $T_LDAP_USER}style = "display:none"{/if}>
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.password.label}</div>
                    <div class = "explanation">{$smarty.const._PASSWORDMUSTBE6CHARACTERS|replace:"%x":$T_CONFIGURATION.password_length}</div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.password.html}</div>
              {if $T_PERSONAL_INFO_FORM.password.error}<div class = "error">{$T_PERSONAL_INFO_FORM.password.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow" {if $T_LDAP_USER}style = "display:none"{/if}>
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.passrepeat.label}</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.passrepeat.html}</div>
              {if $T_PERSONAL_INFO_FORM.passrepeat.error}<div class = "error">{$T_PERSONAL_INFO_FORM.passrepeat.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow">
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.email.label}</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.email.html}</div>
              {if $T_PERSONAL_INFO_FORM.email.error}<div class = "error">{$T_PERSONAL_INFO_FORM.email.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow">
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.firstName.label}</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.firstName.html}</div>
              {if $T_PERSONAL_INFO_FORM.firstName.error}<div class = "error">{$T_PERSONAL_INFO_FORM.firstName.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow">
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.lastName.label}</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.lastName.html}</div>
              {if $T_PERSONAL_INFO_FORM.lastName.error}<div class = "error">{$T_PERSONAL_INFO_FORM.lastName.error}</div>{/if}
             </div>
         </div>
        {foreach name = 'profile_fields' key = key item = item from = $T_USER_PROFILE_FIELDS }
      <div class = "formRow">
          <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.$item.label}</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.$item.html}</div>
              {if $T_PERSONAL_INFO_FORM.$item.error}<div class = "error">{$T_PERSONAL_INFO_FORM.$item.error}</div>{/if}
             </div>
         </div>
        {/foreach}
      <div class = "formRow">
             <div class = "formLabel">
                    <div class = "header">{$T_PERSONAL_INFO_FORM.comments.label}</div>
                    {*<div class = "explanation">{$smarty.const._ENTERANYCOMMENTS}</div>*}
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.comments.html}</div>
              {if $T_PERSONAL_INFO_FORM.comments.error}<div class = "error">{$T_PERSONAL_INFO_FORM.comments.error}</div>{/if}
             </div>
         </div>
      <div class = "formRow">
             <div class = "formLabel">
                    <div class = "header">&nbsp;</div>
                    <div class = "explanation"></div>
             </div>
          <div class = "formElement">
                 <div class = "field">{$T_PERSONAL_INFO_FORM.submit_register.html}</div>
             </div>
         </div>
        </form>
