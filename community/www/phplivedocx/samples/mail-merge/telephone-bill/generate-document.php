#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';


$date = new Zend_Date();

$date->setLocale(LOCALE);

$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

$phpLiveDocx->setLocalTemplate('template.doc');


$phpLiveDocx->assign('customer_number', sprintf("#%'10s\n",  rand(0,1000000000)));
$phpLiveDocx->assign('invoice_number',  sprintf("#%'10s\n",  rand(0,1000000000)));
$phpLiveDocx->assign('account_number',  sprintf("#%'10s\n",  rand(0,1000000000)));


$billData = array (  
    'phone'           => '+49 421 335 9000',
    'date'            => $date->get(Zend_Date::DATE_LONG),
    'name'            => 'James Henry Brown',
    'service_phone'   => '+49 421 335 910',
    'service_fax'     => '+49 421 335 9180',
    'month'           => sprintf('%s %s', $date->get(Zend_Date::MONTH_NAME), $date->get(Zend_Date::YEAR)),
    'monthly_fee'     =>  '€ 15.00',
    'total_net'       => '€ 100.00',
    'tax'             =>      '19%',
    'tax_value'       =>  '€ 15.00',
    'total'           => '€ 130.00'
);

$phpLiveDocx->assign($billData);


$billConnections = array (
    array ('connection_number' => '+49 421 335 912', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 913', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 914', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 916', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03')
);

$phpLiveDocx->assign('connection', $billConnections);


$documentProperties = array (
    'title'    => sprintf('Telephone Invoice (%s)', $billData['name']),
    'author'   => 'TIS Telecom', 
    'subject'  => sprintf('Your telephone invoice for %s', $billData['month']),
    'keywords' => sprintf('Telephone, Payment, Invoice, %s', $billData['month'])
);

$phpLiveDocx->setDocumentProperties($documentProperties);


$phpLiveDocx->createDocument();

$document = $phpLiveDocx->retrieveDocument('pdf');

unset($phpLiveDocx);

file_put_contents('document.pdf', $document);

