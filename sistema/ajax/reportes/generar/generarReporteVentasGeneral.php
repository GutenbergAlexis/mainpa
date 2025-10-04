<?php
	include('../../../util/database.php');
	
	$tipoComprobante = isset($_POST['rep-tipo-comprobante']) ? $_POST['rep-tipo-comprobante'] : "";
	$fechaDesde      = isset($_POST['rep-fecha-desde'])      ? $_POST['rep-fecha-desde']      : "";
	$fechaHasta      = isset($_POST['rep-fecha-hasta'])      ? $_POST['rep-fecha-hasta']      : "";

	$selectVentasGenerales = 
		"SELECT com.fec_emision, par1.descripcion AS tip_comprobante, 
			concat(com.ser_comprobante, '-', lpad(com.num_comprobante, 6, '0')) AS num_comprobante, 
			concat_ws(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS cliente, 
			if(com.anulado = 0, com.mon_igv, 0) AS mon_igv, if(com.anulado = 0, com.mon_neto, 0) AS mon_neto, 
			if(com.anulado = 0, com.mon_total, 0) AS mon_total, par2.descripcion AS med_pago, 
			if(com.anulado = 0, com.observaciones, 'ANULADO') AS observaciones, if(com.pagado = 0, 'no pagado', 'pagado') AS pagado, 
			if(com.emitido = 0, 'no emitido', 'emitido') AS emitido, if(com.entregado = 0, 'no entregado', 'entregado') AS entregado, 
			com.usu_creacion AS usuario, cli.num_documento 
			FROM comprobantes com 
			JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8 
			JOIN clientes cli ON cli.id = com.id_cliente 
			JOIN pagos pag ON pag.id_comprobante = com.id 
			JOIN parametros par2 ON par2.codigo = pag.codigo_medio_pago AND par2.padre = 4 
			WHERE com.emitido = 1";
	
	if (!is_null($tipoComprobante) && !empty($tipoComprobante)) 
	{
		$selectVentasGenerales = $selectVentasGenerales." AND par1.id = '$tipoComprobante'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$selectVentasGenerales = $selectVentasGenerales." AND com.fec_emision >= STR_TO_DATE('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$selectVentasGenerales = $selectVentasGenerales." AND com.fec_emision >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$fechaHasta            = $fechaHasta.' 23:59:59';
		$selectVentasGenerales = $selectVentasGenerales." AND com.fec_emision <= STR_TO_DATE('$fechaHasta', '%d/%m/%Y %H:%i:%s')";
	}
	
	$selectVentasGenerales = $selectVentasGenerales." ORDER BY 1 ASC, 3 ASC";
	
	if (!$resultSelectVentasGenerales = mysqli_query($con, $selectVentasGenerales)) 
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
						 ->setTitle("Reporte de ventas general")
						 ->setSubject("Reporte de ventas general")
						 ->setDescription("Reporte de ventas general")
						 ->setKeywords("reporte de ventas general")
						 ->setCategory("Reporte excel");

	$tituloReporte   = "Reporte de ventas general";
	$titulosColumnas = array('FEC. EMISIÓN', 'TIPO COMPROBANTE', 'NÚM. COMPROBANTE', 'NÚM. DOCUMENTO', 'CLIENTE', 'SUB TOTAL', 'IGV', 'TOTAL', 'MODO DE PAGO', 'OBSERVACIONES', 'PAGADO', 'EMITIDO', 'ENTREGADO', 'VENDEDOR');
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A1:N2');
					
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
				->setCellValue('H3', $titulosColumnas[7])
				->setCellValue('I3', $titulosColumnas[8])
				->setCellValue('J3', $titulosColumnas[9])
				->setCellValue('K3', $titulosColumnas[10])
				->setCellValue('L3', $titulosColumnas[11])
				->setCellValue('M3', $titulosColumnas[12])
				->setCellValue('N3', $titulosColumnas[13]);
	
	//Se agregan los datos del reporte
	$i = 4;
	
	if(mysqli_num_rows($resultSelectVentasGenerales) > 0) 
	{
		while ($rowSelectVentasGenerales = mysqli_fetch_assoc($resultSelectVentasGenerales)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
				->setcellvalue('A'.$i, $rowSelectVentasGenerales['fec_emision'])
				->setcellvalue('B'.$i, $rowSelectVentasGenerales['tip_comprobante'])
				->setcellvalue('C'.$i, $rowSelectVentasGenerales['num_comprobante'])
				->setCellValueExplicit('D'.$i, $rowSelectVentasGenerales['num_documento'], PHPExcel_Cell_DataType::TYPE_STRING)
				->setcellvalue('E'.$i, utf8_encode($rowSelectVentasGenerales['cliente']))
				->setcellvalue('F'.$i, $rowSelectVentasGenerales['mon_neto'])
				->setcellvalue('G'.$i, $rowSelectVentasGenerales['mon_igv'])
				->setcellvalue('H'.$i, $rowSelectVentasGenerales['mon_total'])
				->setcellvalue('I'.$i, utf8_encode($rowSelectVentasGenerales['med_pago']))
				->setcellvalue('J'.$i, utf8_encode($rowSelectVentasGenerales['observaciones']))
				->setcellvalue('K'.$i, $rowSelectVentasGenerales['pagado'])
				->setcellvalue('L'.$i, $rowSelectVentasGenerales['emitido'])
				->setcellvalue('M'.$i, $rowSelectVentasGenerales['entregado'])
				->setcellvalue('N'.$i, $rowSelectVentasGenerales['usuario']);
				$i++;
		}
	}
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A4:N4');
					
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A4', 'No hay resultados para mostrar');
	}
		
	$estiloTituloReporte = array(
		'font' => array(
			'name'   => 'Arial',
			'bold'   => true,
			'italic' => false,
			'strike' => false,
			'size'   => 16,
			'color'  => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'	=> PHPExcel_Style_Fill::FILL_SOLID
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
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => '444444'
			)
		),
		'borders' => array(
			'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM
			),
			'bottom' => array(
				'style' => PHPExcel_Style_Border::BORDER_MEDIUM
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
				'type'  => PHPExcel_Style_Fill::FILL_SOLID 
			),
			'borders' => array(
				'left'  => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		)
	);
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:N1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('A3:N3')->applyFromArray($estiloTituloColumnas);		
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:N".($i-1));
			
	for($i = 'A'; $i <= 'N'; $i++)
	{
		$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Ventas generales');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-ventas-generales-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	/* FIN - GENERACIÓN DEL REPORTE */
?>