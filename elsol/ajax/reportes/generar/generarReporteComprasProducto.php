<?php
	include('../../../util/database.php');
	
	$proveedor  = isset($_POST['rep-proveedor']) ? $_POST['rep-proveedor'] : "";
	$fechaDesde = isset($_POST['rep-fecha-desde']) ? $_POST['rep-fecha-desde'] : "";
	$fechaHasta = isset($_POST['rep-fecha-hasta']) ? $_POST['rep-fecha-hasta'] : "";

	$query = 
			"SELECT CMP.FEC_COMPRA, PRV.RAZON_SOCIAL AS PROVEEDOR, PRO.DESCRIPCION AS PRODUCTO, DCMP.CANTIDAD AS CANTIDAD, DCMP.COSTO AS COS_NETO, ROUND(DCMP.CANTIDAD*DCMP.COSTO, 2) AS COS_TOTAL
				FROM compras CMP
				JOIN proveedores PRV ON PRV.ID = CMP.ID_PROVEEDOR 
				JOIN det_compra DCMP ON DCMP.ID_COMPRA = CMP.ID 
				JOIN productos PRO ON PRO.ID = DCMP.ID_PRODUCTO 
				WHERE 1 = 1";
	
	if (!is_null($proveedor) && !empty($proveedor)) {
		$query = $query." AND PRV.ID = '$proveedor'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) {
		$query = $query." AND CMP.FEC_COMPRA >= STR_TO_DATE('$fechaDesde', '%d/%m/%Y')";
	} else {
		$query = $query." AND CMP.FEC_COMPRA >= date_sub(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) {
		$query = $query." AND CMP.FEC_COMPRA <= STR_TO_DATE('$fechaHasta', '%d/%m/%Y')";
	}
	
	$query = $query." ORDER BY 1 ASC, 3 ASC";
	
	if (!$result = mysqli_query($con, $query)) {
		exit(mysqli_error($con));
	}
	
	/* INICIO - GENERACIÓN DEL REPORTE */
	
	$number = 1;
					
	date_default_timezone_set('America/Lima');

	if (PHP_SAPI == 'cli') {
		die('Este archivo solo se puede ver desde un navegador web');
	}

	/** Se agrega la libreria PHPExcel */
	require_once '../../../util/PHPExcel/PHPExcel.php';

	// Se crea el objeto PHPExcel
	$objPHPExcel = new PHPExcel();

	// Se asignan las propiedades del libro
	$objPHPExcel->getProperties()->setCreator("Mainpa") //Autor
						 ->setLastModifiedBy("Mainpa") //Ultimo usuario que lo modificó
						 ->setTitle("Reporte de compras por producto")
						 ->setSubject("Reporte de compras por producto")
						 ->setDescription("Reporte de compras por producto")
						 ->setKeywords("reporte de compras por producto")
						 ->setCategory("Reporte excel");

	$tituloReporte = "Reporte de compras por producto";
	$titulosColumnas = array('FECHA DE COMPRA', 'PROVEEDOR', 'PRODUCTO', 'CANTIDAD', 'COSTO UNITARIO', 'COSTO TOTAL');
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A1:F1');
					
	// Se agregan los titulos del reporte
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $tituloReporte)
				->setCellValue('A3', $titulosColumnas[0])
				->setCellValue('B3', $titulosColumnas[1])
				->setCellValue('C3', $titulosColumnas[2])
				->setCellValue('D3', $titulosColumnas[3])
				->setCellValue('E3', $titulosColumnas[4])
				->setCellValue('F3', $titulosColumnas[5]);
	
	//Se agregan los datos del reporte
	$i = 4;
	
	if(mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, $row['FEC_COMPRA'])
				->setCellValue('B'.$i, utf8_encode($row['PROVEEDOR']))
				->setCellValue('C'.$i, utf8_encode($row['PRODUCTO']))
				->setCellValue('D'.$i, $row['CANTIDAD'])
				->setCellValue('E'.$i, $row['COS_NETO'])
				->setCellValue('F'.$i, $row['COS_TOTAL']);
				$i++;
		}
	} else {
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A4:F4');
					
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'No hay resultados para mostrar');
	}
		
	$estiloTituloReporte = array(
		'font' => array(
			'name'   => 'Verdana',
			'bold'   => true,
			'italic' => false,
			'strike' => false,
			'size'   => 16,
			'color'  => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'	=> PHPExcel_Style_Fill::FILL_SOLID /*,
			'color'	=> array('argb' => 'FF220835')*/
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_NONE                    
			)
		), 
		'alignment' =>  array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'rotation'   => 0,
				'wrap'       => TRUE
		)
	);

	$estiloTituloColumnas = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => 'FFFFFF'
			)
		),
		'fill' 	=> array(
			'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
			'rotation'   => 90 /*,
			'startcolor' => array(
				'rgb' => 'c47cf2'
			),
			'endcolor'   => array(
				'argb' => 'FF431a5d'
			)*/
		),
		'borders' => array(
			'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM /*,
				'color' => array(
					'rgb' => '143860'
				) */
			),
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM /*,
				'color' => array(
					'rgb'   => '143860'
				)*/
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		));
		
	$estiloInformacion = new PHPExcel_Style();
	$estiloInformacion->applyFromArray(
		array(
			'font'  => array(
				'name'  => 'Arial',               
				'color' => array(
					'rgb'   => '000000'
				)
			),
			'fill' 	=> array(
				'type'  => PHPExcel_Style_Fill::FILL_SOLID /*,
				'color'	=> array('argb' => 'FFd9b7f4') */
			),
			'borders' => array(
				'left'  => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN /*,
					'color' => array(
						'rgb' => '3a2a47'
					)*/
				)
			)
		)
	);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($estiloTituloColumnas);		
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:F".($i-1));
	
	for($i = 'A'; $i <= 'F'; $i++){
		$objPHPExcel->setActiveSheetIndex(0)			
			->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Compras por producto');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-compras-por-producto-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	
	/* FIN - GENERACIÓN DEL REPORTE */
?>