<?php
	include('../util/database.php');
	session_start();
	
	$idProveedor = $_POST['idProveedor'];
	$fechaDesde  = $_POST['fechaDesde'];
	$fechaHasta  = $_POST['fechaHasta'];

	$query = 
			"SELECT cmp.*, pro.ruc, pro.razon_social 
				FROM compras cmp 
				JOIN proveedores pro ON pro.id = cmp.id_proveedor";
	
	if (!is_null($idProveedor) && !empty($idProveedor)) 
	{
		$query = $query." AND pro.id = '$idProveedor'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$query = $query." AND cmp.fec_compra >= str_to_date('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$query = $query." AND cmp.fec_compra >= date_sub(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$query = $query." AND cmp.fec_compra <= str_to_date('$fechaHasta', '%d/%m/%Y')";
	}
	
	$query = $query." ORDER BY cmp.id DESC";
	
	if (!$result = mysqli_query($con, $query)) 
	{
		exit(mysqli_error($con));
	}
?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th width="3%">N°</th>
				<th width="51%">Proveedor</th>
				<th width="10%">Fecha</th>
				<th width="10%">Monto Neto</th>
				<th width="10%">Monto IGV</th>
				<th width="10%">Monto Total</th>
				<th width="6%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php 
	if(mysqli_num_rows($result) > 0) {
		$number = 1;
		while ($row = mysqli_fetch_assoc($result)) 
		{
?>
			<tr>
				<td><?php echo $number; ?></td>
				<td><?php echo $row['ruc'].' - '.$row['razon_social']; ?></td>
				<td><?php echo $row['fec_compra']; ?></td>
				<td style="text-align:right;"><?php echo number_format($row['mon_neto'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($row['mon_igv'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($row['mon_total'], 2, '.', ''); ?></td>
				<td style="text-align:center;">
					<label onclick="cargarCompra('<?php echo $row['id']; ?>')" class="btn btn-default btn-circle" data-toggle="tooltip" title="ver">
						<i class="fa fa-eye"></i>
					</label>
				</td>
			</tr>
<?php 
			$number++;
		}
	}
	else 
	{
?>
			<tr><td colspan="7">No existen compras guardadas.</td></tr>
<?php
	}
?>
		</tbody>
	</table>