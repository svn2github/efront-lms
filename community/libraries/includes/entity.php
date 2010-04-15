<?php
/**

 * Common entity file

 * 

 * This file handles the common operations in an entity

 * @version 3.6.0

 */
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}
$smarty -> assign("T_ENTITY_NAME", $entityName);
$smarty -> assign("_change_", $_change_);
$loadScripts[] = 'includes/entity';
if (isset($_GET['delete']) && in_array($_GET['delete'], $legalValues) && $_change_) {
    try {
        $entity = new $entityName($_GET['delete']);
        $entity -> delete();
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} else if (isset($_GET['activate']) && in_array($_GET['activate'], $legalValues) && $_change_) {
    try {
        $entity = new $entityName($_GET['activate']);
        $entity -> activate();
        echo json_encode(array('active' => 1));
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} else if (isset($_GET['deactivate']) && in_array($_GET['deactivate'], $legalValues) && $_change_) {
    try {
        $entity = new $entityName($_GET['deactivate']);
        $entity -> deactivate();
        echo json_encode(array('active' => 0));
    } catch (Exception $e) {
        header("HTTP/1.0 500 ");
        echo urlencode($e -> getMessage()).' ('.$e -> getCode().')';
    }
    exit;
} else if ((isset($_GET['add']) || (isset($_GET['edit']) && in_array($_GET['edit'], $legalValues))) && $_change_) {
    try {
        //Create the form, unless it already exists
        if (!isset($entityForm) || !($entityForm instanceof HTML_QuickForm)) {
            $entityForm = new HTML_QuickForm("create_form", "post", basename($_SERVER['PHP_SELF'])."?ctg=$entityName".(isset($_GET['add']) ? '&add=1' : '&edit='.$_GET['edit']), "", null, true);
        }
        //Initialize the entity, either with the specified edit parameter, or with an empty array
        if (isset($_GET['edit'])) {
         $entity = new $entityName($_GET['edit']);
     } else {
         $entity = new $entityName(array());
     }
     //Get the form from the entity itself
     $entityForm = $entity -> getForm($entityForm);
     //try/catch here in order to keep displaying the form in case of an error
     try {
         //Assign the form to the entity to handle it accordingly
         if ($entityForm -> isSubmitted() && $entityForm -> validate()) {
             //if $values is set, then the parent file may have done some processing itself
             if (isset($values) && $values) {
                 $entity -> handleForm($entityForm, array_merge($entityForm -> exportValues(), $values));
             } else {
                 $entity -> handleForm($entityForm);
             }

             $message = _OPERATIONCOMPLETEDSUCCESSFULLY;
             $message_type = 'success';
         }
     } catch (Exception $e) {
         $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
         $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
         $message_type = 'failure';
     }


        $entityForm -> setJsWarnings(_BEFOREJAVASCRIPTERROR, _AFTERJAVASCRIPTERROR);
        $entityForm -> setRequiredNote(_REQUIREDNOTE);

        $renderer = new HTML_QuickForm_Renderer_ArraySmarty($smarty);
     $renderer->setRequiredTemplate(
        '{$html}{if $required}
             &nbsp;<span class = "formRequired">*</span>
         {/if}'
         );

     $renderer->setErrorTemplate(
        '{$html}{if $error}
             <div class = "formError">{$error}</div>
         {/if}'
         );
        $entityForm -> accept($renderer);

     $smarty -> assign('T_ENTITY_FORM', $renderer -> toArray());

    } catch (Exception $e) {
        $smarty -> assign("T_EXCEPTION_TRACE", $e -> getTraceAsString());
        $message = $e -> getMessage().' ('.$e -> getCode().') &nbsp;<a href = "javascript:void(0)" onclick = "eF_js_showDivPopup(\''._ERRORDETAILS.'\', 2, \'error_details\')">'._MOREINFO.'</a>';
        $message_type = 'failure';
    }
}


?>
