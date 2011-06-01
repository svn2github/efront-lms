<?php
class module_blogs extends EfrontModule {

 public function getName() {
        return _BLOGS_BLOG;
    }

    public function getPermittedRoles() {
        return array("student","professor","administrator");
    }

 public function getModule() {
  $smarty = $this -> getSmartyVar();
  $currentLesson = $this -> getCurrentLesson();
        $currentUser = $this -> getCurrentUser();
  try {
            $role = $currentUser -> getRole($this -> getCurrentLesson());
        }
        catch (Exception $e){
            $currentUser = EfrontUserFactory :: factory($_SESSION['s_login']);
            $role = $currentUser -> getRole($this -> getCurrentLesson());
        }

  if (isset($_GET['delete_blog']) && eF_checkParameter($_GET['delete_blog'], 'id')) {
   $blog = eF_getTableData("module_blogs","users_LOGIN","id=".$_GET['delete_blog']);
   if ($blog[0]['users_LOGIN'] != $_SESSION['s_login']){
    eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_BLOGS_NOACCESS));
    exit;
   }
   $articles = eF_getTableDataFlat("module_blogs_articles","id","blogs_ID=".$_GET['delete_blog']);

   if (sizeof($articles) > 0) {
    $articlesList = implode(",",$articles['id']);
    eF_deleteTableData("module_blogs_comments", "blogs_articles_ID IN ($articlesList)");
   }
   eF_deleteTableData("module_blogs_articles", "blogs_ID=".$_GET['delete_blog']);
   eF_deleteTableData("module_blogs", "id=".$_GET['delete_blog']);
  }

  if (isset($_GET['deactivate_blog']) && eF_checkParameter($_GET['deactivate_blog'], 'id')) {
   $blog = eF_getTableData("module_blogs","users_LOGIN","id=".$_GET['deactivate_blog']);
   if ($blog[0]['users_LOGIN'] != $_SESSION['s_login']){
    eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_BLOGS_NOACCESS));
    exit;
   }
   if (eF_updateTableData("module_blogs", array('active' => 0), "id=".$_GET['deactivate_blog'])) {
                $message = _BLOGS_BLOGDEACTIVATED;
                $message_type = 'success';
            } else {
                $message = _BLOGS_BLOGDEACTIVATEDPROBLEM;
                $message_type = "failure";
            }
  }

  if (isset($_GET['activate_blog']) && eF_checkParameter($_GET['activate_blog'], 'id')) {
   $blog = eF_getTableData("module_blogs","users_LOGIN","id=".$_GET['activate_blog']);
   if ($blog[0]['users_LOGIN'] != $_SESSION['s_login']){
    eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_BLOGS_NOACCESS));
    exit;
   }
   if (eF_updateTableData("module_blogs", array('active' => 1), "id=".$_GET['activate_blog'])) {
                $message = _BLOGS_BLOGACTIVATED;
                $message_type = 'success';
            } else {
                $message = _BLOGS_BLOGACTIVATEDPROBLEM;
                $message_type = "failure";
            }
  }

  if (isset($_GET['delete_article']) && eF_checkParameter($_GET['delete_article'], 'id')) {
   $blog = eF_getTableData("module_blogs_articles","blogs_ID,users_LOGIN","id=".$_GET['delete_article']);

   $blogTemp = eF_getTableData("module_blogs","users_LOGIN","id=".$blog[0]['blogs_ID']);
   if ($blog[0]['users_LOGIN'] != $_SESSION['s_login'] && $blogTemp[0]['users_LOGIN'] != $_SESSION['s_login']){
    eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$blog[0]['blogs_ID']."&message=".urlencode(_BLOGS_NOACCESS));
    exit;
   }

   eF_deleteTableData("module_blogs_comments", "blogs_articles_ID=".$_GET['delete_article']);
   eF_deleteTableData("module_blogs_articles", "id=".$_GET['delete_article']);
   $message = _BLOGS_ARTICLEWASDELETEDSUCCESSFULLY;
   $message_type = "success";
   eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$blog[0]['blogs_ID']."&message=".urlencode($message)."&message_type=".$message_type);
  }

  if (isset($_GET['delete_comment']) && eF_checkParameter($_GET['delete_comment'], 'id')) {
   $article = eF_getTableData("module_blogs_articles","blogs_ID,users_LOGIN","id=".$_GET['article_id']);
   $blogTemp = eF_getTableData("module_blogs","users_LOGIN","id=".$article[0]['blogs_ID']);
   $commentTemp = eF_getTableData("module_blogs_comments","users_LOGIN","id=".$_GET['delete_comment']);
   if ($commentTemp[0]['users_LOGIN'] != $_SESSION['s_login'] && $blogTemp[0]['users_LOGIN'] != $_SESSION['s_login']){
    eF_redirect("".$this -> moduleBaseUrl."&view_article=".$_GET['article_id']."&message=".urlencode(_BLOGS_NOACCESS));
    exit;
   }
   eF_deleteTableData("module_blogs_comments", "id=".$_GET['delete_comment']);

   $message = _BLOGS_COMMENTWASDELETEDSUCCESSFULLY;
   $message_type = "success";
   eF_redirect("".$this -> moduleBaseUrl."&view_article=".$_GET['article_id']."&message=".urlencode($message)."&message_type=".$message_type);
  }


  if ((isset($_GET['add_blog']) || isset($_GET['edit_blog']))) {
   if (isset($_GET['add_blog']) && $_SESSION['s_type'] != "professor") {
    eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_BLOGS_NOACCESS));
   }
   if (isset($_GET['edit_blog'])) {
    $blog_data = eF_getTableData("module_blogs", "*", "id=".$_GET['edit_blog']);
    if ($blog_data[0]['users_LOGIN'] != $_SESSION['s_login']) {
     eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode(_BLOGS_NOACCESS));
    }
    $post_target = $this -> moduleBaseUrl.'&edit_blog='.$_GET['edit_blog'];
   } else {
    $post_target = $this -> moduleBaseUrl.'&add_blog';
   }
   global $load_editor;
   $load_editor = true;
      $form = new HTML_QuickForm("blog_add_form", "post", $post_target."&blog_id=".$_GET['blog_id'], "", null, true); //Build the form
   $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
   $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
   $form -> addElement('textarea', 'description', _DESCRIPTION, 'class = "inputContentTextarea simpleEditor" style = "width:100%;height:20em;"');
   $form -> addElement("advcheckbox", "registered", _BLOGS_ACCESSIBLE, null, 'class = "inputCheckBox"', array(0, 1));
   $form -> addElement('submit', 'submit_add_blog', _SUBMIT, 'class = "flatButton"');

   if (isset($_GET['edit_blog'])) {
    $form -> setDefaults(array('title' => $blog_data[0]['name'],
         'description' => $blog_data[0]['description'],
         'registered' => $blog_data[0]['registered']));
   }

   if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
    $values = $form -> exportValues();

    $fields = array("name" => $values['title'],
       "lessons_ID" => $values['lessons_ID'] ? $values['lessons_ID'] : $_SESSION['s_lessons_ID'],
       "description" => $values['description'],
       "registered" => $values['registered']);

    if (isset($_GET['edit_blog'])) {
     if (eF_updateTableData("module_blogs", $fields, "id=".$_GET['edit_blog'])) {
      $message = _BLOGS_BLOGUPDATEDSUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _BLOGS_BLOGNOTUPDATED;
      $message_type = 'failure';
     }
      eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type);
    } else {
     $fields['users_LOGIN'] = $_SESSION['s_login'];
     $fields['timestamp'] = time();
     //pr($fields);
     $new_id = eF_insertTableData("module_blogs", $fields);
     if ($new_id) {
      $message = _BLOGS_BLOGADDEDSUCCESSFULLY;
      $message_type = 'success';
      eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type."&edit_blog=".$new_id."&tab=blog_creators");
     } else {
      $message = _BLOGS_BLOGNOTADDED;
      $message_type = 'failure';
      eF_redirect("".$this -> moduleBaseUrl."&message=".urlencode($message)."&message_type=".$message_type);
     }

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
   $smarty -> assign('T_BLOG_ADD_FORM', $renderer -> toArray()); //Assign the form to the template

   try {
    $lessonUsers = $currentLesson -> getUsers(); //Get all users that have this lesson
    unset($lessonUsers[$currentUser -> login]); //Remove the current user from the list, he can't set parameters for his self!
    $users = $lessonUsers;

    $blogsCreators = eF_getTableDataFlat("module_blogs_users","*","blogs_ID=".$_GET['edit_blog']);
    $creatorsAssoc = array_combine(array_values($blogsCreators['users_LOGIN']), array_values($blogsCreators['users_LOGIN']));

    $nonBlogsCreators = array_diff_key($users,$creatorsAssoc);
    $blogsCreatorsTemp = array_diff_key($users,$nonBlogsCreators);

    foreach ($users as $key => $user) {
     in_array($key, array_values($blogsCreators['users_LOGIN'])) ? $users[$key]['blog_creator'] = true : $users[$key]['blog_creator'] = false;
    }


//pr($users);
    $roles = eF_getTableDataFlat("user_types","name","active=1 AND basic_user_type!='administrator'"); //Get available roles
    if (sizeof($roles) > 0) {
     $roles = array_combine($roles['name'], $roles['name']); //Match keys with values, it's more practical this way
    }
    $roles = array_merge(array('student' => _STUDENT, 'professor' => _PROFESSOR), $roles); //Append basic user types to the beginning of the array
//pr($roles);
    if (isset($_GET['ajax']) && $_GET['ajax'] == 'usersTable') {
     isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'uint') ? $limit = $_GET['limit'] : $limit = G_DEFAULT_TABLE_SIZE;

     if (isset($_GET['sort']) && eF_checkParameter($_GET['sort'], 'text')) {
      $sort = $_GET['sort'];
      isset($_GET['order']) && $_GET['order'] == 'desc' ? $order = 'desc' : $order = 'asc';
     } else {
      $sort = 'login';
     }
     $users = eF_multiSort($users, $sort, $order);
     $smarty -> assign("T_USERS_SIZE", sizeof($users));
     if (isset($_GET['filter'])) {
      $users = eF_filterData($users, $_GET['filter']);
     }
     if (isset($_GET['limit']) && eF_checkParameter($_GET['limit'], 'int')) {
      isset($_GET['offset']) && eF_checkParameter($_GET['offset'], 'int') ? $offset = $_GET['offset'] : $offset = 0;
      $users = array_slice($users, $offset, $limit);
     }
     $smarty -> assign("T_ROLES", $roles);
     $smarty -> assign("T_ALL_USERS", $users);

     $smarty -> assign("T_BLOGS_USERS", $blogsCreators['users_LOGIN']); //We assign separately the lesson's users, to know when to display the checkboxes as "checked"
     $smarty -> assign("T_CURRENT_USER", $currentUser);
     return true;

    }

   } catch (Exception $e) {
    $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
    $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
    $message_type = 'failure';
   }

   if (isset($_GET['postAjaxRequest'])) {
    try {
    if (isset($_GET['login']) && eF_checkParameter($_GET['login'], 'login')) {
      if (!in_array($_GET['login'], array_values($blogsCreators['users_LOGIN']))) {
       $fields = array ('blogs_ID' => $_GET['edit_blog'],
           'users_login' => $_GET['login']);
       $res = eF_insertTableData("module_blogs_users",$fields);

      }
      if (in_array($_GET['login'], array_values($blogsCreators['users_LOGIN']))) {
       eF_deleteTableData("module_blogs_users", "blogs_ID=".$_GET['edit_blog']." AND users_LOGIN='".$_GET['login']."'");
      }
     } else if (isset($_GET['addAll'])) {
      isset($_GET['filter']) ? $nonBlogsCreators = eF_filterData($nonBlogsCreators, $_GET['filter']) : null;
      foreach ($nonBlogsCreators as $key => $value) {
       $fields = array ('blogs_ID' => $_GET['edit_blog'],
           'users_login' => $key);
       $res = eF_insertTableData("module_blogs_users",$fields);
      }
     } else if (isset($_GET['removeAll'])) {
      isset($_GET['filter']) ? $blogCreators = eF_filterData($blogsCreatorsTemp, $_GET['filter']) : null;
      foreach ($blogsCreatorsTemp as $key => $value) {
       eF_deleteTableData("module_blogs_users", "blogs_ID=".$_GET['edit_blog']." AND users_LOGIN='".$key."'");
      }
     }
    } catch (Exception $e) {
     header("HTTP/1.0 500 ");
     echo $e -> getMessage().' ('.$e -> getCode().')';
    }
    exit;
   }
  } elseif ((isset($_GET['add_article']) || isset($_GET['edit_article']))) {
   $resAccess = eF_getTableData("module_blogs", "*", "id=".$_GET['blog_id']);
   if (isset($_GET['edit_article'])) {
    $article_data = eF_getTableData("module_blogs_articles", "*", "id=".$_GET['edit_article']);
    if ($resAccess[0]['users_LOGIN'] != $_SESSION['s_login'] && $article_data[0]['users_LOGIN'] != $_SESSION['s_login']) {
     eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$_GET['blog_id']."&message=".urlencode(_BLOGS_NOACCESS));
    }
    $post_target = $this -> moduleBaseUrl.'&edit_article='.$_GET['edit_article'];
   } else {
    $creator = eF_getTableData("module_blogs_users","*", "blogs_ID=".$_GET['blog_id']." and users_LOGIN='".$_SESSION['s_login']."'");
    if ($resAccess[0]['users_LOGIN'] != $_SESSION['s_login'] && sizeof($creator) == 0) {
     eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$_GET['blog_id']."&message=".urlencode(_BLOGS_NOACCESS));
    }
    $post_target = $this -> moduleBaseUrl.'&add_article';
   }
   global $load_editor;
   $load_editor = true;
      $form = new HTML_QuickForm("article_add_form", "post", $post_target."&blog_id=".$_GET['blog_id'], "", null, true); //Build the form
   $form -> addElement('text', 'title', _TITLE, 'class = "inputText"');
   $form -> addRule('title', _THEFIELD.' "'._TITLE.'" '._ISMANDATORY, 'required', null, 'client');
   $form -> addElement('textarea', 'data', _DATA, 'class = "simpleEditor"  id="blog_article_data" style = "width:100%;height:25em;"');
   $form -> addElement('submit', 'submit_add_article', _SUBMIT, 'class = "flatButton"');

   if (isset($_GET['edit_article'])) {
   $form -> setDefaults(array( 'title' => $article_data[0]['title'],
          'data' => $article_data[0]['data']));
   }

   if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
    $values = $form -> exportValues();

    $fields = array("title" => $values['title'],
       "data" => $values['data']);

    if (isset($_GET['edit_article'])) {
    if (eF_updateTableData("module_blogs_articles", $fields, "id=".$_GET['edit_article'])) {
      $message = _BLOGS_ARTICLEUPDATEDSUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _BLOGS_ARTICLENOTUPDATED;
      $message_type = 'failure';
     }

      eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$_GET['blog_id']."&message=".urlencode($message)."&message_type=".$message_type);
    } else {
     $fields['users_LOGIN'] = $_SESSION['s_login'];
     $fields['timestamp'] = time();
     $fields['blogs_ID'] = $_GET['blog_id'];
     //pr($fields);exit;
     $new_id = eF_insertTableData("module_blogs_articles", $fields);
     if ($new_id) {
      $message = _BLOGS_ARTICLEADDEDSUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _BLOGS_ARTICLENOTADDED;
      $message_type = 'failure';
     }
     eF_redirect("".$this -> moduleBaseUrl."&view_blog=".$_GET['blog_id']."&message=".urlencode($message)."&message_type=".$message_type);
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
   $smarty -> assign('T_ARTICLE_ADD_FORM', $renderer -> toArray()); //Assign the form to the template
  } elseif ((isset($_GET['add_comment']) || isset($_GET['edit_comment']))) {
   if (isset($_GET['edit_comment'])) {
    $comment_data = eF_getTableData("module_blogs_comments", "*", "id=".$_GET['edit_comment']);
    $blogAccess = eF_getTableData("module_blogs_articles", "*", "id=".$_GET['article_id']);
    if ($comment_data[0]['users_LOGIN'] != $_SESSION['s_login'] && $blogAccess[0]['users_LOGIN'] != $_SESSION['s_login']) {
     eF_redirect("".$this -> moduleBaseUrl."&view_article=".$_GET['article_id']."&message=".urlencode(_BLOGS_NOACCESS));
    }
    $post_target = $this -> moduleBaseUrl.'&edit_comment='.$_GET['edit_comment'];
   } else {
    $post_target = $this -> moduleBaseUrl.'&add_comment';
   }
   global $load_editor;
   $load_editor = true;
      $form = new HTML_QuickForm("blog_comment_form", "post", $post_target."&article_id=".$_GET['article_id'], "", null, true); //Build the form
   $form -> addElement('textarea', 'data', _COMMENT, 'class = "inputContentTextarea simpleEditor" style = "width:80%;height:10em;"');
   $form -> addElement('submit', 'submit_add_comment', _SUBMIT, 'class = "flatButton"');

   if (isset($_GET['edit_comment'])) {
   $form -> setDefaults(array('data' => $comment_data[0]['data']));
   }

   if ($form -> isSubmitted() && $form -> validate()) { //If the form is submitted and validated
    $values = $form -> exportValues();

    $fields = array("data" => $values['data'],
       "users_LOGIN" => $currentUser -> user['login'],
       "blogs_articles_ID" => $_GET['article_id'],
       "timestamp" => time());

    if (isset($_GET['edit_comment'])) {
     if (eF_updateTableData("module_blogs_comments", $fields, "id=".$_GET['edit_comment'])) {
      $message = _BLOGS_COMMENTUPDATEDSUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _BLOGS_COMMENTNOTUPDATED;
      $message_type = 'failure';
     }
      eF_redirect("".$this -> moduleBaseUrl."&view_article=".$_GET['article_id']."&message=".urlencode($message)."&message_type=".$message_type);
    } else {
     //pr($fields);
     $new_id = eF_insertTableData("module_blogs_comments", $fields);
     if ($new_id) {
      $message = _BLOGS_COMMENTADDEDSUCCESSFULLY;
      $message_type = 'success';
     } else {
      $message = _BLOGS_COMMENTNOTADDED;
      $message_type = 'failure';
     }
     eF_redirect("".$this -> moduleBaseUrl."&view_article=".$_GET['article_id']."&message=".urlencode($message)."&message_type=".$message_type);
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
   $smarty -> assign('T_COMMENT_ADD_FORM', $renderer -> toArray()); //Assign the form to the template


   $article = eF_getTableData("module_blogs_articles", "*", "id=".$_GET['article_id']);

   $blogComments = eF_getTableData("module_blogs_comments", "*", "blogs_articles_ID=".$_GET['article_id'],"timestamp asc");
   $article[0]['comments'] = sizeof($blogComments);
   $blog = eF_getTableData("module_blogs", "*", "id=".$article[0]['blogs_ID']);

   $creator = eF_getTableData("module_blogs_users","*", "blogs_ID=".$article[0]['blogs_ID']." and users_LOGIN='".$_SESSION['s_login']."'");
   if (sizeof($creator) > 0) {
    $smarty -> assign("T_BLOGS_ISBLOGCREATOR", 1);
   }
   $smarty -> assign("T_BLOGS_BLOG", $blog[0]);
   $smarty -> assign("T_BLOGS_ARTICLE", $article[0]);
   $smarty -> assign("T_BLOGS_COMMENTS", $blogComments);

  } elseif (isset($_GET['view_blog'])) {
   $blog = eF_getTableData("module_blogs", "*", "id=".$_GET['view_blog']);
   $creator = eF_getTableData("module_blogs_users","*", "blogs_ID=".$_GET['view_blog']." and users_LOGIN='".$_SESSION['s_login']."'");
   if (sizeof($creator) > 0) {
    $smarty -> assign("T_BLOGS_ISBLOGCREATOR", 1);
   }
   $blogPosts = eF_getTableData("module_blogs_articles", "*", "blogs_ID=".$_GET['view_blog'],"timestamp desc");
//pr($blogPosts);
   $indexing = array();

   foreach ($blogPosts as $key => $value) {
    $indexing[date('Y',$blogPosts[$key]['timestamp'])][date('F',$blogPosts[$key]['timestamp'])][$value['id']] = $value['title'];

    $blogComments = eF_getTableData("module_blogs_comments", "*", "blogs_articles_ID=".$value['id'], "timestamp desc");
    $blogPosts[$key]['last_comment'] = $blogComments[0];
    $blogPosts[$key]['comments'] = sizeof($blogComments);
   }
   //pr($indexing);

   //pr($blogPosts);
   $lastComments = eF_getTableData("module_blogs_comments as com,module_blogs_articles as art", "com.id as comment_id,com.data,com.timestamp,art.id as article_id,art.title,com.users_LOGIN", "com.blogs_articles_ID=art.id and art.blogs_ID=".$_GET['view_blog'],"com.timestamp desc");
   $smarty -> assign("T_BLOGS_INDEXING", $indexing);
   $smarty -> assign("T_BLOGS_LASTCOMMENTS", $lastComments);
   $smarty -> assign("T_BLOGS_BLOG", $blog[0]);
   $smarty -> assign("T_BLOGS_POSTS", $blogPosts);
  } elseif (isset($_GET['view_article'])) {
   $article = eF_getTableData("module_blogs_articles", "*", "id=".$_GET['view_article']);

   $blogComments = eF_getTableData("module_blogs_comments", "*", "blogs_articles_ID=".$_GET['view_article'],"timestamp asc");
   $article[0]['comments'] = sizeof($blogComments);
   $blog = eF_getTableData("module_blogs", "*", "id=".$article[0]['blogs_ID']);

   $creator = eF_getTableData("module_blogs_users","*", "blogs_ID=".$article[0]['blogs_ID']." and users_LOGIN='".$_SESSION['s_login']."'");
   if (sizeof($creator) > 0) {
    $smarty -> assign("T_BLOGS_ISBLOGCREATOR", 1);
   }

   $smarty -> assign("T_BLOGS_BLOG", $blog[0]);
   $smarty -> assign("T_BLOGS_ARTICLE", $article[0]);
   $smarty -> assign("T_BLOGS_COMMENTS", $blogComments);


  } else {
   $lessonBlogs = eF_getTableData("module_blogs", "*", "lessons_ID=".$currentLesson->lesson['id']);
   foreach ($lessonBlogs as $key => $value) {
   //echo $value['id'];
    $res = eF_getTableData("module_blogs_articles", "*", "blogs_ID=".$value['id'], "timestamp desc");
    $lessonBlogs[$key]['last_article'] = $res[0];
  /*		$creators  = eF_getTableData("module_blogs_users","*","blogs_ID=".$value['id']." and users_LOGIN='".$_SESSION['s_login']."'");

				if (sizeof($creators) > 0) {

					$lessonBlogs[$key]['is_creator'] = 1;

				} else{

					$lessonBlogs[$key]['is_creator'] = 0;

				} */
   }
   //pr($lessonBlogs);
   //$smarty -> assign("T_BLOGS_LASTARTICLE", $lastArticle);
   $smarty -> assign("T_BLOGS_LESSONBLOGS", $lessonBlogs);
   $smarty -> assign("T_BLOGS_CLESSON", $currentLesson);
   $smarty -> assign("T_BLOGS_CUSER", $currentUser);
   $smarty -> assign("T_BLOGS_ROLE", $role);
   return true;
  }
 }
 public function getSmartyTpl(){
  $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module.tpl";
 }
 public function getLessonModule() {
  $smarty = $this -> getSmartyVar();
  $inner_table_options = array(array('text' => _BLOGS_GOTOBLOGS,
           'image' => $this -> moduleBaseLink."images/redo.png", 'href' => $this -> moduleBaseUrl));
        $smarty -> assign("T_BLOGS_INNERTABLE_OPTIONS", $inner_table_options);
        $smarty -> assign("T_MODULE_BLOGS_BLOGPAGES" , $blogPages);

        $blogs = eF_getTableData("module_blogs","*","lessons_ID=".$_SESSION['s_lessons_ID'],"timestamp desc");

  $smarty -> assign("T_BLOGS_BLOGS", $blogs);

        return true;
 }

    public function getLessonSmartyTpl() {
        $smarty = $this -> getSmartyVar();
        $smarty -> assign("T_MODULE_BLOGS_BASEDIR" , $this -> moduleBaseDir);
        $smarty -> assign("T_MODULE_BLOGS_BASEURL" , $this -> moduleBaseUrl);
        $smarty -> assign("T_MODULE_BLOGS_BASELINK", $this -> moduleBaseLink);
        return $this -> moduleBaseDir . "module_InnerTable.tpl";
    }

    public function getSidebarLinkInfo() {

     $currentUser = $this -> getCurrentUser();
        $link_of_menu_system = array (array ('id' => 'blogs_link_id1',
                                               'title' => _BLOGS_BLOG,
                                               'image' => $this -> moduleBaseDir.'images/eFrontBlog16',
                                               'eFrontExtensions' => '1',
                                               'link' => $this -> moduleBaseUrl));

  return array ("current_lesson" => $link_of_menu_system);
    }

 public function getNavigationLinks() {
        $currentUser = $this -> getCurrentUser();
  $currentLesson = $this -> getCurrentLesson();
        if (isset($_GET['view_blog'])){
   $res = eF_getTableData("module_blogs","name","id=".$_GET['view_blog']);
            return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl),
       array ('title' => $res[0]['name'], 'link' => $this -> moduleBaseUrl."&view_blog=".$_GET['view_blog']));
        }
  elseif (isset($_GET['view_article'])){
   $resArticle = eF_getTableData("module_blogs_articles","title,blogs_ID","id=".$_GET['view_article']);
   $resBlog = eF_getTableData("module_blogs","name","id=".$resArticle[0]['blogs_ID']);
            return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl),
       array ('title' => $resBlog[0]['name'], 'link' => $this -> moduleBaseUrl."&view_blog=".$resArticle[0]['blogs_ID']),
       array ('title' => $resArticle[0]['title'], 'link' => $this -> moduleBaseUrl."&view_article=".$_GET['view_article']));
  } elseif (isset($_GET['add_blog']) || isset($_GET['edit_blog'])) {
    return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl),
       array ('title' => _BLOGS_EDITBLOG, 'link' => $_SERVER['REQUEST_URI']));
  }elseif (isset($_GET['add_article']) || isset($_GET['edit_article'])) {
   $resBlog = eF_getTableData("module_blogs","name","id=".$_GET['blog_id']);
   return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl),
       array ('title' => $resBlog[0]['name'], 'link' => $this -> moduleBaseUrl."&view_blog=".$_GET['blog_id']),
       array ('title' => _BLOGS_EDITARTICLE, 'link' => $_SERVER['REQUEST_URI']));
  } elseif (isset($_GET['add_comment']) || isset($_GET['edit_comment'])) {
   $resArticle = eF_getTableData("module_blogs_articles","title,blogs_ID","id=".$_GET['article_id']);
   $resBlog = eF_getTableData("module_blogs","name","id=".$resArticle[0]['blogs_ID']);
   return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($this -> getCurrentLesson()) . ".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl),
       array ('title' => $resBlog[0]['name'], 'link' => $this -> moduleBaseUrl."&view_blog=".$resArticle[0]['blogs_ID']),
       array ('title' => $resArticle[0]['title'], 'link' => $this -> moduleBaseUrl."&view_article=".$_GET['article_id']),
       array ('title' => _BLOGS_EDITCOMMENT, 'link' => $_SERVER['REQUEST_URI']));
  }
        else{
   return array ( array ('title' => _MYLESSONS, 'onclick' => "location='".$currentUser -> getRole($currentLesson).".php?ctg=lessons';top.sideframe.hideAllLessonSpecific();"),
       array ('title' => $currentLesson -> lesson['name'], 'link' => $currentUser -> getRole($currentLesson).".php?ctg=control_panel"),
       array ('title' => _BLOGS_BLOG, 'link' => $this -> moduleBaseUrl));
  }


    }

 public function getLessonCenterLinkInfo() {
        $currentUser = $this -> getCurrentUser();
        if ($currentUser -> getRole($this -> getCurrentLesson()) == "professor") {
            return array('title' => _BLOGS_BLOG,
                         'image' => $this -> moduleBaseDir.'images/eFrontBlog32.png',
                         'link' => $this -> moduleBaseUrl);
        }
    }

 public function getLinkToHighlight() {
        return 'blogs_link_id1';
    }

 public function onInstall() {
  eF_executeNew("drop table if exists module_blogs ");
  $res1 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_blogs` (
        `id` int(11) NOT NULL auto_increment,
        `name` varchar(255) NOT NULL,
        `lessons_ID` int(11) NOT NULL default '0',
        `users_LOGIN` varchar(255) NOT NULL,
        `description` text,
        `active` tinyint(1) NOT NULL default '1',
        `registered` tinyint(1) NOT NULL default '1',
        `timestamp` varchar(10) NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        eF_executeNew("drop table if exists module_blogs_articles ");
  $res2 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_blogs_articles` (
       `id` int(11) NOT NULL auto_increment,
       `title` varchar(255) NOT NULL,
       `blogs_ID` int(11) NOT NULL default '0',
       `users_LOGIN` varchar(255) NOT NULL,
       `timestamp` varchar(10) NOT NULL,
       `data` text,
       `active` tinyint(1) NOT NULL default '1',
       PRIMARY KEY (`id`)
       ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  eF_executeNew("drop table if exists module_blogs_comments");
  $res3 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_blogs_comments` (
       `id` int(11) NOT NULL auto_increment,
       `blogs_articles_ID` int(11) NOT NULL default '0',
       `users_LOGIN` varchar(255) NOT NULL,
       `timestamp` varchar(10) NOT NULL,
       `data` text,
       `active` tinyint(1) NOT NULL default '1',
       PRIMARY KEY (`id`)
       ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        eF_executeNew("drop table if exists module_blogs_users");
  $res4 = eF_executeNew("CREATE TABLE IF NOT EXISTS `module_blogs_users` (
       `blogs_ID` int(11) NOT NULL default '0',
       `users_LOGIN` varchar(255) NOT NULL,
       primary key (`users_LOGIN`, `blogs_ID`)
       ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");				

  return ($res1 && $res2 && $res3 && $res4);
 }


 public function onUninstall() {
        $res1 = eF_executeNew("DROP TABLE module_blogs_comments;");
        $res2 = eF_executeNew("DROP TABLE module_blogs_articles;");
  $res3 = eF_executeNew("DROP TABLE module_blogs");
  $res4 = eF_executeNew("DROP TABLE module_blogs_users");
        return ($res1 && $res2 && $res3 && $res4);
    }

 public function getModuleCSS (){
  return $this->moduleBaseDir.'blogs_custom.css';
 }


 public function isLessonModule() {
  return true;
 }
}
?>
