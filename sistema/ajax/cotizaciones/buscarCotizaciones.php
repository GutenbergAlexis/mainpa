<?php
date_default_timezone_set('America/Lima');
session_start();
require_once __DIR__ . '/../../../../configSiVentas/db.php'; 
	
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../../index.php"); 
    exit;
}
	
$codVenta     = $_POST['codVenta'];
$numDocumento = $_POST['numDocumento'];
$fechaDesde   = $_POST['fechaDesde'];
$fechaHasta   = $_POST['fechaHasta'];

$selectVentas = "
	SELECT com.*, cli.numero_documento, 
		CONCAT_WS(' ', 
			NULLIF(TRIM(cli.nombre_razon_social), ''), 
			NULLIF(TRIM(cli.segundo_nombre), ''), 
			NULLIF(TRIM(cli.apellido_paterno), ''), 
			NULLIF(TRIM(cli.apellido_materno), '')
		) AS nombre_razon_social 
	FROM comprobantes com 
	JOIN clientes cli ON cli.id = com.id_cliente
";

if (!is_null($codVenta) && !empty($codVenta)) {
	$selectVentas = $selectVentas." AND com.id = '$codVenta'";
}

if (!is_null($numDocumento) && !empty($numDocumento)) {
	$selectVentas = $selectVentas." AND cli.numero_documento = '$numDocumento'";
}

if (!is_null($fechaDesde) && !empty($fechaDesde)) {
	$selectVentas = $selectVentas." AND com.fecha_emision >= str_to_date('$fechaDesde', '%d/%m/%Y')";
} else {
	$selectVentas = $selectVentas." AND com.fecha_emision >= date_sub(NOW(), INTERVAL 7 DAY)";
}

if (!is_null($fechaHasta) && !empty($fechaHasta)) {
	$selectVentas = $selectVentas." AND com.fecha_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
}

$selectVentas = $selectVentas." ORDER BY com.id DESC";

if (!$resultVentas = mysqli_query($con, $selectVentas)) {
	exit(mysqli_error($con));
}
?>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th class="tabla-cabecera" width="3%">N°</th>
			<th class="tabla-cabecera" width="57%">Nombre/Razón Social</th>
			<th class="tabla-cabecera" width="10%">Monto 1Neto</th>
			<th class="tabla-cabecera" width="10%">Monto IGV</th>
			<th class="tabla-cabecera" width="10%">Monto Total</th>
			<th class="tabla-cabecera" width="10%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
<?php
if(mysqli_num_rows($resultVentas) > 0) {
	$number = 1;
	while ($rowVentas = mysqli_fetch_assoc($resultVentas)) {
?>
		<tr>
			<td class="centrar tabla-detalle"><?php echo $number; ?></td>
			<td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($rowVentas['num_documento'].' - '.$rowVentas['nombre_razon_social'], 'UTF-8', 'ISO-8859-1'); ?></td>
			<td class="derecha tabla-detalle"><?php echo number_format($rowVentas['monto_neto'], 2, '.', ''); ?></td>
			<td class="derecha tabla-detalle"><?php echo number_format($rowVentas['monto_igv'], 2, '.', ''); ?></td>
			<td class="derecha tabla-detalle"><?php echo number_format($rowVentas['monto_total'], 2, '.', ''); ?></td>
			<td class="centrar tabla-detalle">
				<form method="post" action="actualizar-comprobante.php">
<?php
		if($rowVentas['pagado'] == 0) {
?>
					<i class="fa fa-dollar" style="color:red;" data-toggle="tooltip" title="no pagado"></i>
<?php
		} else {
?>
					<i class="fa fa-dollar" style="color:green;" data-toggle="tooltip" title="pagado"></i>
<?php 			
		}
		if($rowVentas['emitido'] == 0) {
?>
					<i class="fa fa-file" style="color:red;" data-toggle="tooltip" title="no emitido"></i>
<?php			
		} else {
?>
					<i class="fa fa-file" style="color:green;" data-toggle="tooltip" title="emitido"></i>
<?php
		}
		if ($rowVentas['entregado'] == 0) {
?>
					<i class="fa fa-user" style="color:red;" data-toggle="tooltip" title="no entregado"></i>
<?php			
		} else {
?>
					<i class="fa fa-user" style="color:green;" data-toggle="tooltip" title="entregado"></i>
<?php 						
		}
?>			
					<button type="submit" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar">
						<i class="fa fa-pencil"></i>
					</button>
					<input type="hidden" name="idComprobante" value="<?php echo $rowVentas['id']; ?>">
				</form>
			</td>
		</tr>
<?php 			
		$number++;
	}
} else {
?>
		<tr><td colspan="6">No se encontraron comprobantes.</td></tr>
<?php	
}
?>
	</tbody>
</table>