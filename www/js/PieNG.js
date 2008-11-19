// (c) dynarch.com 2003
// Author: Mihai Bazon, http://dynarch.com/mishoo/

function changeImages()
{
if (/MSIE [56].*Windows/.test(navigator.userAgent)) (function() {
        // fucked-up browser (Internet Explorer for Windows)
        var blank = new Image;
        blank.src = 'images/others/blank.gif';
        var imgs = document.getElementsByTagName("img");//alert(imgs[1].parentNode.tagName);
        for (var i = imgs.length; --i >= 0;) {
                var img = imgs[i];
                var src = img.src;
                if (!/\.png$/.test(src))
                        continue;
                var s = img.runtimeStyle;
                s.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "',sizingMethod='scale')";
                if (img.offsetWidth) {                              //peris: added this if, because without it, images loading with display:none wouldn't display
                    s.width = img.offsetWidth + "px";
                    s.height = img.offsetHeight + "px";
                    img.src = blank.src;
                }
        }
})();
}

function changeImage(img)
{
if (/MSIE [56].*Windows/.test(navigator.userAgent)) (function() {
        // fucked-up browser (Internet Explorer for Windows)
        var blank = new Image;
        blank.src = 'images/others/blank.gif';
        var src = img.src;
        if (!/\.png$/.test(src))
                return;
        var s = img.runtimeStyle;
        s.width = img.offsetWidth + "px";
        s.height = img.offsetHeight + "px";
        s.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "',sizingMethod='scale')";
        img.src = blank.src;
})();
}
