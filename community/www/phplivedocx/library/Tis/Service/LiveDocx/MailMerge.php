<?php

/** Tis_Service_LiveDocx **/
require_once 'Tis/Service/LiveDocx.php';

/** Zend_Pdf **/
require_once 'Zend/Pdf.php';


/**
 * phpLiveDocx - LiveDocx.MailMerge
 *
 * The template based document creation platform
 *
 * Technical documentation and sample applications:
 * http://www.phpLiveDocx.org
 *
 * Contact the author:
 * http://www.phpLiveDocx.org/contact/
 *
 * Zend Framework (required by this class):
 * http://www.ZendFramework.com
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file phplivedocx/docs/LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://www.phpLiveDocx.org/articles/phplivedocx-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to the author so we can send you a copy immediately.
 *
 * @package    Tis_Service_LiveDocx
 * @subpackage Tis_Service_LiveDocx_MailMerge
 * @author     Jonathan Maron
 * @copyright  (c) 2008 - 2009 Jonathan Maron
 * @license    http://www.phpLiveDocx.org/articles/phplivedocx-license/ New BSD License
 *
 */
class Tis_Service_LiveDocx_MailMerge extends Tis_Service_LiveDocx
{
    /**
     * URI of LiveDocx.MailMerge service
     */
    const ENDPOINT = PHPLIVEDOCXAPI;

    /**
     * Field values
     *
     * @var array
     */
    protected $fieldValues;

    /**
     * Block field values
     *
     * @var array
     */
    protected $blockFieldValues;

    /**
     * Document properties of PDF file (only)
     *
     * @var array
     */
    protected $documentProperties;

    // -------------------------------------------------------------------------

    /**
     * Connect and log into LiveDocx.MailMerge service
     *
     * @param string $username
     * @param string $password
     *
     * @return throws Tis_Service_LiveDocx_Exception
     * @return boolean
     */
    public function __construct($username, $password)
    {
        $this->fieldValues = array();
        $this->blockFieldValues = array();

        $this->setDefaultDocumentProperties();

        $this->initSoapClient(self::ENDPOINT);

        return $this->logIn($username, $password);
    }

    /**
     * Clean up and log out of LiveDocx service
     *
     * @return boolean
     */
    public function __destruct ()
    {
        return parent::__destruct();
    }

    // -------------------------------------------------------------------------

    /**
     * Set the filename of a LOCAL template
     * (i.e. a template stored locally on YOUR server)
     *
     * @param string $filename
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function setLocalTemplate ($filename)
    {
        try {
            $this->liveDocx->SetLocalTemplate(
                array(
                    'template' => base64_encode(file_get_contents($filename)),
                    'format'   => self::getFormat($filename)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot set local template');
        }

        return null;
    }

    /**
     * Set the filename of a REMOTE template
     * (i.e. a template stored remotely on the LIVEDOCX server)
     *
     * @param string $filename
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function setRemoteTemplate ($filename)
    {
        try {
            $this->liveDocx->SetRemoteTemplate(
                array(
                    'filename' => $filename
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot set remote template');
        }

        return null;
    }

    /**
     * Set an associate array of keys and values pairs
     *
     * @param array $values
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function setFieldValues ($values)
    {
        try {
            $this->liveDocx->SetFieldValues(
                array (
                    'fieldValues' => self::assocArrayToArrayOfArrayOfString($values)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot set field values');
        }

        return null;
    }

    /**
     * Set an array of key and value
     *
     * @param string $field
     * @param string $value
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function setFieldValue ($field, $value)
    {
        try {
            $this->fieldValues[$field] = $value;
        } catch (Exception $e) {
            self::throwException($e, 'Cannot set field value');
        }

        return null;
    }

    /**
     * Set block field values
     *
     * @param string $blockName
     * @param array $blockFieldValues
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function setBlockFieldValues ($blockName, $blockFieldValues)
    {
        try {
            $this->liveDocx->SetBlockFieldValues(
                array (
                    'blockName' => $blockName,
                    'blockFieldValues' => self::multiAssocArrayToArrayOfArrayOfString($blockFieldValues)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot set block field values');
        }

        return null;
    }

    /**
     * Assign values to template fields
     *
     * @param array|string $field
     * @param array|string $value
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function assign ($field, $value = null)
    {
        try {
            if (is_array($field) && is_null($value)) {
                foreach ($field as $fieldName => $fieldValue) {
                    $this->setFieldValue($fieldName, $fieldValue);
                }
            } elseif (is_array($value)) {
                $this->setBlockFieldValues($field, $value);
            } else {
                $this->setFieldValue($field, $value);
            }
        } catch (Exception $e) {
            self::throwException($e, 'Cannot assign data to template');
        }

        return null;
    }

    // -------------------------------------------------------------------------

    /**
     * Merge assigned data with template to geneate document
     *
     * @throws Tis_Service_LiveDocx_Excpetion
     * @return void
     */
    public function createDocument ()
    {
        if (count($this->fieldValues) > 0) {
            $this->setFieldValues($this->fieldValues);
        }

        $this->fieldValues = array();
        $this->blockFieldValues = array();

        try {
            $this->liveDocx->CreateDocument();
        } catch (Exception $e) {
            self::throwException($e, 'Cannot create document');
        }

        return null;
    }

