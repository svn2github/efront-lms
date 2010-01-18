{capture name="system_vars"}
{$T_SYSTEM_VARIABLES_FORM.javascript}
 <form {$T_SYSTEM_VARIABLES_FORM.attributes}>
 {$T_SYSTEM_VARIABLES_FORM.hidden}
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._SECURITYSETTINGS}</legend>
  <table class = "configurationSettings">
         <tr><td class = "labelCell">{$smarty.const._ADMINEMAIL}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.system_email.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._ALLOWEDEXTENSIONS}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_white_list.html}</td></tr>
         <tr><td></td><td class = "infoCell">{$smarty.const._COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._DISALLOWEDEXTENSIONS}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_black_list.html}</td></tr>
         <tr><td></td><td class = "infoCell">{$smarty.const._COMMASEPARATEDLISTASTERISKEXTENSIONEXAMPLE}.{$smarty.const._DENIALTAKESPRECEDENCE}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._MINIMUMPASSWORDLENGTH}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.password_length.html}</td></tr>
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.autologout_time.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.autologout_time.html} {$smarty.const._MINUTESOFINACTIVITY}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._LOGOUTREDIRECT}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.logout_redirect.html}</td></tr>
   <tr><td class = "labelCell">{$smarty.const._ADDITIONALACCOUNTS}:&nbsp;</td>
                  <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.mapped_accounts.html}</td></tr>
 {* <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.smarty_caching.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.smarty_caching.html}</td></tr>
         {if $T_SYSTEM_VARIABLES_FORM.smarty_caching.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.smarty_caching.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.smarty_caching_timeout.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.smarty_caching_timeout.html}</td></tr>
         {if $T_SYSTEM_VARIABLES_FORM.smarty_caching_timeout.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.smarty_caching_timeout.error}</td></tr>{/if}
   *}
  </table>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._LANGUAGESETTINGS}</legend>
  <table class = "configurationSettings">
         <tr><td class = "labelCell">{$smarty.const._DEFAULTLANGUAGE}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.default_language.html}</td></tr>
         {if $T_SYSTEM_VARIABLES_FORM.default_language.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.default_language.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$smarty.const._ONLYONELANGUAGE}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.onelanguage.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._TRANSLATEFILESYSTEM}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.file_encoding.html}</td></tr>
  </table>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._USERACTIVATIONSETTINGS}</legend>
  <table class = "configurationSettings">
      <tr><td class = "labelCell">{$smarty.const._EXTERNALLYSIGNUP}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.signup.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._AUTOMATICUSERACTIVATION}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.activation.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._MAILUSERACTIVATION}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.mail_activation.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._VIEWINSERTGROUPKEY}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.insert_group_key.html}</td></tr>
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.show_license_note.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.show_license_note.html}</td></tr>
         <tr id = "license_note" style = "{if !$T_CONFIGURATION.show_license_note}display:none{/if}"><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.license_note.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.license_note.html}</td></tr>
         {if $T_SYSTEM_VARIABLES_FORM.license_note.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.license_note.error}</td></tr>{/if}
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.reset_license_note.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.reset_license_note.html}</td></tr>
         <tr><td></td><td class = "infoCell">{$smarty.const._USETHISINCASEYOUWANTALLUSERSTORECOMPLYTOLICENSENOTE}</td></tr>
  </table>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._COMMUNICATIONWITHTHIRDPARTY}</legend>
  <table class = "configurationSettings">
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.zip_method.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.zip_method.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._ENABLEDAPI}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.api.html}</td></tr>
         <tr><td class = "labelCell">{$smarty.const._ENABLEMATHCONTENT}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.math_content.html}</td></tr>
{* <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.license_server.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.license_server.html}</td></tr>*}
   <tr><td class = "labelCell">{$smarty.const._MATHSERVER}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.math_server.html}</td></tr>
   <tr><td></td><td class = "infoCell">{$smarty.const._MATHSERVERINFO}</td></tr>
   <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.math_images.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.math_images.html}</td></tr>
   <tr><td></td><td class = "infoCell">{$smarty.const._MATHIMAGESINFO}</td></tr>
         {if isset($T_SYSTEM_VARIABLES_FORM.paypal)}
         <tr><td class = "labelCell">{$smarty.const._PAYPALUSE}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.paypal.html}</td></tr>
         {/if}
{*
         <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.use_sso.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.use_sso.html}</td></tr>
         {if $T_SYSTEM_VARIABLES_FORM.use_sso.error}<tr><td></td><td class = "formError">{$T_SYSTEM_VARIABLES_FORM.use_sso.error}</td></tr>{/if}
