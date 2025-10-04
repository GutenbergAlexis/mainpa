<?php
	include('../../../util/database.php');
	
	$tipoComprobante = $_POST['tipoComprobante'];
	$fechaDesde      = $_POST['fechaDesde'];
	$fechaHasta      = $_POST['fechaHasta'];

	$selectVentasGenerales = 
		"SELECT com.fec_emision, par1.descripcion AS tip_comprobante, 
			concat(com.ser_comprobante, '-', lpad(com.num_comprobante, 6, '0')) AS num_comprobante, 
			concat_ws(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS cliente, 
			if(com.anulado = 0, com.mon_igv, 0) AS mon_igv, if(com.anulado = 0, com.mon_neto, 0) AS mon_neto, 
			if(com.anulado = 0, com.mon_total, 0) AS mon_total, par2.descripcion AS med_pago, 
			if(com.anulado = 0, com.observaciones, 'ANULADO') AS observaciones, if(com.pagado = 0, 'no pagado', 'pagado') AS pagado, 
			if(com.emitido = 0, 'no emitido', 'emitido') AS emitido, if(com.entregado = 0, 'no entregado', 'entregado') AS entregado, 
			com.usu_creacion AS usuario
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
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>N°</th>
						<th>Fec. Emisión</th>
						<th>Tip. Comprobante</th>
						<th>Núm. Comprobante</th>
						<th>Cliente</th>
						<th>Sub Total</th>
						<th>IGV</th>
						<th>Total</th>
						<th>Mod. Pago</th>
						<th>Observaciones</th>
						<th>Pagado</th>
						<th>Emitido</th>
						<th>Entregado</th>
						<th>Vendedor</th>
					</tr>
				</thead>
				<tbody>
<?php 
	if(mysqli_num_rows($resultSelectVentasGenerales) > 0) 
	{
		$number = 1;
		while ($rowSelectVentasGenerales = mysqli_fetch_assoc($resultSelectVentasGenerales)) 
		{
?>
					<tr>
						<td><?php echo $number; ?></td>
						<td><?php echo $rowSelectVentasGenerales['fec_emision']; ?></td>
						<td><?php echo $rowSelectVentasGenerales['tip_comprobante']; ?></td>
						<td><?php echo $rowSelectVentasGenerales['num_comprobante']; ?></td>
						<td><?php echo utf8_encode($rowSelectVentasGenerales['cliente']); ?></td>
						<td align="right"><?php echo $rowSelectVentasGenerales['mon_neto']; ?></td>
						<td align="right"><?php echo $rowSelectVentasGenerales['mon_igv']; ?></td>
						<td align="right"><?php echo $rowSelectVentasGenerales['mon_total']; ?></td>
						<td><?php echo utf8_encode($rowSelectVentasGenerales['med_pago']); ?></td>
						<td><?php echo utf8_encode($rowSelectVentasGenerales['observaciones']); ?></td>
						<td><?php echo $rowSelectVentasGenerales['pagado']; ?></td>
						<td><?php echo $rowSelectVentasGenerales['emitido']; ?></td>
						<td><?php echo $rowSelectVentasGenerales['entregado']; ?></td>
						<td><?php echo $rowSelectVentasGenerales['usuario']; ?></td>
					</tr>
<?php 
			$number++;
		}
	}
	else 
	{
?>
					<tr><td colspan="14">No existen registros para su búsqueda.</td></tr>
<?php 
	}
?>
				</tbody>
			</table>