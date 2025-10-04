<?php
	include('../../../util/database.php');
	
	$fechaDetalleDiario = isset($_POST['rep-fecha-detalle-diario']) ? $_POST['rep-fecha-detalle-diario'] : "";
	
	$selectDetalleEfectivoVentas = 
		"SELECT DATE_FORMAT(COM.fec_emision, '%d/%m/%Y') AS FECHA, IF(COM.anulado = 0, PAG.monto, 0) AS DET_EFECTIVO, 
			CONCAT(COM.ser_comprobante, '-', LPAD(COM.num_comprobante, 6, '0')) AS NUM_COMPROBANTE, 
			IF(COM.anulado = 0, '', 'ANULADO') AS DESCRIPCION, PAR.descripcion AS TIP_COMPROBANTE
			FROM comprobantes COM 
			JOIN pagos PAG ON PAG.id_comprobante = COM.id 
			JOIN parametros PAR ON PAR.codigo = COM.tip_comprobante AND PAR.padre = 8 
			WHERE DATE_FORMAT(COM.fec_emision, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			AND PAG.codigo_medio_pago = 1 AND COM.emitido = 1";
			
	$selectDetalleEfectivoOtros = 
		"SELECT DATE_FORMAT(OI.fecha, '%d/%m/%Y') AS FECHA, OI.monto AS DET_OTROS, 
			OI.descripcion AS DESCRIPCION, PAR.descripcion AS DES_TIPO_INGRESO
			FROM otros_ingresos OI 
			JOIN parametros PAR ON PAR.codigo = OI.tip_ingreso AND PAR.padre = 18 
			WHERE DATE_FORMAT(OI.fecha, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y')";
			
	$selectDetalleCredito = 
		"SELECT DATE_FORMAT(COM.fec_emision, '%d/%m/%Y') AS FECHA, IF(COM.anulado = 0, PAG.monto, 0) AS DET_CREDITO, 
			CONCAT(COM.ser_comprobante, '-', LPAD(COM.num_comprobante, 6, '0')) AS NUM_COMPROBANTE, 
			IF(COM.anulado = 0, PAR2.descripcion, 'ANULADO') AS DESCRIPCION, PAR1.descripcion AS TIP_COMPROBANTE
			FROM comprobantes COM 
			JOIN pagos PAG ON PAG.id_comprobante = COM.id 
			JOIN parametros PAR1 ON PAR1.codigo = COM.tip_comprobante AND PAR1.padre = 8 
			JOIN parametros PAR2 ON PAR2.codigo = PAG.codigo_medio_pago AND PAR2.padre = 4 
			WHERE DATE_FORMAT(COM.fec_emision, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			AND PAG.codigo_medio_pago <> 1 AND COM.emitido = 1";
			
	$selectDetalleEgresos = 
		"SELECT CONCAT('COMPRAS - ', PRO.razon_social) AS DESCRIPCION, CMP.mon_total AS DET_EGRESOS, 
			DATE_FORMAT(CMP.fec_compra, '%d/%m/%Y') AS FECHA
			FROM compras CMP 
			JOIN proveedores PRO ON PRO.id = CMP.id_proveedor 
			WHERE DATE_FORMAT(CMP.fec_compra, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y')";
	
	if (!$resultDetalleEfectivoVentas = mysqli_query($con, $selectDetalleEfectivoVentas)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultDetalleEfectivoOtros = mysqli_query($con, $selectDetalleEfectivoOtros)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultDetalleCredito = mysqli_query($con, $selectDetalleCredito)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultDetalleEgresos = mysqli_query($con, $selectDetalleEgresos)) 
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
						 ->setTitle("Reporte detalle diario")
						 ->setSubject("Reporte detalle diario")
						 ->setDescription("Reporte detalle diario")
						 ->setKeywords("reporte detalle diario")
						 ->setCategory("Reporte excel");

	$tituloReporte           = "Reporte detalle diario - ".$fechaDetalleDiario;
	$fechaDetalle            = strtotime(substr($fechaDetalleDiario, 6, 4).'-'.substr($fechaDetalleDiario, 3, 2).'-'.substr($fechaDetalleDiario, 0, 2));
	$cajaAnterior            = "Caja del día ".date('d/m/Y', strtotime('-1 day', $fechaDetalle));
	$tituloTablaIngresos     = "Ingresos";
	$tituloTablaEgresos      = "Egresos";
	$tituloEfectivo          = "Efectivo";
	$tituloVentas            = "Ventas";
	$tituloOtros             = "Otros";
	$tituloCredito           = "No efectivo";
	$titulosColumnasIngresos = array('Tipo Comprobante', 'Número Comprobante', 'Descripción', 'Monto');
	$titulosColumnasEgresos  = array('Número Comprobante', 'Descripción', 'Monto');
	$tituloEfectivoDia       = "Efectivo día";
	$tituloIngresosDia       = "Ingresos día";
		
	//Se agregan los datos del reporte
	$filaInicial               = 7;
	$filaDetalleEfectivoVentas = $filaInicial;
	
	if(mysqli_num_rows($resultDetalleEfectivoVentas) > 0) 
	{
		while ($rowDetalleEfectivoVentas = mysqli_fetch_assoc($resultDetalleEfectivoVentas)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('D'.$filaDetalleEfectivoVentas, $rowDetalleEfectivoVentas['TIP_COMPROBANTE'])
						->setCellValue('E'.$filaDetalleEfectivoVentas, $rowDetalleEfectivoVentas['NUM_COMPROBANTE'])
						->setCellValue('F'.$filaDetalleEfectivoVentas, $rowDetalleEfectivoVentas['DESCRIPCION'])
						->setCellValue('G'.$filaDetalleEfectivoVentas, $rowDetalleEfectivoVentas['DET_EFECTIVO']);
						
			$filaDetalleEfectivoVentas++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleEfectivoVentas.':F'.$filaDetalleEfectivoVentas); //Total Ventas
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleEfectivoVentas, 'Total: ');
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('G'.$filaDetalleEfectivoVentas, '=SUM(G'.$filaInicial.':G'.($filaDetalleEfectivoVentas-1).')');
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleEfectivoVentas.':G'.$filaDetalleEfectivoVentas);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleEfectivoVentas, 'No hay resultados para mostrar');
	}
	
	$filaDetalleEfectivoOtros = $filaDetalleEfectivoVentas + 1;
	
	if(mysqli_num_rows($resultDetalleEfectivoOtros) > 0) 
	{
		while ($rowDetalleEfectivoOtros = mysqli_fetch_assoc($resultDetalleEfectivoOtros)) 
		{	
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('F'.$filaDetalleEfectivoOtros, $rowDetalleEfectivoOtros['DES_TIPO_INGRESO'].' - '.utf8_encode($rowDetalleEfectivoOtros['DESCRIPCION']))
						->setCellValue('G'.$filaDetalleEfectivoOtros, $rowDetalleEfectivoOtros['DET_OTROS']);
						
			$filaDetalleEfectivoOtros++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleEfectivoOtros.':F'.$filaDetalleEfectivoOtros); //Total Otros
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleEfectivoOtros, 'Total: ');
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('G'.$filaDetalleEfectivoOtros, '=SUM(G'.($filaDetalleEfectivoVentas+1).':G'.($filaDetalleEfectivoOtros-1).')');
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleEfectivoOtros.':G'.$filaDetalleEfectivoOtros);	
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleEfectivoOtros, 'No hay resultados para mostrar');
	}
	
	$filaDetalleCredito = $filaDetalleEfectivoOtros + 1;
	
	if(mysqli_num_rows($resultDetalleCredito) > 0) 
	{
		while ($rowDetalleCredito = mysqli_fetch_assoc($resultDetalleCredito)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('D'.$filaDetalleCredito, $rowDetalleCredito['TIP_COMPROBANTE'])
						->setCellValue('E'.$filaDetalleCredito, $rowDetalleCredito['NUM_COMPROBANTE'])
						->setCellValue('F'.$filaDetalleCredito, $rowDetalleCredito['DESCRIPCION'])
						->setCellValue('G'.$filaDetalleCredito, $rowDetalleCredito['DET_CREDITO']);
						
			$filaDetalleCredito++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleCredito.':F'.$filaDetalleCredito); //Total Crédito
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleCredito, 'Total: ');
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('G'.$filaDetalleCredito, '=SUM(G'.($filaDetalleEfectivoOtros+1).':G'.($filaDetalleCredito-1).')');
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('D'.$filaDetalleCredito.':G'.$filaDetalleCredito);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('D'.$filaDetalleCredito, 'No hay resultados para mostrar');
	}
	
	$filaDetalleEgresos = $filaInicial;
	
	if(mysqli_num_rows($resultDetalleEgresos) > 0) 
	{
		while ($rowDetalleEgresos = mysqli_fetch_assoc($resultDetalleEgresos)) 
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('J'.$filaDetalleEgresos, $rowDetalleEgresos['DESCRIPCION']) 
						->setCellValue('K'.$filaDetalleEgresos, $rowDetalleEgresos['DET_EGRESOS']);
						
			$filaDetalleEgresos++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('I'.$filaDetalleEgresos.':J'.$filaDetalleEgresos); //Total Egresos
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('I'.$filaDetalleEgresos, 'Total: ');
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('K'.$filaDetalleEgresos, '=SUM(K'.$filaInicial.':K'.($filaDetalleEgresos-1).')');
	} 
	else 
	{
		$objPHPExcel->setActiveSheetIndex(0)
					->mergeCells('I'.$filaDetalleEgresos.':K'.$filaDetalleEgresos);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('I'.$filaDetalleEgresos, 'No hay resultados para mostrar');
	}
	
	$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('B1:K1')  //Título reporte
				->mergeCells('B3:D3')  //Caja anterior 
				->mergeCells('B5:G5')  //Título tabla ingresos
				->mergeCells('B6:C6')  //Vacio
				->mergeCells('I5:K5')  //Título tabla egresos
				->mergeCells('B'.$filaInicial.':B'.$filaDetalleEfectivoOtros)                   //Efectivo
				->mergeCells('C'.$filaInicial.':C'.$filaDetalleEfectivoVentas)                  //Ventas
				->mergeCells('C'.($filaDetalleEfectivoVentas+1).':C'.$filaDetalleEfectivoOtros) //Otros
				->mergeCells('B'.($filaDetalleEfectivoOtros+1).':C'.$filaDetalleCredito)        //Crédito
				->mergeCells('C'.($filaDetalleCredito+2).':D'.($filaDetalleCredito+2))          //Efectivo día
				->mergeCells('F'.($filaDetalleCredito+2).':G'.($filaDetalleCredito+2));         //Ingresos día
					
	// Se agregan los titulos del reporte
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B1', $tituloReporte)
				->setCellValue('B3', $cajaAnterior)
				->setCellValue('B5', $tituloTablaIngresos)
				->setCellValue('I5', $tituloTablaEgresos)
				->setCellValue('B'.$filaInicial, $tituloEfectivo)
				->setCellValue('C'.$filaInicial, $tituloVentas)
				->setCellValue('C'.($filaDetalleEfectivoVentas+1), $tituloOtros)
				->setCellValue('B'.($filaDetalleEfectivoOtros+1), $tituloCredito)
				->setCellValue('D6', $titulosColumnasIngresos[0])
				->setCellValue('E6', $titulosColumnasIngresos[1])
				->setCellValue('F6', $titulosColumnasIngresos[2])
				->setCellValue('G6', $titulosColumnasIngresos[3])
				->setCellValue('I6', $titulosColumnasEgresos[0])
				->setCellValue('J6', $titulosColumnasEgresos[1])
				->setCellValue('K6', $titulosColumnasEgresos[2])
				->setCellValue('C'.($filaDetalleCredito+2), $tituloEfectivoDia)
				->setCellValue('C'.($filaDetalleCredito+3), 'Caja')
				->setCellValue('D'.($filaDetalleCredito+3), '=E3')
				->setCellValue('C'.($filaDetalleCredito+4), 'Efectivo ventas')
				->setCellValue('D'.($filaDetalleCredito+4), '=G'.$filaDetalleEfectivoVentas)
				->setCellValue('C'.($filaDetalleCredito+5), 'Efectivo otros')
				->setCellValue('D'.($filaDetalleCredito+5), '=G'.$filaDetalleEfectivoOtros)
				->setCellValue('C'.($filaDetalleCredito+6), 'Egresos')
				->setCellValue('D'.($filaDetalleCredito+6), '=K'.$filaDetalleEgresos)
				->setCellValue('C'.($filaDetalleCredito+7), 'Total: ')
				->setCellValue('D'.($filaDetalleCredito+7), '=SUM(D'.($filaDetalleCredito+3).':'.'D'.($filaDetalleCredito+5).')-D'.($filaDetalleCredito+6))
				->setCellValue('F'.($filaDetalleCredito+2), $tituloIngresosDia)
				->setCellValue('F'.($filaDetalleCredito+3), 'No efectivo')
				->setCellValue('G'.($filaDetalleCredito+3), '=G'.$filaDetalleCredito)
				->setCellValue('F'.($filaDetalleCredito+4), 'Efectivo ventas')
				->setCellValue('G'.($filaDetalleCredito+4), '=G'.$filaDetalleEfectivoVentas)
				->setCellValue('F'.($filaDetalleCredito+5), 'Efectivo otros')
				->setCellValue('G'.($filaDetalleCredito+5), '=G'.$filaDetalleEfectivoOtros)
				->setCellValue('F'.($filaDetalleCredito+6), 'Total: ')
				->setCellValue('G'.($filaDetalleCredito+6), '=SUM(G'.($filaDetalleCredito+3).':'.'G'.($filaDetalleCredito+5).')');
	
	//Estilos de las celdas
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
	
	$estiloDiaAnterior = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array(
				'rgb' => 'C6E0B4'
			)
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);

	$estiloCabeceraTablas = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'D0CECE'
			)
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);

	$estiloEfectivo = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'E2EFDA'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);

	$estiloVentas = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FCE4D6'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);

	$estiloOtros = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FFF2CC'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);

	$estiloCredito = array(
		'font' => array(
			'name'  => 'Arial',
			'bold'  => true,                          
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'D9E1F2'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		),
		'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap'       => TRUE
		)
	);
	
	$estiloDatosEfectivoVentas2 = array(
		'font' => array(
			'name'  => 'Arial', 
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FCE4D6'
			) 
		),
		'borders' => array(
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN 
			),
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN 
			)
		)
	);
	$estiloDatosEfectivoVentas = new PHPExcel_Style();
	$estiloDatosEfectivoVentas->applyFromArray($estiloDatosEfectivoVentas2);
	
	$estiloTotalEfectivoVentas2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FCE4D6'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN 
			)
		), 
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);
	$estiloTotalEfectivoVentas = new PHPExcel_Style();
	$estiloTotalEfectivoVentas->applyFromArray($estiloTotalEfectivoVentas2);
	
	$estiloDatosEfectivoOtros2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FFF2CC'
			) 
		),
		'borders' => array(
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			),
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	$estiloDatosEfectivoOtros = new PHPExcel_Style();
	$estiloDatosEfectivoOtros->applyFromArray($estiloDatosEfectivoOtros2);
	
	$estiloTotalEfectivoOtros2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'FFF2CC'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		), 
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);
	$estiloTotalEfectivoOtros = new PHPExcel_Style();
	$estiloTotalEfectivoOtros->applyFromArray($estiloTotalEfectivoOtros2);
	
	$estiloDatosCredito2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'D9E1F2'
			) 
		),
		'borders' => array(
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	$estiloDatosCredito = new PHPExcel_Style();
	$estiloDatosCredito->applyFromArray($estiloDatosCredito2);
	
	$estiloTotalCredito2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'D9E1F2'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		), 
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);
	$estiloTotalCredito = new PHPExcel_Style();
	$estiloTotalCredito->applyFromArray($estiloTotalCredito2);
	
	$estiloDatosEgresos2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'E2EFDA'
			) 
		),
		'borders' => array(
			'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			), 
			'right' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	$estiloDatosEgresos = new PHPExcel_Style();
	$estiloDatosEgresos->applyFromArray($estiloDatosEgresos2);
	
	$estiloTotalEgresos2 = array(
		'font' => array(
			'name'  => 'Arial',               
			'color' => array(
				'rgb' => '000000'
			)
		),
		'fill' => array(
			'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
			'color' => array(
				'rgb' => 'E2EFDA'
			) 
		),
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		), 
		'alignment' => array(
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);
	$estiloTotalEgresos = new PHPExcel_Style();
	$estiloTotalEgresos->applyFromArray($estiloTotalEgresos2);
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	
	//Estilos cabeceras
	$objPHPExcel->getActiveSheet()->getStyle('B1:K1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->getStyle('B3:E3')->applyFromArray($estiloDiaAnterior);
	$objPHPExcel->getActiveSheet()->getStyle('E3')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	$objPHPExcel->getActiveSheet()->getStyle('B5:G6')->applyFromArray($estiloCabeceraTablas);
	$objPHPExcel->getActiveSheet()->getStyle('I5:K6')->applyFromArray($estiloCabeceraTablas);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$filaInicial.':B'.$filaDetalleEfectivoOtros)->applyFromArray($estiloEfectivo);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$filaInicial.':C'.$filaDetalleEfectivoVentas)->applyFromArray($estiloVentas);
	$objPHPExcel->getActiveSheet()->getStyle('C'.($filaDetalleEfectivoVentas+1).':C'.$filaDetalleEfectivoOtros)->applyFromArray($estiloOtros);
	$objPHPExcel->getActiveSheet()->getStyle('B'.($filaDetalleEfectivoOtros+1).':C'.$filaDetalleCredito)->applyFromArray($estiloCredito);
	$objPHPExcel->getActiveSheet()->getStyle('C'.($filaDetalleCredito+2).':D'.($filaDetalleCredito+2))->applyFromArray($estiloCabeceraTablas);
	$objPHPExcel->getActiveSheet()->getStyle('F'.($filaDetalleCredito+2).':G'.($filaDetalleCredito+2))->applyFromArray($estiloCabeceraTablas);
	
	/** Estilos datos **/
	//Efectivo ventas
	for ($i = $filaInicial; $i <= $filaDetalleEfectivoVentas; $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloDatosEfectivoVentas2)->getNumberFormat()->setFormatCode('0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosEfectivoVentas, 'D'.$filaInicial.':F'.($filaDetalleEfectivoVentas-1));
	
	$objPHPExcel->getActiveSheet()->getStyle('G'.$filaDetalleEfectivoVentas)->applyFromArray($estiloTotalEfectivoVentas2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalEfectivoVentas, 'D'.$filaDetalleEfectivoVentas.':F'.$filaDetalleEfectivoVentas);
	
	//Efectivo otros
	for ($i = $filaDetalleEfectivoVentas; $i <= $filaDetalleEfectivoOtros; $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloDatosEfectivoOtros2)->getNumberFormat()->setFormatCode('0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosEfectivoOtros, 'D'.($filaDetalleEfectivoVentas+1).':F'.($filaDetalleEfectivoOtros-1));
	
	$objPHPExcel->getActiveSheet()->getStyle('G'.$filaDetalleEfectivoOtros)->applyFromArray($estiloTotalEfectivoOtros2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalEfectivoOtros, 'D'.$filaDetalleEfectivoOtros.':F'.$filaDetalleEfectivoOtros);
	
	//Crédito
	for ($i = $filaDetalleEfectivoOtros; $i <= $filaDetalleCredito; $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloDatosCredito2)->getNumberFormat()->setFormatCode('0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosCredito, 'D'.($filaDetalleEfectivoOtros+1).':F'.($filaDetalleCredito-1));
	
	$objPHPExcel->getActiveSheet()->getStyle('G'.$filaDetalleCredito)->applyFromArray($estiloTotalCredito2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalCredito, 'D'.$filaDetalleCredito.':F'.$filaDetalleCredito);
	
	//Egresos 
	for ($i = $filaInicial; $i <= $filaDetalleEgresos; $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($estiloDatosEgresos2)->getNumberFormat()->setFormatCode('0.00');
		$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosEgresos, 'I'.$filaInicial.':J'.($filaDetalleEgresos-1));
	
	$objPHPExcel->getActiveSheet()->getStyle('K'.$filaDetalleEgresos)->applyFromArray($estiloTotalEgresos2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalEgresos, 'I'.$filaDetalleEgresos.':J'.$filaDetalleEgresos);
	
	//Efectivo día
	for ($i = ($filaDetalleCredito+3); $i <= ($filaDetalleCredito+6); $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($estiloDatosEfectivoVentas2)->getNumberFormat()->setFormatCode('0.00');
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosEfectivoVentas, 'C'.($filaDetalleCredito+3).':C'.($filaDetalleCredito+6));
	
	$objPHPExcel->getActiveSheet()->getStyle('D'.($filaDetalleCredito+7))->applyFromArray($estiloTotalEfectivoVentas2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalEfectivoVentas, 'C'.($filaDetalleCredito+7).':C'.($filaDetalleCredito+7));
	
	//Ingresos día
	for ($i = ($filaDetalleCredito+3); $i <= ($filaDetalleCredito+5); $i++) 
	{
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($estiloDatosEfectivoOtros2)->getNumberFormat()->setFormatCode('0.00');
	}
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloDatosEfectivoOtros, 'F'.($filaDetalleCredito+3).':F'.($filaDetalleCredito+5));
	
	$objPHPExcel->getActiveSheet()->getStyle('G'.($filaDetalleCredito+6))->applyFromArray($estiloTotalEfectivoOtros2)->getNumberFormat()->setFormatCode('0.00');
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloTotalEfectivoOtros, 'F'.($filaDetalleCredito+6).':F'.($filaDetalleCredito+6));
	
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
			
	for($i = 'A'; $i <= 'B'; $i++)
	{
		$objPHPExcel->setActiveSheetIndex(0)
			->getColumnDimension($i)->setAutoSize(TRUE);
	}
	
	// Se asigna el nombre a la hoja
	$objPHPExcel->getActiveSheet()->setTitle('Ingresos diarios');

	// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
	$objPHPExcel->setActiveSheetIndex(0);
	// Inmovilizar paneles 
	// $objPHPExcel->getActiveSheet(0)->freezePane('A4');
	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0, 4);

	// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	$file = "reporte-de-ingresos-diarios-" .date("YmdHis"). ".xlsx";
	header('Content-Disposition: attachment;filename="' .$file. '"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
	
	/* FIN - GENERACIÓN DEL REPORTE */
?>