*}
   <tr><td class = "labelCell">{$T_SYSTEM_VARIABLES_FORM.phplivedocx_server.label}:&nbsp;</td>
             <td class = "elementCell">{$T_SYSTEM_VARIABLES_FORM.phplivedocx_server.html}&nbsp;
    {$T_SYSTEM_VARIABLES_FORM.phplivedocx_username.label}:&nbsp;{$T_SYSTEM_VARIABLES_FORM.phplivedocx_username.html}&nbsp;
    {$T_SYSTEM_VARIABLES_FORM.phplivedocx_password.label}:&nbsp;{$T_SYSTEM_VARIABLES_FORM.phplivedocx_password.html}&nbsp;
    </td></tr>
   <tr><td></td>
    <td class = "infoCell">{$smarty.const._PHPLIVEDOCXINFO}</td></tr>
  </table>
 </fieldset>
     <table class = "configurationSettings">
         <tr><td></td>
          <td class = "submitCell">{$T_SYSTEM_VARIABLES_FORM.submit_system_variables.html}</td></tr>
     </table>
    </form>
{/capture}
{capture name = "appearance"}
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._CUSTOMIZATION}</legend>
  {$T_CUSTOMIZATION_FORM.javascript}
  <form {$T_CUSTOMIZATION_FORM.attributes}>
      {$T_CUSTOMIZATION_FORM.hidden}
      <table style = "width:100%">
          <tr><td class = "labelCell">{$smarty.const._ADDITIONALFOOTER}:&nbsp;</td>
              <td class = "elementCell">{$T_CUSTOMIZATION_FORM.additional_footer.html}</td></tr>
          <tr><td class = "labelCell">{$smarty.const._SITENAME}:&nbsp;</td>
              <td class = "elementCell">{$T_CUSTOMIZATION_FORM.site_name.html}</td></tr>
          <tr><td class = "labelCell">{$smarty.const._SITEMOTO}:&nbsp;</td>
              <td class = "elementCell">{$T_CUSTOMIZATION_FORM.site_motto.html}</td></tr>
          <tr><td class = "labelCell">{$smarty.const._VIEWDIRECTORY}:&nbsp;</td>
                 <td class = "elementCell">{$T_CUSTOMIZATION_FORM.lessons_directory.html}</td></tr>
             <tr><td class = "labelCell">{$T_CUSTOMIZATION_FORM.collapse_catalog.label}:&nbsp;</td>
                 <td class = "elementCell">{$T_CUSTOMIZATION_FORM.collapse_catalog.html}</td></tr>
             <tr><td class = "labelCell">{$T_CUSTOMIZATION_FORM.display_empty_blocks.label}:&nbsp;</td>
                 <td class = "elementCell">{$T_CUSTOMIZATION_FORM.display_empty_blocks.html}</td></tr>
             <tr><td class = "labelCell">{$T_CUSTOMIZATION_FORM.username_format.label}:&nbsp;</td>
                 <td class = "elementCell">{$T_CUSTOMIZATION_FORM.username_format.html}</td></tr>
    <tr><td></td>
     <td class = "infoCell">{$smarty.const._USERNAMEFORMATINFO}</td></tr>
    <tr><td colspan = "2">&nbsp;</td></tr>
          <tr><td></td><td class = "submitCell">{$T_CUSTOMIZATION_FORM.submit_system_variables.html}</td></tr>
   </table>
  </form>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._THEMELOGO}</legend>
  {$T_UPLOAD_LOGO_FORM.javascript}
     <form {$T_UPLOAD_LOGO_FORM.attributes}>
         {$T_UPLOAD_LOGO_FORM.hidden}
         <table id = "logo_settings">
             <tr><td></td>
              <td><img src = "{$T_LOGO}" alt = "{$smarty.const._NOLOGOFOUND}" title = "{$smarty.const._EFRONTLOGO}" {if isset($T_NEWWIDTH)} width = "{$T_NEWWIDTH}" height = "{$T_NEWHEIGHT}"{/if}/></td></tr>
             <tr><td colspan = "2">&nbsp;</td></tr>
             <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.logo.html}</td></tr>
             <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_UPLOAD_SIZE}</b> {$smarty.const._KB}</td></tr>
             {if $T_UPLOAD_FILE_FORM.logo.0.error}<tr><td></td><td class = "formError">{$T_UPLOAD_FILE_FORM.logo.0.error}</td></tr>{/if}
     {if $T_GD_LOADED}
             <tr><td class = "labelCell">{$smarty.const._LOGOWIDTH}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.logo_max_width.html} px</td></tr>
             {if $T_UPLOAD_LOGO_FORM.logo_max_width.error}<tr><td></td><td class = "formError">{$T_UPLOAD_LOGO_FORM.logo_max_width.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._LOGOHEIGHT}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.logo_max_height.html} px</td></tr>
             {if $T_UPLOAD_LOGO_FORM.logo_max_height.error}<tr><td></td><td class = "formError">{$T_UPLOAD_LOGO_FORM.logo_max_height.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$T_UPLOAD_LOGO_FORM.normalize_dimensions.label}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.normalize_dimensions.html}</td></tr>
     {/if}
             <tr><td class = "labelCell">{$smarty.const._USETHEMELOGO}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_LOGO_FORM.default_logo.html}</td></tr>
             <tr><td></td><td class = "submitCell">{$T_UPLOAD_LOGO_FORM.submit_upload_logo.html}</td></tr>
         </table>
     {if $T_RELOAD_FORM} {assign var = 'div_error' value = "set_logo_table', 1, 'set_logo_table"} {/if}
     </form>
 </fieldset>
 <fieldset class = "fieldsetSeparator">
  <legend>{$smarty.const._THEMEFAVICON}</legend>
  {$T_UPLOAD_FAVICON_FORM.javascript}
     <form {$T_UPLOAD_FAVICON_FORM.attributes}>
         {$T_UPLOAD_FAVICON_FORM.hidden}
         <table id = "favicon_settings">
             <tr><td></td><td><img src = "{$T_FAVICON}" alt = "{$smarty.const._FAVICON}" title = "{$smarty.const._FAVICON}"/></td></tr>
             <tr><td colspan = "2">&nbsp;</td></tr>
             <tr><td class = "labelCell">{$smarty.const._FILENAME}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_FAVICON_FORM.favicon.html}</td></tr>
             <tr><td></td><td class = "infoCell">{$smarty.const._EACHFILESIZEMUSTBESMALLERTHAN} <b>{$T_MAX_UPLOAD_SIZE}</b> {$smarty.const._KB}</td></tr>
             {if $T_UPLOAD_FILE_FORM.favicon.0.error}<tr><td></td><td class = "formError">{$T_UPLOAD_FILE_FORM.favicon.0.error}</td></tr>{/if}
             <tr><td class = "labelCell">{$smarty.const._USETHEMEFAVICON}:&nbsp;</td>
                 <td class = "elementCell">{$T_UPLOAD_FAVICON_FORM.default_favicon.html}</td></tr>
             <tr><td></td><td class = "submitCell">{$T_UPLOAD_FAVICON_FORM.submit_upload_favicon.html}</td></tr>
         </table>
     </form>
 </fieldset>
{/capture}
{capture name = "smtp_vars"}
{$T_SMTP_VARIABLES_FORM.javascript}
<form {$T_SMTP_VARIABLES_FORM.attributes}>
 {$T_SMTP_VARIABLES_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._SMTPSERVER}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_host.html}</td></tr>
        <tr><td></td><td class = "infoCell">{$smarty.const._IFUSESSLTHENPHPOPENSSL}</td></tr>
        {if $T_SMTP_VARIABLES_FORM.smtp_host.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_host.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._SMTPPORT}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_port.html}</td></tr>
        {if $T_SMTP_VARIABLES_FORM.smtp_port.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_port.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._SMTPUSER}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_user.html}</td></tr>
        {if $T_SMTP_VARIABLES_FORM.smtp_user.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_user.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._SMTPPASSWORD}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_pass.html}</td></tr>
        {if $T_SMTP_VARIABLES_FORM.smtp_pass.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_pass.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._SMTPTIMEOUT}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_timeout.html}</td></tr>
        {if $T_SMTP_VARIABLES_FORM.smtp_timeout.error}<tr><td></td><td class = "formError">{$T_SMTP_VARIABLES_FORM.smtp_timeout.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._SMTPAUTH}:&nbsp;</td>
            <td class = "elementCell">{$T_SMTP_VARIABLES_FORM.smtp_auth.html}</td></tr>
        <tr><td></td><td class = "submitCell">{$T_SMTP_VARIABLES_FORM.check_smtp.html}&nbsp;{$T_SMTP_VARIABLES_FORM.submit_smtp_variables.html}</td></tr>
    </table>
