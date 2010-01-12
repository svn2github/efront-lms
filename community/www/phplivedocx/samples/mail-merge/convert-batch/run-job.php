#!/usr/bin/php
<?php

require_once dirname(__FILE__) . '/../../common.php';
require_once dirname(__FILE__) . '/../../Converter.php';

// -----------------------------------------------------------------------------

define('PATH_BASE',       dirname(__FILE__) );
define('PATH_INPUT',      PATH_BASE . DIRECTORY_SEPARATOR . 'queue');
define('PATH_INPUT_DONE', PATH_BASE . DIRECTORY_SEPARATOR . 'queue_done');
define('PATH_OUTPUT',     PATH_BASE . DIRECTORY_SEPARATOR . 'output');
define('FILENAME_LOCK',   PATH_BASE . DIRECTORY_SEPARATOR . 'job.lock');

// -----------------------------------------------------------------------------

$date = new Zend_Date(LOCALE);

echo "### " . basename($_SERVER['SCRIPT_NAME']) . " Batch Converter\n\n";
echo "### Start: ". $date->get(Zend_Date::W3C) ."\n\n";

unset($date);

// -----------------------------------------------------------------------------

if (is_file(FILENAME_LOCK)) {

    printf("ERROR: There is already an instance running (%s)\n", FILENAME_LOCK);

    // -------------------------------------------------------------------------

} else {

    $phpLiveDocx = new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

    $outputFormats = $phpLiveDocx->getDocumentFormats();

    // -------------------------------------------------------------------------

    $options = array (
        'output-format|of=s' => sprintf('Output file format (%s).', implode(', ', $outputFormats))
    );

    try {
        $opts = new Zend_Console_Getopt ($options);
        $opts->parse();
    } catch (Zend_Console_Getopt_Exception $e) {
        echo $e->getUsageMessage();
        exit();
    }

    $outputFormat = $opts->getOption('output-format');
    $outputFormat = strtolower($outputFormat);

    if (!in_array($outputFormat, $outputFormats)) {
        echo $opts->getUsageMessage();
        exit();
    }

    // -------------------------------------------------------------------------

    touch(FILENAME_LOCK);

    // -------------------------------------------------------------------------

    $path = new DirectoryIterator(PATH_INPUT);

    $inputFormats = $phpLiveDocx->getTemplateFormats();

    foreach ($path as $file) {

        $inputFormat = Tis_Service_LiveDocx::getFormat($file->getFilename());

        if (in_array($inputFormat, $inputFormats)) {

            $inputFilename      = PATH_INPUT      . DIRECTORY_SEPARATOR . $file->getFilename();
            $inputDoneFilename  = PATH_INPUT_DONE . DIRECTORY_SEPARATOR . $file->getFilename();
            $outputFilename     = PATH_OUTPUT     . DIRECTORY_SEPARATOR . Converter::getFilename($file->getFilename(), $outputFormat);

            printf('Converting %s to %s... ', basename($inputFilename), basename($outputFilename));

            if ($inputFormat === $outputFormat) {
                rename($inputFilename, $inputDoneFilename);
                print("SKIPPED.\n");
            } else {
                $convertedDocument = Converter::convert($inputFilename, $outputFormat);
                if (false !== $convertedDocument) {
                    file_put_contents($outputFilename, $convertedDocument);
                    rename($inputFilename, $inputDoneFilename);
                    print("DONE.\n");
                } else {
                    print("ERROR.\n");
                }
            }
        }
    }

    // -------------------------------------------------------------------------

    if (is_file(FILENAME_LOCK)) {
        unlink(FILENAME_LOCK);
    }

    // -------------------------------------------------------------------------

}

// -----------------------------------------------------------------------------

$date = new Zend_Date(LOCALE);

echo "\n### End: ". $date->get(Zend_Date::W3C) ."\n";

unset($date);

// -----------------------------------------------------------------------------
