<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();

	$query = 
			"SELECT usu.id, usu.num_documento, usu.user, usu.direccion, usu.celular, 
				CONCAT_WS(' ', usu.pri_nombre, usu.seg_nombre, usu.pri_apellido, usu.seg_apellido) AS nombre_completo, 
				par.descripcion AS perfil 
				FROM usuarios usu 
				JOIN parametros par ON par.codigo = usu.perfil AND par.padre = 15 
				ORDER BY usu.id ASC";
	
	if (!$result = mysqli_query($con, $query)) {
		exit(mysqli_error($con));
	}
?>
	<table id="table-usuarios" class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="tabla-cabecera" width="3%">N°</th>
				<th class="tabla-cabecera" width="10%">Documento</th>
				<th class="tabla-cabecera" width="10%">Usuario</th>
				<th class="tabla-cabecera" width="10%">Perfil</th>
				<th class="tabla-cabecera" width="24%">Nombre completo</th>
				<th class="tabla-cabecera" width="24%">Dirección</th>
				<th class="tabla-cabecera" width="10%">Celular</th>
				<th class="tabla-cabecera" width="9%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php 
	if(mysqli_num_rows($result) > 0) {
		$number = 1;
		while ($row = mysqli_fetch_assoc($result)) {
?>
			<tr>
				<td class="centrar tabla-detalle"><?php echo $number; ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['num_documento']; ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['user']; ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['perfil']; ?></td>
				<td class="tabla-detalle"><?php echo utf8_encode($row['nombre_completo']); ?></td>
				<td class="tabla-detalle"><?php echo utf8_encode($row['direccion']); ?></td>
				<td class="centrar tabla-detalle"><?php echo $row['celular']; ?></td>
				<td class="centrar tabla-detalle">
					<label onclick="cargarUsuario('<?php echo $row['id']; ?>')" class="btn btn-default btn-circle" 
							data-toggle="tooltip" title="editar">
						<i class="fa fa-pencil"></i>
					</label>
					<label onclick="eliminarUsuario('<?php echo $row['id']; ?>')" class="btn btn-danger btn-circle" 
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
		<tr><td colspan="7">No existen usuarios.</td></tr>
<?php 
	}
?>
		<tbody>
	</table>