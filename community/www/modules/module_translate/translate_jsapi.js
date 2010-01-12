if (!window['google']) {
window['google'] = {};
}
if (!window['google']['loader']) {
window['google']['loader'] = {};
google.loader.ServiceBase = 'http://www.google.com/uds';
google.loader.GoogleApisBase = 'http://ajax.googleapis.com/ajax';
google.loader.ApiKey = 'notsupplied';
google.loader.KeyVerified = true;
google.loader.LoadFailure = false;
google.loader.ClientLocation = {"latitude":37.983,"longitude":23.733,"address":{"city":"Athens","region":"Attica","country":"Greece","country_code":"GR"}};
google.loader.AdditionalParams = '';
(function() { 
function w(a){if(a in A){return A[a]}return A[a]=navigator.userAgent.toLowerCase().indexOf(a)!=-1}
var A={};function E(){return w("msie")}
function F(){return w("safari")||w("konqueror")}
function K(a,b){var c=function(){}
;c.prototype=b.prototype;a.I=b.prototype;a.prototype=new c}
function P(a,b){var c=a._JSAPI_boundArgs||[];c=c.concat(Array.prototype.slice.call(arguments,2));if(typeof a._JSAPI_boundSelf!="undefined"){b=a._JSAPI_boundSelf}if(typeof a._JSAPI_boundFn!="undefined"){a=a._JSAPI_boundFn}var d=function(){var e=c.concat(Array.prototype.slice.call(arguments));return a.apply(b,e)}
;d._JSAPI_boundArgs=c;d._JSAPI_boundSelf=b;d._JSAPI_boundFn=a;return d}
function B(a){var b=new Error(a);b.toString=function(){return this.message}
;return b}
;
var i={};var x={};var G={};var U={};var s=null;var M=false;function S(a,b,c){var d=i[":"+a];if(!d){throw B("Module: '"+a+"' not found!");}else{if(c&&!c["language"]&&c["locale"]){c["language"]=c["locale"]}var e=c&&c["callback"]!=null;if(e&&!d.n()){throw B("Module: '"+a+"' must be loaded before DOM onLoad!");}else if(e){if(d.i(b,c)){window.setTimeout(c["callback"],0)}else{d.j(b,c)}}else{if(!d.i(b,c)){d.j(b,c)}}}}
function Z(a,b){if(b){Y(a)}else{z(window,"load",a)}}
function z(a,b,c){if(a.addEventListener){a.addEventListener(b,c,false)}else if(a.attachEvent){a.attachEvent("on"+b,c)}else{var d=a["on"+b];if(d!=null){a["on"+b]=Q([c,d])}a["on"+b]=c}}
function Q(a){return function(){for(var b=0;b<a.length;b++){a[b]()}}
}
var p=[];function Y(a){if(p.length==0){z(window,"load",t);if(!E()&&!F()&&w("mozilla")||window.opera){window.addEventListener("DOMContentLoaded",t,false)}else if(E()){window.setTimeout(H,10);document.attachEvent("onreadystatechange",J)}else if(F()){window.setTimeout(I,10)}}p.push(a)}
function H(){try{if(p.length>0){document.documentElement.doScroll("left")}}catch(a){window.setTimeout(H,10);return}t()}
var L={loaded:true,complete:true};function J(){if(L[document.readyState]){document.detachEvent("onreadystatechange",J);t()}}
function I(){if(L[document.readyState]){t()}else if(p.length>0){window.setTimeout(I,10)}}
function t(){for(var a=0;a<p.length;a++){p[a]()}p.length=0}
function X(a){var b=window.location.href;var c;var d=b.length;for(var e in a){var f=b.indexOf(e);if(f!=-1&&f<d){c=e;d=f}}s=c?a[c]:null}
function r(a,b,c){if(c){var d;if(a=="script"){d=document.createElement("script");d.type="text/javascript";d.src=b}else if(a=="css"){d=document.createElement("link");d.type="text/css";d.href=b;d.rel="stylesheet"}var e=document.getElementsByTagName("head")[0];if(!e){e=document.body.parentNode.appendChild(document.createElement("head"))}e.appendChild(d)}else{if(a=="script"){document.write('<script src="'+b+'" type="text/javascript"><\/script>')}else if(a=="css"){document.write('<link href="'+b+'" type="text/css" rel="stylesheet"></link>'
)}}}
function k(a,b){var c=a.split(/\./);var d=window;for(var e=0;e<c.length-1;e++){if(!d[c[e]]){d[c[e]]={}}d=d[c[e]]}d[c[c.length-1]]=b}
function R(a,b,c){a[b]=c}
function V(a){x=a}
function W(a){for(var b in a){if(typeof b=="string"&&b&&b.charAt(0)==":"&&!i[b]){i[b]=new n(b.substring(1),a[b])}}}
k("google.load",S);k("google.setOnLoadCallback",Z);k("google.loader.writeLoadTag",r);k("google.loader.setApiKeyLookupMap",X);k("google.loader.callbacks",G);k("google.loader.eval",U);k("google.loader.rfm",V);k("google.loader.rpl",W);k("google_exportSymbol",k);k("google_exportProperty",R);
function h(a){this.a=a;this.l={};this.b={};this.initialLoad=true}
h.prototype.d=function(a,b){var c="";if(b!=undefined){if(b["language"]!=undefined){c+="&hl="+encodeURIComponent(b["language"])}if(b["nocss"]!=undefined){c+="&output="+encodeURIComponent("nocss="+b["nocss"])}if(b["nooldnames"]!=undefined){c+="&nooldnames="+encodeURIComponent(b["nooldnames"])}if(b["packages"]!=undefined){c+="&packages="+encodeURIComponent(b["packages"])}if(b["callback"]!=null){c+="&async=2"}if(b["other_params"]!=undefined){c+="&"+b["other_params"]}}if(!this.initialLoad){if(google[this.a]
&&google[this.a].JSHash){c+="&sig="+encodeURIComponent(google[this.a].JSHash)}var d=[];for(var e in this.l){if(e.charAt(0)==":"){d.push(e.substring(1))}}for(var e in this.b){if(e.charAt(0)==":"){d.push(e.substring(1))}}c+="&have="+encodeURIComponent(d.join(","))}if(s!=null&&!M){c+="&key="+encodeURIComponent(s);M=true}return google.loader.ServiceBase+"/?file="+this.a+"&v="+a+google.loader.AdditionalParams+c}
;h.prototype.p=function(a){var b=null;if(a){b=a["packages"]}var c=null;if(b){if(typeof b=="string"){c=[a["packages"]]}else if(b.length){c=[];for(var d=0;d<b.length;d++){if(typeof b[d]=="string"){c.push(b[d].replace(/^\s*|\s*$/,"").toLowerCase())}}}}if(!c){c=["default"]}var e=[];for(var d=0;d<c.length;d++){if(!this.l[":"+c[d]]){e.push(c[d])}}return e}
;h.prototype.j=function(a,b){var c=this.p(b);var d=b&&b["callback"]!=null;if(d){var e=new y(b["callback"])}var f=[];for(var j=c.length-1;j>=0;j--){var g=c[j];if(d){e.t(g)}if(this.b[":"+g]){c.splice(j,1);if(d){this.b[":"+g].push(e)}}else{f.push(g)}}if(c.length){if(b&&b["packages"]){b["packages"]=c.sort().join(",")}if(!b&&x[":"+this.a]!=null&&x[":"+this.a].versions[":"+a]!=null&&!google.loader.AdditionalParams&&this.initialLoad){var m=x[":"+this.a];google[this.a]=google[this.a]||{};for(var u in m.properties)
{if(u&&u.charAt(0)==":"){google[this.a][u.substring(1)]=m.properties[u]}}r("script",google.loader.ServiceBase+m.path+m.js,d);if(m.css){r("css",google.loader.ServiceBase+m.path+m.css,d)}}else{r("script",this.d(a,b),d)}if(this.initialLoad){this.initialLoad=false}for(var j=0;j<f.length;j++){var g=f[j];this.b[":"+g]=[];if(d){this.b[":"+g].push(e)}}}}
;h.prototype.g=function(a){for(var b=0;b<a.components.length;b++){this.l[":"+a.components[b]]=true;var c=this.b[":"+a.components[b]];if(c){for(var d=0;d<c.length;d++){c[d].v(a.components[b])}delete this.b[":"+a.components[b]]}}v("hl",this.a)}
;h.prototype.i=function(a,b){return this.p(b).length==0}
;h.prototype.n=function(){return true}
;function y(a){this.u=a;this.k={};this.m=0}
y.prototype.t=function(a){this.m++;this.k[":"+a]=true}
;y.prototype.v=function(a){if(this.k[":"+a]){this.k[":"+a]=false;this.m--;if(this.m==0){window.setTimeout(this.u,0)}}}
;function T(a){i[":"+a.module].g(a)}
k("google.loader.loaded",T);
function l(a,b,c,d,e,f,j,g){this.a=a;this.B=b;this.A=c;this.q=d;this.s=e;this.z=f;this.r=j||{};this.e=false;this.o=false;this.f=[];if(typeof g=="string"){this.h=g}else if(g){this.h=b}else{this.h=null}this.F=g;G[this.a]=P(this.g,this)}
K(l,h);l.prototype.j=function(a,b){var c=b&&b["callback"]!=null;if(c){this.f.push(b["callback"]);b["callback"]="google.loader.callbacks."+this.a}else{this.e=true}r("script",this.d(a,b),c)}
;l.prototype.i=function(a,b){var c=b&&b["callback"]!=null;if(c){return this.o}else{return this.e}}
;l.prototype.g=function(){this.o=true;for(var a=0;a<this.f.length;a++){window.setTimeout(this.f[a],0)}this.f=[]}
;l.prototype.d=function(a,b){var c="";if(this.q!=null){c+="&"+this.q+"="+encodeURIComponent(s?s:google.loader.ApiKey)}if(this.s!=null){c+="&"+this.s+"="+encodeURIComponent(a)}var d=google.loader.ServiceBase.charAt(4)=="s";var e;if(d&&this.h){e=this.h}else{e=this.B;d=false}if(b!=null){for(var f in b){if(this.r[":"+f]!=null){var j=b[f];var g=this.r[":"+f];if(typeof g=="string"){c+="&"+g+"="+encodeURIComponent(j)}else{c+="&"+g(j)}}else if(f=="other_params"){c+="&"+b[f]}else if(f=="base_domain"){e=e.replace(
/^[^\/]*/,b[f]);d=false}}}google[this.a]={};if(!this.A&&c!=""){c="?"+c.substring(1)}v("el",this.a);return(d?"https":"http")+"://"+e+c}
;l.prototype.n=function(){return this.z}
;
function n(a,b){this.a=a;this.c=b;this.e=false}
K(n,h);n.prototype.j=function(a,b){this.e=true;r("script",this.d(a,b),false)}
;n.prototype.i=function(a,b){return this.e}
;n.prototype.g=function(){}
;n.prototype.d=function(a,b){if(!this.c["versions"][":"+a]){if(this.c["aliases"]){var c=this.c["aliases"][":"+a];if(c){a=c}}if(!this.c["versions"][":"+a]){throw B("Module: '"+this.a+"' with version '"+a+"' not found!");}}var d=b&&b["uncompressed"]?"uncompressed":"compressed";var e=google.loader.GoogleApisBase+"/libs/"+this.a+"/"+a+"/"+this.c["versions"][":"+a][d];v("el",this.a);return e}
;n.prototype.n=function(){return false}
;
function o(){}
var D=o.w=false;var N=o.C=5;var q=o.H=[];var O=o.G=function(){if(!D){z(window,"unload",C);D=(o.w=true)}}
;var v=o.record=function(a,b){O();var c=a+(b?"|"+b:"");q.push("r"+q.length+"="+encodeURIComponent(c));var d=q.length>N?0:15000;window.setTimeout(C,d)}
;var C=o.D=function(){if(q.length){var a=new Image;a.src=google.loader.ServiceBase+"/stats?"+q.join("&")+"&nocache="+Number(new Date);q.length=0}}
;k("google.loader.recordStat",v);
i[":search"]=new h("search");i[":feeds"]=new h("feeds");i[":language"]=new h("language");i[":elements"]=new h("elements");i[":maps"]=new l("maps","maps.google.com/maps?file=googleapi",true,"key","v",true,{":language":"hl",":callback":function(a){return"callback="+encodeURIComponent(a)+"&async=2"}
},"maps-api-ssl.google.com/maps?file=googleapi");i[":gdata"]=new h("gdata");i[":sharing"]=new l("sharing","www.google.com/s2/sharing/js",false,"key","v",false,{":locale":"hl"});i[":annotations"]=new l("annotations","www.google.com/reviews/scripts/annotations_bootstrap.js",false,"key","v",true,{":language":"hl",":country":"gl",":callback":"callback"});i[":visualization"]=new h("visualization");i[":books"]=new l("books","books.google.com/books/api.js",false,"key","v",true,{":language":"hl",":callback"
:"callback"});i[":earth"]=new h("earth");

 })()

google.loader.rfm({":feeds":{"versions":{":1":"1",":1.0":"1"},"path":"/api/feeds/1.0/9485f1e38d6efe511beac9408eb45c79/","js":"default+el.I.js","css":"default.css","properties":{":JSHash":"9485f1e38d6efe511beac9408eb45c79",":Version":"1.0"}},":search":{"versions":{":1":"1",":1.0":"1"},"path":"/api/search/1.0/9f362c5f2d2bc9f3c3f4585dc7dae979/","js":"default+el.I.js","css":"default.css","properties":{":JSHash":"9f362c5f2d2bc9f3c3f4585dc7dae979",":NoOldNames":false,":Version":"1.0"}},":language":{"versions":{":1":"1",":1.0":"1"},"path":"/api/language/1.0/0f2391a71287b4b0b02372874207fa7e/","js":"default+el.I.js","properties":{":JSHash":"0f2391a71287b4b0b02372874207fa7e",":Version":"1.0"}},":earth":{"versions":{":1":"1",":1.0":"1"},"path":"/api/earth/1.0/ab3a093c3613b0ef66e8aaf4edb7d3ea/","js":"default.I.js","properties":{":JSHash":"ab3a093c3613b0ef66e8aaf4edb7d3ea",":Version":"1.0"}},":gdata":{"versions":{":1":"1",":1.2":"1"},"path":"/api/gdata/1.2/5bd3c24f42bbfa0c36f04d54e9a07bae/","js":"default.I.js","properties":{":JSHash":"5bd3c24f42bbfa0c36f04d54e9a07bae",":Version":"1.2"}}});
google.loader.rpl({":scriptaculous":{"versions":{":1.8.1":{"uncompressed":"scriptaculous.js","compressed":"scriptaculous.js"}},"aliases":{":1.8":"1.8.1",":1":"1.8.1"}},":mootools":{"versions":{":1.11":{"uncompressed":"mootools.js","compressed":"mootools-yui-compressed.js"}},"aliases":{":1":"1.11"}},":jqueryui":{"versions":{":1.5.2":{"uncompressed":"jquery-ui.js","compressed":"jquery-ui.min.js"}},"aliases":{":1":"1.5.2",":1.5":"1.5.2"}},":prototype":{"versions":{":1.6.0.2":{"uncompressed":"prototype.js","compressed":"prototype.js"}},"aliases":{":1":"1.6.0.2",":1.6":"1.6.0.2"}},":jquery":{"versions":{":1.2.3":{"uncompressed":"jquery.js","compressed":"jquery.min.js"},":1.2.6":{"uncompressed":"jquery.js","compressed":"jquery.min.js"}},"aliases":{":1":"1.2.6",":1.2":"1.2.6"}},":dojo":{"versions":{":1.1.1":{"uncompressed":"dojo/dojo.xd.js.uncompressed.js","compressed":"dojo/dojo.xd.js"}},"aliases":{":1":"1.1.1",":1.1":"1.1.1"}}});
}


