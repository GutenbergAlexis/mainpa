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
		$selectVentas = $selectVentas." AND date(com.fec_emision) >= str_to_date('$fechaDesde', '%d/%m/%Y %H:%i:%s')";
	} 
	else 
	{
		$selectVentas = $selectVentas." AND date(com.fec_emision) >= date_sub(NOW(), INTERVAL 1 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$selectVentas = $selectVentas." AND date(com.fec_emision) < date_add(str_to_date('$fechaHasta', '%d/%m/%Y'), INTERVAL 1 DAY)";
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
				<th width="39%">Nombre/Razón Social</th>
				<th width="8%">Vendedor</th>
				<th width="8%">Monto Neto</th>
				<th width="8%">Monto IGV</th>
				<th width="8%">Monto Total</th>
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
				<td><?php echo $rowSelectVentas['num_comprobante']; ?></td>
				<td><?php echo date("d.m.Y", strtotime($rowSelectVentas['fec_emision'])); ?></td>
				<td><?php echo utf8_encode($rowSelectVentas['num_documento'].' - '.$rowSelectVentas['nombre_razon_social']); ?></td>
				<td><?php echo $rowSelectVentas['usu_creacion']; ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_neto'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_igv'], 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectVentas['mon_total'], 2, '.', ''); ?></td>
				<td style="text-align:center;"> 
					<form method="post" action="actualizar-comprobante.php">
<?php
			if($rowSelectVentas['pagado'] == 0) 
			{
				$colorPagado = "color:red;";
				$titlePagado = "no pagado";
			}
			else
			{
				$colorPagado = "color:green;";
				$titlePagado = "pagado";
			}
			
			if($rowSelectVentas['emitido'] == 0) 
			{
				$colorEmitido = "color:red;";
				$titleEmitido = "no emitido";
			}
			else
			{
				$colorEmitido = "color:green;";
				$titleEmitido = "emitido";
			}
			
			if($rowSelectVentas['entregado'] == 0) 
			{
				$colorEntregado = "color:red;";
				$titleEntregado = "no entregado";
			}
			else
			{
				$colorEntregado = "color:green;";
				$titleEntregado = "entregado";
			}
			
			if($rowSelectVentas['anulado'] == 1) 
			{
				$colorPagado    = "";
				$colorEmitido   = "";
				$colorEntregado = "";
				$titlePagado    = "anulado";
				$titleEmitido   = "anulado";
				$titleEntregado = "anulado";
				$disabled       = "disabled";
			}
			else 
			{
				$disabled       = "";
			}
?>
						<i class="fa fa-dollar" style="<?php echo $colorPagado; ?>" data-toggle="tooltip" title="<?php echo $titlePagado; ?>"></i>
						<i class="fa fa-file" style="<?php echo $colorEmitido; ?>" data-toggle="tooltip" title="<?php echo $titleEmitido; ?>"></i>
						<i class="fa fa-user" style="<?php echo $colorEntregado; ?>" data-toggle="tooltip" title="<?php echo $titleEntregado; ?>"></i>
						<button type="submit" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar" <?php echo $disabled; ?> >
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
			<tr><td colspan="9">No se encontraron comprobantes.</td></tr>
<?php	
	}
?>
		</tbody>
	</table>