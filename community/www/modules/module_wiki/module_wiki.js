if ($('wiki_frame')) {

	if (top.sideframe) {
		window.document.getElementById("wiki_frame").style.height=parseInt(top.sideframe.document.documentElement.scrollHeight-60)+ "px";
	} else {
		window.document.getElementById("wiki_frame").style.height=parseInt(document.documentElement.scrollHeight-140)+ "px";
	}
}