 <table>
 <tr><td {if $T_FACEBOOK_EXTERNAL_LOGIN}colspan="2"{/if} align="justify">{$T_FACEBOOK_LOGIN_WELCOME}</td></tr>
 <tr><td {if $T_FACEBOOK_EXTERNAL_LOGIN}colspan="2"{/if}>&nbsp;</td></tr>
 <tr><td style="vertical-align:top">
 <fieldset class = "fieldsetSeparator">
 <legend>{$smarty.const._MERGEWITHEXISTINGACCOUNT}</legend>

    {$T_EXISTING_USER_LOGIN_FORM.javascript}
    <form {$T_EXISTING_USER_LOGIN_FORM.attributes}>
     {$T_EXISTING_USER_LOGIN_FORM.hidden}

  <div class = "formRow">
      <div class = "formLabel">
                <div class = "header">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_login.label}</div>
         </div>
      <div class = "formElement">
             <div class = "field">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_login.html}</div>
          {if $T_EXISTING_USER_LOGIN_FORM.fb_existing_login.error}<div class = "error">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_login.error}</div>{/if}
         </div>

     </div>
  <div class = "formRow">
      <div class = "formLabel">
                <div class = "header">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_password.label}</div>
         </div>
      <div class = "formElement">
             <div class = "field">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_password.html}</div>
          {if $T_EXISTING_USER_LOGIN_FORM.fb_existing_password.error}<div class = "error">{$T_EXISTING_USER_LOGIN_FORM.fb_existing_password.error}</div>{/if}
         </div>
     </div>
  <div class = "formRow">
         <div class = "formLabel">
                <div class = "header">&nbsp;</div>
                <div class = "explanation"></div>
         </div>
      <div class = "formElement">
             <div class = "field">{$T_EXISTING_USER_LOGIN_FORM.submit_login_existing.html}</div>
         </div>
     </div>
     </form>
    </fieldset>

     </td>
     {if $T_FACEBOOK_EXTERNAL_LOGIN}
      <td style="vertical-align:top">
  <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._CREATENEWACCOUNTTOMERGEWITHFACEBOOK}</legend>

     {$T_NEW_FACEBOOK_USER_LOGIN_FORM.javascript}

     <form {$T_NEW_FACEBOOK_USER_LOGIN_FORM.attributes}>
      {$T_NEW_FACEBOOK_USER_LOGIN_FORM.hidden}


   <div class = "formRow">
       <div class = "formLabel">
                 <div class = "header">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_login.label}</div>
          </div>
       <div class = "formElement">
              <div class = "field">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_login.html}</div>
           {if $T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_login.error}<div class = "error">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_login.error}</div>{/if}
          </div>

      </div>
   <div class = "formRow">
       <div class = "formLabel">
                 <div class = "header">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_password.label}</div>
          </div>
       <div class = "formElement">
              <div class = "field">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_password.html}</div>
           {if $T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_password.error}<div class = "error">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_password.error}</div>{/if}
          </div>
      </div>
   <div class = "formRow">
       <div class = "formLabel">
                 <div class = "header">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_passrepeat.label}</div>
          </div>
       <div class = "formElement">
              <div class = "field">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_passrepeat.html}</div>
           {if $T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_passrepeat.error}<div class = "error">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_passrepeat.error}</div>{/if}
          </div>
      </div>


   <div class = "formRow">
       <div class = "formLabel">
                 <div class = "header">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_email.label}</div>
          </div>
       <div class = "formElement">
              <div class = "field">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_email.html}</div>
           {if $T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_email.error}<div class = "error">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.fb_new_email.error}</div>{/if}
          </div>
      </div>


   <div class = "formRow">
          <div class = "formLabel">
                 <div class = "header">&nbsp;</div>
                 <div class = "explanation"></div>
          </div>
       <div class = "formElement">

              <div class = "field">{$T_NEW_FACEBOOK_USER_LOGIN_FORM.submit_login_new.html}</div>
              <br>
          </div>
      </div>

      </form>
     </fieldset>

      </td>
 {/if}
      </tr>
      </table>
