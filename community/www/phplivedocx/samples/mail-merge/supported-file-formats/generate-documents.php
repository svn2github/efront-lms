#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$date = new Zend_Date();

$date->setLocale(LOCALE);

$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

$phpLiveDocx->assign('software', 'ACE Downloader 2.8');
$phpLiveDocx->assign('licensee', 'Paul Peterson');
$phpLiveDocx->assign('company',  'Bresoft Ltd');
$phpLiveDocx->assign('date',     $date->get(Zend_Date::DATE_LONG));
$phpLiveDocx->assign('time',     $date->get(Zend_Date::TIME_LONG));
$phpLiveDocx->assign('city',     'Royal Tunbridge Wells');
$phpLiveDocx->assign('country',  'United Kingdom');

$phpLiveDocx->createDocument();

foreach ($phpLiveDocx->getDocumentFormats() as $format) {
    $documentFile = sprintf('document.%s', $format);
    printf('Retrieving %s version (%s)... ', strtoupper($format), $documentFile);
    $document = $phpLiveDocx->retrieveDocument($format);
    file_put_contents($documentFile, $document);
    print("DONE.\n");
}

unset($phpLiveDocx);
