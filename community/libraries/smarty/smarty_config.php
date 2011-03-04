<?php
/**

* Smarty configuration file

*/
$smarty = new Smarty;
//The templates can be read by the 'external/' directory as well
$smarty -> template_dir = array(G_CURRENTTHEMEPATH.'external/',
                                G_CURRENTTHEMEPATH.'templates/');
//$smarty -> template_dir  = G_CURRENTTHEMEPATH.'templates/';
$smarty -> plugins_dir[] = G_CURRENTTHEMEPATH.'templates/custom_plugins/';
$smarty -> plugins_dir[] = G_DEFAULTTHEMEPATH.'templates/custom_plugins/'; //We add this to search for non-existent plugins to the default theme
$smarty -> compile_dir = G_CURRENTTHEMECACHE.'templates_c/';
$smarty -> cache_dir = G_CURRENTTHEMECACHE.'cache/';
$smarty -> config_dir = $path.'smarty/configs/';

is_dir(G_CURRENTTHEMECACHE) or mkdir(G_CURRENTTHEMECACHE, 0755); //Create cache and template cache directories, if they don't exist
is_dir($smarty -> cache_dir) or mkdir($smarty -> cache_dir, 0755);
is_dir($smarty -> compile_dir) or mkdir($smarty -> compile_dir, 0755);

/**

 * Handles missing template files

 *

 * This function implements the default_template_handler_func, that is called by smarty

 * when a template file is missing. In this case, the file from the default theme is used,

 * as long as it exists.

 *

 * @param $resource_type

 * @param $resource_name

 * @param $template_source

 * @param $template_timestamp

 * @param $smarty_obj

 * @return boolean whether the override was successful

 */
function default_template ($resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
 if ($resource_type == 'file' && is_file(G_DEFAULTTHEMEPATH.'templates/'.$resource_name)) {
  $template_source = file_get_contents(G_DEFAULTTHEMEPATH.'templates/'.$resource_name);
  $template_timestamp = filemtime(G_DEFAULTTHEMEPATH.'templates/'.$resource_name);
  return true;
 } else {
  return false;
 }
}
// set the default handler
$smarty -> default_template_handler_func = 'default_template';
$smarty -> caching = false;
?>
