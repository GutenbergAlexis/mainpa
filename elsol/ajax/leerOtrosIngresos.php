<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();

	$query = 
			"SELECT oi.*, par.descripcion AS des_tipo_ingreso 
				FROM otros_ingresos oi 
				JOIN parametros par ON par.codigo = oi.tip_ingreso AND par.padre = 18 
				WHERE MONTH(oi.fecha) = MONTH(NOW()) AND YEAR(oi.fecha) = YEAR(NOW()) 
				ORDER BY oi.fecha DESC";
	
	if (!$result = mysqli_query($con, $query)) 
	{
		exit(mysqli_error($con));
	}
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>N°</th>
						<th>Tipo de ingreso</th>
						<th>Descripción</th>
						<th>Monto</th>
						<th>Fecha</th>
						<th>Observaciones</th>
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
						<td><?php echo $row['des_tipo_ingreso']; ?></td>
						<td><?php echo utf8_encode($row['descripcion']); ?></td>
						<td style="text-align:right;"><?php echo number_format($row['monto'], 2, '.', ''); ?></td>
						<td><?php echo $row['fecha']; ?></td>
						<td><?php echo utf8_encode($row['observaciones']); ?></td>
					</tr>
<?php
			$number++;
		}
	}
	else 
	{
?>					
					<tr><td colspan="7">No existen otros ingresos.</td></tr>
<?php
	}
?>
				<tbody>
			</table>