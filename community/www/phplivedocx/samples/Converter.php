<?php

/**
 * Example of how to convert word processing documents, using phpLiveDocx.
 *
 * Supported input formats  : docx, doc, rtf, txd
 * Supported output formats : docx, doc, rtf, txd, pdf, txt
 *
 * In a future version of phpLiveDocx, this functionality will be made available
 * directly in the core API. For the time being, this class offers document
 * conversion with the current version of phpLiveDocx (v1.0).
 *
 * NOTE: This class accesses the constants USERNAME and PASSWORD. These are your
 *       credentials to the phpLiveDocx service and must have been defined
 *       before the class can work. e.g.
 *
 *       define ('USERNAME', 'myUsername');
 *       define ('PASSWORD', 'myPassword');
 */
class Converter
{
    /**
     * Convert a word processing document from one format to another
     *
     * @param string $filename File to convert
     * @param string $format   Format into which to convert (docx, doc, rtf, txd, pdf, txt)
     * @return binary|false
     */
    public static function convert($filename, $format)
    {
        $ret = false;

        try {

            $phpLiveDocx = @ new Tis_Service_LiveDocx_MailMerge(USERNAME, PASSWORD);

            $phpLiveDocx->setLocalTemplate($filename);
            $phpLiveDocx->createDocument();

            $ret = $phpLiveDocx->retrieveDocument($format);

            unset($phpLiveDocx);

        } catch (Tis_Service_LiveDocx_Exception $e) { }

        return $ret;
    }

    /**
     * Helper method to return the filename of the converted document
     *
     * @param string $filename File to convert
     * @param string $format   Format into which to convert (docx, doc, rtf, txd, pdf, txt)
     * @return string
     */
    public static function getFilename($filename, $format)
    {
        $pattern = sprintf('\.%s$', Tis_Service_LiveDocx::getFormat($filename));
        $replace = '.' . $format;

        return preg_replace("/{$pattern}/", $replace, $filename);
    }
}
