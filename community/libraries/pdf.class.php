<?php
/**
* Efront PDF Classes file
*
* @package eFront
* @version 3.6.7
*/

//This file cannot be called directly, only included.
if (str_replace(DIRECTORY_SEPARATOR, "/", __FILE__) == $_SERVER['SCRIPT_FILENAME']) {
    exit;
}

/**
 * PDF exceptions
 *
 * @package eFront
 */
class EfrontPdfException extends Exception
{
}

/**
 * System class
 *
 * This class incorporates system-wise static functions
 *
 * @since 3.6.7
 * @package eFront
 */
class EfrontPdf
{
 public $defaultSettings = array('header_height' => 12,
            'large_header_font_size' => 12,
         'medium_header_font_size' => 10,
         'small_header_font_size' => 8,
         'content_font_size' => 6,
         'default_font' => 'dejavusans');


 public function __construct($title = 'PDF Report') {
  $this -> initializePdf();
  $this -> printPdfHeader($title);
 }

 private function initializePdf() {
  $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 }

 private function printPdfHeader($title) {
  $this->pdf->SetCreator(formatLogin($_SESSION['s_login']));
  $this->pdf->SetAuthor(formatLogin($_SESSION['s_login']));
  $this->pdf->SetTitle($title);
  //$this->pdf->SetSubject($title);
  //$this->pdf->SetKeywords('pdf, '._EMPLOYEEFORM);

  $this->pdf->setPrintHeader(false);
  $this->pdf->setPrintFooter(false);

  $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $this->pdf->setFontSubsetting(false);

  $this->pdf->AddPage();

  $logoFile = EfrontSystem::getSystemLogo();
  $this->pdf->Image($logoFile['path'], '', '', 0, 0, '', '', 'T');

  $this->pdf->SetFont($this -> defaultSettings['default_font']);

  $this -> printLargeTitle($GLOBALS['configuration']['site_name']);
  $this -> printMediumTitle($GLOBALS['configuration']['site_motto']);
  $this -> printSeparatorHeader($title);

 }

 private function printLargeTitle($text) {
  $this->pdf->SetFont('', 'B', $this -> defaultSettings['large_header_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
 }

 private function printMediumTitle($text) {
  $this->pdf->SetFont('', 'B', $this -> defaultSettings['medium_header_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
 }

 private function printSeparatorHeader($text) {
  $this->pdf->Ln(12);
  $this->pdf->SetFont($defaultFont, 'B', $this -> defaultSettings['medium_header_font_size']);
  $this->pdf->SetFillColor(220, 220, 220);
  $this->pdf->Cell(0, $this -> defaultSettings['header_height'], $text, 'BT', 1, 'C', true);
  $this->pdf->SetFillColor(240, 240, 240);
 }

 private function printSimpleContent($text) {
  $this->pdf->SetFont('', '', $this -> defaultSettings['content_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
 }

 private function printMultiContent($text, $width = 0) {
  $this->pdf->SetFont('', '', $this -> defaultSettings['content_font_size']);
  $this->pdf->MultiCell($width, 0, $text, 0, 'L', 0, 0);
 }

 public function printInformationSection($title, $info, $imageFile = false) {
  if (sizeof($info) > 0) {
   $this->pdf->setTextColor(0,0,0);
   $this->pdf->Ln(12);
   $this -> printMediumTitle($title);
   $this->pdf->Ln(2);
   try {
    if ($imageFile) {
     if (!($imageFile instanceOf EfrontFile)) {
      $imageFile = new EfrontFile($imageFile);
     }
     $this->pdf->Image($imageFile['path'], '', '', 0, 0, '', '', 'T');
    }
   } catch (Exception $e) {/*do nothing if the image could not be embedded*/}

   foreach ($info as $label => $value) {
    if ($value) {
     $this -> printSimpleContent($label.': '.$value);
    }
   }
  }
 }

 public function printDataSection($title, $data) {
  if (sizeof($data) > 0) {
   $this->pdf->setTextColor(0,0,0);
   $this->pdf->Ln(12);
   $this -> printMediumTitle($title);
   $this->pdf->Ln(2);

   $pageWidth = $this->pdf -> GetPageWidth();
   $columnWidth = $pageWidth/sizeof(current($data));
   $count = 0;

   foreach (current($data) as $columnTitle => $foo) {
    $this->printMultiContent($columnTitle, $columnWidth);
   }
   $this->pdf->Ln(5);
   foreach ($data as $rowIndex => $row) {
    if (!isset($row['active']) || $row['active']) {
     ($count++%2) ? $this->pdf -> setTextColor(18,52,86) : $this->pdf -> setTextColor(101,67,33);
    } else {
     $this->pdf -> setTextColor(255,0,0);
    }
    foreach ($row as $columnIndex => $column) {
     $this->printMultiContent($column, $columnWidth);
    }
    $this->pdf->Ln(5);
   }
  }
 }

 public function outputPdf($name = 'report.pdf') {
  header("Content-type: application/pdf");
  header("Content-disposition: attachment; filename=".$name);
  echo $this->pdf->Output('', 'S');
  //$this->pdf -> Output($name, 'D');
 }



}
