<?php
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

$GLOBALS['loadScripts'][] = 'includes/filemanager';

if (isset($currentUser -> coreAccess['files']) && $currentUser -> coreAccess['files'] == 'hidden') {
    eF_redirect(basename($_SERVER['PHP_SELF'])."?ctg=control_panel&message=".urlencode(_UNAUTHORIZEDACCESS)."&message_type=failure");
}

try {
    if (isset($_GET['display_metadata']) && (eF_checkParameter($_GET['display_metadata'], 'id') || strpos($_GET['display_metadata'], $currentLesson -> getDirectory()) !== false)) {
        $form = new HTML_QuickForm("empty_form", "post", null, null, null, true);
        $file = new EfrontFile(urldecode($_GET['display_metadata']));
        if ($file['id'] == -1) {
            $imported = FileSystemTree :: importFiles($file['path']);
            $file = new EfrontFile(key($imported));
        }
        $fileMetadata = unserialize($file['metadata']);
        $metadata = new DublinCoreMetadata($fileMetadata);
        $smarty -> assign("T_FILE_METADATA", $file);
        if (!isset($currentUser -> coreAccess['files']) || $currentUser -> coreAccess['files'] == 'change') {
            $smarty -> assign("T_FILE_METADATA_HTML", $metadata -> toHTML($form));
        } else {
            $smarty -> assign("T_FILE_METADATA_HTML", $metadata -> toHTML($form, true, false));
        }
        if (isset($_GET['postAjaxRequest'])) {
            if (in_array($_GET['dc'], array_keys($metadata -> metadataAttributes))) {
             $_GET['value'] = urldecode($_GET['value']);
                if ($_GET['value']) {
                    $fileMetadata[$_GET['dc']] = $_GET['value'];
                } else {
                    unset($fileMetadata[$_GET['dc']]);
                }
                $file['metadata'] = serialize($fileMetadata);
                $file -> persist();
            }
            echo $_GET['value'];
            exit;
        }
    } else if (isset($_GET['insert_editor_file'])) {
        //Input a file that was picked by clicking on the "insert to editor" arrow, in the file manager
        try {
            $file_id = urldecode($_GET['file_id']);
            $file_insert = new EfrontFile($file_id);
            if (strpos($file_insert['mime_type'] , "image") !== false) {
                $img_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo "<img src=\"".$img_return."\" border=0 />";
            } elseif (strpos($file_insert['mime_type'] , "pdf") !== false) {
                $pdf_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo '<iframe src="'.$pdf_return.'" frameborder="0" name="pdfFrame_'.urlencode($file_insert['id']).'" width="100%" height="600"></iframe>';
            } elseif (strpos($file_insert['mime_type'] , "flash") !== false) {
                $flash_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                if ($_GET['editor_mode'] == "true") {
                    echo '<img width="400" height="400" src="editor/tiny_mce/plugins/media/img/trans.gif"  title="src:\''.$flash_return.'\',width:\'400\',height:\'400\'" alt="'.$flash_return.'" class="mceItemFlash" />';
                } else {
                    echo '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="400" height="400">
       <param name="src" value="'.$flash_return.'" />
       <param name="url" value="'.$flash_return.'" />
       <param name="width" value="400" />
       <param name="height" value="400" />
       <embed type="application/x-shockwave-flash" url="'.$flash_return.'" src="'.$flash_return.'" width="400" height="400"></embed>
       </object>';
                }
            } elseif (strpos($file_insert['mime_type'] , "audio") !== false) {
                $audio_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                if ($_GET['editor_mode'] == "true") {
                    echo '<img width=400 height=400 src="editor/tiny_mce/plugins/media/img/trans.gif" title="src:\''.urlencode($audio_return).'\',width:\'200\',height:\'200\'" alt="'.urlencode($audio_return).'" class="mceItemWindowsMedia" />';exit;
                } else {
                    echo '
      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="100" height="30" data="editor/tiny_mce/plugins/media/player.swf" id="audioplayer1">
      <param name="movie" value="editor/tiny_mce/plugins/media/player.swf">
      <param name="FlashVars" value="playerID=1&amp;soundFile='.urlencode($audio_return).'">
      <param name="quality" value="high">
      <param name="menu" value="false">
      <param name="wmode" value="transparent">
      <embed type="application/x-shockwave-flash" id="audioplayer1" flashvars="playerID=1&amp;soundFile='.urlencode($audio_return).'" quality="high" menu="false" wmode="transparent" src="editor/tiny_mce/plugins/media/player.swf"></embed>
      </object>';
                    exit;
                }
            } elseif (strpos($file_insert['mime_type'] , "flv") !== false) {
                $flv_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo '<iframe src="editor/tiny_mce/plugins/media/img/flv_player.swf?flvToPlay=##EFRONTEDITOROFFSET##'.$flv_return.'" frameborder="0" height="300" width="300"></iframe>';
            } elseif (strpos($file_insert['mime_type'] , "wmv") !== false) {
                $wmv_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                if ($_GET['editor_mode'] == "true") {
                    echo '<img width=400 height=400 src="editor/tiny_mce/plugins/media/img/trans.gif" title="src:\''.$wmv_return.'\',width:\'400\',height:\'400\'" alt="'.$wmv_return.'" class="mceItemWindowsMedia" />';exit;
                } else {
                    echo '<object classid="clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" width="300" height="300">
       <param name="width" value="300" />
       <param name="height" value="300" />
       <param name="src" value="'.$wmv_return.'" />
       <param name="url" value="'.$wmv_return.'" />
       <embed type="application/x-mplayer2" width="300" height="300" url="'.$wmv_return.'" src="'.$wmv_return.'"></embed>
      </object>';
                }
            } elseif (strpos($file_insert['mime_type'] , "html") !== false) {
                $html_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo '<iframe src="'.$html_return.'" frameborder="0" name="htmlFrame_'.urlencode($file_insert['id']).'" width="100%" height="500px"></iframe>';
            } elseif (strpos($file_insert['extension'] , "class") !== false) {
                //$java_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                $codebase_return = mb_substr($file_insert['directory'], mb_strlen(G_ROOTPATH."www/"));
                //echo '<iframe src="'.$html_return.'" frameborder="0" name="htmlFrame_'.urlencode($file_insert['id']).'" width="100%" height="100%"></iframe>';
                echo '<table width="632" height="345" rules="rows" frame="box" cellspacing="4" cellpadding="4" border="2" style="border-style: dotted; border-width: 3px;  vertical-align: top; color: rgb(51, 51, 51); background-color: rgb(204, 255, 153);"><tbody><tr><td align="center" valign="center"><applet codebase="'.$codebase_return.'" code="'.$file_insert['name'].'" width="632" height="345"/></applet><img src="images/file_types/java.gif" /></td></tr></tbody></table>';
                //pr($file_insert);
            } elseif (strpos($file_insert['mime_type'] , "mp4") !== false) {
                $video_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
                echo '<object height="400" width="400" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" classid="clsid:02bf25d5-8c17-4b23-bc80-d3488abddc6b"><param name="src" value="'.$video_return.'" /><embed height="400" width="400" src="'.$video_return.'" type="video/quicktime"></embed></object>';
            } elseif (strpos($file_insert['mime_type'] , "x-m4v") !== false) {
                $video_return = mb_substr($file_insert['path'], mb_strlen(G_ROOTPATH."www/"));
    echo '<object height="400" width="500" codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" classid="clsid:02bf25d5-8c17-4b23-bc80-d3488abddc6b"><param name="src" value="'.$video_return.'" /><embed height="400" width="400" src="'.$video_return.'" type="video/quicktime"></embed></object>';
   } else {
                echo "<a href=view_file.php?action=download&file=".$file_id.">".$file_insert['physical_name']."</a>";
            }
        } catch (Exception $e) {
            header("HTTP/1.0 500 ");
            echo $e -> getMessage().' ('.$e -> getCode().')';
        }
        exit;

    } else {
        if (!isset($filesystem)) {
            $filesystem = new FileSystemTree($basedir, true);
        }

        $filesystem -> handleAjaxActions($currentUser);

        if (!isset($options['table_id']) || !$options['table_id']) {
            $options['table_id'] = 'filesTable';
        }
        isset($filesystemIterator) OR $filesystemIterator = '';
        isset($extraFileTools) OR $extraFileTools = array();
        isset($extraHeaderOptions) OR $extraHeaderOptions = array();
        isset($extraColumns) OR $extraColumns = array();

        if (isset($_GET['ajax']) && $_GET['ajax'] == $options['table_id']) {
         try {
          isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

          if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
           $sort = $_GET['sort'];
           isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
          } else {
           $sort = 'login';
          }

          if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
           isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
          }
          isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = false;
          isset($_GET['other']) ? $other = $_GET['other'] : $other = '';
/*        		    		

        		if (mb_strpos(realpath($_GET['other']), realpath($basedir) ) !== false) {

        			isset($_GET['other'])  ? $other  = $_GET['other']  : $other  = '';

        		} else {

        			throw new EfrontFileException(_YOUCANNOTACCESSTHEREQUESTEDRESOURCE, EfrontFileException::UNAUTHORIZED_ACTION);

        		}

*/
          $ajaxOptions = array('sort' => $sort, 'order' => $order, 'limit' => $limit, 'offset' => $offset, 'filter' => $filter);
          //$extraFileTools = array(array('image' => 'images/16x16/arrow_right.png', 'title' => _INSERTEDITOR, 'action' => 'insert_editor'));
          $filesystemCode = $filesystem -> toHTML($url, $other, $ajaxOptions, $options, $extraFileTools, $extraDirectoryTools, $extraHeaderOptions, $filesystemIterator, false, $extraColumns);
          $smarty -> assign("T_DISPLAYCODE", $filesystemCode);
          $smarty -> display('display_code.tpl');
          exit;
         } catch (Exception $e) {
          handleAjaxExceptions($e);
         }
        }
        $smarty -> assign("T_FILE_MANAGER", $filesystem -> toHTML($url, false, false, $options, $extraFileTools, $extraDirectoryTools, $extraHeaderOptions, $filesystemIterator, false, $extraColumns));
    }
} catch (Exception $e) {
 handleNormalFlowExceptions($e);
}
