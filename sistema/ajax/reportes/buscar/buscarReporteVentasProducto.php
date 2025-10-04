<?php
	include('../../../util/database.php');
	
	$tipoComprobante = $_POST['tipoComprobante'];
	$fechaDesde      = $_POST['fechaDesde'];
	$fechaHasta      = $_POST['fechaHasta'];

	$query = 
		"SELECT com.fec_emision, par1.descripcion AS tip_comprobante, 
			concat(com.ser_comprobante, '-', lpad(com.num_comprobante, 6, '0')) AS num_comprobante, 
			concat_ws(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS cliente, 
			pro.descripcion AS producto, dcom.cantidad_final AS cantidad, format(dcom.precio, 2) AS pre_unitario, 
			format(round(dcom.cantidad_final*dcom.precio, 2), 2) AS pre_total 
			FROM comprobantes com
			JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8 
			JOIN clientes cli ON cli.id = com.id_cliente 
			JOIN det_comprobante dcom ON dcom.id_comprobante = com.ID 
			JOIN productos pro ON pro.id = dcom.id_producto 
			WHERE com.anulado = 0 AND com.emitido = 1";
	
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
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>N°</th>
						<th>Fec. Emisión</th>
						<th>Tip. Comprobante</th>
						<th>Núm. Comprobante</th>
						<th>Cliente</th>
						<th>Producto</th>
						<th>Cantidad</th>
						<th>P.U.</th>
						<th>P.T.</th>
					</tr>
				</thead>
				<tbody>
<?php
	if(mysqli_num_rows($result) > 0) 
	{
		$number = 1;
		while ($row = mysqli_fetch_assoc($result)) 
		{
?>
					<tr>
						<td><?php echo $number; ?></td>
						<td><?php echo $row['fec_emision']; ?></td>
						<td><?php echo utf8_encode($row['tip_comprobante']); ?></td>
						<td><?php echo $row['num_comprobante']; ?></td>
						<td><?php echo utf8_encode($row['cliente']); ?></td>
						<td><?php echo utf8_encode($row['producto']); ?></td>
						<td align="center"><?php echo $row['cantidad']; ?></td>
						<td align="right"><?php echo $row['pre_unitario']; ?></td>
						<td align="right"><?php echo $row['pre_total']; ?></td>
					</tr>
<?php
			$number++;
		}
	}
	else 
	{
?>
					<tr><td colspan="9">No existen registros para su búsqueda.</td></tr>
<?php
	}
?>
				</tbody>
			</table>