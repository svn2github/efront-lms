#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

print(listDecorator($phpLiveDocx->listTemplates()));

unset($phpLiveDocx);