</form>
{/capture}
{capture name = "locale_vars"}
{$T_LOCALE_VARIABLES_FORM.javascript}
<form {$T_LOCALE_VARIABLES_FORM.attributes}>
 {$T_LOCALE_VARIABLES_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._TIMEZONE}:&nbsp;</td>
            <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.time_zone.html}</td></tr>
        {if $T_LOCALE_VARIABLES_FORM.time_zone.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.time_zone.error}</td></tr>{/if}
  <tr><td class = "labelCell">{$smarty.const._CURRENCY}:&nbsp;</td>
         <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.currency.html}</td></tr>
        <tr><td class = "labelCell">{$T_LOCALE_VARIABLES_FORM.currency_order.label}:&nbsp;</td>
         <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.currency_order.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._DECIMALPOINT}:&nbsp;</td>
            <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.decimal_point.html}</td></tr>
        {if $T_LOCALE_VARIABLES_FORM.decimal_point.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.decimal_point.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._THOUSANDSSEPARATOR}:&nbsp;</td>
            <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.thousands_sep.html}</td></tr>
        {if $T_LOCALE_VARIABLES_FORM.thousands_sep.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.thousands_sep.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._DATEFORMAT}:&nbsp;</td>
            <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.date_format.html}</td></tr>
        {if $T_LOCALE_VARIABLES_FORM.date_format.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.date_format.error}</td></tr>{/if}
        {*<tr><td class = "labelCell">{$smarty.const._SPECIFICLOCALE}:&nbsp;</td>
            <td class = "elementCell">{$T_LOCALE_VARIABLES_FORM.set_locale.html}</td></tr>
        {if $T_LOCALE_VARIABLES_FORM.set_locale.error}<tr><td></td><td class = "formError">{$T_LOCALE_VARIABLES_FORM.set_locale.error}</td></tr>{/if}*}
        <tr><td></td><td class = "submitCell">{$T_LOCALE_VARIABLES_FORM.submit_locale.html}</td></tr>
 </table>
