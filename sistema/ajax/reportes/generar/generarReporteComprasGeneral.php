<?php
	include('../../../util/database.php');
	
	$proveedor  = isset($_POST['rep-proveedor']) ? $_POST['rep-proveedor'] : "";
	$fechaDesde = isset($_POST['rep-fecha-desde']) ? $_POST['rep-fecha-desde'] : "";
	$fechaHasta = isset($_POST['rep-fecha-hasta']) ? $_POST['rep-fecha-hasta'] : "";

	$query = 
			"SELECT CMP.ID, CMP.FEC_COMPRA, PRV.RAZON_SOCIAL AS PROVEEDOR, ROUND(CMP.MON_IGV, 2) AS MON_IGV, ROUND(CMP.MON_NETO, 2) AS MON_NETO, ROUND(CMP.MON_TOTAL, 2) AS MON_TOTAL, PAR2.DESCRIPCION AS MED_PAGO, CMP.OBSERVACIONES AS OBSERVACIONES, CMP.USU_CREACION AS USUARIO
				FROM compras CMP 
				JOIN proveedores PRV ON PRV.ID = CMP.ID_PROVEEDOR 
				JOIN parametros PAR2 ON PAR2.CODIGO = CMP.PAR_MEDIO_PAGO AND PAR2.PADRE = 4 
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
						 ->setTitle("Reporte de compras general")
						 ->setSubject("Reporte de compras general")
						 ->setDescription("Reporte de compras general")
						 ->setKeywords("reporte de compras general")
						 ->setCategory("Reporte excel");

	$tituloReporte = "Reporte de compras general";
	$titulosColumnas = array('FECHA DE COMPRA', 'PROVEEDOR', 'SUB TOTAL', 'IGV', 'TOTAL', 'MODO DE PAGO', 'OBSERVACIONES', 'USUARIO');
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A1:H1');
					
	// Se agregan los titulos del reporte
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $tituloReporte)
				->setCellValue('A3', $titulosColumnas[0])
				->setCellValue('B3', $titulosColumnas[1])
				->setCellValue('C3', $titulosColumnas[2])
				->setCellValue('D3', $titulosColumnas[3])
				->setCellValue('E3', $titulosColumnas[4])
				->setCellValue('F3', $titulosColumnas[5])
				->setCellValue('G3', $titulosColumnas[6])
				->setCellValue('H3', $titulosColumnas[7]);
	
	//Se agregan los datos del reporte
	$i = 4;
	
	if(mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, $row['FEC_COMPRA'])
				->setCellValue('B'.$i, utf8_encode($row['PROVEEDOR']))
				->setCellValue('C'.$i, $row['MON_NETO'])
				->setCellValue('D'.$i, $row['MON_IGV'])
				->setCellValue('E'.$i, $row['MON_TOTAL'])
				->setCellValue('F'.$i, $row['MED_PAGO'])
				->setCellValue('G'.$i, utf8_encode($row['OBSERVACIONES']))
				->setCellValue('H'.$i, $row['USUARIO']);
				$i++;
		}
	} else {
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A4:H4');
					
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
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);		
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:H".($i-1));
			
	for($i = 'A'; $i <= 'H'; $i++){
		$objPHPExcel->setActiveSheetIndex(0)			
			->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Compras generales');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-compras-generales-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	
	/* FIN - GENERACIÓN DEL REPORTE */
?>