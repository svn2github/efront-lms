
	var optionsChecker = {};
	
	optionsChecker.init = function() {
		//set check options here
		optionsChecker.checkFlash = 1;
		optionsChecker.checkAcrobat = 1;
		optionsChecker.checkScreenRes = 1;
		optionsChecker.checkPopupBlocker = 1;
		optionsChecker.checkClient = 1;
		optionsChecker.checkCookies = 1;
		optionsChecker.checkJava = 1;
		//set valid clients in an array (values are: IE6|IE7|IE8|IE9|Firefox 3|Firefox 4|Chrome 5|Safari 5|Firefox|Chrome|Safari)
		optionsChecker.validClients = ["IE7","IE8","Firefox","Chrome","Safari", "IE9"];		
		optionsChecker.screenRes = {};
		optionsChecker.screenRes.minWidth = 1024;
		optionsChecker.screenRes.minHeight = 768;

		
		optionsChecker.define();
		optionsChecker.perform();
		optionsChecker.perform_acrobat_detection();
		optionsChecker.perform_java_detection();
	}

	optionsChecker.define = function() {
		optionsChecker.screenResFunction = function() {
			if (window.screen.height < optionsChecker.screenRes.minHeight || window.screen.width < optionsChecker.screenRes.minWidth) {
				return false;
			}
			return true;
		}
		
		optionsChecker.browserRegex = function(browser) {
			switch(browser) {
				case "IE6":
					optionsChecker.supportedClients[0] = /MSIE 6/g;
					break;
				case "IE7":
					optionsChecker.supportedClients[1] = /MSIE 7/g;
					break;
				case "IE8":
					optionsChecker.supportedClients[2] = /MSIE 8/g;
					break;
				case "Firefox 3":
					optionsChecker.supportedClients[3] = /Firefox\/3/g;
					break;
				case "Chrome 5":
					optionsChecker.supportedClients[4] = /Chrome\/5/g;
					break;
				case "Safari 5":
					optionsChecker.supportedClients[5] = /Safari\/5/g;
					break;
				case "Firefox 4":
					optionsChecker.supportedClients[6] = /Firefox\/4/g;
					break;
				case "Chrome":
					optionsChecker.supportedClients[7] = /Chrome/g;
					break;
				case "Safari":
					optionsChecker.supportedClients[8] = /Safari/g;
					break;
				case "Firefox":
					optionsChecker.supportedClients[9] = /Firefox/g;
					break;
				case "IE9":
					optionsChecker.supportedClients[10] = /MSIE 9/g;
					break;
			}
		}
		
		optionsChecker.cookieEnabledFunction = function() {
			if (navigator.cookieEnabled) {
				return true;
			} else {
				return false;
			}
		}
		
		optionsChecker.flashFunction = function() {
			if (navigator.mimeTypes["application/x-shockwave-flash"]) {
				return true;
			} else {
					try {
						axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.8");
					} catch (e) {
						return false;
					}
					return true;
			}
			return false;
		}
		
		optionsChecker.popupBlockerFunction = function() {
			var testWindow = window.open('', '', 'left=10,width=100,height=100');
			if (!testWindow) {
				return false;
			}
			testWindow.close();
			return true;
		}
		
		optionsChecker.supportedClientsFunction = function() {
			//compile client regex
			optionsChecker.supportedClients = [];
			for (i in optionsChecker.validClients) {
				optionsChecker.browserRegex(optionsChecker.validClients[i]);
			}
			var userAgent = navigator.userAgent;
			for(i in optionsChecker.supportedClients) {
				if (userAgent.search(optionsChecker.supportedClients[i]) > 1 ) {
					if (navigator.userAgent.indexOf("MSIE 6") > 0) {
						return "IE6";
					} else {
						return true;
					}
				}
			}
			return false;
		}
		optionsChecker.perform_acrobat_detection = function()
			{ 
			  //
			  // The returned object
			  // 
			  var browser_info = {
			    name: null,
			    acrobat : null,
			    acrobat_ver : null
			  };
			  
			  if(navigator && (navigator.userAgent.toLowerCase()).indexOf("chrome") > -1) browser_info.name = "chrome";
			  else if(navigator && (navigator.userAgent.toLowerCase()).indexOf("msie") > -1) browser_info.name = "ie";
			  else if(navigator && (navigator.userAgent.toLowerCase()).indexOf("firefox") > -1) browser_info.name = "firefox";
			  else if(navigator && (navigator.userAgent.toLowerCase()).indexOf("msie") > -1) browser_info.name = "other";
				
				
			 try
			 {
			  if(browser_info.name == "ie")
			  {          
			   var control = null;
			   
			   //
			   // load the activeX control
			   //                
			   try
			   {
			    // AcroPDF.PDF is used by version 7 and later
			    control = new ActiveXObject('AcroPDF.PDF');
			   }
			   catch (e){}
			   
			   if (!control)
			   {
			    try
			    {
			     // PDF.PdfCtrl is used by version 6 and earlier
			     control = new ActiveXObject('PDF.PdfCtrl');
			    }
			    catch (e) {}
			   }
			   
			   if(!control)
			   {     
			    browser_info.acrobat == null;
			    return browser_info;  
			   }
			   
			   version = control.GetVersions().split(',');
			   version = version[0].split('=');
			   browser_info.acrobat = "installed";
			   browser_info.acrobat_ver = parseFloat(version[1]);                
			  }
			  else if(browser_info.name == "chrome")
			  {
			   for(key in navigator.plugins)
			   {
			    if(navigator.plugins[key].name == "Chrome PDF Viewer" || navigator.plugins[key].name == "Adobe Acrobat")
			    {
			     browser_info.acrobat = "installed";
			     browser_info.acrobat_ver = parseInt(navigator.plugins[key].version) || "Chome PDF Viewer";
			    }
			   } 
			  }
			  //
			  // NS3+, Opera3+, IE5+ Mac, Safari (support plugin array):  check for Acrobat plugin in plugin array
			  //    
			  else if(navigator.plugins != null)
			  {      
			   var acrobat = navigator.plugins['Adobe Acrobat'];
			   if(acrobat == null)
			   {           
			    browser_info.acrobat = null;
			    return browser_info;
			   }
			   browser_info.acrobat = "installed";
			   browser_info.acrobat_ver = parseInt(acrobat.version[0]);                   
			  }
			  
			  
			 }
			 catch(e)
			 {
			  browser_info.acrobat_ver = null;
			 }
			  return browser_info.acrobat;
			}
		
		optionsChecker.perform_java_detection = function()
		{
			for (x = 0; x < navigator.plugins.length; x++) {
		        if (navigator.plugins[x].name.indexOf('Java(TM)') != -1) {
	//	        	return true;
		        }
		    }
			if (navigator.javaEnabled()) {
	//				return true;
			}
			return false;
		}
		
	}
	
	optionsChecker.perform = function() {
		document.write("<table width=\"100%\">");
		if (optionsChecker.checkClient == "1") {
			document.write("<tr><td align=\"left\">"+translationcheckBrowser+"</td>");
			var clientCheck = optionsChecker.supportedClientsFunction();
			if (clientCheck == "IE6") {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckBrowserIE+"' /></td></tr>");
			} else if (clientCheck > 0) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckBrowserFailed+"' /></td></tr>");
			}
		}
		
		if (optionsChecker.checkScreenRes == "1") {
			document.write("<tr><td>"+translationcheckResolution+"</td>");
			var srCheck = optionsChecker.screenResFunction();
			if (srCheck) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else { 
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+ translationcheckResolutionFailed + optionsChecker.screenRes.minWidth+"x"+optionsChecker.screenRes.minHeight+".' /></td></tr>");
			}
		}
		
		if (optionsChecker.checkPopupBlocker == "1") {
			document.write("<tr><td>"+translationcheckPopupBlocker+"</td>");
			var popCheck = optionsChecker.popupBlockerFunction();
			if (popCheck) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckPopupBlockerFailed+"' /></td></tr>");
			}
		}
		
		if (optionsChecker.checkCookies == "1") {
			document.write("<tr><td>"+translationcheckCookies+"</td>");
			var cookieCheck = optionsChecker.cookieEnabledFunction();
			if (cookieCheck) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckCookiesFailed+"' /></td></tr>");
			}
		}
		
		if (optionsChecker.checkFlash == "1") {
			document.write("<tr><td>"+translationcheckFlash+"</td>");
			var flashCheck = optionsChecker.flashFunction();
			if (flashCheck) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckFlashFailed+"' /></span><a href = \"http://get.adobe.com/flashplayer\" target = \"_blank\"><img src='themes/default/images/16x16/help.png' title='"+translationshelp+"' /></a></td></tr>");
			}
		}
		
		if (optionsChecker.checkAcrobat) {
			document.write("<tr><td>"+translationcheckAcrobat+"</td>");
			var acrobatCheck = optionsChecker.perform_acrobat_detection();
			if (acrobatCheck == "installed") {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckAcrobatFailed+"' /><a href = \"http://get.adobe.com/reader\" target = \"_blank\"><img src='themes/default/images/16x16/help.png' title='"+translationshelp+"' /></a></td></tr>");
			}
		}
		
		if (optionsChecker.checkJava == "1") {
			document.write("<tr><td>"+translationcheckJava+"</td>");
			var javaCheck = optionsChecker.perform_java_detection();
			if (javaCheck == true) {
				document.write("<td><img src='themes/default/images/16x16/success.png' /></td></tr>");
			} else {
				document.write("<td><img src='themes/default/images/16x16/error_delete.png' title='"+translationcheckJavaFailed+"' /><a href = \"http://www.java.com/en/download/index.jsp\" target = \"_blank\"><img src='themes/default/images/16x16/help.png' title='"+translationshelp+"' /></a></td></tr>");
			}
		}
		
		//close span
		document.write("</table>");
	}

	optionsChecker.init();