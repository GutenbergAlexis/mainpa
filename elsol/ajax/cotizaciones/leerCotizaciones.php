<?php
	include('../../util/database.php');
	session_start();
	
	$codCotizacion = $_POST['codCotizacion'];
	$numDocumento  = $_POST['numDocumento'];
	$fechaDesde    = $_POST['fechaDesde'];
	$fechaHasta    = $_POST['fechaHasta'];
	
	$selectCotizaciones = 
		"SELECT cot.*, cli.num_documento, 
			CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social 
			FROM cotizaciones cot 
			JOIN clientes cli ON cli.id = cot.id_cliente";
	
	if (!is_null($codCotizacion) && !empty($codCotizacion)) 
	{
		$selectCotizaciones = $selectCotizaciones." AND cot.id = '$codCotizacion'";
	}
	
	if (!is_null($numDocumento) && !empty($numDocumento)) 
	{
		$selectCotizaciones = $selectCotizaciones." AND cli.num_documento = '$numDocumento'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) 
	{
		$selectCotizaciones = $selectCotizaciones." AND cot.fec_emision >= str_to_date('$fechaDesde', '%d/%m/%Y')";
	} 
	else 
	{
		$selectCotizaciones = $selectCotizaciones." AND cot.fec_emision >= date_sub(NOW(), INTERVAL 3 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) 
	{
		$selectCotizaciones = $selectCotizaciones." AND cot.fec_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
	}
	
	$selectCotizaciones = $selectCotizaciones." ORDER BY cot.id DESC";
	
	if (!$resultSelectCotizaciones = mysqli_query($con, $selectCotizaciones)) 
	{
		exit(mysqli_error($con));
	}
?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th width="3%">N°</th>
				<th width="59%">Nombre/Razón Social</th>
				<th width="10%">Monto Neto</th>
				<th width="10%">Monto IGV</th>
				<th width="10%">Monto Total</th>
				<th width="8%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php 
	if(mysqli_num_rows($resultSelectCotizaciones) > 0) 
	{
		$number = 1;
		while ($rowSelectCotizaciones = mysqli_fetch_assoc($resultSelectCotizaciones)) 
		{
?>
			<tr>
				<td><?php echo $number; ?></td>
				<td><?php echo utf8_encode($rowSelectCotizaciones['num_documento'].' - '.$rowSelectCotizaciones['nombre_razon_social']); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectCotizaciones['mon_neto'] , 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectCotizaciones['mon_igv']  , 2, '.', ''); ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectCotizaciones['mon_total'], 2, '.', ''); ?></td>
				<td style="text-align:center;">
					<form method="post" action="../ingresos/cargar-cotizacion.php" style="float:left;" >
						<button type="submit" class="btn btn-default btn-circle" data-toggle="tooltip" title="ver">
							<i class="fa fa-eye"></i>
						</button>
						<input type="hidden" id="id-cargar-cotizacion" name="id-cargar-cotizacion" value="<?php echo $rowSelectCotizaciones['id']; ?>">
					</form>
					<form method="post" action="../../ajax/cotizaciones/verCotizacion.php" target="_blank" style="float:left;" >
						<button type="submit" class="btn btn-default btn-circle" style="color:green;" data-toggle="tooltip" title="ver PDF">
							<i class="fa fa-file"></i>
						</button>
						<input type="hidden" id="id-ver-cotizacion" name="id-ver-cotizacion" value="<?php echo $rowSelectCotizaciones['id']; ?>">
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
			<tr><td colspan="6">No se encontraron cotizaciones.</td></tr>
<?php 
	}
?>
		</tbody>
	</table>