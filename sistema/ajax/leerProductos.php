<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();

	$selectProductos = 
			"SELECT pro.id, pro.codigo, pro.descripcion, par.abreviatura AS unidad_medida, pro.precio, pro.stock 
				FROM productos pro 
				JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29 
				ORDER BY pro.id ASC";
	
	if (!$resultSelectProductos = mysqli_query($con, $selectProductos)) 
	{
		exit(mysqli_error($con));
	}
?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th width="3%"></th>
				<th width="12%">Código</th>
				<th width="40%">Descripción</th>
				<th width="12%">Uni. Medida</th>
				<th width="12%">Stock</th>
				<th width="12%">Precio unitario</th>
				<th width="9%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
<?php 
	if(mysqli_num_rows($resultSelectProductos) > 0) {
		$number = 1;
		while ($rowSelectProductos = mysqli_fetch_assoc($resultSelectProductos)) 
		{
			$onclickCargar   = 'onclick="cargarProducto('.$rowSelectProductos['id'].')"'; 
			$onclickEliminar = 'onclick="eliminarProducto('.$rowSelectProductos['id'].')"';
			$disabled        = '';
			
			if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 2) 
			{
				$onclickCargar   = '';
				$onclickEliminar = '';
				$disabled        = 'disabled';
			}
?>
			<tr>
				<td><?php echo $number; ?></td>
				<td><?php echo $rowSelectProductos['codigo']; ?></td>
				<td><?php echo utf8_encode($rowSelectProductos['descripcion']); ?></td>
				<td><?php echo utf8_encode($rowSelectProductos['unidad_medida']); ?></td>
				<td style="text-align:right;"><?php echo $rowSelectProductos['stock']; ?></td>
				<td style="text-align:right;"><?php echo number_format($rowSelectProductos['precio'], 2, '.', ''); ?></td>
				<td>
					<label <?php echo $onclickCargar; ?> class="btn btn-default btn-circle" data-toggle="tooltip" title="editar" <?php echo $disabled; ?> >
						<i class="fa fa-pencil"></i>
					</label>
					<label <?php echo $onclickEliminar; ?> class="btn btn-danger btn-circle" data-toggle="tooltip" title="eliminar" <?php echo $disabled; ?> >
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
			<tr><td colspan="7">No existen productos.</td></tr>
<?php 
	}
?>
		</tbody>
	</table>