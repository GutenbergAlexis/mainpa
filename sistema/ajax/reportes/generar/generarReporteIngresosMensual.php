<?php
	include('../../../util/database.php');
	
	$mesPeriodo  = isset($_POST['rep-mes-periodo']) ? $_POST['rep-mes-periodo'] : "";
	$anioPeriodo = isset($_POST['rep-anio-periodo']) ? $_POST['rep-anio-periodo'] : "";

	$selectIngresosMensuales = 
		"SELECT PAR.DESCRIPCION AS INGRESO, SUM(COM.MON_TOTAL) AS MONTO 
			FROM comprobantes COM 
			JOIN parametros PAR ON PAR.CODIGO = COM.TIP_COMPROBANTE AND PAR.PADRE = 8 
			WHERE LAST_DAY(COM.FEC_EMISION) = LAST_DAY(STR_TO_DATE('01/$mesPeriodo/$anioPeriodo', '%d/%m/%Y'))
			AND COM.ANULADO = 0 AND COM.EMITIDO = 1
			GROUP BY COM.TIP_COMPROBANTE
			UNION
		SELECT PAR.DESCRIPCION AS INGRESO, SUM(OIN.MONTO) AS MONTO 
			FROM otros_ingresos OIN 
			JOIN parametros PAR ON PAR.CODIGO = OIN.TIP_INGRESO AND PAR.PADRE = 18
			WHERE LAST_DAY(OIN.FECHA) = LAST_DAY(STR_TO_DATE('01/$mesPeriodo/$anioPeriodo', '%d/%m/%Y'))
			GROUP BY OIN.TIP_INGRESO";
	
	if (!$resultSelectIngresosMensuales = mysqli_query($con, $selectIngresosMensuales)) 
	{
		exit(mysqli_error($con));
	}
	
	/* INICIO - GENERACIÓN DEL REPORTE */
	
	$number = 1;
					
	date_default_timezone_set('America/Lima');

	if (PHP_SAPI == 'cli') 
	{
		die('Este archivo solo se puede ver desde un navegador web');
	}

	/** Se agrega la libreria PHPExcel */
	require_once '../../../util/PHPExcel/PHPExcel.php';

	// Se crea el objeto PHPExcel
	$objPHPExcel = new PHPExcel();

	// Se asignan las propiedades del libro
	$objPHPExcel->getProperties()->setCreator("Mainpa") //Autor
						 ->setLastModifiedBy("Mainpa") //Ultimo usuario que lo modificó
						 ->setTitle("Reporte de ingresos mensuales")
						 ->setSubject("Reporte de ingresos mensuales")
						 ->setDescription("Reporte de ingresos mensuales")
						 ->setKeywords("reporte de ingresos mensuales")
						 ->setCategory("Reporte excel");

	$tituloReporte = "Reporte de  ingresos mensuales";
	$titulosColumnas = array('INGRESO', 'MONTO');
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A1:B1');
					
	// Se agregan los titulos del reporte
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $tituloReporte)
				->setCellValue('A3', $titulosColumnas[0])
				->setCellValue('B3', $titulosColumnas[1]);
		
	//Se agregan los datos del reporte
	$i = 4;
	
	if(mysqli_num_rows($resultSelectIngresosMensuales) > 0) 
	{
		while ($rowSelectIngresosMensuales = mysqli_fetch_assoc($resultSelectIngresosMensuales)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$i, $rowSelectIngresosMensuales['INGRESO'])
						->setCellValue('B'.$i, $rowSelectIngresosMensuales['MONTO']);
			$i++;
		}
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A4:B4');
					
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
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($estiloTituloColumnas);		
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:B".($i-1));
			
	for($i = 'A'; $i <= 'B'; $i++)
	{
		$objPHPExcel->setActiveSheetIndex(0)			
			->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Ingresos mensuales');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-ingresos-mensuales-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	
	/* FIN - GENERACIÓN DEL REPORTE */
?>