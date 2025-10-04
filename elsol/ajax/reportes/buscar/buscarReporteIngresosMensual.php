<?php
	include('../../../util/database.php');
	
	$mesPeriodo  = $_POST['mesPeriodo'];
	$anioPeriodo = $_POST['anioPeriodo'];

	$query = 
			"SELECT PAR.DESCRIPCION AS INGRESO, FORMAT(ROUND(SUM(COM.MON_TOTAL), 2), 2) AS MONTO 
			FROM comprobantes COM 
			JOIN parametros PAR ON PAR.CODIGO = COM.TIP_COMPROBANTE AND PAR.PADRE = 8 
			WHERE LAST_DAY(COM.FEC_EMISION) = LAST_DAY(STR_TO_DATE('01/$mesPeriodo/$anioPeriodo', '%d/%m/%Y'))
			AND COM.ANULADO = 0 AND COM.EMITIDO = 1
			GROUP BY COM.TIP_COMPROBANTE
			UNION
			SELECT PAR.DESCRIPCION AS INGRESO, FORMAT(ROUND(SUM(OIN.MONTO), 2), 2) AS MONTO 
			FROM otros_ingresos OIN 
			JOIN parametros PAR ON PAR.CODIGO = OIN.TIP_INGRESO AND PAR.PADRE = 18
			WHERE LAST_DAY(OIN.FECHA) = LAST_DAY(STR_TO_DATE('01/$mesPeriodo/$anioPeriodo', '%d/%m/%Y'))
			GROUP BY OIN.TIP_INGRESO";
	
	if (!$result = mysqli_query($con, $query)) 
	{
		exit(mysqli_error($con));
	}
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>N°</th>
						<th>Ingreso</th>
						<th>Monto</th>
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
						<td><?php echo $row['INGRESO']; ?></td>
						<td align="right"><?php echo $row['MONTO']; ?></td>
					</tr>
<?php
			$number++;
		}
	}
	else 
	{
?>
					<tr><td colspan="3">No existen registros para su búsqueda.</td></tr>
<?php 
	}
?>
				</tbody>
			</table>