#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

print('Checking whether a template is available... ');
if (true === $phpLiveDocx->templateExists('template-1.docx')) {
    print('EXISTS. ');
} else {
    print('DOES NOT EXIST. ');
}
print("DONE\n");