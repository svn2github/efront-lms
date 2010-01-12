{* smarty template for change_style.php *}

{if $smarty.get.close}
<script language = "JavaScript">
<!--
    self.opener.location.reload(); 
    window.close();
//-->
</script>
{/if}

<script>
<!--
{literal}
    function changePreviewImage(el) {
        var thumb_name = 'thumb_'+el.options[el.selectedIndex].value;
        document.getElementById('img_src').src         = '/css/custom_css/'+el.options[el.selectedIndex].value+'_thumb.jpg';
        document.getElementById('selectedImage').value = '/css/custom_css/'+el.options[el.selectedIndex].value+'_full.jpg';
    }
    
    function test() {
    alert('sd');
    }
    
{/literal}
//-->
</script>
<form name = "change_style_form" action = "" method = "post">
<table align = "center">
    <tr><td>{$smarty.const._SELECTNEWSTYLE}:
        </td><td>
            <select name = "css" onChange = "changePreviewImage(this)">
                <option value = "default">{$smarty.const._PREDEFINED}</option>
                {html_options values = $T_AVAILABLE_CSS output = $T_AVAILABLE_CSS selected = $T_CURRENT_CSS}
            </select>
        </td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr><td colspan = "2" align = "center">
            <input class = "flatButton" type = "submit" name = "submit_set_css" value = "{$smarty.const._SETSTYLE}">
        </td></tr>
</table>
</form>
<br/><br/>

<table align = "center" style = "border: 1px solid black;width:50%;height:50%">
    <tr><td class = "topTitle">{$smarty.const._PREVIEW}</td></tr>
    <tr><td class = "centerAlign">
            <a href = "javascript:void(0)" id = "img_href" onclick = "popUp(document.getElementById('selectedImage').value, '850', '750')"><img id = "img_src" src = "css/custom_css/{$T_CURRENT_CSS}_thumb.jpg" border = "0" onload = "this.style.display='';document.getElementById('notFoundMsg').style.display = 'none'" onError = "this.style.display='none';document.getElementById('notFoundMsg').style.display = ''"></a>
            <span id = "notFoundMsg" class = "emptyCategory" >{$smarty.const._NOPREVIEWIMAGEFOUND}</span>
        </td></tr>
</table>

<input type = "hidden" id = "selectedImage" value = "" />

{if $smarty.post.submit_set_css}
<script>
<!--
    self.opener.parent.location.reload();
//-->
</script>
{/if}