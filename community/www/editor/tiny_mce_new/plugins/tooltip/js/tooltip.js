tinyMCEPopup.requireLangPack();

var Tooltip = {

	init : function(ed) {
	},
	insert : function(file, title) {
	var ed = tinyMCEPopup.editor, t = this, f = document.forms[0];
	var formObj = document.forms[0];
    var html      = '';
    var tooltipTerm      	= formObj.term.value;
    var tooltipExplanation  = formObj.explanation.value;

	html +='<a class="glossary" onmouseover="new Tip(this, \''+tooltipExplanation+'\')" href="javascript:void(0)">'+tooltipTerm+'</a>';
	ed.execCommand('mceInsertContent', false, html);
		tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(Tooltip.init, Tooltip);