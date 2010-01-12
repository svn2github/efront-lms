#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

$counter = 1;
foreach ($phpLiveDocx->listTemplates() as $result) {
    printf('%d) %s', $counter, $result['filename']);
    $template = $phpLiveDocx->downloadTemplate($result['filename']);
    file_put_contents('downloaded-' . $result['filename'], $template);
    print(" - DOWNLOADED.\n");
    $counter++;
}

unset($phpLiveDocx);