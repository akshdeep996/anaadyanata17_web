<?php

# Simple PHP script to generate registration report in Office Excel format
# Importing PHPExcel library
require_once dirname(__FILE__) . '/PHPExcel.php';

# Setting timezone, required by Excel library
date_default_timezone_set('Asia/Kolkata');

# Fetching data from server
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => '<API>',
    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));


$resp = curl_exec($curl);
$json_response = json_decode($resp, true);
curl_close($curl);

if(!$resp) {
    echo "Invalid response from server";
    exit();
}
# Decoding json from string
$registration = json_decode($reg_json, true);

# Temp array to store registration details
$r = array();
/*
  $r = [
      "eventKey" : [
          {name, email, event, agent, college, phone}, 
          {...}
      ], [...]
  ]
*/
foreach($json_response  as $reg){
    if( isset($r[$reg['eventKey']] ) ) {
        array_push($r[$reg['eventKey']], $reg);
    } else {
        $r[$reg['eventKey']] = array();
        array_push($r[$reg['eventKey']], $reg);
    }
} 


# Create new PHPExcel object
$objPHPExcel = new PHPExcel();

# Set document properties
$objPHPExcel->getProperties()->setCreator("Priyesh Kumar")
    ->setLastModifiedBy("Priyesh Kumar")
    ->setTitle("Anaadyanta 2017 report file")
    ->setDescription("This document contains registeration details for all events");

# Add home screen
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('C4', 'Anaadyanta 2017')
    ->setCellValue('H9', '9, 10, 11 March')
    ->setCellValue('C11', 'Registeration report for all events')
    ->setCellValue('C18', 'https://github.com/priyesh9875/anaadyanta')
    ->setCellValue('H18', 'https://github.com/akshdeep996/anaadyanata17_web');

# Formatting home screen
$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
$objPHPExcel->getActiveSheet()->getStyle('C4')->getFont()->setSize(40);
$objPHPExcel->getActiveSheet()->mergeCells("C4:L8");
$objPHPExcel->getActiveSheet()->mergeCells("H9:L9");
$objPHPExcel->getActiveSheet()->mergeCells("C11:L11");
$objPHPExcel->getActiveSheet()->mergeCells("C18:G18");
$objPHPExcel->getActiveSheet()->mergeCells("H18:L18");
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    )
);
$objPHPExcel->getActiveSheet()->getStyle("C4:L8")->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getStyle("H9:L8")->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getStyle("C11:L11")->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getStyle("C18:G18")->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getStyle("H18:L18")->applyFromArray($style);


# Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Home');

# Creating dynamic sheets as per events
$sheet_count = 1;
foreach($r as $reg) {
    # New sheet
    $objPHPExcel->createSheet($sheet_count)
        ->setCellValue('A1', 'Event')
        ->setCellValue('B1', 'Name')
        ->setCellValue('C1', 'Email')
        ->setCellValue('D1', 'Phone')
        ->setCellValue('E1', 'College')
        ->setCellValue('F1', 'Agent');

    # Setting sheet as active to work on
    $objPHPExcel->setActiveSheetIndex($sheet_count);            
    
    # Getting reference to current sheet
    $sheet = $objPHPExcel->getActiveSheet();

    # Formatting header row
    $sheet->getStyle("A1")->getFont()->setBold(true);
    $sheet->getStyle("B1")->getFont()->setBold(true);
    $sheet->getStyle("C1")->getFont()->setBold(true);
    $sheet->getStyle("D1")->getFont()->setBold(true);
    $sheet->getStyle("E1")->getFont()->setBold(true);
    $sheet->getStyle("E1")->getFont()->setBold(true);
    
    # Title of sheet as event title
    $sheet->setTitle($reg[0]['event']);
    

    $count = 2;
    
    # Adding rows to sheet, as per registration details
    foreach($reg as $p) {
        $sheet->setCellValue('A'.$count, $p['event'])
            ->setCellValue('B'.$count, $p['name'])
            ->setCellValue('C'.$count, $p['email'])
            ->setCellValue('D'.$count, $p['phone']);
        if(isset($p['college'])) {
            $sheet->setCellValue('E'.$count, $p['college']);
        } else {
            $sheet->setCellValue('E'.$count, "No college");
        }
        if(isset($p['agent'])) {
            $sheet->setCellValue('F'.$count, $p['agent']);
        } else {
            $sheet->setCellValue('F'.$count, "app");
        }

        # Making columns to auto resize for fit content
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $count++;
    }

    $sheet_count++;

}

# Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

# Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="registration-report.xlsx"');
header('Cache-Control: max-age=0');
# If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
# If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
# More info at https://github.com/PHPOffice/PHPExcel
?>