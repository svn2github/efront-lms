<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html {if $smarty.server.PHP_SELF|basename == 'browse.php' || $smarty.server.PHP_SELF|basename == 'browsecontent.php'}class = "whitebg"{/if} {if $smarty.get.popup || $T_POPUP_MODE}class = "popup"{/if} {if $T_RTL}dir = "rtl"{/if} {if $T_OPEN_FACEBOOK_SESSION}xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml"{/if}>
<head>
    <base href = "{$smarty.const.G_SERVERNAME}">
    <meta http-equiv = "Content-Language" content = "{$smarty.const._HEADERLANGUAGEHTMLTAG}">
    <meta http-equiv = "keywords" content = "education">
    <meta http-equiv = "description" content = "Collaborative Elearning Platform">
    <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8">
    <link rel="shortcut icon" href="{if $T_FAVICON}{$T_FAVICON}{else}themes/default/images/favicon.png{/if}">
    <link rel = "stylesheet" type = "text/css" href = "{$smarty.const.G_CURRENTTHEMECSS}">
    {foreach name = 'module_css_list' item = item key = key from = $T_MODULE_CSS}
    <link rel = "stylesheet" type = "text/css" href = "{$item}?build={$smarty.const.G_BUILD}" /> {*///MODULES LINK STYLESHEETS*}
    {/foreach}
    <title>{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{else}{$smarty.const._EFRONTNAME}{/if} | {if $T_CONFIGURATION.site_motto}{$T_CONFIGURATION.site_motto}{else}{$smarty.const._THENEWFORMOFADDITIVELEARNING}{/if}</title>

    {if $T_OPEN_FACEBOOK_SESSION}
    <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
    {/if}

    <script type = "text/javascript">
        var ajaxObjects = new Array();
        top.document.title = "{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{else}{$smarty.const._EFRONTNAME}{/if} | {if $T_TITLE_BAR}{$T_TITLE_BAR}{else}{if $T_CONFIGURATION.site_motto}{$T_CONFIGURATION.site_motto}{else}{$smarty.const._THENEWFORMOFADDITIVELEARNING}{/if}{/if}";
        if (window.name == 'POPUP_FRAME') var popup=1;
        {if $T_BROWSER == 'IE6'}var globalImageExtension = 'gif';{else}var globalImageExtension = 'png';{/if}

        {if $T_THEME_SETTINGS->options.sidebar_interface == 0}
        var usingHorizontalInterface = false;
        {else}
        var usingHorizontalInterface = true;
        {/if}

        var sessionLogin = "{$smarty.session.s_login}";
        var translationsToJS = new Array();
    </script>

{* Do not check for menus when called by popups or the sidebar*}
{if !$T_POPUP_MODE && !$smarty.get.popup && isset($T_CTG) && $T_THEME_SETTINGS->options.sidebar_interface == 0}
<script>
//ctg, op, tab, type, module_menu)
var category = "{$T_CTG}&{$T_OP}&{$smarty.get.tab}&{$smarty.get.type}&{$T_MODULE_HIGHLIGHT}&{$T_OPTION}";
{literal}
if (top.sideframe && top.sideframe.document.getElementById('hasLoaded')) {
{/literal}
   {if $T_CTG == 'personal' && isset($smarty.get.tab) && $smarty.get.tab == 'file_record'}
       top.sideframe.changeTDcolor('file_manager');
   {elseif $T_CTG == 'control_panel' && $smarty.session.s_type != "administrator"}
       top.sideframe.changeTDcolor('lesson_main');
   {elseif $T_CTG == 'content' && isset($smarty.get.type) && $smarty.get.type == 'theory'}
       top.sideframe.changeTDcolor('theory');
   {elseif $T_CTG == 'tests'}
       top.sideframe.changeTDcolor('tests');
   {elseif $T_CTG == 'projects'}
       top.sideframe.changeTDcolor('exercises');
   {elseif $T_CTG == 'system_config' || $T_CTG == 'themes'}
       top.sideframe.changeTDcolor('control_panel');
   {elseif $T_CTG == 'glossary'}
       top.sideframe.changeTDcolor('glossary');
   {elseif $T_CTG == 'content' && $T_OP == 'file_manager'}
       top.sideframe.changeTDcolor('file_manager');
   {elseif $T_CTG == 'users' && $smarty.session.employee_type == $smarty.const._SUPERVISOR}
       top.sideframe.changeTDcolor('employees');
   {elseif $T_CTG == 'statistics'}
       top.sideframe.changeTDcolor('statistics_{$T_OPTION}');
   {elseif $smarty.const.G_VERSIONTYPE == 'enterprise' && ($T_CTG == "module_hcd")}
        {if ($T_OP == "reports")}
            top.sideframe.changeTDcolor('search_employee');
        {elseif isset($T_OP) && $T_OP != ''}
            top.sideframe.changeTDcolor('{$T_OP}');
        {else}
            top.sideframe.changeTDcolor('hcd_control_panel');
        {/if}
   {elseif $T_CTG == 'social'}
        {if $T_OP == 'people'}
            top.sideframe.changeTDcolor('people');
        {elseif $T_OP == 'timeline'}
            {if isset($smarty.get.lessons_ID)}
                top.sideframe.changeTDcolor('timeline');
            {else}
                top.sideframe.changeTDcolor('system_timeline');
            {/if}
        {/if}
   {elseif $T_CTG == 'module'}
        top.sideframe.changeTDcolor('{$T_MODULE_HIGHLIGHT}');
   {else}
       top.sideframe.changeTDcolor('{$T_CTG}');
   {/if}
{literal}
}
{/literal}
var translations = new Array(); //used for passing language tags to js
</script>
{/if}

<script>var translations = new Array(); /*used for passing language tags to js*/</script>

</head>
{* Using that to avoid creating a body for the sidebar*}
{if $smarty.server.PHP_SELF|@basename != 'new_sidebar.php'}
<body {if isset($T_CURRENT_CTG)}id = "body_{$T_CURRENT_CTG}"{/if} onkeypress = "if (window.eF_js_keypress) eF_js_keypress(event)" >
{/if}
