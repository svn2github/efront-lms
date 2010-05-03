/**

 * $Id: editor_plugin_src.js  2009-11-17 16:30:32Z makriria $

 *

 * @author makriria

 * @copyright www.efrontlearning.net

 */
(function() {
 tinymce.create('tinymce.plugins.SaveTemplatePlugin', {
  init : function(ed, url) {
   // Register commands
   ed.addCommand('mceSaveTemplate', function() {
    ed.windowManager.open({
     file : url + '/save_template.php',
     width : 350 + parseInt(ed.getLang('save_template.delta_width', 0)),
     height : 160 + parseInt(ed.getLang('save_template.delta_height', 0)),
     inline : 1
    }, {
     plugin_url : url
    });
   });
   // Register buttons
   ed.addButton('save_template', {title : 'save_template_dlg.save_template_title', cmd : 'mceSaveTemplate'});
  },
  getInfo : function() {
   return {
    longname : 'SaveTemplate',
    author : 'Moxiecode Systems AB',
    authorurl : 'http://tinymce.moxiecode.com',
    infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/save_template',
    version : tinymce.majorVersion + "." + tinymce.minorVersion
   };
  }
 });
 // Register plugin
 tinymce.PluginManager.add('save_template', tinymce.plugins.SaveTemplatePlugin);
})();