google.load("language", "1");
    google.setOnLoadCallback(translate_init);

function translate_init() {
	if ($('src')) {	
		var src = document.getElementById('src');
	}
	if ($('dst')) { // remove js error in control panel page
	  var dst = document.getElementById('dst');
      var i=0;
      for (l in google.language.Languages) {
        var lng = l.toLowerCase();
        var lngCode = google.language.Languages[l];
        if (google.language.isTranslatable(lngCode)) {
        	 if($('src')) {	
				if (lng == sessionlanguage) {  // default src option in session language
					src.options.add(new Option(lng, lngCode, true));
				}else {
					src.options.add(new Option(lng, lngCode));	
				}
			}
          dst.options.add(new Option(lng, lngCode));
        }
      }

      translate_submitChange();
	}
}

    function translate_submitChange() {
      var value = document.getElementById('source').value;
      if($('src')) {
		var src = document.getElementById('src').value;
      }
	  var dest = document.getElementById('dst').value;
      google.language.translate(value, src, dest, translateResult);
      return false;
    }

    function translateResult(result) {
      var resultBody = document.getElementById("translate_results_body");
      if (result.translation) {
        var str = result.translation.replace('>', '&gt;').replace('<', '&lt;');
        resultBody.innerHTML = str;
      } else {
        resultBody.innerHTML = '<span style="color:red">'+errortranslating+'</span>';
      }
    }