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
				<th width="3%"></th>
				<th width="10%">N° doc.</th>
				<th width="31%">Nombre/Razón social</th>
				<th width="30%">Dirección</th>
				<th width="17%">Teléfono/Celular</th>
				<th width="9%">&nbsp;</th>
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
				<td><?php echo $row['num_documento']; ?></td>
				<td><?php echo utf8_encode($row['nombre_razon_social']); ?></td>
				<td><?php echo utf8_encode($row['direccion']); ?></td>
				<td><?php echo $row['telefono']; ?></td>
				<td>
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