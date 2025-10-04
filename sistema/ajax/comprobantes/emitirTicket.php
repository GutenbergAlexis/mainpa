<?php
	date_default_timezone_set('America/Lima');
	
	include('../../util/database.php');
	include('../../util/numerosALetras.php');
    require('../../util/fpdf/fpdf.php');
	session_start();
	
	$idComprobante        = $_POST['id-comprobante'];
	$montoTotal           = $_POST['monto-total'];
	$usuario              = $_SESSION['user'];
	$medioPago            = array();
	$montoPagado          = array();
	$vMontoTotal          = 0.0;
	$respuesta['mensaje'] = "";
	$fechaActual          = date('d-m-Y H:i:s');
	
	//Inicio validaciones
	for ($i = 1; $i <= 10; $i++) 
	{
		if ($_POST['CB'.$i] == $i) 
		{
			$monto = $_POST['MP'.$i];
			array_push($medioPago, $i);
			array_push($montoPagado, $monto);
		}
	}
	
	$respuesta['mensaje'] .= empty($medioPago) ? "-Debe ingresar al menos un medio de pago.\n" : "";
	
	if ((count($montoPagado) == 1 && !empty($montoPagado[0])) || count($montoPagado) > 1) 
	{
		for ($i = 0; $i < count($montoPagado); $i++) 
		{
			$vMontoTotal += $montoPagado[$i];
		}
		
		$respuesta['mensaje'] .= $vMontoTotal != $montoTotal ? "-El monto a pagar no corresponde con el calculado en el comprobante.\n" : "";
	}
	//Fin validaciones
	
	if (empty($respuesta['mensaje'])) //Consultar si existe alguna validación
	{
		$selectComprobante = "
			SELECT com.id, com.id_cliente, cli.tip_documento, cli.num_documento, par2.descripcion AS des_tipo_documento, 
				cli.direccion, com.tip_comprobante, par1.descripcion AS des_comprobante, par3.descripcion AS des_med_pago, 
				CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social, 
				com.guia_remision, DATE_FORMAT(com.fec_emision, '%d/%m/%Y %H:%i:%s') AS fecha, com.ord_compra, 
				IF(com.observaciones = '', '-', com.observaciones) AS observaciones, com.emitido, com.par_medio_pago, 
				com.usu_creacion AS vendedor  
			FROM comprobantes com
			JOIN clientes cli ON cli.id = com.id_cliente
			JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8
			JOIN parametros par2 ON par2.codigo = cli.tip_documento AND par2.padre = 12
			LEFT JOIN parametros par3 ON par3.codigo = com.par_medio_pago AND par3.padre = 4
			WHERE com.id = '$idComprobante'";

		$selectDetComprobante = "
			SELECT dcom.id_producto AS id_producto, pro.codigo AS cod_producto, pro.descripcion AS des_producto, dcom.cantidad, 
				dcom.precio AS precio_unitario, dcom.precio*dcom.cantidad_final precio_total, pro.unidad_medida, 
				dcom.espesor, dcom.ancho, dcom.largo, dcom.cantidad_final, par.abreviatura AS abr_unidad_medida 
			FROM det_comprobante dcom
			JOIN productos pro ON pro.id = dcom.id_producto 
			JOIN parametros par ON par.codigo = pro.unidad_medida 
			WHERE par.padre = 29 AND dcom.id_comprobante = '$idComprobante'";
		
		$updateActualizarEmitido = "
			UPDATE comprobantes com 
			SET com.emitido = 1, com.usu_modificacion = '$usuario', com.fec_modificacion = STR_TO_DATE('$fechaActual', '%d-%m-%Y %H:%i:%s') 
			WHERE com.id = '$idComprobante'";
		
		$deletePagos = "
			DELETE FROM pagos 
			WHERE id_comprobante = '$idComprobante'";
		
		if (!$resultDeletePagos = mysqli_query($con, $deletePagos)) 
		{
			exit(mysqli_error($con));
		}
		
		for ($i = 0; $i < count($medioPago); $i++) 
		{
			$vMedioPago   = $medioPago[$i];
			$vMontoPagado = count($montoPagado) == 1 ? $montoTotal : $montoPagado[$i];
			
			$insertPagos = "
				INSERT INTO pagos (id_comprobante, codigo_medio_pago, monto) 
				VALUES ('$idComprobante', '$vMedioPago', '$vMontoPagado')";
			
			if (!$resultInsertPagos = mysqli_query($con, $insertPagos)) 
			{
				exit(mysqli_error($con));
			}
		}
		
		if (!$resultSelectComprobante = mysqli_query($con, $selectComprobante)) 
		{
			exit(mysqli_error($con));
		}
		
		if (!$resultSelectDetComprobante = mysqli_query($con, $selectDetComprobante)) 
		{
			exit(mysqli_error($con));
		}
		
		if (!$resultUpdateActualizarEmitido = mysqli_query($con, $updateActualizarEmitido)) 
		{
			exit(mysqli_error($con));
		}
		
		if(mysqli_num_rows($resultSelectComprobante) > 0) 
		{
			while ($rowSelectComprobante = mysqli_fetch_assoc($resultSelectComprobante)) 
			{
				$tipoComprobante = $rowSelectComprobante['tip_comprobante'];
				
				$selectNumeroComprobante = "
					SELECT ser.ser_comprobante, ser.num_comprobante 
					FROM serie_comprobante ser 
					WHERE ser.tip_comprobante = '$tipoComprobante' 
						AND ser.estado = '1'";
				
				if (!$resultSelectNumeroComprobante = mysqli_query($con, $selectNumeroComprobante)) 
				{
					exit(mysqli_error($con));
				}
				
				while ($rowSelectNumeroComprobante = mysqli_fetch_assoc($resultSelectNumeroComprobante)) 
				{
					$serieComprobante  = $rowSelectNumeroComprobante['ser_comprobante'];
					$numeroComprobante = $rowSelectNumeroComprobante['num_comprobante'] + 1;
				}
				
				$updateSerieNumeroComprobante = "
					UPDATE comprobantes 
					SET ser_comprobante = '$serieComprobante', num_comprobante = '$numeroComprobante' 
					WHERE id = '$idComprobante'";
				
				if (!$resultUpdateSerieNumeroComprobante = mysqli_query($con, $updateSerieNumeroComprobante)) 
				{
					exit(mysqli_error($con));
				}
				
				$updateNumeroComprobante = "
					UPDATE serie_comprobante ser 
					SET ser.num_comprobante = '$numeroComprobante'
					WHERE ser.ser_comprobante = '$serieComprobante'";
				
				if (!$resultUpdateNumeroComprobante = mysqli_query($con, $updateNumeroComprobante)) 
				{
					exit(mysqli_error($con));
				}
				
				$pdf = new FPDF('P', 'mm', array(130, 500));
				$pdf->AddPage();
				$pdf->SetMargins(5, 5, 5); 
				$pdf->SetFont('Arial', 'B', 24);
				$pdf->Cell(112, 10, 'MAINPA', 0, 1, 'C');
				$pdf->SetFont('Arial', '', 14);
				$pdf->Cell(120, 8, utf8_decode('AV. PRÓCERES DE LA INDEPENDENCIA'), 0, 1, 'C');
				$pdf->Cell(120, 8, 'NRO. 2975 URB. CANTO GRANDE', 0, 1, 'C');
				$pdf->Cell(120, 8, 'SAN JUAN DE LURIGANCHO - LIMA - LIMA', 0, 1, 'C');
				$pdf->Cell(120, 8, 'Telf.: 389-5159', 0, 1, 'C');
				$pdf->Cell(120, 8, '', 0, 1, 'C');
				$pdf->SetFont('Arial', 'B', 14);
				$pdf->Cell(120, 8, 'TICKET DE VENTA', 0, 1, 'C');
				$pdf->Cell(120, 8, $serieComprobante.'-'.str_pad($numeroComprobante, 9, "0", STR_PAD_LEFT), 0, 1, 'C');
				$pdf->SetFont('Arial', '', 14);
				$pdf->Cell(120, 8, '', 0, 1, 'C');
				$pdf->MultiCell(120, 8, utf8_decode('CLIENTE: '.$rowSelectComprobante['num_documento']).' - '.$rowSelectComprobante['nombre_razon_social'], 0, 1, '');
				$pdf->MultiCell(120, 8, utf8_decode('DIREC.: '.$rowSelectComprobante['direccion']), 0, 1, '');
				$pdf->Cell(120, 8, 'FECHA: '.$rowSelectComprobante['fecha'], 0, 1, '');
				$pdf->Cell(120, 8, '', 0, 1, 'C');
				
				/** inicio productos **/
				/*$pdf->SetFont('Arial', '', 10);
				$pdf->Cell(120, 8, '-----------------------------------------------------------------------------------------------------', 0, 1, '');
				$pdf->SetFont('Arial', 'B', 13);
				$pdf->Cell(15, 8, utf8_decode('CAN.'), 0, 0, '');
				$pdf->Cell(15, 8, utf8_decode('U.M.'), 0, 0, '');
				$pdf->Cell(60, 8, utf8_decode('DESCRIPCIÓN'), 0, 0, '');
				$pdf->Cell(15, 8, utf8_decode('P.Unit.'), 0, 0, '');
				$pdf->Cell(15, 8, utf8_decode('P.Total'), 0, 1, '');*/
				$pdf->SetFont('Arial', '', 10);
				$pdf->Cell(120, 8, '-----------------------------------------------------------------------------------------------------', 0, 1, '');
				
				$montoTotal    = 0;
				$montoEnLetras = '-';
				
				$pdf->SetFont('Arial', '', 14);
				
				if(mysqli_num_rows($resultSelectDetComprobante) > 0) 
				{
					while ($rowSelectDetComprobante = mysqli_fetch_assoc($resultSelectDetComprobante)) 
					{
						$selectStockProducto = "
							SELECT pro.id, pro.stock 
							FROM productos pro 
							WHERE pro.id = $rowSelectDetComprobante[id_producto]";
						
						if (!$resultSelectStockProducto = mysqli_query($con, $selectStockProducto)) 
						{
							exit(mysqli_error($con));
						}
						
						while ($rowSelectStockProducto = mysqli_fetch_assoc($resultSelectStockProducto)) 
						{
							$stockProducto = $rowSelectStockProducto['stock'] - $rowSelectDetComprobante['cantidad_final'];
						}
						
						$updateStockProducto = "
							UPDATE productos pro 
							SET pro.stock = $stockProducto 
							WHERE pro.id = $rowSelectDetComprobante[id_producto]";
						
						if (!$resultUpdateStockProducto = mysqli_query($con, $updateStockProducto)) 
						{
							exit(mysqli_error($con));
						}
						
						if ($rowSelectDetComprobante['unidad_medida'] == 3) 
						{
							$pdf->Cell(120, 8, $rowSelectDetComprobante['des_producto'].' - '.$rowSelectDetComprobante['cantidad'].' - '.$rowSelectDetComprobante['espesor'].' X '.$rowSelectDetComprobante['ancho'].' X '.$rowSelectDetComprobante['largo'], 0, 1, '');
						} 
						else 
						{
							$pdf->Cell(120, 8, $rowSelectDetComprobante['des_producto'], 0, 1, '');
						}
						
						$pdf->Cell(20, 8, $rowSelectDetComprobante['cantidad_final'], 0, 0, '');
						$pdf->Cell(20, 8, $rowSelectDetComprobante['abr_unidad_medida'], 0, 0, '');
						$pdf->Cell(40, 8, '', 0, 0, '');
						$pdf->Cell(20, 8, number_format($rowSelectDetComprobante['precio_unitario'], 2, '.', ''), 0, 0, 'R');
						$pdf->Cell(20, 8, number_format($rowSelectDetComprobante['precio_total'], 2, '.', ''), 0, 1, 'R');
						$montoTotal += $rowSelectDetComprobante['precio_total'];
					}
				}
				
				$pdf->SetFont('Arial', '', 10);
				$pdf->Cell(120, 8, '-----------------------------------------------------------------------------------------------------', 0, 1, '');
				$metodoReflex  = new ReflectionMethod('numerosALetras', 'to_word');
				$montoEnLetras = $metodoReflex->invoke(new numerosALetras(), round($montoTotal, 2), 'PEN');
				/** fin productos **/
				
				$pdf->SetFont('Arial', '', 14);
				$pdf->Cell(120, 8, 'TOTAL:   S/ '.number_format($montoTotal, 2, '.', ''), 0, 1, 'R');
				$pdf->MultiCell(120, 8, utf8_decode('OBSERVACIONES: '.$rowSelectComprobante['observaciones']), 0);
				$pdf->MultiCell(120, 8, 'SON: '.$montoEnLetras, 0);
				$pdf->Cell(120, 8, utf8_decode('VENDEDOR: '.$rowSelectComprobante['vendedor']), 0, 1, '');
				$pdf->Cell(120, 8, '', 0, 1, 'C');
				$pdf->MultiCell(120, 8, utf8_decode('Nota: Una vez entregado el material, no hay cambios ni devoluciones. El plazo de recojo es de 72 horas, luego se le cobrará el costo de almacenaje.'), 0, 'C');
				$pdf->SetFont('Arial', 'I', 18);
				$pdf->Cell(120, 8, '', 0, 1, 'C');
				$pdf->Cell(120, 8, utf8_decode('¡Gracias por su preferencia!'), 0, 1, 'C');
				$pdf->Output();
			}
		}
	}
	else
	{
		$respuesta['estado'] = 200;
		echo '<script language="javascript">';
		echo 	'alert("Existen errores.")';
		echo '</script>';
		echo $respuesta['mensaje'];
	}
?>