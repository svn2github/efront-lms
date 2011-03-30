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
 * PDF class
 *
 * This class incorporates pdf functions
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


 /**
	 * Create a PDF instance, using the designated title
	 *
	 * @param string $title The pdf title
	 * @since 3.6.7
	 * @access public
	 */
 public function __construct($title = 'PDF Report') {
  $this->initializePdf();
  $this->printPdfHeader($title);
 }

 /**
	 * Prints a section in the pdf file that displays rows of information, in the form:
	 * Title
	 * Name: Value
	 * Name: Value
	 * Name: Value
	 *
	 * Optionally, an image file can be inserted next to the info.
	 *
	 * @param string $title The title appearing on top of the section
	 * @param array $info An array of arrays with key/value pairs
	 * @param mixed $imageFile An EfrontFile or path to an image file, or false to use none
	 * @since 3.6.7
	 * @access public
	 */
 public function printInformationSection($title, $info, $imageFile = false) {
  if (sizeof($info) > 0) {
   $this->printSectionTitle($title);
   $this->printSectionImage($imageFile);

   foreach ($info as $row) {
    if ($row[1]) { //If there are information print it; otherwise, print an empty line when there is an image, in order to keep distances as designed
     !$imageFile ? $this->printSimpleContent($row[0].': '.$row[1]) : $this->printSimpleContent($row[0].': '.$row[1], false);
    } elseif ($imageFile) {
     $this->printSimpleContent('', false);
    }
   }
  }
 }

 /**
	 * Prints a section in the pdf file that displays rows of data, in the form:
	 * Title
	 * Column 1 | Column 2 | Column 3 | Column 4
	 * Data 1.1 | Data 1.2 | Data 1.3 | Data 1.4
	 * Data 2.1 | Data 2.2 | Data 2.3 | Data 2.4
	 * ...
	 *
	 * @param string $title The title appearing on top of the section
	 * @param array $data An array of arrays in key/value pairs
	 * @since 3.6.7
	 * @access public
	 */
 public function printDataSection($title, $data, $formatting, $subSections = array()) {
  if (sizeof($data) > 0) {
   if ($title) {
    $this->printSectionTitle($title);
   }
   $formatting = $this -> getFormatting($formatting, current($data));
   $this->printColumnTitles(current($data), $formatting);

   $count = 0;
   foreach ($data as $rowIndex => $row) {
    $this -> setDataSectionRowColor($row, $count);
    $idx = 1;
    $rowHeight = $this->calculateRowHeight($row, $formatting);
    foreach ($row as $columnTitle => $column) {
     $idx++ == sizeof($row) ? $newLine = 1 : $newLine = 0;
     $this->printMultiContent($column, array('height' => $rowHeight) + $formatting[$columnTitle], $newLine);
    }
    if (isset($subSections[$rowIndex])) {
     $subSectionData = $subSections[$rowIndex]['data'];
     $subSectionFormatting = $this -> getFormatting($subSections[$rowIndex]['formatting'], current($subSectionData));

     if (!empty($subSectionFormatting)) {
      $subSectionTitleFormat = array('bold' => 'B', 'border' => 'T', 'width' => 0) + current($subSectionFormatting);
     } else {
      $subSectionTitleFormat = array('bold' => 'B', 'border' => 'T', 'width' => 0);
     }
     $this->pdf->setTextColor(0,0,0);
     $this->pdf->SetFont('', 'B', $this->defaultSettings['content_font_size']);
     $this->printMultiContent($subSections[$rowIndex]['title'], $subSectionTitleFormat, 1);

     $this->printDataSection('', $subSectionData, $subSectionFormatting, $subSections[$rowIndex]['subSections']);

     $this->printMultiContent('', array('fill' => 0) + $subSectionTitleFormat, 1);
    }
   }
  }
 }

 /**
	 * Outputs the PDF file
	 *
	 * @param string $name The PDF file name
	 * @since 3.6.7
	 * @access public
	 */
 public function outputPdf($name = 'report.pdf') {
  header("Content-type: application/pdf");
  header("Content-disposition: attachment; filename=".$name);
  echo $this->pdf->Output('', 'S');
  //$this->pdf->Output($name, 'D');
 }


 private function calculateRowHeight($row, $formatting) {
  $height = 0;
  foreach ($row as $columnTitle => $column) {
   $height = max($height, $this->pdf->getStringHeight($formatting[$columnTitle]['width'], $column));
  }
  return $height;
 }



 private function setDataSectionRowColor(& $row, & $count) {
  if (!isset($row['active']) || $row['active']) {
   ($count++%2) ? $this->pdf->setTextColor(18,52,86) : $this->pdf->setTextColor(101,67,33);
  } else {
   $this->pdf->setTextColor(255,0,0);
  }
  unset($row['active']);
 }


 private function getFormatting($columnFormatting, $row) {
  $pageWidth = $this->pdf->GetPageWidth();
  $columnWidth = ($pageWidth/sizeof($row)) - 10;

  unset($row['active']);

  $formatting = array();
  $height = 0;
  foreach ($row as $title => $foo) {
   if (isset($columnFormatting[$title]['width'])) {
    if (strpos($columnFormatting[$title]['width'], '%') !== false) {
     $width = (($pageWidth - 20)*((int)$columnFormatting[$title]['width'])/100);
    } else {
     $width = $columnFormatting[$title]['width'];
    }
   } else {
    $width = $columnWidth;
   }

   $formatting[$title] = array('align' => in_array($columnFormatting[$title]['align'], array('L', 'C', 'R', 'J')) ? $columnFormatting[$title]['align'] : 'L',
          'border' => in_array($columnFormatting[$title]['border'], array('L', 'T', 'R', 'B')) ? $columnFormatting[$title]['border'] : 0,
          'height' => $columnFormatting[$title]['height'] ? $columnFormatting[$title]['height'] : 0,
          'width' => $width,
          'fill' => $columnFormatting[$title]['fill'] ? true : false);
  }


  return $formatting;
 }

 private function printColumnTitles($titleRow, $formatting) {
  $this->pdf->setTextColor(0,0,0);
  $idx = 1;
  unset($titleRow['active']);

  $rowHeight = $this->calculateRowHeight(array_combine(array_keys($titleRow), array_keys($titleRow)), $formatting);
  foreach ($titleRow as $columnTitle => $foo) {
   $idx++ == sizeof($titleRow) ? $newLine = 1 : $newLine = 0;
   $this->printMultiContent($columnTitle,
          array('bold' => 'B', 'height' => $rowHeight) + $formatting[$columnTitle],
          $newLine);
  }
 }


 private function printSectionImage($imageFile){
  try {
   if ($imageFile) {
    if (!($imageFile instanceOf EfrontFile)) {
     $imageFile = new EfrontFile($imageFile);
    }
    if (extension_loaded('gd')) {
     $this->pdf->Image($imageFile['path'], '', '', 0, 0, '', '', 'T');
    }
   }
  } catch (Exception $e) {/*do nothing if the image could not be embedded*/}
 }

 public function printSectionTitle($title) {
  $this->pdf->setTextColor(0,0,0);
  $this->pdf->Ln(12);
  $this->printMediumTitle($title);
  $this->pdf->Ln(2);
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
  if (extension_loaded('gd')) {
   $this->pdf->Image($logoFile['path'], '', '', 0, 0, '', '', 'T');
  }
  $this->pdf->SetFont($this->defaultSettings['default_font']);

  $this->printLargeTitle($GLOBALS['configuration']['site_name']);
  $this->printSmallTitle($GLOBALS['configuration']['site_motto']);
  $this->printSeparatorHeader($title);

 }

 private function printLargeTitle($text) {
  $this->pdf->SetFont('', 'B', $this->defaultSettings['large_header_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
 }

 private function printMediumTitle($text) {
  $this->pdf->SetFont('', 'B', $this->defaultSettings['medium_header_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
  $this->pdf->SetFont('', 'B', $this->defaultSettings['content_font_size']);
 }

 private function printSmallTitle($text) {
  $this->pdf->SetFont('', 'B', $this->defaultSettings['small_header_font_size']);
  $this->pdf->Cell(0, 0, $text, 0, 2);
 }

 private function printSeparatorHeader($text) {
  $this->pdf->Ln(12);
  $this->pdf->SetFont($defaultFont, 'B', $this->defaultSettings['medium_header_font_size']);
  $this->pdf->SetFillColor(220, 220, 220);
  $this->pdf->MultiCell(0, $this->defaultSettings['header_height'], $text, 'BT', 'C', true, 1);
  $this->pdf->SetFillColor(240, 240, 240);
 }

 private function printSimpleContent($text, $multi = true) {
  $this->pdf->SetFont('', '', $this->defaultSettings['content_font_size']);
  if ($multi) {
   $this->pdf->MultiCell(0, 0, $text, 0, 'L', 0, 1);
  } else {
   $this->pdf->Cell(0, 0, $text, 0, 2);
  }
 }

 private function printMultiContent($text, $formatting, $newLine = 0) {
  $this->pdf->SetFont('', $formatting['bold'], $this->defaultSettings['content_font_size']);
  $this->pdf->MultiCell($formatting['width'],
         $formatting['height'],
         $text,
         $formatting['border'],
         $formatting['align'],
         $formatting['fill'],
         $newLine);
 }





}
