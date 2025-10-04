<?php
	include('../util/database.php');
	session_start();
	
	$codVenta     = $_POST['codVenta'];
	$numDocumento = $_POST['numDocumento'];
	$fechaDesde   = $_POST['fechaDesde'];
	$fechaHasta   = $_POST['fechaHasta'];

	$selectVentas = 
			"SELECT com.*, cli.num_documento, 
				CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social 
				FROM comprobantes com 
				JOIN clientes cli ON cli.id = com.id_cliente";
	
	if (!is_null($codVenta) && !empty($codVenta)) 
	{
		$selectVentas = $selectVentas." AND com.id = '$codVenta'";
	}
	
	if (!is_null($numDocumento) && !empty($numDocumento)) 
	{
		$selectVentas = $selectVentas." AND cli.num_documento = '$numDocumento'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$selectVentas = $selectVentas." AND com.fec_emision >= str_to_date('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$selectVentas = $selectVentas." AND com.fec_emision >= date_sub(NOW(), INTERVAL 7 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$selectVentas = $selectVentas." AND com.fec_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
	}
	
	$selectVentas = $selectVentas." ORDER BY com.id DESC";
	
	if (!$resultSelectVentas = mysqli_query($con, $selectVentas)) 
	{
		exit(mysqli_error($con));
	}
?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th width="3%">N°</th>
				<th width="57%">Nombre/Razón Social</th>
				<th width="10%">Monto Neto</th>
				<th width="10%">Monto IGV</th>
				<th width="10%">Monto Total</th>
				<th width="10%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php
	if(mysqli_num_rows($resultSelectVentas) > 0) 
	{
		$number = 1;
		while ($rowSelectVentas = mysqli_fetch_assoc($resultSelectVentas)) 
		{
?>
			<tr>
				<td><?php echo $number; ?></td>
				<td><?php echo $rowSelectVentas['num_documento'].' - '.$rowSelectVentas['nombre_razon_social']; ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_neto'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_igv'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_total'], 2, '.', ''); ?></td>
				<td style="text-align:center;"> 
					<form method="post" action="actualizar-comprobante.php">
<?php
			if($rowSelectVentas['pagado'] == 0) 
			{
?>
						<i class="fa fa-dollar" style="color:red;" data-toggle="tooltip" title="no pagado"></i>
<?php
			} 
			else 
			{
?>
						<i class="fa fa-dollar" style="color:green;" data-toggle="tooltip" title="pagado"></i>
<?php 			
			}
			if($rowSelectVentas['emitido'] == 0) 
			{
?>
						<i class="fa fa-file" style="color:red;" data-toggle="tooltip" title="no emitido"></i>
<?php			
			} 
			else 
			{
?>
						<i class="fa fa-file" style="color:green;" data-toggle="tooltip" title="emitido"></i>
<?php
			}
			if ($rowSelectVentas['entregado'] == 0) 
			{
?>
						<i class="fa fa-user" style="color:red;" data-toggle="tooltip" title="no entregado"></i>
<?php			
			} 
			else 
			{
?>
						<i class="fa fa-user" style="color:green;" data-toggle="tooltip" title="entregado"></i>
<?php 						
			}
?>			
						<!--input class="btn btn-default btn-circle" style="background-image:url(../img/pencil-icon.png);background-size:32px 32px;width:32px;height:32px;" type="submit" value=""-->
						<button type="submit" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar">
							<i class="fa fa-pencil"></i>
						</button>
						<input type="hidden" name="idComprobante" value="<?php echo $rowSelectVentas['id']; ?>">
					</form>
				</td>
			</tr>
<?php 			
			$number++;
		}
	} 
	else 
	{
?>
			<tr><td colspan="6">No se encontraron comprobantes.</td></tr>
<?php	
	}
?>
		</tbody>
	</table>