</form>
{/capture}
{capture name = "php_vars"}
{$T_PHP_VARIABLES_FORM.javascript}
<form {$T_PHP_VARIABLES_FORM.attributes}>
 {$T_PHP_VARIABLES_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._MAXFILESIZE} ({$smarty.const._KB}):&nbsp;</td>
            <td class = "elementCell">{$T_PHP_VARIABLES_FORM.max_file_size.html}</td></tr>
        {if $T_PHP_VARIABLES_FORM.max_file_size.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.max_file_size.error}</td></tr>{/if}
        <tr><td></td><td class = "infoCell">{$smarty.const._MAXFILEISAFFECTEDANDIS}: {$T_MAX_FILE_SIZE} {$smarty.const._KB}</td></tr>
        <tr><td class = "labelCell">{$smarty.const._MEMORYLIMIT} (memory_limit):&nbsp;</td>
            <td class = "elementCell">{$T_PHP_VARIABLES_FORM.memory_limit.html} {$smarty.const._MEGABYTES}</td></tr>
        {if $T_PHP_VARIABLES_FORM.memory_limit.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.memory_limit.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._MAXEXECUTIONTIME} (max_execution_time):&nbsp;</td>
            <td class = "elementCell">{$T_PHP_VARIABLES_FORM.max_execution_time.html} {$smarty.const._SECONDS}</td></tr>
        {if $T_PHP_VARIABLES_FORM.max_execution_time.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.max_execution_time.error}</td></tr>{/if}
        <tr><td class = "labelCell">{$smarty.const._GZHANDLER}:&nbsp;</td>
            <td class = "elementCell">{$T_PHP_VARIABLES_FORM.gz_handler.html}</td></tr>
        {if $T_PHP_VARIABLES_FORM.gz_handler.error}<tr><td></td><td class = "formError">{$T_PHP_VARIABLES_FORM.gz_handler.error}</td></tr>{/if}
        <tr><td></td><td class = "infoCell">{$smarty.const._LEAVEBLANKTOUSEPHPINI}</td></tr>
        <tr><td></td><td class = "submitCell">{$T_PHP_VARIABLES_FORM.submit_php.html}</td></tr>
    </table>
