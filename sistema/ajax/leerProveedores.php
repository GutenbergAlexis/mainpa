<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();
	
	$query = 
			"SELECT prv.id, prv.ruc, prv.razon_social, prv.direccion, prv.contacto, prv.telefono, prv.correo
				FROM proveedores prv 
				ORDER BY prv.id ASC";
	
	if (!$result = mysqli_query($con, $query)) 
	{
		exit(mysqli_error($con));
	}
?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="tabla-cabecera" width="3%">N°</th>
				<th class="tabla-cabecera" width="10%">Documento</th>
				<th class="tabla-cabecera" width="18%">Razón social</th>
				<th class="tabla-cabecera" width="18%">Dirección</th>
				<th class="tabla-cabecera" width="15%">Contacto</th>
				<th class="tabla-cabecera" width="10%">Teléfono</th>
				<th class="tabla-cabecera" width="17%">Correo</th>
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
				<td class="centrar tabla-detalle"><?php echo $row['ruc']; ?></td>
				<td class="tabla-detalle"><?php echo utf8_encode($row['razon_social']); ?></td>
				<td class="tabla-detalle"><?php echo utf8_encode($row['direccion']); ?></td>
				<td class="tabla-detalle"><?php echo utf8_encode($row['contacto']); ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['telefono']; ?></td>
				<td class="tabla-detalle"><?php echo $row['correo']; ?></td>
				<td class="centrar tabla-detalle">
					<label onclick="cargarProveedor('<?php echo $row['id']; ?>')" class="btn btn-default btn-circle" 
							data-toggle="tooltip" title="editar">
						<i class="fa fa-pencil"></i>
					</label>
					<label onclick="eliminarProveedor('<?php echo $row['id']; ?>')" class="btn btn-danger btn-circle" 
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
			<tr><td colspan="8">No existen proveedores.</td></tr>
<?php 
	}
?>
		</tbody>
	</table>