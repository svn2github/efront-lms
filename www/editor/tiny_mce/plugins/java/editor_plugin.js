/**
 * $Id: editor_plugin_src.js 162 2007-01-03 16:16:52Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('java');

var TinyMCE_JavaPlugin = {
    getInfo : function() {
        return {
            longname : 'Java',
            author : 'makriria',
            authorurl : 'http://tinymce.moxiecode.com',
            version : tinyMCE.majorVersion + "." + tinyMCE.minorVersion
        };
    },

    initInstance : function(inst) {
        if (!tinyMCE.settings['java_skip_plugin_css'])
            tinyMCE.importCSS(inst.getDoc(), tinyMCE.baseURL + "/plugins/java/css/content.css");
    },

    getControlHTML : function(cn) {
        switch (cn) {
            case "java":
                return tinyMCE.getButtonHTML(cn, 'lang_java_desc', '{$pluginurl}/images/java.gif', 'mceJava');
        }

        return "";
    },

    execCommand : function(editor_id, element, command, user_interface, value) {

        // Handle commands
        switch (command) {
            case "mceJava":
                var name = "", swffile = "", swfwidth = "", swfheight = "", action = "insert";
                var template = new Array();
                var inst = tinyMCE.getInstanceById(editor_id);
                var focusElm = inst.getFocusElement();

                template['file']   = '../../plugins/java/java.php'; // Relative to theme
                //template['width']  = 430;
                //template['height'] = 175;
                template['width']  = 430;
                template['height'] = 470;



                template['width'] += tinyMCE.getLang('lang_java_delta_width', 0);
                template['height'] += tinyMCE.getLang('lang_java_delta_height', 0);

                // Is selection a image
                if (focusElm != null && focusElm.nodeName.toLowerCase() == "img") { // mipos na fygei
                    name = tinyMCE.getAttrib(focusElm, 'class');

                    if (name.indexOf('mceItemJava') == -1) // Not a Java
                        return true;

                    // Get rest of Java items
                    swffile = tinyMCE.getAttrib(focusElm, 'alt');

                    if (tinyMCE.getParam('convert_urls'))
                        swffile = eval(tinyMCE.settings['urlconverter_callback'] + "(swffile, null, true);");

                    swfwidth = tinyMCE.getAttrib(focusElm, 'width');
                    swfheight = tinyMCE.getAttrib(focusElm, 'height');
                    action = "update";
                }

                tinyMCE.openWindow(template, {editor_id : editor_id, inline : "yes", swffile : swffile, swfwidth : swfwidth, swfheight : swfheight, action : action});
            return true;
       }

       // Pass to next handler in chain
       return false;
    },

    cleanup : function(type, content) {
        switch (type) {
            case "insert_to_editor_dom":
        //alert(1);
                // Force relative/absolute
                if (tinyMCE.getParam('convert_urls')) {
                    var imgs = content.getElementsByTagName("img");
                    for (var i=0; i<imgs.length; i++) {
                        if (tinyMCE.getAttrib(imgs[i], "class") == "mceItemJava") {
                            var src = tinyMCE.getAttrib(imgs[i], "alt");
                            var src2 = tinyMCE.getAttrib(imgs[i], "title");

                            if (tinyMCE.getParam('convert_urls'))
                                src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, null, true);");

                            imgs[i].setAttribute('alt', src);
                            imgs[i].setAttribute('title', src2);
                        }
                    }
                }
                break;

            case "get_from_editor_dom":
            //alert(2);
                var imgs = content.getElementsByTagName("img");
                for (var i=0; i<imgs.length; i++) {
                    if (tinyMCE.getAttrib(imgs[i], "class") == "mceItemJava") {

                        var src = tinyMCE.getAttrib(imgs[i], "alt");
                        var src2 = tinyMCE.getAttrib(imgs[i], "title");

                        if (tinyMCE.getParam('convert_urls'))
                            src = eval(tinyMCE.settings['urlconverter_callback'] + "(src, null, true);");
//alert(src);
                        imgs[i].setAttribute('alt', src);
                        imgs[i].setAttribute('title', src2);
                    }
                }
                break;

            case "insert_to_editor":
            //alert(3);
                var startPos = 0;
                var embedList = new Array();

                // Fix the embed and object elements
                content = content.replace(new RegExp('<[ ]*applet','gi'),'<applet');
                content = content.replace(new RegExp('<[ ]*/applet[ ]*>','gi'),'</applet>');


                // Parse all embed tags
                while ((startPos = content.indexOf('<applet', startPos+1)) != -1) {
                    var endPos = content.indexOf('>', startPos);
                    var attribs = TinyMCE_JavaPlugin._parseAttributes(content.substring(startPos + 7, endPos));
                    embedList[embedList.length] = attribs;
                }

                // Parse all object tags and replace them with images from the embed data
                var index = 0;
                while ((startPos = content.indexOf('<applet', startPos)) != -1) {
                    if (index >= embedList.length)
                        break;

                    var attribs = embedList[index];

                    // Find end of object
                    endPos = content.indexOf('</applet>', startPos);
                    endPos += 9;
//alert(attribs["code"]);
                    // Insert image
                    var contentAfter = content.substring(endPos);
                    content = content.substring(0, startPos);
                    content += '<img width="' + attribs["width"] + '" height="' + attribs["height"] + '"';
                    content += ' src="' + (tinyMCE.getParam("theme_href") + '/images/spacer.gif') + '" title="' + attribs["code"] + '"';
//alert(contentAfter);
                    //content += ' alt="' + attribs["codebase"] + '" class="mceItemJava" />' + content.substring(endPos);
                    content += ' alt="' + attribs["codebase"] + '" class="mceItemJava" />'; //delete content.substring(endPos) ..it causes problem in FF...makriria
                    content += contentAfter;
                    index++;

                    startPos++;
                }

                // Parse all embed tags and replace them with images from the embed data
        /*      var index = 0;
                while ((startPos = content.indexOf('<applet', startPos)) != -1) {
                    if (index >= embedList.length)
                        break;

                    var attribs = embedList[index];

                    // Find end of embed
                    endPos = content.indexOf('>', startPos);
                    endPos += 9;

                    // Insert image
                    var contentAfter = content.substring(endPos);
                    content = content.substring(0, startPos);
                    content += '<img width="' + attribs["width"] + '" height="' + attribs["height"] + '"';
                    content += ' src="' + (tinyMCE.getParam("theme_href") + '/images/spacer.gif') + '" title="' + attribs["src"] + '"';
                    content += ' alt="' + attribs["src"] + '" class="mceItemJava" />' + content.substring(endPos);
                    content += contentAfter;
                    index++;

                    startPos++;
                }*/

                break;

            case "get_from_editor":
            //alert(4);
                // Parse all img tags and replace them with object+embed
                var startPos = -1;

                while ((startPos = content.indexOf('<img', startPos+1)) != -1) {
                    var endPos = content.indexOf('/>', startPos);
                    var attribs = TinyMCE_JavaPlugin._parseAttributes(content.substring(startPos + 4, endPos));
//alert(attribs["title"]);

                    // Is not Java, skip it
                    if (attribs['class'] != "mceItemJava")
                        continue;

                    endPos += 2;

                    var embedHTML = '';
                /*  var wmode = tinyMCE.getParam("java_wmode", "");
                    var quality = tinyMCE.getParam("java_quality", "high");
                    var menu = tinyMCE.getParam("java_menu", "false");*/

/*var formObj = document.forms[0];
    var file      = formObj.file.value;
    var codebase  = formObj.codebase.value;
    var width     = formObj.width.value;
    var height    = formObj.height.value;*/
//alert(file);
//alert(attribs["alt"]);
//alert(attribs["height"]);
//alert(attribs["width"]);
embedHTML +='<applet codebase="' + attribs["alt"] +'" code="' + attribs["title"] + '" width="'+ attribs["width"] + '" height="' +attribs["height"]+ '"/></applet>';
                    // Insert embed/object chunk
                    chunkBefore = content.substring(0, startPos);
                    chunkAfter = content.substring(endPos);
                    content = chunkBefore + embedHTML + chunkAfter;
//content = embedHTML;

                }
                break;
        }

        // Pass through to next handler in chain
        return content;
    },

    handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
        if (node == null)
            return;

        do {
            if (node.nodeName == "IMG" && tinyMCE.getAttrib(node, 'class').indexOf('mceItemJava') == 0) {
                tinyMCE.switchClass(editor_id + '_java', 'mceButtonSelected');
                return true;
            }
        } while ((node = node.parentNode));

        tinyMCE.switchClass(editor_id + '_java', 'mceButtonNormal');

        return true;
    },

    // Private plugin internal functions

    _parseAttributes : function(attribute_string) {

        var attributeName = "";
        var attributeValue = "";
        var withInName;
        var withInValue;
        var attributes = new Array();
        var whiteSpaceRegExp = new RegExp('^[ \n\r\t]+', 'g');

        if (attribute_string == null || attribute_string.length < 2)
            return null;

        withInName = withInValue = false;

        for (var i=0; i<attribute_string.length; i++) {
            var chr = attribute_string.charAt(i);

            if ((chr == '"' || chr == "'") && !withInValue)
                withInValue = true;
            else if ((chr == '"' || chr == "'") && withInValue) {
                withInValue = false;

                var pos = attributeName.lastIndexOf(' ');
                if (pos != -1)
                    attributeName = attributeName.substring(pos+1);

                attributes[attributeName.toLowerCase()] = attributeValue.substring(1);
                attributeName = "";
                attributeValue = "";
            } else if (!whiteSpaceRegExp.test(chr) && !withInName && !withInValue)
                withInName = true;

            if (chr == '=' && withInName)
                withInName = false;

            if (withInName)
                attributeName += chr;

            if (withInValue)
                attributeValue += chr;
        }
        return attributes;
    }
};

tinyMCE.addPlugin("java", TinyMCE_JavaPlugin);
