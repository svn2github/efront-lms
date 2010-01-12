#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

printf("Supported TEMPLATE file formats (input)  : %s.\n",
    arrayDecorator($phpLiveDocx->getTemplateFormats()));

printf("Supported DOCUMENT file formats (output) : %s.\n",
    arrayDecorator($phpLiveDocx->getDocumentFormats()));

unset($phpLiveDocx);
