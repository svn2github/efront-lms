var url = tinyMCE.getParam("java_external_list_url");
if (url != null) {
    // Fix relative
    if (url.charAt(0) != '/' && url.indexOf('://') == -1)
        url = tinyMCE.documentBasePath + "/" + url;

    document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');
}

function init() {
    tinyMCEPopup.resizeToInnerSize();

    document.getElementById("filebrowsercontainer").innerHTML = getBrowserHTML('filebrowser','file','java','java');

    // Image list outsrc
    var html = getJavaListHTML('filebrowser','file','java','java');
    if (html == "")
        document.getElementById("linklistrow").style.display = 'none';
    else
        document.getElementById("linklistcontainer").innerHTML = html;

    var formObj = document.forms[0];
    var swffile   = tinyMCE.getWindowArg('swffile');
    var swfwidth  = '' + tinyMCE.getWindowArg('swfwidth');
    var swfheight = '' + tinyMCE.getWindowArg('swfheight');

    if (swfwidth.indexOf('%')!=-1) {
        formObj.width2.value = "%";
        formObj.width.value  = swfwidth.substring(0,swfwidth.length-1);
    } else {
        formObj.width2.value = "px";
        formObj.width.value  = swfwidth;
    }

    if (swfheight.indexOf('%')!=-1) {
        formObj.height2.value = "%";
        formObj.height.value  = swfheight.substring(0,swfheight.length-1);
    } else {
        formObj.height2.value = "px";
        formObj.height.value  = swfheight;
    }

    formObj.file.value = swffile;
    formObj.insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true);

    selectByValue(formObj, 'linklist', swffile);

    // Handle file browser
    if (isVisible('filebrowser'))
        document.getElementById('file').style.width = '230px';

    // Auto select Java in list
    if (typeof(tinyMCEJavaList) != "undefined" && tinyMCEJavaList.length > 0) {
        for (var i=0; i<formObj.linklist.length; i++) {
            if (formObj.linklist.options[i].value == tinyMCE.getWindowArg('swffile'))
                formObj.linklist.options[i].selected = true;
        }
    }
}

function getJavaListHTML() {
    if (typeof(tinyMCEJavaList) != "undefined" && tinyMCEJavaList.length > 0) {
        var html = "";

        html += '<select id="linklist" name="linklist" style="width: 250px" onfocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="this.form.file.value=this.options[this.selectedIndex].value;">';
        html += '<option value="">---</option>';

        for (var i=0; i<tinyMCEJavaList.length; i++)
            html += '<option value="' + tinyMCEJavaList[i][1] + '">' + tinyMCEJavaList[i][0] + '</option>';

        html += '</select>';

        return html;
    }

    return "";
}

function insertJava() {
    var formObj = document.forms[0];
    var html      = '';
    var file      = formObj.file.value;
    var codebase  = formObj.codebase.value;
    var width     = formObj.width.value;
    var height    = formObj.height.value;
    if (formObj.width2.value=='%') {
        width = width + '%';
    }
    if (formObj.height2.value=='%') {
        height = height + '%';
    }

    if (width == "")
        width = 100;

    if (height == "")
        height = 100;

    //html +='<applet codebase="' + codebase +'" code="' + file + '" />';
    //html +='<table rules="rows" frame="box" cellspacing="4" cellpadding="4" border="2" style="border-style: dotted; border-width: 3px;  vertical-align: top; color: rgb(51, 51, 51); background-color: rgb(204, 255, 153);"><tbody><tr><td align="center" rowspan="1" colspan="4"><br /><applet codebase="' + codebase +'" code="' + file + '" width="'+ width + '" height="' +height+ '" /></applet></td></tr></tbody></table>';
    //html +='<hr><applet codebase="' + codebase +'" code="' + file + '" width="'+ width + '" height="' +height+ '" /></applet><hr>';

//alert('geiaaaa');
//alert(html);
/*  html += ''
        + '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" mce_src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
        + 'border="0" alt="' + file + '" title="' + file + '" class="mceItemJava" />';*/


    html += ''
        + '<img src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" mce_src="' + (tinyMCE.getParam("theme_href") + "/images/spacer.gif") + '" '
        + 'width="' + width + '" height="' + height + '" '
        + 'border="0" alt="' + codebase + '" title="' + file + '" class="mceItemJava" />';
//alert(file);
    tinyMCEPopup.execCommand("mceInsertContent", true, html);
    tinyMCE.selectedInstance.repaint();




//opener.tinyMCE.selectedInstance.execCommand('mceInsertContent',false,html);


    tinyMCEPopup.close();
}
