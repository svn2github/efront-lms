<?php

/**

 * @author makriria

 * @copyright 2008

 */
class module_quick_mails extends EfrontModule {
    public function getName() {
        return _MAILS_MODULEMAILS;
    }
    public function getPermittedRoles() {
        return array("student","professor");
    }


 public function getModule() {
  $smarty = $this -> getSmartyVar();
  global $load_editor;
  $load_editor = true;
  $current_user = $this -> getCurrentUser();
  $smarty -> assign("T_MODULE_CURRENT_USER" , $current_user ->getType());

  $form = new HTML_QuickForm("module_mail_form", "post", $this ->moduleBaseUrl, "", "id = 'module_mail_form'");

  $form -> addElement('hidden', 'recipients', $_GET['rec']);
  $form -> addElement('text', 'subject', _SUBJECT, 'class = "inputText" style = "width:400px"');
  $form -> addElement('textarea', 'body', _BODY, 'class = "simpleEditor" style = "width:100%;height:200px"');
  $form -> addElement('checkbox', 'email', _SENDASEMAILALSO, null, 'class = "inputCheckBox"');
  $form -> addRule('subject', _THEFIELD.' "'._SUBJECT.'" '._ISMANDATORY, 'required', null, 'client');
  $form -> addRule('recipients', _THEFIELD.' "'._RECIPIENTS.'" '._ISMANDATORY, 'required', null, 'client');
  $form -> addElement('file', 'attachment[0]', _ATTACHMENT, null, 'class = "inputText"');
  $form -> addElement('submit', 'submit_mail', _SEND, 'class = "flatButton"');


  if ($form -> isSubmitted() && $form -> validate()) {
      $values = $form -> exportValues();
   switch ($values['recipients']) {
    case "lesson_students":
     $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
         $lessonUsers = $lesson -> getUsers("student");
         foreach ($lessonUsers as $value){
      $mail_recipients[] = $value['login'];
     }
     //pr($mail_recipients);return;
     break;
    case "lesson_professors":
     $lesson = new EfrontLesson($_SESSION['s_lessons_ID']);
         $lessonUsers = $lesson -> getUsers("professor");
         foreach ($lessonUsers as $value){
      $mail_recipients[] = $value['login'];
     }
     break;
    case "admin":
     $result = eF_getTableData("users", "*", "user_type='administrator' and user_types_ID=0"); //not
     foreach($result as $value){
      $mail_recipients[] = $value['login'];
     }
    break;
   }
   //$list = implode(",",$mail_recipients);

         $pm = new eF_PersonalMessage($_SESSION['s_login'], $mail_recipients, $values['subject'], $values['body']);

         if ($_FILES['attachment']['name'][0] != "") {
             if ($_FILES['attachment']['size'][0] ==0 || $_FILES['attachment']['size'][0] > G_MAXFILESIZE ) { //If the directory could not be created, display an erro message
                 $message = _EACHFILESIZEMUSTBESMALLERTHAN." ".G_MAXFILESIZE." Bytes";
                 $message_type = 'failure';
             }
            //Upload user avatar file
             $pm -> sender_attachment_timestamp = time();

             $user_dir = G_UPLOADPATH.$_SESSION['s_login'].'/message_attachments/Sent/'.$pm -> sender_attachment_timestamp.'/';
             mkdir($user_dir,0755);
             $filesystem = new FileSystemTree($user_dir);
             $uploadedFile = $filesystem -> uploadFile('attachment', $user_dir, 0);

             $pm -> sender_attachment_fileId = $uploadedFile['id'];
             $pm -> setAttachment($uploadedFile['path']);
         }

         if ($pm -> send($values['email'], $values)) {
             $message = _MESSAGEWASSENT;
             $message_type = 'success';
         } else {
             $message = $pm -> errorMessage;
             $message_type = 'failure';
         }

  }
  $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty); //Create a smarty renderer
  $renderer -> setRequiredTemplate (
      '{$html}{if $required}
         &nbsp;<span class = "formRequired">*</span>
      {/if}');
  $form -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR); //Set javascript error messages
  $form -> setRequiredNote(_REQUIREDNOTE);
  $form -> accept($renderer); //Assign this form to the renderer, so that corresponding template code is created

  $smarty -> assign('T_MODULE_MAIL_FORM', $renderer -> toArray());
  $smarty -> assign("T_MESSAGE_MAIL" , $message);
  $smarty -> assign("T_MESSAGE_MAIL_TYPE" , $message_type);
  //pr($renderer -> toArray());
  return true;
 }

 public function getSmartyTpl(){
  $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_MAIL_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_MAIL_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_MAIL_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
 }

 public function getLessonModule() {
  $smarty = $this -> getSmartyVar();
        return true;
 }

    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_MAIL_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_MAIL_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_MAIL_BASELINK", $this -> moduleBaseLink);
        $current_user = $this -> getCurrentUser();
  $smarty -> assign("T_MODULE_CURRENT_USER" , $current_user ->getType());
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getLessonLinkInfo() {
            return array('title' => _MAILS_MODULEMAILS,
                         'image' => 'images/32x32/mail.png',
                         'link' => $this -> moduleBaseUrl);

    }
    public function isLessonModule(){
  return true;
 }

}
?>