</form>
{/capture}
{capture name = "multiple_logins"}
{$T_MULTIPLE_LOGINS_FORM.javascript}
<form {$T_MULTIPLE_LOGINS_FORM.attributes}>
 {$T_MULTIPLE_LOGINS_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._ALLOWMULTIPLELOGINSGLOBALLY}:&nbsp;</td>
            <td class = "elementCell">{$T_MULTIPLE_LOGINS_FORM.global.html}</td></tr>
        {if $T_MULTIPLE_LOGINS_FORM.groups}
        <tr><td class = "labelCell">{$smarty.const._EXCEPTFORTHEGROUPS}:&nbsp;</td>
            <td class = "elementCell">{$T_MULTIPLE_LOGINS_FORM.groups.html}</td></tr>
        {if $T_MULTIPLE_LOGINS_FORM.groups.error}<tr><td></td><td class = "formError">{$T_MULTIPLE_LOGINS_FORM.groups.error}</td></tr>{/if}{/if}
        <tr><td class = "labelCell">{$smarty.const._EXCEPTFORTHEROLES}:&nbsp;</td>
            <td class = "elementCell">{$T_MULTIPLE_LOGINS_FORM.user_types.html}</td></tr>
        {*{if $T_MULTIPLE_LOGINS_FORM.user_types.error}<tr><td></td><td class = "formError">{$T_MULTIPLE_LOGINS_FORM.user_types.error}</td></tr>{/if}
        {*<tr><td class = "labelCell">{$smarty.const._EXCEPTFORTHEUSERS}:&nbsp;</td>
            <td class = "elementCell">{$T_MULTIPLE_LOGINS_FORM.users.html}</td></tr>
        {if $T_MULTIPLE_LOGINS_FORM.users.error}<tr><td></td><td class = "formError">{$T_MULTIPLE_LOGINS_FORM.users.error}</td></tr>{/if}*}
        <tr><td></td><td class = "infoCell">{$smarty.const._HOLDDOWNCTRLFORMULTIPLESELECT}</td></tr>
        <tr><td colspan = "2">&nbsp;</td></tr>
        <tr><td></td><td class = "submitCell">{$T_MULTIPLE_LOGINS_FORM.submit_multiple_logins.html}</td></tr>
 </table>
</form>
{/capture}
{capture name = "disable_options"}
{$T_DISABLE_VARIABLES_FORM.javascript}
<form {$T_DISABLE_VARIABLES_FORM.attributes}>
 {$T_DISABLE_VARIABLES_FORM.hidden}
    <table class = "formElements">
        <tr><td class = "labelCell">{$smarty.const._PROJECTS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_projects.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._BOOKMARKS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_bookmarks.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._COMMENTS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_comments.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._ONLINEUSERS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_online_users.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._GLOSSARY}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_glossary.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._CALENDAR}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_calendar.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._SURVEYS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_surveys.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._ANNOUNCEMENTS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_news.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._MESSAGES}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_messages.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._FORUMS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_forum.html}</td></tr>
  <tr><td class = "labelCell">{$smarty.const._TESTS}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.disable_tests.html}</td></tr>
        <tr><td class = "labelCell">{$smarty.const._CHAT}:&nbsp;</td>
            <td class = "elementCell">{$T_DISABLE_VARIABLES_FORM.chat_enabled.html}</td></tr>
  <tr><td></td>
  <td class = "infoCell">{$smarty.const._SELECTOPTIONSDISABLED}</td></tr>
        <tr><td></td><td class = "submitCell">{$T_DISABLE_VARIABLES_FORM.submit_disable.html}</td></tr>
 </table>
