 <script type = "text/javascript" src = "editor/tiny_mce_new/tiny_mce_gzip.js"></script>
     {literal}

        <script type = "text/javascript">
        <!--
            tinyMCE_GZ.init({
                mode : "specific_textareas",
                editor_selector : "mceEditor,templateEditor,simpleEditor,digestEditor",
                plugins : 'java,asciimath,asciisvg,table,style,save,advhr,advimage,advlink,emotions,iespell,preview,zoom,searchreplace,print,contextmenu,media,paste,directionality,fullscreen,index_link',
                themes : 'simple,advanced',
                languages : '{/literal}{$smarty.const._CURRENTLANGUAGESYMBOL}{literal}', //theoritically, here must be all suported languages but tinymce reads only the last one (possibly a bug). So we load only the session language(makriria 2207/07/30)
                disk_cache : true, // it was false... check lang issue
                debug : false,
                events : "onClick",
                handle_event_callback : "myHandleEvent"
        });
        // -->

        </script>
 {/literal}
 <script type="text/javascript" src="editor/tiny_mce_new/plugins/asciimath/js/ASCIIMathMLwFallback.js"></script>
 <script type="text/javascript" src="editor/tiny_mce_new/plugins/asciisvg/js/ASCIIsvgPI.js"></script>
 {literal}
 <script type="text/javascript">
  var AScgiloc = 'editor/tiny_mce_new/php/svgimg.php';
  var AMTcgiloc = '{/literal}{$T_CONFIGURATION.math_server}{literal}';
 </script>
 {/literal}
 <script type = "text/javascript" src = "editor/tiny_mce_new/efront_init_tiny_mce.php"></script>
