#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

print('Uploading template... ');
$phpLiveDocx->uploadTemplate('template-1.docx');
print("DONE.\n");

print('Uploading template... ');
$phpLiveDocx->uploadTemplate('template-2.docx');
print("DONE.\n");
