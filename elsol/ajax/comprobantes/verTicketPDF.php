<?php
	date_default_timezone_set('America/Lima');

	include('../../util/database.php');
	include('../../util/numerosALetras.php');
    //require('../../util/fpdf/fpdf.php');
	require('../../util/rotation.php');
	session_start();
	
	$idComprobante = $_POST['id-comprobante'];
	
	$selectComprobante = "
		SELECT com.id, com.id_cliente, cli.tip_documento, cli.num_documento, par2.descripcion AS des_tipo_documento, 
			com.ser_comprobante, com.num_comprobante, cli.direccion, 
			CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social, 
			com.tip_comprobante, par1.descripcion AS des_comprobante, par3.descripcion AS des_med_pago, 
			com.guia_remision, DATE_FORMAT(com.fec_emision, '%d/%m/%Y %H:%i:%s') AS fecha, com.ord_compra, 
			IF(com.observaciones = '', '-', com.observaciones) AS observaciones, com.emitido, com.par_medio_pago, 
			com.usu_creacion AS vendedor, com.anulado   
		FROM comprobantes com
		JOIN clientes cli ON cli.id = com.id_cliente
		JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8
		JOIN parametros par2 ON par2.codigo = cli.tip_documento AND par2.padre = 12
		LEFT JOIN parametros par3 ON par3.codigo = com.par_medio_pago AND par3.padre = 4
		WHERE com.id = '$idComprobante'";

	$selectDetComprobante = "
		SELECT dcom.id_producto, pro.codigo AS cod_producto, pro.descripcion AS des_producto, dcom.cantidad, 
			dcom.precio AS precio_unitario, dcom.precio*dcom.cantidad_final precio_total, pro.unidad_medida, 
			dcom.espesor, dcom.ancho, dcom.largo, dcom.cantidad_final, par.abreviatura AS abr_unidad_medida 
		FROM det_comprobante dcom
		JOIN productos pro ON pro.id = dcom.id_producto 
		JOIN parametros par ON par.codigo = pro.unidad_medida 
		WHERE par.padre = 29 AND dcom.id_comprobante = '$idComprobante'";
	
	if (!$resultSelectComprobante = mysqli_query($con, $selectComprobante)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultSelectDetComprobante = mysqli_query($con, $selectDetComprobante)) 
	{
		exit(mysqli_error($con));
	}
	
	if(mysqli_num_rows($resultSelectComprobante) > 0) 
	{
		while ($rowResultSelectComprobante = mysqli_fetch_assoc($resultSelectComprobante)) 
		{
			class PDF extends PDF_Rotate
			{
				function RotatedText($x, $y, $txt, $angle)
				{
					$this->Rotate($angle, $x, $y);
					$this->Text($x, $y, $txt);
					$this->Rotate(0);
				}
			}
			
			$pdf=new PDF('P', 'mm', array(130, 500));
			$pdf->AddPage();
			$pdf->SetMargins(5, 5, 5);
			$pdf->SetFont('Arial', 'B', 24);
			$pdf->MultiCell(112, 10, 'COMERCIAL MADERERA EL SOL', 0, 'C');
			$pdf->SetFont('Arial', '', 14);
			$pdf->Cell(120, 8, 'AV. PACHACUTEC MZ. AW LOTE 13', 0, 1, 'C');
			$pdf->MultiCell(120, 8, 'CERCADO DE JICAMARCA - SAN ANTONIO - HUAROCHIRI', 0, 'C');
			$pdf->Cell(120, 8, 'Telf.: 415-9046', 0, 1, 'C');
			$pdf->Cell(120, 8, '', 0, 1, 'C');
			$pdf->SetFont('Arial', 'B', 14);
			$pdf->Cell(120, 8, 'TICKET DE VENTA', 0, 1, 'C');
			$pdf->Cell(120, 8, $rowResultSelectComprobante['ser_comprobante'].'-'.str_pad($rowResultSelectComprobante['num_comprobante'], 9, "0", STR_PAD_LEFT), 0, 1, 'C');
			$pdf->SetFont('Arial', '', 14);
			$pdf->Cell(120, 8, '', 0, 1, 'C');
			$pdf->MultiCell(120, 8, utf8_decode('CLIENTE: '.$rowResultSelectComprobante['num_documento']).' - '.$rowResultSelectComprobante['nombre_razon_social'], 0, 1, '');
			$pdf->MultiCell(120, 8, utf8_decode('DIREC.: '.$rowResultSelectComprobante['direccion']), 0, 1, '');
			$pdf->Cell(120, 8, 'FECHA: '.$rowResultSelectComprobante['fecha'], 0, 1, '');
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
				while ($rowResultSelectDetComprobante = mysqli_fetch_assoc($resultSelectDetComprobante)) 
				{
					if ($rowResultSelectDetComprobante['unidad_medida'] == 3) 
					{
						$pdf->Cell(120, 8, $rowResultSelectDetComprobante['des_producto'].' - '.$rowResultSelectDetComprobante['cantidad'].' - '.$rowResultSelectDetComprobante['espesor'].' X '.$rowResultSelectDetComprobante['ancho'].' X '.$rowResultSelectDetComprobante['largo'], 0, 1, '');
					} 
					else 
					{
						$pdf->Cell(120, 8, $rowResultSelectDetComprobante['des_producto'], 0, 1, '');
					}
					
					$pdf->Cell(20, 8, $rowResultSelectDetComprobante['cantidad_final'], 0, 0, '');
					$pdf->Cell(20, 8, $rowResultSelectDetComprobante['abr_unidad_medida'], 0, 0, '');
					$pdf->Cell(40, 8, '', 0, 0, '');
					$pdf->Cell(20, 8, number_format($rowResultSelectDetComprobante['precio_unitario'], 2, '.', ''), 0, 0, 'R');
					$pdf->Cell(20, 8, number_format($rowResultSelectDetComprobante['precio_total'], 2, '.', ''), 0, 1, 'R');
					$montoTotal += $rowResultSelectDetComprobante['precio_total'];
				}
			}
			
			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(120, 8, '-----------------------------------------------------------------------------------------------------', 0, 1, '');
			$metodoReflex  = new ReflectionMethod('numerosALetras', 'to_word');
			$montoEnLetras = $metodoReflex->invoke(new numerosALetras(), round($montoTotal, 2), 'PEN');
			/** fin productos **/
			
			$pdf->SetFont('Arial', '', 14);
			$pdf->Cell(120, 8, 'TOTAL:   S/ '.number_format($montoTotal, 2, '.', ''), 0, 1, 'R');
			$pdf->MultiCell(120, 8, utf8_decode('OBSERVACIONES: '.$rowResultSelectComprobante['observaciones']), 0);
			$pdf->MultiCell(120, 8, 'SON: '.$montoEnLetras, 0);
			$pdf->Cell(120, 8, utf8_decode('VENDEDOR: '.$rowResultSelectComprobante['vendedor']), 0, 1, '');
			$pdf->Cell(120, 8, '', 0, 1, 'C');
			$pdf->MultiCell(120, 8, utf8_decode('Nota: Una vez entregado el material, no hay cambios ni devoluciones. El plazo de recojo es de 72 horas, luego se le cobrará el costo de almacenaje.'), 0, 'C');
			$pdf->SetFont('Arial', 'I', 18);
			$pdf->Cell(120, 8, '', 0, 1, 'C');
			$pdf->Cell(120, 8, utf8_decode('¡Gracias por su preferencia!'), 0, 1, 'C');
			
			if ($rowResultSelectComprobante['anulado'] == 1)
			{
				$pdf->SetFont('Arial', '', 50);
				$pdf->SetTextColor(255, 0, 0);
				$pdf->RotatedText(30, 150, 'ANULADO', 30);
			}
			
			$pdf->Output();
		}
	}
?>