</form>
{/capture}
{capture name="view_config"}
<div class="tabber">
    <div class="tabbertab {if ($smarty.get.tab == 'vars')}tabbertabdefault{/if}">
        <h3>{$smarty.const._CONFIGURATIONVARIABLES}</h3>
        {eF_template_printBlock title=$smarty.const._CONFIGURATIONVARIABLES data=$smarty.capture.system_vars image='32x32/settings.png'}
    </div>
    <div class="tabbertab {if ($smarty.get.tab == 'appearance')}tabbertabdefault{/if}">
        <h3>{$smarty.const._APPEARANCE}</h3>
        {eF_template_printBlock title=$smarty.const._APPEARANCE data=$smarty.capture.appearance image='32x32/themes.png'}
    </div>
    <div class="tabbertab {if ($smarty.get.tab == 'smtp')}tabbertabdefault{/if}">
        {if ($smarty.get.email_conf == '1')}
            {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONARECORRECT type='success'}
        {elseif ($smarty.get.email_conf == '-1')}
            {eF_template_printMessage message=$smarty.const._SMTPCONFIGURATIONERROR type='failure'}
        {else}
        {/if}
        <h3>{$smarty.const._SMTP}</h3>
        {eF_template_printBlock title=$smarty.const._SMTP data=$smarty.capture.smtp_vars image='32x32/mail.png'}
    </div>
    <div class="tabbertab {if ($smarty.get.tab == 'locale')}tabbertabdefault{/if}">
        <h3>{$smarty.const._LOCALE}</h3>
        {eF_template_printBlock title=$smarty.const._LOCALE data=$smarty.capture.locale_vars image='32x32/locale.png'}
    </div>
    <div class="tabbertab {if ($smarty.get.tab == 'php')}tabbertabdefault{/if}">
        <h3>{$smarty.const._PHP}</h3>
        {eF_template_printBlock title=$smarty.const._PHP data=$smarty.capture.php_vars image='32x32/php.png'}
    </div>
    <div class="tabbertab {if ($smarty.get.tab == 'multiple_logins')}tabbertabdefault{/if}" title = "{$smarty.const._MULTIPLELOGINS}">
        {eF_template_printBlock title=$smarty.const._MULTIPLELOGINS data=$smarty.capture.multiple_logins image='32x32/keys.png'}
    </div>
 <div class="tabbertab {if ($smarty.get.tab == 'disable')}tabbertabdefault{/if}" title = "{$smarty.const._DISABLEOPTIONS}">
        {eF_template_printBlock title=$smarty.const._DISABLEOPTIONS data=$smarty.capture.disable_options image='32x32/generic.png'}
    </div>
{/capture}
{*moduleConfig: The configuration settings page*}
{capture name = "moduleConfig"}
 <tr><td class="moduleCell">
  {eF_template_printBlock title = $smarty.const._CONFIGURATIONVARIABLES data = $smarty.capture.view_config image='32x32/tools.png'}
    </td></tr>
{/capture}
