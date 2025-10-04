<?php
	include('../../../util/database.php');
	
	$proveedor  = $_POST['proveedor'];
	$fechaDesde = $_POST['fechaDesde'];
	$fechaHasta = $_POST['fechaHasta'];

	$query = 
			"SELECT CMP.ID, CMP.FEC_COMPRA, PRV.RAZON_SOCIAL AS PROVEEDOR, FORMAT(ROUND(CMP.MON_IGV, 2), 2) AS MON_IGV, FORMAT(ROUND(CMP.MON_NETO, 2), 2) AS MON_NETO, FORMAT(ROUND(CMP.MON_TOTAL, 2), 2) AS MON_TOTAL, PAR2.DESCRIPCION AS MED_PAGO, CMP.OBSERVACIONES AS OBSERVACIONES, CMP.USU_CREACION AS USUARIO
				FROM compras CMP 
				JOIN proveedores PRV ON PRV.ID = CMP.ID_PROVEEDOR 
				JOIN parametros PAR2 ON PAR2.CODIGO = CMP.PAR_MEDIO_PAGO AND PAR2.PADRE = 4 
				WHERE 1 = 1";
	
	if (!is_null($proveedor) && !empty($proveedor)) {
		$query = $query." AND PRV.ID = '$proveedor'";
	}
	
	if (!is_null($fechaDesde) && !empty($fechaDesde)) {
		$query = $query." AND CMP.FEC_COMPRA >= STR_TO_DATE('$fechaDesde', '%d/%m/%Y')";
	} else {
		$query = $query." AND CMP.FEC_COMPRA >= date_sub(NOW(), INTERVAL 30 DAY)";
	}
	
	if (!is_null($fechaHasta) && !empty($fechaHasta)) {
		$query = $query." AND CMP.FEC_COMPRA <= STR_TO_DATE('$fechaHasta', '%d/%m/%Y')";
	}
	
	$query = $query." ORDER BY 1 ASC, 2 ASC";
	
	if (!$result = mysqli_query($con, $query)) {
		exit(mysqli_error($con));
	}
	
	$data = 
			'<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>N°</th>
						<th>Fec. Compra</th>
						<th>Proveedor</th>
						<th>Sub Total</th>
						<th>IGV</th>
						<th>Total</th>
						<th>Mod. Pago</th>
						<th>Observaciones</th>
						<th>Usuario</th>
					</tr>
				</thead>
				<tbody>';
	
	if(mysqli_num_rows($result) > 0) {
		$number = 1;
		while ($row = mysqli_fetch_assoc($result)) {
			$data .= 
					'<tr>
						<td>' .$number. '</td>
						<td>' .$row['FEC_COMPRA']. '</td>
						<td>' .utf8_encode($row['PROVEEDOR']). '</td>
						<td align="right">' .$row['MON_NETO']. '</td>
						<td align="right">' .$row['MON_IGV']. '</td>
						<td align="right">' .$row['MON_TOTAL']. '</td>
						<td>' .$row['MED_PAGO']. '</td>
						<td>' .utf8_encode($row['OBSERVACIONES']). '</td>
						<td>' .$row['USUARIO']. '</td>
					</tr>
				<tbody>';
			$number++;
		}
	}
	else 
	{
		$data .= 
					'<tr><td colspan="7">No existen registros para su búsqueda.</td></tr>
				</tbody>';
	}
	$data .= 
			'</table>';

    echo $data;
?>