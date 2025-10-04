<?php
	include('../../../util/database.php');
	
	$tipoComprobante = isset($_POST['rep-tipo-comprobante']) ? $_POST['rep-tipo-comprobante'] : "";
	$fechaDesde      = isset($_POST['rep-fecha-desde'])      ? $_POST['rep-fecha-desde']      : "";
	$fechaHasta      = isset($_POST['rep-fecha-hasta'])      ? $_POST['rep-fecha-hasta']      : "";

	$query = 
		"SELECT com.fec_emision, par1.descripcion AS tip_comprobante, 
			concat(com.ser_comprobante, '-', lpad(com.num_comprobante, 6, '0')) AS num_comprobante, 
			concat_ws(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS cliente, 
			pro.descripcion AS producto, dcom.cantidad_final AS cantidad, dcom.precio AS pre_unitario, 
			round(dcom.cantidad_final*dcom.precio, 2) AS pre_total
			FROM comprobantes com
			JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8 
			JOIN clientes cli ON cli.id = com.id_cliente 
			JOIN det_comprobante dcom ON dcom.id_comprobante = com.ID 
			JOIN productos pro ON pro.id = dcom.id_producto 
			WHERE com.anulado = 0 and com.emitido = 1";
	
	if (!is_null($tipoComprobante) && !empty($tipoComprobante)) 
	{
		$query = $query." AND par1.id = '$tipoComprobante'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$query = $query." AND com.fec_emision >= STR_TO_DATE('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$query = $query." AND com.fec_emision >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$query = $query." AND com.fec_emision <= STR_TO_DATE('$fechaHasta', '%d/%m/%Y')";
	}
	
	$query = $query." ORDER BY 1 ASC, 3 ASC";
	
	if (!$result = mysqli_query($con, $query)) 
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
						 ->setTitle("Reporte de ventas por producto")
						 ->setSubject("Reporte de ventas por producto")
						 ->setDescription("Reporte de ventas por producto")
						 ->setKeywords("reporte de ventas por producto")
						 ->setCategory("Reporte excel");

	$tituloReporte = "Reporte de ventas por producto";
	$titulosColumnas = array('FECHA DE EMISIÓN', 'TIPO DE COMPROBANTE', 'NÚMERO DE COMPROBANTE', 'CLIENTE', 'PRODUCTO', 'CANTIDAD', 'PRECIO UNITARIO', 'PRECIO TOTAL');
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A1:H2');
					
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
	
	if(mysqli_num_rows($result) > 0) 
	{
		while ($row = mysqli_fetch_assoc($result)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setcellvalue('A'.$i, $row['fec_emision'])
						->setcellvalue('B'.$i, utf8_encode($row['tip_comprobante']))
						->setcellvalue('C'.$i, $row['num_comprobante'])
						->setcellvalue('D'.$i, utf8_encode($row['cliente']))
						->setcellvalue('E'.$i, utf8_encode($row['producto']))
						->setcellvalue('F'.$i, $row['cantidad'])
						->setcellvalue('G'.$i, $row['pre_unitario'])
						->setcellvalue('H'.$i, $row['pre_total']);
			$i++;
		}
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('A4:H4');
					
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
	
	$objPHPExcel->getActiveSheet()->getStyle('A1:H2')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);		
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:H".($i-1));
			
	for ($i = 'A'; $i <= 'H'; $i++)
	{
		$objPHPExcel->setActiveSheetIndex(0)			
			->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Ventas por producto');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-ventas-por-producto-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	/* FIN - GENERACIÓN DEL REPORTE */
?>