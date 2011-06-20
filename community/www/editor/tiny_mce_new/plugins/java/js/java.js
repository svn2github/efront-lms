var JavaDialog = {
	preInit : function() {
		var url;

		tinyMCEPopup.requireLangPack();

		if (url = tinyMCEPopup.getParam("external_java_list_url"))
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	},

	init : function(ed) {
	},
	insert : function(file, title) {
	var ed = tinyMCEPopup.editor, t = this, f = document.forms[0];
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

	html +='<table class="mceJava" width="'+width+'" height="'+height+'" rules="rows" frame="box" cellspacing="4" cellpadding="4" border="2" style="border-style: dotted; border-width: 3px;  vertical-align: top; color: rgb(51, 51, 51); background-color: rgb(204, 255, 153);"><tbody><tr><td align="center" valign="center"><applet codebase="'+ codebase +'" code="'+file+ '" width="' + width + '" height="'+ height+ '"/></applet><img src="editor/tiny_mce_new/plugins/java/img/java.gif" /></td></tr></tbody></table>';
    //html +='<applet codebase="' + codebase +'" code="' + file + '" />';
    //html +='<table rules="rows" frame="box" cellspacing="4" cellpadding="4" border="2" style="border-style: dotted; border-width: 3px;  vertical-align: top; color: rgb(51, 51, 51); background-color: rgb(204, 255, 153);"><tbody><tr><td align="center" rowspan="1" colspan="4"><br /><applet codebase="' + codebase +'" code="' + file + '" width="'+ width + '" height="' +height+ '" /></applet></td></tr></tbody></table>';
    //html +='<hr><applet codebase="' + codebase +'" code="' + file + '" width="'+ width + '" height="' +height+ '" /></applet><hr>';


    //tinyMCEPopup.execCommand("mceInsertContent", true, html);
	ed.execCommand('mceInsertContent', false, html);
    //tinyMCE.selectedInstance.repaint();
	//ed.execCommand('mceRepaint');



		tinyMCEPopup.close();
	}
};

JavaDialog.preInit();
tinyMCEPopup.onInit.add(JavaDialog.init, JavaDialog);
