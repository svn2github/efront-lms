<?php

require_once ('../../../../libraries/configuration.php');
require_once ('../class_includer.php');

// Taking care of the localisation here
$language = $GLOBALS['configuration']['default_language'];
$default_localisation_path = 'lang/lang-english.php';
$localisation_path = 'lang/lang-'.$language.'.php';

if (!include($localisation_path)) {
 include($default_localisation_path);
}
// END localisation

$favicon = '../../../themes/default/'.$GLOBALS['smarty']->_tpl_vars['T_FAVICON'];

$site_name = trim($GLOBALS['configuration']['site_name']);
$site_motto = trim($GLOBALS['configuration']['site_motto']);
$page_title = $site_name . ($site_name ? ' | ' : '') . $site_motto;
if (!$page_title) $page_title = 'JOBS MANAGER';

$settings = new Settings();

$protocol = 'http';
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
 $protocol = 'https';
}
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . '://' . $host;
if (substr($baseUrl, -1)=='/') {
 $baseUrl = substr($baseUrl, 0, strlen($baseUrl)-1);
}
$logo_path = $baseUrl.$_SERVER['REQUEST_URI'];
$logo_path = substr($logo_path, 0, stripos($logo_path, 'public'));
$logo_path .= 'uploads/';
$logo_path .= $settings->getLogoFilename();


// Determining page structure
if ($settings->getListLocation() == "RIGHT") {
 $menu_location = 'right';
 $content_location = 'left';
}
else {
 $menu_location = 'left';
 $content_location = 'right';
}

$menu_type = strtolower($settings->getListType());

