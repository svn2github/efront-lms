<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <base href = "{$smarty.const.G_SERVERNAME}">
    <meta http-equiv = "Content-Language" content = "{$smarty.const._HEADERLANGUAGETAG}">
    <meta http-equiv = "keywords"         content = "education">
    <meta http-equiv = "description"      content = "Collaborative Elearning Platform">
    <meta http-equiv = "Content-Type"     content = "text/html; charset = utf-8">
    <link rel="shortcut icon" href="images/favicon.ico">
    {if $T_CONFIGURATION.css}<link rel = "stylesheet" type = "text/css" href = "{$smarty.const.G_CUSTOMCSSLINK}{$T_CONFIGURATION.css}">
    {else}<link rel = "stylesheet" type = "text/css" href = "css/css_global.php">
    {/if}

    {*///MODULES LINK STYLESHEETS*}
    {foreach name = 'module_css_list' item = item key = key from = $T_MODULE_CSS}
    <link rel = "stylesheet" type = "text/css" href = "{$item}" />
    {/foreach}

    <title>{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{else}{$smarty.const._EFRONT}{/if} | {if $T_CONFIGURATION.site_moto}{$T_CONFIGURATION.site_moto}{else}{$smarty.const._THENEWFORMOFADDITIVELEARNING}{/if}</title>
{if $T_HEADER_EDITOR}
    <script type = "text/javascript" src = "editor/tiny_mce/tiny_mce_gzip.js"></script>
    {literal}
        <script type = "text/javascript" >
        <!--
            tinyMCE_GZ.init({
                mode : "specific_textareas",
                editor_selector : "mceEditor,templateEditor,intermediateEditor,simpleEditor",
                plugins : 'table,save,advhr,advimage,advlink,emotions,iespell,preview,zoom,java,searchreplace,print,contextmenu,media,mathtype,paste,fullscreen,template,index_link,lessons_info',
                themes : 'simple,advanced',
                languages : '{/literal}{$smarty.const._CURRENTLANGUAGESYMBOL}{literal}', //theoritically, here must be all suported languages but tinymce reads only the last one (possibly a bug). So we load only the session language(makriria 2207/07/30)
                disk_cache : false,
                debug : false
        });
        // -->
        </script>
    {/literal}
    <script type = "text/javascript" src = "editor/efront_init_tiny_mce.php"></script>
{/if}
    <script type = "text/javascript" >{if $T_BROWSER == 'IE6'}var globalImageExtension = 'gif';{else}var globalImageExtension = 'png';{/if}</script>

    {foreach name = 'scripts_list' item = item key = key from = $T_HEADER_LOAD_SCRIPTS}
    <script type = "text/javascript" src = "js/{$item}.php"> </script>
    {/foreach}

    {*///MODULES LINK JAVASCRIPT CODE*}
    {foreach name = 'module_scripts_list' item = item key = key from = $T_MODULE_JS}
    <script type = "text/javascript" src = "{$item}"> </script>
    {/foreach}

    <script type = "text/javascript">
        var ajaxObjects    = new Array();
        top.document.title = "{if $T_CONFIGURATION.site_name}{$T_CONFIGURATION.site_name}{else}{$smarty.const._EFRONT}{/if} | {if $T_TITLE_BAR}{$T_TITLE_BAR}{else}{if $T_CONFIGURATION.site_moto}{$T_CONFIGURATION.site_moto}{else}{$smarty.const._THENEWFORMOFADDITIVELEARNING}{/if}{/if}";
        if (window.name == 'POPUP_FRAME') var popup=1;
    </script>
</head>
<body id = "body_{$T_CURRENT_CTG}" onkeypress = "if (window.eF_js_keypress) eF_js_keypress(event)">
{*<body id = "body_{$T_CURRENT_CTG}" onload = "jeF_initialize()" onkeypress = "eF_js_keypress(event)">*}



