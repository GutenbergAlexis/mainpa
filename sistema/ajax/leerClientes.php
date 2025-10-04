<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();

	$query = 
			"SELECT cli.id, cli.num_documento, cli.direccion, 
				CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social, 
				CONCAT_WS(' / ', cli.telefono, cli.celular) AS telefono 
				FROM clientes cli 
				ORDER BY cli.id ASC";
	
	if (!$result = mysqli_query($con, $query)) 
	{
		exit(mysqli_error($con));
	}
?>
	
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="tabla-cabecera" width="3%">N°</th>
				<th class="tabla-cabecera" width="10%">Número de documento</th>
				<th class="tabla-cabecera" width="31%">Nombre/Razón social</th>
				<th class="tabla-cabecera" width="30%">Dirección</th>
				<th class="tabla-cabecera" width="17%">Teléfono/Celular</th>
				<th class="tabla-cabecera" width="9%">&nbsp;</th>
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
				<td class="centrar tabla-detalle"><?php echo $number; ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['num_documento']; ?></td>
				<td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($row['nombre_razon_social'], 'UTF-8', 'ISO-8859-1'); ?></td>
				<td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($row['direccion'], 'UTF-8', 'ISO-8859-1'); ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['telefono']; ?></td>
				<td class="centrar tabla-detalle">
					<label onclick="cargarCliente('<?php echo $row['id']; ?>')" class="btn btn-default btn-circle" 
							data-toggle="tooltip" title="editar">
						<i class="fa fa-pencil"></i>
					</label>
					<label onclick="eliminarCliente('<?php echo $row['id']; ?>')" class="btn btn-danger btn-circle" 
							data-toggle="tooltip" title="eliminar">
						<i class="fa fa-times"></i>
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
			<tr><td colspan="5">No existen clientes.</td></tr>
<?php 
	}
?>
		</tbody>
	</table>