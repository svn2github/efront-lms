//created in 2007/07/31 by makriria from http://developer.mozilla.org/en/docs/skins/devmo/devmo.js

// The following toggleSidebar function was added by DRichardson (dria)
// on Aug 29th, 2005.
/*
function showSidebarToggle() {
  if (document.createTextNode) {
    // Uses DOM calls to avoid document.write + XHTML issues

    var linkHolder = document.getElementById('sidebarlinkholder');
    if (!linkHolder) return;

    var outerSpan = document.createElement('span');
    outerSpan.className = 'sidebartoggle';

    var sidebartoggleLink = document.createElement('a');
    sidebartoggleLink.id = 'sidebartogglelink';
    sidebartoggleLink.className = 'internal';
    sidebartoggleLink.href = 'javascript:toggleSidebar()';
    sidebartoggleLink.appendChild(document.createTextNode(sidebarHideText));

    outerSpan.appendChild(document.createTextNode('['));
    outerSpan.appendChild(sidebartoggleLink);
    outerSpan.appendChild(document.createTextNode(']'));

    linkHolder.appendChild(document.createTextNode(' '));
    linkHolder.appendChild(outerSpan);

    var cookiePos = document.cookie.indexOf("hidesidebar=");
    if (cookiePos > -1 && document.cookie.charAt(cookiePos + 8) == 1)
     toggleSidebar();
  }
}*/

/*
function toggleSidebar() {
	var sidebar = document.getElementById('sidebar_right');
	var content = document.getElementById('content');
	var sidetoggleLink = document.getElementById('sidebartogglelink')

	if (sidebar && sidetoggleLink && sidebar.style.display == 'none') {
		changeText(sidetoggleLink, sidebarHideText);
			sidebar.style.display = 'block';
			content.style.marginRight = '230px';
		} else {
		changeText(sidetoggleLink, sidebarShowText);
			sidebar.style.display = 'none';
			content.style.marginRight = '0px;';
		}
}
*/
/*
function pause(millisecondi)
{
    var now = new Date();
    var exitTime = now.getTime() + millisecondi;

    while(true)
    {
        now = new Date();
        if(now.getTime() > exitTime) return;
    }
}*/

function sidebarUp() {
//alert('a');

var content = document.getElementById('singleColumn');
	var sidebarslideup = document.getElementById('sidebarslideup');
	var sidebarslidedown = document.getElementById('sidebarslidedown');
		var sidemenu_td = document.getElementById('sideMenu_td');
		var sidemenu = document.getElementById('sideMenu');
		sidemenu.style.display = 'none';
		sidemenu_td.style.width = '0px';
	sidebarslideup.style.display = 'none';
	sidebarslidedown.style.display = 'inline';
		/*	content.style.marginRight = '0px';*/
	document.cookie = 'hidesidebar=1';
}

function sidebarDown() {
	var content = document.getElementById('singleColumn');
	var sidebarslideup = document.getElementById('sidebarslideup');
	var sidebarslidedown = document.getElementById('sidebarslidedown');
		var sidemenu_td = document.getElementById('sideMenu_td');
		var sidemenu = document.getElementById('sideMenu');
		sidemenu.style.display = 'block';
		sidemenu_td.style.width = '225px';
	sidebarslideup.style.display = 'inline';
	sidebarslidedown.style.display = 'none';
	document.cookie = 'hidesidebar=0';
}


function sidebarCheck() {		
	var content = document.getElementById('singleColumn');

	var sidebarslideup = document.getElementById('sidebarslideup');
	var sidebarslidedown = document.getElementById('sidebarslidedown');
		var sidemenu_td = document.getElementById('sideMenu_td');
		var sidemenu = document.getElementById('sideMenu');
	var cookieSidebarPos = document.cookie.indexOf("hidesidebar=");
	if (cookieSidebarPos > -1 && document.cookie.charAt(cookieSidebarPos + 12) == 1) {
		sidemenu.style.display = 'none';	
		sidemenu_td.style.width = '0px';
		sidebarslideup.style.display = 'none';
		sidebarslidedown.style.display = 'inline';
	}
}