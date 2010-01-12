#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';

// -----------------------------------------------------------------------------

// Get geographical information, based on current IP

try {

    $client = new Zend_Http_Client();

    $client->setUri('http://ipLocationTools.com/ip_query.php?output=json');

    $response = $client->request();

    if (200 === $response->getStatus()) {

        $geoIp = Zend_Json::decode($response->getBody());

        if (isset($geoIp['City']) && strlen($geoIp['City']) > 0) {
            $city = $geoIp['City'];
        }

        if (isset($geoIp['CountryCode']) && strlen($geoIp['CountryCode']) > 0) {

            $countryCode = $geoIp['CountryCode'];

            $countries = Zend_Locale::getCountryTranslationList(LOCALE);

            if (isset($countries[$countryCode])) {
                $country = $countries[$countryCode];
            }
        }
    }

} catch (Zend_Http_Client_Exception $e) { }

// -----------------------------------------------------------------------------

// Generate document

$date = new Zend_Date();

$date->setLocale(LOCALE);

$phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

$phpLiveDocx->setLocalTemplate('template.docx');

$phpLiveDocx->assign('software', 'Magic Graphical Compression Suite v1.9');
$phpLiveDocx->assign('licensee', 'Henry Döner-Meyer');
$phpLiveDocx->assign('company',  'Megasoft Co-operation');
$phpLiveDocx->assign('date',     $date->get(Zend_Date::DATE_LONG));
$phpLiveDocx->assign('time',     $date->get(Zend_Date::TIME_LONG));
$phpLiveDocx->assign('city',     (isset($city))    ? $city    : 'Berlin');  // use geoIP data (see above) or default
$phpLiveDocx->assign('country',  (isset($country)) ? $country : 'Germany');

$documentProperties = array (
    'title'    => 'License Agreement',
    'author'   => 'Megasoft Co-operation',
    'subject'  => 'Magic Graphical Compression Suite v1.9',
    'keywords' => 'graphics, magical, compression, license'
);

$phpLiveDocx->setDocumentProperties($documentProperties);

$phpLiveDocx->createDocument();

/*
foreach ($phpLiveDocx->getDocumentFormats() as $documentFormat) {
    $document = $phpLiveDocx->retrieveDocument($documentFormat);
    file_put_contents('document.' . $documentFormat, $document);
}
*/

$document = $phpLiveDocx->retrieveDocument('pdf');

file_put_contents('document.pdf', $document);

unset($phpLiveDocx);
