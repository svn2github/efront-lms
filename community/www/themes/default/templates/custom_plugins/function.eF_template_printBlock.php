<?php
/**

* prints a block

*

*/
function smarty_function_eF_template_printBlock($params, &$smarty) {
 if ($params['title'] == "") {return '';}
    $params['link'] ? $params['title'] = '<a href = "'.$params['link'].'">'.$params['title'].'</a>' : null;
 $params['data'] ? $params['content'] = $params['data'] : null; //'data' is used in printInnertable, and we put this here for compatibility 
 /**

	 * Cookies for remembering the open/close status of blocks, and to display status depending on lesson layout settings if it's the control panel

	 * @todo: Make it better, to comply with new blocks (this one's copied from old innerTable functions 

	 */
    $innerTableIdentifier = $GLOBALS['innerTableIdentifier'];
    $cookieString = md5($_SESSION['s_login'].$_SESSION['s_lessons_ID'].$GLOBALS['innerTableIdentifier'].urlencode($params['title']));
    $cookieValue = $_COOKIE['innerTables'][$cookieString];
    /**

     * $params['settings'] is an array that defines the way this block will appear. Currently supported

     * settings are:

     * - nohandle: Will not display an open/close handle, the block will be always bisible

     */
    if (isset($params['settings'])) {
        isset($params['settings']['nohandle']) ? $nohandle = true : $nohandle = false;
    }
    /**

     * $params['options'] is an array with handles that are displayed on the block header,

     * and are encompassing custom functionality. Each handle is an <a> element that contains an <img> element.

     * The array is populated with any of the following entries:

     * - image: The source of the handle's image

     * - text: The text displayed in the title and alt fields of the image

     * - href: The link that the handle points at

     * - onclick: an action assigned to the onclick event of the <a> tag

     * - class: The class name of the <a> tag

     * - target: The target that the <a> link will open at

     * - id: The id of the <a> tag

     */
    if (isset($params['options'])) {
     $optionsString = '';
     foreach ($params['options'] as $key => $value) {
   isset($value['onClick']) ? $value['onclick'] = $value['onClick'] : null; //sometimes onClick is used instead of onclick.
   isset($value['class']) && $value['class'] ? $classstr = 'class = "'.$value['class'].'"' : $classstr = '';
      isset($value['target']) && $value['target'] ? $target = 'target = "'.$value['target'].'"' : $target = '';
      isset($value['id']) && $value['id'] ? $id = 'id = "'.$value['id'].'"' : $id = '';
      isset($value['href']) && $value['href'] ? $href = 'href = "'.$value['href'].'"' : $href = '';
      isset($value['onclick'])&& $value['onclick'] ? $onclick = 'onclick = "'.$value['onclick'].'"' : $onclick = '';
            !isset($params['absoluteImagePath']) && $value['image'] ? $value['image'] = 'images/'.$value['image'] : null; //if absoluteImagePath is specified, it means that $params['image'] contains an absolute path (or anyway it refers to an image not under www/images/) 
      if ($href) {
          $optionsString .= "<a $id $href $onclick $target $classstr><img src = '".$value['image']."' title = '".$value['text']."' alt = '".$value['text']."' /></a>";
      } else {
          $optionsString .= "<img class = 'handle' $id $onclick $classstr src = '".$value['image']."' title = '".$value['text']."' alt = '".$value['text']."' />";
      }
     }
    }
    //$optionsString .= "<a href = 'http://docs.efrontlearning.net' target = '_new'><img src = 'images/16x16/help.png' title = '"._HELP."' alt = '"._HELP."' /></a>";
    //$optionsString .= "<a href = 'javascript:void(0)' onclick = 'eF_js_showDivPopup(\"search\", 0, \"cse\")'><img src = 'images/16x16/help.png' title = '"._HELP."' alt = '"._HELP."' /></a>";
    /**

     * The "links" parameter is used to create an icon table, that is a block content that consists of rows of icons

     * The parameters available are the same as for the options case above, with the only difference being that this time

     * there is a $params['links'] parameter instead of $params['options'].

     * In addition, if an optional "groups" parameter is defined, then the available icons may be divided into groups

     * Keep in mind that there can't be $params['content'] and $params['link'] at the same time; the former overwrites the latter.

     * Each array entry consists of the following:

     * - text: The text that accompanies the icon (mandatory)

     * - image: The icon src (mandatory)

     * - href: Where the icon's link will point to, defaults to javascript:void(0)

     * - onclick: An action to perform 

     * - title: The alt/title to use for the icon, defaults to the same as 'text' above

     * - group: If icons are separated to groups, which group to put this entry into

     */
    if (isset($params['links']) && !isset($params['content'])) {
     !isset($params['columns']) || !$params['columns'] ? $params['columns'] = 4 : null;
     $width = round(100 / $params['columns']); //Divide available width so that it can be equally assigned to table cells
     //Use a default group, if none is specified. This way the algorithm for displaying groups is greatly simplified
     if (!isset($params['groups']) || sizeof($params['groups']) == 0) {
      $params['groups'] = array(0 => 0);
     }
     foreach ($params['groups'] as $groupId => $name) {
      $groupId ? $linksString .= '<fieldset class = "fieldsetSeparator"><legend>'.$name.'</legend>' : null;
         $linksString .= '
       <table class = "iconTable">';
         $counter = 0; //$counter is used to count how many icons are put in each group, so that the <tr>s are put in correct place, and empty <td>s are appended where needed    	 
   //Print group separator, only if $groupId > 0. This way, the default group specified above, does not print any group separator
      //$groupId ? $linksString .= '<tr><td class = "group" colspan = "'.$params['columns'].'">'.$name.'</td></tr>' : null;
      foreach (array_values($params['links']) as $key => $value) { //array_values makes sure that entries are displayed correctly, even if keys are not sequential
       if ($value['group'] == $groupId) {
        isset($value['onClick']) ? $value['onclick'] = $value['onClick'] : null; //sometimes onClick is used instead of onclick.
        isset($value['class']) && $value['class'] ? $classstr = 'class = "'.$value['class'].'"' : $classstr = '';
        isset($value['target']) && $value['target'] ? $target = 'target = "'.$value['target'].'"' : $target = '';
        isset($value['id']) && $value['id'] ? $id = 'id = "'.$value['id'].'"' : $id = '';
        isset($value['href']) && $value['href'] ? $href = 'href = "'.$value['href'].'"' : $href = 'href = "javascript:void(0)"';
        isset($value['onclick'])&& $value['onclick'] ? $onclick = 'onclick = "'.$value['onclick'].'"' : $onclick = '';
        isset($value['title']) && $value['title'] ? $title = 'title = "'.$value['title'].'" alt = "'.$value['title'].'"' : $title = 'title = "'.$value['text'].'" alt = "'.$value['text'].'"';
        if ($counter++ % $params['columns'] == 0) {
         $linksString .= '<tr>';
        }
                    $value['image'] && strpos($value['image'], "modules/") === false ? $value['image'] = 'images/'.$value['image'] : null; //Make sure that modules images are taken using absolute paths 
        $linksString .= "
                     <td style = 'width:$width%;' class = 'iconData'>
                         <a $id $href $onclick $target>
                          <img $classstr src = '".$value['image']."' $title /><br>
                          ".$value['text']."
                         </a>
                        </td>";    				
        if ($counter % $params['columns'] == 0) {
         $linksString .= '</tr>';
        }
       }
      }
      //If the icons where not a factor of $params[columns'], then there are some gaps left in the table. We must fill these gaps with empty table cells
         for ($i = $params['columns']; $i > $counter % $params['columns']; $i--) {
             $linksString .= '<td></td>';
         }
            $linksString .= '</table>';
         $groupId ? $linksString .= '</fieldset>' : null;
     }
        $params['content'] = $linksString;
    }
    /**

     * The "main_options" parameter is used to display an options menu (much like "tabs") on the top of the block

     * Each option consists of the following values:

     * - image: The image that will be displayed next to the option

     * - title: The option title

     * - link: the address that the href attribute will point to

     * - selected: if it's 1, then the corresponding option will display as "selected"

     */
    $mainOptions = '';
    if (isset($params['main_options']) && sizeof($params['main_options']) > 0) {
     foreach ($params['main_options'] as $key => $value) {
         isset($value['onClick']) ? $value['onclick'] = $value['onClick'] : null; //sometimes onClick is used instead of onclick.
      $mainOptions .= '
                        <span '.($value['selected'] ? 'class = "selected"': null).'>
                            <a href = "'.$value['link'].'"><img src = "images/'.$value['image'].'" alt = "'.$value['title'].'" title = "'.$value['title'].'"/></a>
                            <a href = "'.$value['link'].'" onclick = "'.$value['onclick'].'">'.$value['title'].'</a>
                        </span>';
     }
     $mainOptions = '<div class = "toolbar">'.$mainOptions.'</div>';
    }
    !isset($params['absoluteImagePath']) && $params['image'] ? $params['image'] = 'images/'.$params['image'] : null; //if absoluteImagePath is specified, it means that $params['image'] contains an absolute path (or anyway it refers to an image not under www/images/) 
    isset($params['image']) && $params['image'] ? $image = '<img src = "'.$params['image'].'" alt = "'.strip_tags($params['title']).'" title = "'.strip_tags($params['title']).'" />' : $image = '';
    if ($GLOBALS['currentTheme'] -> options['images_displaying'] == 2 || ($GLOBALS['currentTheme'] -> options['images_displaying'] == 1 && basename($_SERVER['PHP_SELF']) == 'index.php')) {
     $image = '';
    }
    $handleString = '';
    if ($params['help']) {
     $handleString .= '<img src = "images/16x16/help.png" onclick = "window.open(\''.$GLOBALS['configuration']['help_url'].$params['help'].'\')">';
    }
    if (!$nohandle) {
     if ($cookieValue == 'hidden') {
      $handleString = '<img class = "close" src = "images/16x16/navigate_down.png" onclick = "toggleBlock(this, \''.$cookieString.'\')" id = "'.urlencode($params['title']).'_image">';
      $showContent = 'display:none';
     } else {
      $handleString = '<img class = "open" src = "images/16x16/navigate_up.png" onclick = "toggleBlock(this, \''.$cookieString.'\')"  id = "'.urlencode($params['title']).'_image">';
      $showContent = '';
     }
    }
 //$handleString .= '<img class = "removePopupIcon" '.($_GET['popup'] ? '' : 'style = "display:none"').' src = "images/16x16/close.png" alt = "'._CLOSE.'" title = "'._CLOSE.'" onclick = "closePopup()">';
    $str = '	
    <div class = "block" style = "'.$params['style'].';" id = "'.urlencode($params['title']).'" >
        <div class = "blockContents">
    <span class = "handles">'.$optionsString.$handleString.'</span>
          <span class = "title">'.$image.''.$params['title'].'</span>
          <span class = "subtitle">'.$params['sub_title'].'</span>
          '.$mainOptions.'
          <div class = "content" style = "'.$showContent.'" id = "'.urlencode($params['title']).'_content">
     '.$params['content'].'
    </div>
          <span style = "display:none">&nbsp;</span>
        </div>
    </div>';
    if ($params['tabber']) {
  if($_GET['tab'] == $params['tabber']) {
   $tabberdefault = "tabbertabdefault";
  }
  $str = '<div class = "tabbertab '.$tabberdefault.'"><h3>'.$params['title'].'</h3>'.$str.'</div>';
 }
 if (!$params['content'] && !$params['options']) {
     return '';
 } else {
        return $str;
 }
}
/*

    <div class = "block content">

		<span class = "title">'.$params['title'].'</span>

		<span class = "toggle open" onclick = "toggleBlock(this)"></span>		

		<span class = "subtitle">'.$params['sub_title'].'</span>

		<div class = "content">'.$params['content'].'</div>

		<span style = "display:none">&nbsp;</span>	

	</div>

 */
?>
