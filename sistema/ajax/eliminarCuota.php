<?php
	include('../util/database.php');
	session_start();
	
	$idCuota = $_POST['idCuota'];
	
	// Primero obtener el id_comprobante antes de eliminar
	$selectCuota = "
	    SELECT id_comprobante FROM cuotas WHERE id = '$idCuota'";
	
	if (!$resultSelectCuota = mysqli_query($con, $selectCuota)) {
		exit(mysqli_error($con));
	}
	
	$idComprobante = 0;
	if(mysqli_num_rows($resultSelectCuota) > 0) {
		$row = mysqli_fetch_assoc($resultSelectCuota);
		$idComprobante = $row['id_comprobante'];
	}
	
	// Ahora eliminar la cuota
	$deleteCuota = "
		DELETE FROM cuotas 
		WHERE id = '$idCuota'";
	
	if (!$resultDeleteCuota = mysqli_query($con, $deleteCuota)) {
		exit(mysqli_error($con));
	}
	
	// Devolver las cuotas actualizadas para refrescar la tabla
	$selectCuotas = "
	    SELECT cuo.id, cuo.id_comprobante, DATE_FORMAT(cuo.fecha, '%Y-%m-%d') AS fecha, cuo.monto 
        FROM cuotas cuo 
        WHERE cuo.id_comprobante = '$idComprobante'
        ORDER BY cuo.fecha ASC";
        
    if (!$resultSelectCuotas = mysqli_query($con, $selectCuotas)) {
		exit(mysqli_error($con));
	}
    
    $number = 1;
    $montoTotalCuotas = 0;
    $html = '';
    
    if(mysqli_num_rows($resultSelectCuotas) > 0) {
        while ($row = mysqli_fetch_assoc($resultSelectCuotas)) {
            $html .= '<tr>
                <td class="text-center">'.$number.'</td>
                <td>'.$row['fecha'].'</td>
                <td style="text-align:right;">'.number_format($row['monto'], 2, '.', '').'</td>
                <td class="text-center">
                    <!-- Botones deshabilitados temporalmente -->
                    <button type="button" class="btn btn-default btn-circle disabled" title="Editar">
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-circle disabled" title="Eliminar">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>';
            
            $montoTotalCuotas += $row['monto'];
            $number++;
        }
        
        $html .= '</tbody>
            <tfoot>
                <tr>
                    <td>&nbsp;</td>
                    <td style="text-align:right;font-weight:bold;">Monto total</td>
                    <td style="text-align:right;font-weight:bold;">'.number_format($montoTotalCuotas, 2, '.', '').'</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>';
    } else {
        $html .= '<tr><td colspan="4">Aún no se han agregado cuotas.</td></tr></tbody>';
    }
    
    $montoTotalCuotas = round($montoTotalCuotas, 2);
    
    $response = array(
        'success' => true,
        'html' => $html,
        'montoTotal' => number_format($montoTotalCuotas, 2, '.', ''),
        'montoTotalRaw' => $montoTotalCuotas
    );
    
    echo json_encode($response);
?>