// Processing jobs
$job_manager = new JobManager();
$tmp_jobs = $job_manager->getJobs();
$all_jobs = array();
foreach ($tmp_jobs AS $job_id => $job) {
 $current_job = false;
 if ($job instanceof Job) {
  $current_job = $job;
 }
 else {
  try {
   $job = new Job($job_id);
   if ($job->isActive()) {
    $current_job = $job;
   }
  }
  catch (Exception $e) { /* DO NOTHING */ }
 }

 if ($current_job && $current_job->isActive()) {
  $all_jobs [$current_job->getId()] = $current_job;
 }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
 <HEAD>
  <meta http-equiv = "Content-Type" content = "text/html; charset = utf-8"/>
  <link REL="SHORTCUT ICON" HREF="<?php echo $favicon; ?>">
  <title><?php echo $page_title; ?></title>
  <script language="Javascript" src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
  <script language="Javascript" src="js/jquery-ui-1.7.2.min.js" type="text/javascript"></script>
  <script language="Javascript" src="js/actonjs-core.js" type="text/javascript"></script>
  <script language="Javascript" src="js/actonjs-app.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="css/style.css"/>
 </HEAD>
 <BODY>
  <div style="margin:auto;min-width:700px; max-width:900px;">
  <table cellpadding="0" cellspacing="0" id="wrapper">
   <tr>
    <td>
     <div id="page-head">
     <?php
      if ($settings->getLogoFilename()) {
       echo '<a href=""><img src="'.$logo_path.'" border="0"/></a>';
      }
     ?>
     </div>
    </td>
   </tr>
   <tr>
    <td>
     <div style="position:relative;width:100%;">
      <table cellpadding="0" cellspacing="0" width="100%">
       <tr valign="top">
       <?php
       if ($menu_location == "left") {
        echo '<td style="width:210px;">';
        echo '	<div class="menu-sidebar" style="top:0px;" id="sidebar">';
        echo '		<p><a href="" class="plain">'._MOD_JAM_PUBLIC_PAGE_HOMEPAGE.'</a></p>';
        if (count($all_jobs)) {
         if ($menu_type == 'select') {
          echo '	<select id="" name="" style="width:200px;" onchange="javascript:Actonjs.app.displayJob(this.value);">';
          echo '		<option value="0">'._MOD_JAM_PUBLIC_PAGE_CHOOSE_JOB.'</option>';
          foreach ($all_jobs AS $job_id => $job) {
           print '<option value="'.$job_id.'">'.$job->getTitle() . ' - [' .$job->getCode().']</option>';
          }
          echo '	</select>';
         }
         else {
          foreach ($all_jobs AS $job_id => $job) {
           print '<p><a href="javascript:void(0);" class="plain" onclick="javascript:Actonjs.app.displayJob(\''.$job_id.'\');">'.$job->getCode().'<br/>'.$job->getTitle().'</a></p>';
          }
         }
        }
        echo '	</div>';
        echo '</td>';
        echo '<td style="background:#FFFFFF;width:490px;">';
        echo '	<div class="main-content" id="main_content_div">'.$settings->getAboutContent().'</div>';
        echo '</td>';
        echo '<td style="background:#FFFFFF;">&nbsp;</td>';
       }
       if ($menu_location == 'right') {
        echo '<td style="background:#FFFFFF;">&nbsp;</td>';
        echo '<td style="background:#FFFFFF;width:490px;">';
        echo '	<div class="main-content" id="main_content_div">'.$settings->getAboutContent().'</div>';
        echo '</td>';
        echo '<td style="width:210px;">';
        echo '	<div class="menu-sidebar" style="top:0px;" id="sidebar">';
        echo '		<p><a href="" class="plain">'._MOD_JAM_PUBLIC_PAGE_HOMEPAGE.'</a></p>';
        if (count($all_jobs)) {
        if ($menu_type == 'select') {
          echo '	<select id="" name="" style="width:200px;" onchange="javascript:Actonjs.app.displayJob(this.value);">';
          echo '		<option value="0">'._MOD_JAM_PUBLIC_PAGE_CHOOSE_JOB.'</option>';
          foreach ($all_jobs AS $job_id => $job) {
           print '<option value="'.$job_id.'">'.$job->getTitle() . ' - [' .$job->getCode().']</option>';
          }
          echo '	</select>';
         }
         else {
          foreach ($all_jobs AS $job_id => $job) {
           print '<p><a href="javascript:void(0);" class="plain" onclick="javascript:Actonjs.app.displayJob(\''.$job_id.'\');">'.$job->getCode().'<br/>'.$job->getTitle().'</a></p>';
          }
         }
        }
        echo '	</div>';
        echo '</td>';
       }
       ?>
       </tr>
      </table>
      <div class="form-container">
       <div class="form">
        <div class="form-content">
         <div class="form-mask"></div>
         <form style="margin:0px;">
          <input type="hidden" name="MAX_FILE_SIZE" value="2097152"/><!-- 1MB MAXIMUM FILESIZE -->
          <input type="hidden" name="mod_jam_form_job_id" id="mod_jam_form_job_id" value=""/>
          <table cellpadding="5" cellspacing="0" style="width:100%;">
           <tr>
            <td style="background:#F0F0F0;font-size:14px; font-weight:bold;"><div id="job_code_form"></div></td>
           </tr>
          </table>
          <table cellpadding="3" cellspacing="0" style="width:100%:">
           <tr>
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_NAME; ?>:
            </td>
            <td><input type="text" name="mod_jam_form_name" id="mod_jam_form_name" value="" style="width:200px; border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr>
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_EMAIL; ?>:
            </td>
            <td><input type="text" name="mod_jam_form_email" id="mod_jam_form_email" value="" style="width:200px; border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr>
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_PHONE; ?>:
            </td>
            <td><input type="text" name="mod_jam_form_phone" id="mod_jam_form_phone" value="" style="width:200px; border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr>
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_CITY; ?>:
            </td>
            <td><input type="text" name="mod_jam_form_city" id="mod_jam_form_city" value="" style="width:200px; border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr>
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_COUNTRY; ?>:
            </td>
            <td><input type="text" name="mod_jam_form_country" id="mod_jam_form_country" value="" style="width:200px; border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr valign="top">
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_COVER; ?>:
            </td>
            <td><textarea name="mod_jam_form_cover" id="mod_jam_form_cover" style="width:400px; height:150px; border:solid 1px #D0D0D0;"></textarea></td>
           </tr>
           <tr valign="top">
            <td style="width:120px;text-align:right;">
             <?php echo _MOD_JAM_PUBLIC_PAGE_FORM_RESUME; ?>:
            </td>
            <td><input type="file" name="mod_jam_form_file" id="mod_jam_form_file" style="width:200px;border:solid 1px #D0D0D0;"/></td>
           </tr>
           <tr valign="top">
            <td style="width:120px;text-align:right;"></td>
            <td><input type="button" name="mod_jam_form_btn" id="mod_jam_form_btn" onclick="javascript:Actonjs.app.checkApplication(this.form);" value="<?php echo _MOD_JAM_PUBLIC_PAGE_FORM_SUBMIT; ?>: " style="padding:2px;"/>&nbsp;<?php echo _MOD_JAM_PUBLIC_PAGE_FORM_OR; ?> <a href="javascript:void(0);" class="plain" onclick="javascript:Actonjs.app.cancelForm();"><?php echo _MOD_JAM_PUBLIC_PAGE_FORM_CANCEL; ?></a></td>
           </tr>
          </table>
         </form>
        </div>
       </div>
       <div class="form-trigger<?php echo ' '.$content_location; ?>"><img src="images/apply.png" id="form-trigger-image"/></div>
      </div>
     </div>

    </td>
   </tr>
  </table>
  </div>
 </BODY>
</HTML>
