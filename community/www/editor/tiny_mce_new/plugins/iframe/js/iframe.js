var IframeDialog = {
	init : function(ed) {
		var dom = ed.dom, f = document.forms[0], n = ed.selection.getNode(), w, h, b;
		
		f.name.value = dom.getAttrib(n, 'name');		
		w = dom.getAttrib(n, 'width');
		f.width.value = w ? parseInt(w) : '';
		selectByValue(f, 'width2', w.indexOf('%') != -1 ? '%' : 'px');
		
		h = dom.getAttrib(n, 'height');
		f.height.value = h ? parseInt(h) : '';
		selectByValue(f, 'height2', h.indexOf('%') != -1 ? '%' : 'px');
		
		f.scroll.value = dom.getAttrib(n, 'scrolling');	
		if (f.scroll.value!='yes' && f.scroll.value!='no') f.scroll.value = 'auto';
		
		b = dom.getAttrib(n, 'border');
		selectByValue(f, 'border', b.indexOf('0') != -1 ? '0' : '1');
		
	},

	update : function() {
		var ed = tinyMCEPopup.editor, h, f = document.forms[0];

		h = '<iframe';
	
		if (f.document.value)
			h += ' src="' + f.document.value + '"';
		
		if (f.name.value)
			h += ' name="' + f.name.value + '"';
		
		if (f.width.value)
			h += ' width="' + f.width.value + (f.width2.value == '%' ? '%' : '') + '"';

		if (f.height.value)
			h += ' height="' + f.height.value + (f.height2.value == '%' ? '%' : '') + '"';

		if (f.scroll.value)
			h += ' scrolling="' + f.scroll.value + '"';
		
		if (f.border.value)
			h += ' border="' + f.border.value + '" frameborder="' + f.border.value + '"';

		h += ' ></iframe>';

		ed.execCommand("mceInsertContent", false, h);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(IframeDialog.init, IframeDialog);
