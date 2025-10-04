<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	session_start();
	
	$tipoIngreso = $_POST['tipoIngreso'];
	$fechaDesde  = $_POST['fechaDesde'];
	$fechaHasta  = $_POST['fechaHasta'];

	$query =
			"SELECT oi.*, par.descripcion AS des_tipo_ingreso 
				FROM otros_ingresos oi 
				JOIN parametros par ON par.codigo = oi.tip_ingreso 
				WHERE par.padre = 18";
	
	if (!is_null($tipoIngreso) && !empty($tipoIngreso)) {
		$query = $query." AND oi.tip_ingreso = '$tipoIngreso'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) {
		$query = $query." AND oi.fecha >= str_to_date('$fechaDesde', '%d/%m/%Y')";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) {
		$query = $query." AND oi.fecha <= str_to_date('$fechaHasta', '%d/%m/%Y')";
	}
	
	$query = $query." ORDER BY oi.fecha DESC";
	
	if (!$result = mysqli_query($con, $query)) {
		exit(mysqli_error($con));
	}
	
	$data = 
			'<table class="table table-striped table-bordered">
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
				<tbody>';
	
	if(mysqli_num_rows($result) > 0) {
		$number = 1;
		while ($row = mysqli_fetch_assoc($result)) {
			$data .= 
					'<tr>
						<td>' .$number. '</td>
						<td>' .$row['des_tipo_ingreso'].'</td>
						<td>' .$row['descripcion'].'</td>
						<td style="text-align:right;">' .number_format($row['monto'], 2, '.', ''). '</td>
						<td>' .$row['fecha'].'</td>
						<td>' .$row['observaciones'].'</td>
					</tr>
				<tbody>';
			$number++;
		}
	}
	else 
	{
		$data .= 
					'<tr><td colspan="7">No existen otros ingresos para su búsqueda.</td></tr>
				</tbody>';
	}
	$data .= 
			'</table>';

    echo $data;
?>