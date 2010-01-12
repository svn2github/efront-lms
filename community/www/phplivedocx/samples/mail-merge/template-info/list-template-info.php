#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

// -----------------------------------------------------------------------------

$templateName = 'template-1-text-field.docx';

$phpLiveDocx->setLocalTemplate($templateName);

printf("Field names in %s:\n", $templateName);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf("- %s\n", $fieldName);   
}

// -----------------------------------------------------------------------------

$templateName = 'template-2-text-fields.doc';

$phpLiveDocx->setLocalTemplate($templateName);

printf("\nField names in %s:\n", $templateName);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf("- %s\n", $fieldName);     
}

// -----------------------------------------------------------------------------

$templateName = 'template-block-fields.doc';

$phpLiveDocx->setLocalTemplate($templateName);

printf("\nField names in %s:\n", $templateName);

$fieldNames = $phpLiveDocx->getFieldNames();
foreach ($fieldNames as $fieldName) {
    printf("- %s\n", $fieldName);     
}

printf("\nBlock names in %s:\n", $templateName);

$blockNames = $phpLiveDocx->getBlockNames();
foreach ($blockNames as $blockName) {
    printf("- %s\n", $blockName);    
}

printf("\nBlock field names in %s:\n", $templateName);

foreach ($blockNames as $blockName) {
    $blockFieldNames = $phpLiveDocx->getBlockFieldNames($blockName);
    foreach ($blockFieldNames as $blockFieldName) {
        printf("- %s::%s\n", $blockName, $blockFieldName);          
    }
}

print("\n");

// -----------------------------------------------------------------------------

unset($phpLiveDocx);