<?php
	include('../../../util/database.php');
	
	$proveedor  = $_POST['proveedor'];
	$fechaDesde = $_POST['fechaDesde'];
	$fechaHasta = $_POST['fechaHasta'];

	$query = 
		"SELECT CMP.FEC_COMPRA, PRV.RAZON_SOCIAL AS PROVEEDOR, PRO.DESCRIPCION AS PRODUCTO, DCMP.CANTIDAD AS CANTIDAD, FORMAT(DCMP.COSTO, 2) AS COS_NETO, FORMAT(ROUND(DCMP.CANTIDAD*DCMP.COSTO, 2), 2) AS COS_TOTAL
			FROM compras CMP
			JOIN proveedores PRV ON PRV.ID = CMP.ID_PROVEEDOR 
			JOIN det_compra DCMP ON DCMP.ID_COMPRA = CMP.ID 
			JOIN productos PRO ON PRO.ID = DCMP.ID_PRODUCTO 
			WHERE 1 = 1";
	
	if (!is_null($proveedor) && !empty($proveedor)) 
	{
		$query = $query." AND PRV.ID = '$proveedor'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$query = $query." AND CMP.FEC_COMPRA >= STR_TO_DATE('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$query = $query." AND CMP.FEC_COMPRA >= date_sub(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$query = $query." AND CMP.FEC_COMPRA <= STR_TO_DATE('$fechaHasta', '%d/%m/%Y')";
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
						<th>Fec. Compra</th>
						<th>Proveedor</th>
						<th>Producto</th>
						<th>Cantidad</th>
						<th>Cos. Unitario</th>
						<th>Cos. Total</th>
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
						<td><?php echo $row['FEC_COMPRA']; ?></td>
						<td><?php echo utf8_encode($row['PROVEEDOR']); ?></td>
						<td><?php echo utf8_encode($row['PRODUCTO']); ?></td>
						<td><?php echo $row['CANTIDAD']; ?></td>
						<td align="right"><?php echo $row['COS_NETO']; ?></td>
						<td align="right"><?php echo $row['COS_TOTAL']; ?></td>
					</tr>
<?php 
			$number++;
		}
	}
	else 
	{
?>
					<tr><td colspan="7">No existen registros para su búsqueda.</td></tr>
<?php 
	}
?>
				</tbody>
			</table>