    /**
     * Retrieve document in specified format
     *
     * @param string $format
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return binary
     */
    public function retrieveDocument ($format)
    {
        $ret = null;

        $format = strtolower($format);

        try {
            $result = $this->liveDocx->RetrieveDocument(
                array(
                    'format' => $format
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot retrieve document - call setLocalTemplate() or setRemoteTemplate() first');
        }

        $ret = base64_decode($result->RetrieveDocumentResult);

        if ('pdf' === $format) {
            $pdf = Zend_Pdf::parse($ret);
            $pdf->properties = $this->getDocumentProperties();
            $ret = $pdf->render();
        }

        return $ret;
    }

    // -------------------------------------------------------------------------

    /**
     * Return WMF (aka Windows metafile) data for specified page range of created document
     * Return array contains WMF data (binary) - array key is page number
     *
     * @param integer $fromPage
     * @param integer $toPage
     *
     * @return array
     */
    public function getMetafiles ($fromPage, $toPage)
    {
        $ret = array();

        $result = $this->liveDocx->GetMetafiles(
            array(
                'fromPage' => (integer) $fromPage,
                'toPage'   => (integer) $toPage
            )
        );

        if (isset($result->GetMetafilesResult->string)) {
            $pageCounter = (integer) $fromPage;
            if (is_array($result->GetMetafilesResult->string)) {
                foreach ($result->GetMetafilesResult->string as $string) {
                    $ret[$pageCounter] = base64_decode($string);
                    $pageCounter++;
                }
            } else {
               $ret[$pageCounter] = base64_decode($result->GetMetafilesResult->string);
            }
        }

        return $ret;
    }

    /**
     * Return all the fields in the template
     *
     * @return array
     */
    public function getFieldNames ()
    {
        $ret = array();

        $result = $this->liveDocx->GetFieldNames();

        if (isset($result->GetFieldNamesResult->string)) {
            if (is_array($result->GetFieldNamesResult->string)) {
                $ret = $result->GetFieldNamesResult->string;
            } else {
                $ret[] = $result->GetFieldNamesResult->string;
            }
        }

        return $ret;
    }

    /**
     * Return all the block fields in the template
     *
     * @param string $blockName
     *
     * @return array
     */
    public function getBlockFieldNames ($blockName)
    {
        $ret = array();

        $result = $this->liveDocx->GetBlockFieldNames(
            array(
                'blockName' => $blockName
            )
        );

        if (isset($result->GetBlockFieldNamesResult->string)) {
            if (is_array($result->GetBlockFieldNamesResult->string)) {
                $ret = $result->GetBlockFieldNamesResult->string;
            } else {
                $ret[] = $result->GetBlockFieldNamesResult->string;
            }
        }

        return $ret;
    }

    /**
     * Return all the block fields in the template
     *
     * @return array
     */
    public function getBlockNames ()
    {
        $ret = array();

        $result = $this->liveDocx->GetBlockNames();

        if (isset($result->GetBlockNamesResult->string)) {
            if (is_array($result->GetBlockNamesResult->string)) {
                $ret = $result->GetBlockNamesResult->string;
            } else {
                $ret[] = $result->GetBlockNamesResult->string;
            }
        }

        return $ret;
    }

    // -------------------------------------------------------------------------

    /**
     * Set the default document properties
     *
     * Valid for PDF documents only
     *
     * @return null
     */
    protected function setDefaultDocumentProperties ()
    {
        // Zend_Pdf expects keys with uppercase first letter
        // (IHMO inconsistent to ZF coding standard)

        $date = new Zend_Date();

        $this->documentProperties = array();

        $projectName  = sprintf('phpLiveDocx %s', self::getVersion());
        $projectUrl   = 'http://www.phpLiveDocx.org';
        $creationDate = sprintf('D:%s', $date->toString('YYYYMMddHHmmss'));

        $this->documentProperties['Creator']      = $projectName;
        $this->documentProperties['Producer']     = $projectUrl;
        $this->documentProperties['CreationDate'] = $creationDate;
        $this->documentProperties['ModDate']      = $creationDate;

        return null;
    }

    /**
     * Set the document properties
     *
     * Valid for PDF documents only
     *
     * $properties is an assoc array with the following format:
     *
     * $properties = array (
     *     'title'        => '', // (string)
     *     'author'       => '', // (string)
     *     'subject'      => '', // (string)
     *     'keywords'     => ''  // (string)
     * );
     *
     * @param array $properties
     *
     * @return null
     */
    public function setDocumentProperties ($properties)
    {
        // For consistency, keys in $properties are lowercase.
        // Zend_Pdf expects keys with uppercase first letter
        // (IHMO inconsistent to ZF coding standard)

        $keys = array('Title', 'Author', 'Subject', 'Keywords');
        foreach ($keys as $key) {
            $lowerCaseKey = strtolower($key);
            if (isset($properties[$lowerCaseKey])) {
                $this->documentProperties[$key] = $properties[$lowerCaseKey];
            }
        }

        return null;
    }

    /**
     * Return currently set document properties
     *
     * @return array
     */
    protected function getDocumentProperties ()
    {
        return $this->documentProperties;
    }

    // -------------------------------------------------------------------------

    /**
     * Upload a template file to LiveDocx service
     *
     * @param string $filename
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return void
     */
    public function uploadTemplate ($filename)
    {
        try {
            $this->liveDocx->UploadTemplate(
                array(
                    'template' => base64_encode(file_get_contents($filename)),
                    'filename' => basename($filename)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot upload template');
        }

        return null;
    }

    /**
     * Download template file from LiveDocx service
     *
     * @param string $filename
     *
     * @return binary
     */
    public function downloadTemplate ($filename)
    {
        try {
            $result = $this->liveDocx->DownloadTemplate(
                array(
                    'filename' => basename($filename)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot download template ');
        }

        return base64_decode($result->DownloadTemplateResult);
    }

    /**
     * Delete a template file from LiveDocx service
     *
     * @param string $filename
     *
     * @return void
     */
    public function deleteTemplate ($filename)
    {
        $this->liveDocx->DeleteTemplate(
            array(
                'filename' => basename($filename)
            )
        );

        return null;
    }

    /**
     * List all templates stored on LiveDocx service
     *
     * @return array
     */
    public function listTemplates ()
    {
        $ret = array();

        $result = $this->liveDocx->ListTemplates();

        if (isset($result->ListTemplatesResult)) {
            $ret = self::backendListArrayToMultiAssocArray($result->ListTemplatesResult);
        }

        return $ret;
    }

    /**
     * Check whether a template file is available on LiveDocx service
     *
     * @param string $filename
     *
     * @return boolean
     */
    public function templateExists ($filename)
    {
        $result = $this->liveDocx->TemplateExists(
            array(
                'filename' => basename($filename)
            )
        );

        return (boolean) $result->TemplateExistsResult;
    }

    // -------------------------------------------------------------------------

    /**
     * Share a document - i.e. the document is available to all over the Internet
     *
     * @return string
     */
    public function shareDocument ()
    {
        $ret = null;

        $result = $this->liveDocx->ShareDocument();

        if (isset($result->ShareDocumentResult)) {
            $ret = (string) $result->ShareDocumentResult;
        }

        return $ret;
    }

    /**
     * List all shared documents stored on LiveDocx service
     *
     * @return array
     */
    public function listSharedDocuments ()
    {
        $ret = array();

        $result = $this->liveDocx->ListSharedDocuments();

        if (isset($result->ListSharedDocumentsResult)) {
            $ret = self::backendListArrayToMultiAssocArray($result->ListSharedDocumentsResult);
        }

        return $ret;
    }

    /**
     * Delete a shared document from LiveDocx service
     *
     * @param string $filename
     *
     * @return void
     */
    public function deleteSharedDocument ($filename)
    {
        $this->liveDocx->DeleteSharedDocument(
            array(
                'filename' => basename($filename)
            )
        );

        return null;
    }

    /*
     * Download a shared document from LiveDocx service
     *
     * @param string $filename
     *
     * @throws Tis_Service_LiveDocx_Exception
     * @return binary
     */
    public function downloadSharedDocument ($filename)
    {
        try {
            $result = $this->liveDocx->DownloadSharedDocument(
                array(
                    'filename' => basename($filename)
                )
            );
        } catch (Exception $e) {
            self::throwException($e, 'Cannot download shared document');
        }

        return base64_decode($result->DownloadSharedDocumentResult);
    }

    /**
     * Check whether a shared document is available on LiveDocx service
     *
     * @param string $filename
     *
     * @return boolean
     */
    public function sharedDocumentExists ($filename)
    {
        $ret = false;

        $sharedDocuments = $this->listSharedDocuments();
        foreach ($sharedDocuments as $shareDocument) {
            if (isset($shareDocument['filename']) && basename($filename) === $shareDocument['filename']) {
                $ret = true;
                break 1;
            }
        }

        return $ret;
    }

    // -------------------------------------------------------------------------

    /**
     * Return supported template formats (lowercase)
     *
     * @return array
     */
    public function getTemplateFormats ()
    {
        $ret = array();

        $result = $this->liveDocx->GetTemplateFormats();

        if (isset($result->GetTemplateFormatsResult->string)) {
            $ret = $result->GetTemplateFormatsResult->string;
            $ret = array_map('strtolower', $ret);
        }

        return $ret;
    }

    /**
     * Return supported document formats (lowercase)
     *
     * @return array
     */
    public function getDocumentFormats ()
    {
        $ret = array();

        $result = $this->liveDocx->GetDocumentFormats();

        if (isset($result->GetDocumentFormatsResult->string)) {
            $ret = $result->GetDocumentFormatsResult->string;
            $ret = array_map('strtolower', $ret);
        }

        return $ret;
    }

    // -------------------------------------------------------------------------

    /**
     * Convert LiveDocx service return value from list methods to consistent PHP array
     *
     * @var array $list
     *
     * @return array
     */
    protected static function backendListArrayToMultiAssocArray ($list)
    {
        $ret = array();

        if (isset($list->ArrayOfString)) {

           foreach ($list->ArrayOfString as $a) {

               if (is_array($a)) {      // 1 template only
                   $o = new stdClass();
                   $o->string = $a;
               } else {                 // 2 or more templates
                   $o = $a;
               }
               unset($a);

               if (isset($o->string)) {

                   $date1 = new Zend_Date($o->string[3], Zend_Date::RFC_1123);
                   $date2 = new Zend_Date($o->string[1], Zend_Date::RFC_1123);

                   $ret[] = array (
                        'filename'   => $o->string[0],
                        'fileSize'   => (integer) $o->string[2],
                        'createTime' => (integer) $date1->get(Zend_Date::TIMESTAMP),
                        'modifyTime' => (integer) $date2->get(Zend_Date::TIMESTAMP)
                   );
               }
           }
        }

        return $ret;
    }

    /**
     * Convert assoc array to required SOAP type
     *
     * @param array $multi
     *
     * @return array
     */
    public static function assocArrayToArrayOfArrayOfString ($multi)
    {
        $arrayKeys   = array_keys($multi);
        $arrayValues = array_values($multi);

        return array ($arrayKeys, $arrayValues);
    }

    /**
     * Convert multi assoc array to required SOAP type
     *
     * @param array $multi
     *
     * @return array
     */
    public static function multiAssocArrayToArrayOfArrayOfString ($multi)
    {
        $arrayKeys   = array_keys($multi[0]);
        $arrayValues = array();

        foreach ($multi as $v) {
            $arrayValues[] = array_values($v);
        }

        $_arrayKeys = array();
        $_arrayKeys[0] = $arrayKeys;

        return array_merge($_arrayKeys, $arrayValues);
    }

    // -------------------------------------------------------------------------

} // end of class
