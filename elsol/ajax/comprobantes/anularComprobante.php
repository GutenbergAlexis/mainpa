<?php 
	include('../../util/database.php');
	include('../../util/numerosALetras.php');
	include('../../x/detComprobante.php');
	session_start();
	
	$idComprobante        = $_POST['idComprobante'];
	$usuario              = $_SESSION['user'];
	$respuesta['mensaje'] = "";

	$selectComprobante = 
		"SELECT com.id, com.tip_comprobante, com.ser_comprobante, com.num_comprobante 
			FROM comprobantes com 
			WHERE com.id = '$idComprobante'";
	
	if (!$resultSelectComprobante = mysqli_query($con, $selectComprobante)) 
	{
		exit(mysqli_error($con));
	}

	$ruta = "https://api.nubefact.com/api/v1/85f4c219-d242-41c7-81b5-d4b99ea44242";
	$token = "1a85dc84f5c94e4889178169606934f258554de60056466eaba06bd44acd3020";

	date_default_timezone_set("America/Lima");
	
	$numResultSelectComprobante = mysqli_num_rows($resultSelectComprobante);

	if($numResultSelectComprobante == 1) 
	{
		while ($rowSelectComprobante = mysqli_fetch_assoc($resultSelectComprobante)) 
		{
			$tipComprobante = $rowSelectComprobante['tip_comprobante'];
			$serComprobante = $rowSelectComprobante['ser_comprobante'];
			$numComprobante = $rowSelectComprobante['num_comprobante'];
			
			$data = array(
				"operacion"           => "generar_anulacion",
				"tipo_de_comprobante" => $tipComprobante,
				"serie"               => $serComprobante,
				"numero"              => $numComprobante,
				"motivo"              => "ERROR DEL SISTEMA",
				"codigo_unico"        => ""
			);
		}
		
		if ($tipComprobante == 1 || $tipComprobante == 2) 
		{
			$data_json = json_encode($data);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ruta);
			curl_setopt(
				$ch, CURLOPT_HTTPHEADER, array(
				'Authorization: Token token="'.$token.'"',
				'Content-Type: application/json',
				)
			);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$respuestaNF  = curl_exec($ch);
			curl_close($ch);

			$leer_respuesta = json_decode($respuestaNF, true);
			
			if (isset($leer_respuesta['errors'])) 
			{
				$respuesta['estado']  = '200';
				$respuesta['mensaje'] = str_ireplace('NubeFacT', 'MainpaSoft', $leer_respuesta['errors']);
			} 
			else 
			{
				$enlace    = $leer_respuesta['enlace'];
				$enlacePDF = $enlace.".pdf";

				$updateBajaComprobante = 
					"UPDATE comprobantes com 
						SET com.anulado = 1, com.url = '$enlacePDF', 
						com.usu_modificacion = '$usuario', com.fec_modificacion = NOW()  
						WHERE com.id = '$idComprobante'";
						
				$updateReponeStock = 
					"UPDATE productos PRO 
						JOIN det_comprobante DCOM ON DCOM.id_producto = PRO.id 
						SET PRO.stock = PRO.stock + DCOM.cantidad_final 
						WHERE DCOM.id_comprobante = '$idComprobante'";
				
				if (!$resultUpdateBajaComprobante = mysqli_query($con, $updateBajaComprobante)) 
				{
					exit(mysqli_error($con));
				}
			
				if (!$resultUpdateReponeStock = mysqli_query($con, $updateReponeStock)) 
				{
					exit(mysqli_error($con));
				}
				
				$respuesta['estado']  = '0';
				$respuesta['mensaje'] = 'Comprobante '.$serComprobante.'-'.str_pad($numComprobante, 6, '0', STR_PAD_LEFT).' anulado correctamente.';
			}
		}
		else if ($tipComprobante == 5) 
		{
			$updateBajaTicket = 
				"UPDATE comprobantes com 
					SET com.anulado = 1, 
					com.usu_modificacion = '$usuario', com.fec_modificacion = NOW() 
					WHERE com.id = '$idComprobante'";
					
			$updateReponeStock = 
				"UPDATE productos PRO 
					JOIN det_comprobante DCOM ON DCOM.id_producto = PRO.id 
					SET PRO.stock = PRO.stock + DCOM.cantidad_final 
					WHERE DCOM.id_comprobante = '$idComprobante'";
			
			if (!$resultUpdateBajaTicket = mysqli_query($con, $updateBajaTicket)) 
			{
				exit(mysqli_error($con));
			}
			
			if (!$resultUpdateReponeStock = mysqli_query($con, $updateReponeStock)) 
			{
				exit(mysqli_error($con));
			}
			
			$respuesta['estado']  = '0';
			$respuesta['mensaje'] = 'Ticket '.$serComprobante.'-'.str_pad($numComprobante, 6, '0', STR_PAD_LEFT).' anulado correctamente.';
		}
		else 
		{
			$respuesta['estado']  = '200';
			$respuesta['mensaje'] = 'El tipo de comprobante no puede ser anulado.';
		}
	}
	else if ($numResultSelectComprobante == 0)
	{
		$respuesta['estado']  = '200';
		$respuesta['mensaje'] = 'No se pudo anular el comprobante.';
	}
	else 
	{
		$respuesta['estado']  = '200';
		$respuesta['mensaje'] = 'Se encontró más de un comprobante a anular.';
	}
	
	echo json_encode($respuesta);
?>