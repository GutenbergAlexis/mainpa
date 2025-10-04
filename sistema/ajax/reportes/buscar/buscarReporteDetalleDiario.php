<?php
	include('../../../util/database.php');
	
	$fechaDetalleDiario = $_POST['fechaDetalleDiario'];

	$selectTotalEfectivo = 
		"SELECT DATE_FORMAT(COM.fec_emision, '%d/%m/%Y') AS FECHA, format(SUM(PAG.monto), 2) AS TOT_EFECTIVO
			FROM comprobantes COM 
			JOIN pagos PAG ON PAG.id_comprobante = COM.id
			WHERE DATE_FORMAT(COM.fec_emision, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			AND PAG.codigo_medio_pago = 1 AND COM.anulado = 0 AND COM.emitido = 1 
			GROUP BY DATE_FORMAT(COM.fec_emision, '%d/%m/%Y')";
			
	$selectTotalCredito = 
		"SELECT DATE_FORMAT(COM.fec_emision, '%d/%m/%Y') AS FECHA, format(SUM(PAG.monto), 2) AS TOT_CREDITO
			FROM comprobantes COM 
			JOIN pagos PAG ON PAG.id_comprobante = COM.id
			WHERE DATE_FORMAT(COM.fec_emision, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			AND PAG.codigo_medio_pago <> 1 AND COM.anulado = 0 AND COM.emitido = 1 
			GROUP BY DATE_FORMAT(COM.fec_emision, '%d/%m/%Y')";
			
	$selectTotalOtros = 
		"SELECT DATE_FORMAT(OI.fecha, '%d/%m/%Y') AS FECHA, format(SUM(OI.monto), 2) AS TOT_OTROS 
			FROM otros_ingresos OI 
			WHERE DATE_FORMAT(OI.fecha, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			GROUP BY DATE_FORMAT(OI.fecha, '%d/%m/%Y')";
			
	$selectTotalEgresos = 
		"SELECT DATE_FORMAT(CMP.fec_compra, '%d/%m/%Y') AS FECHA, format(SUM(CMP.mon_total), 2) AS TOT_EGRESOS 
			FROM compras CMP 
			WHERE DATE_FORMAT(CMP.fec_compra, '%Y-%m-%d') = STR_TO_DATE('$fechaDetalleDiario', '%d/%m/%Y') 
			GROUP BY DATE_FORMAT(CMP.fec_compra, '%d/%m/%Y')";
	
	if (!$resultTotalEfectivo = mysqli_query($con, $selectTotalEfectivo)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultTotalCredito = mysqli_query($con, $selectTotalCredito)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultTotalOtros = mysqli_query($con, $selectTotalOtros)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultTotalEgresos = mysqli_query($con, $selectTotalEgresos)) 
	{
		exit(mysqli_error($con));
	}
	
	if(mysqli_num_rows($resultTotalEfectivo) > 0) 
	{
		while ($rowTotalEfectivo = mysqli_fetch_assoc($resultTotalEfectivo)) 
		{
			$totalEfectivo = $rowTotalEfectivo['TOT_EFECTIVO'];
		}
	}
	
	if(mysqli_num_rows($resultTotalCredito) > 0) 
	{
		while ($rowTotalCredito = mysqli_fetch_assoc($resultTotalCredito)) 
		{
			$totalCredito = $rowTotalCredito['TOT_CREDITO'];
		}
	}
		
	if(mysqli_num_rows($resultTotalOtros) > 0) 
	{
		while ($rowTotalOtros = mysqli_fetch_assoc($resultTotalOtros)) 
		{
			$totalOtros = $rowTotalOtros['TOT_OTROS'];
		}
	}
		
	if(mysqli_num_rows($resultTotalEgresos) > 0) 
	{
		while ($rowTotalEgresos = mysqli_fetch_assoc($resultTotalEgresos)) 
		{
			$totalEgresos = $rowTotalEgresos['TOT_EGRESOS'];
		}
	}
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="12%" rowspan="2" style="text-align:center;vertical-align:middle;">Fecha</th>
						<th width="66%" colspan="3" style="text-align:center;vertical-align:middle;">Ingresos</th>
						<th width="22%" rowspan="2" style="text-align:center;vertical-align:middle;">Egresos</th>
					</tr>
					<tr>
						<th width="22%" style="text-align:center;vertical-align:middle;">Efectivo</th>
						<th width="22%" style="text-align:center;vertical-align:middle;">Crédito</th>
						<th width="22%" style="text-align:center;vertical-align:middle;">Otros</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td align="right"><?php echo $fechaDetalleDiario; ?></td>
						<td align="right"><?php echo $totalEfectivo == "" ? "0.00" : $totalEfectivo; ?></td>
						<td align="right"><?php echo $totalCredito  == "" ? "0.00" : $totalCredito; ?></td>
						<td align="right"><?php echo $totalOtros    == "" ? "0.00" : $totalOtros; ?></td>
						<td align="right"><?php echo $totalEgresos  == "" ? "0.00" : $totalEgresos; ?></td>
					</tr>
				</tbody>
			</table>