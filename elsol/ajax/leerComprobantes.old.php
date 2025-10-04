<?php
	include('../util/database.php');
	session_start();
	
	$codVenta       = $_POST['codVenta'];
	$numDocumento   = $_POST['numDocumento'];
	$fechaDesde     = $_POST['fechaDesde'];
	$fechaHasta     = $_POST['fechaHasta'];
	$numComprobante = $_POST['numComprobante'];

	$selectVentas = 
		"SELECT com.*, cli.num_documento, 
			IF(com.num_comprobante > 0, CONCAT(com.ser_comprobante, '-', LPAD(com.num_comprobante, 6, '0')), '') AS num_comprobante, 
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
		$selectVentas = $selectVentas." AND com.fec_emision >= date_sub(NOW(), INTERVAL 1 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$selectVentas = $selectVentas." AND com.fec_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
	}
	
	if (!is_null($numComprobante) && !empty($numComprobante)) 
	{
		$expNumComprobante = explode("-", $numComprobante.'-');
		$serieComprobante  = $expNumComprobante[0];
		$numeroComprobante = $expNumComprobante[1];
		
		$selectVentas = $selectVentas." AND com.ser_comprobante = '$serieComprobante' AND com.num_comprobante = '$numeroComprobante'";
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
				<th width="8%">Comprobante</th>
				<th width="8%">Fec. Emis.</th>
				<th width="32%">Nombre/Razón Social</th>
				<th width="8%">Vendedor</th>
				<th width="8%">Monto Neto</th>
				<th width="8%">Monto IGV</th>
				<th width="8%">Monto Total</th>
				<th width="17%">&nbsp;</th>
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
				<td><?php echo $rowSelectVentas['num_comprobante']; ?></td>
				<td><?php echo date("d.m.Y", strtotime($rowSelectVentas['fec_emision'])); ?></td>
				<td><?php echo utf8_encode($rowSelectVentas['num_documento'].' - '.$rowSelectVentas['nombre_razon_social']); ?></td>
				<td><?php echo $rowSelectVentas['usu_creacion']; ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_neto'] , 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_igv']  , 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_total'], 2, '.', ''); ?></td>
				<td style="text-align:center;">
					<form method="post" action="../../ajax/comprobantes/verTicketPDF.php" target="_blank" >
<?php 
			if ($rowSelectVentas['anulado'] == 0)
			{
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
			}
			else 
			{
?>
					<i class="fa fa-user" data-toggle="tooltip" title="anulado"></i>
<?php 
			}
?>
					<button type="button" class="btn btn-default btn-circle" onclick="cargarComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="ver">
						<i class="fa fa-eye"></i>
					</button>
<?php 
			if ($rowSelectVentas['emitido'] == 0) 
			{
?>
					<button type="button" class="btn btn-default btn-circle" style="color:red;" data-toggle="tooltip" title="no emitido" disabled>
						<i class="fa fa-file"></i>
					</button>
<?php 
			}
			else 
			{
				if ($rowSelectVentas['tip_comprobante'] == 5)
				{
?>
					<button type="submit" class="btn btn-default btn-circle" style="color:green;" data-toggle="tooltip" title="ver PDF">
						<i class="fa fa-file"></i>
					</button>
					<input type="hidden" id="id-comprobante" name="id-comprobante" value="<?php echo $rowSelectVentas['id']; ?>">
<?php 
				}
				else 
				{
?>
					<button type="button" class="btn btn-default btn-circle" style="color:green;" onclick="window.open('<?php echo $rowSelectVentas['url']; ?>');" data-toggle="tooltip" title="ver PDF">
						<i class="fa fa-file"></i>
					</button>
<?php 
				}
			}
			if ($rowSelectVentas['anulado'] == 0)
			{
				if ($rowSelectVentas['pagado'] == 0) 
				{
?>
					<button type="button" class="btn btn-default btn-circle" style="color:red;" onclick="pagarComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="pagar">
						<i class="fa fa-dollar"></i>
					</button>
<?php 
				}
				else 
				{
?>
					<button type="button" class="btn btn-default btn-circle" style="color:green;" data-toggle="tooltip" title="pagado" disabled>
						<i class="fa fa-dollar"></i>
					</button>
<?php 
				}
			}
			else 
			{
?>
					<button type="button" class="btn btn-default btn-circle" data-toggle="tooltip" title="anulado" disabled>
						<i class="fa fa-dollar"></i>
					</button>
<?php 
			}
			if ($rowSelectVentas['emitido'] == 1) 
			{
				if ($rowSelectVentas['anulado'] == 0) 
				{
?>
					<button type="button" class="btn btn-default btn-circle" style="color:red;" onclick="anularComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="anular">
						<i class="fa fa-arrow-down"></i>
					</button>
<?php 
				}
				else 
				{
?>
					<button type="button" class="btn btn-default btn-circle" data-toggle="tooltip" title="anulado" disabled>
						<i class="fa fa-arrow-down"></i>
					</button>
<?php 
				}
			}
			else 
			{
?>
					<button type="button" class="btn btn-default btn-circle" disabled>
						<i class="fa fa-arrow-down"></i>
					</button>
<?php
			}
?>
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
			<tr><td colspan="6">No existen comprobantes guardados.</td></tr>
<?php 
	}
?>
		</tbody>
	</table>