<?php
	date_default_timezone_set('America/Lima');

	include('../util/database.php');
	include('../x/cuotas.php');
	include('../x/detComprobante.php');
	session_start();
	
	$idCliente            = $_POST['idCliente'];
	$tipoComprobante      = $_POST['tipoComprobante'];
	$numeroDocumento      = $_POST['numeroDocumento'];
	$ordenCompra          = $_POST['ordenCompra'];
	$guiaRemision         = $_POST['guiaRemision'];
	$condicionPago        = $_POST['condicionPago'];
	$descMedioPago        = $condicionPago == 1 ? "CONTADO" : "CREDITO";
	//$descMedioPago        = empty($_POST['descMedioPago']) ? "CONTADO" : utf8_decode($_POST['descMedioPago']);
	$observaciones        = mb_convert_encoding($_POST['observaciones'], 'UTF-8', 'ISO-8859-1');
	$montoNeto            = $_POST['montoNeto'];
	$montoIGV             = $_POST['montoIGV'];
	$montoTotal           = $_POST['montoTotal'];
	$montoTotalCuotas     = $condicionPago == 2 ? 0 : $_POST['montoTotalCuotas']; // Solo se usa si condicionPago es 2
	
    /** Detracción - inicio **/
    $aplicaDetraccion     = $_POST['aplicaDetraccion'];
    $bienServicioDet      = $_POST['bienServicioDet'];
    $medioPagoDet         = $_POST['medioPagoDet'];
    $porcentajeDet        = $_POST['porcentajeDet'];
    $montoDet             = $_POST['montoDet'];
    /** Detracción - fin **/
    
	// Procesar cuotas desde JSON si es venta a crédito
	$cuotas = [];
	if ($condicionPago == 2 && isset($_POST['cuotasJSON'])) {
		$cuotasArray = json_decode($_POST['cuotasJSON'], true);
		if ($cuotasArray) {
			$idCuota = 1;
			$montoTotalCuotas = 0;
			foreach ($cuotasArray as $cuotaItem) {
				$oCuota = new cuotas($idCuota, 1, $cuotaItem['fecha'], $cuotaItem['monto']);
				$cuotas[$idCuota] = $oCuota;
				$montoTotalCuotas += $cuotaItem['monto'];
				$idCuota++;
			}
			$montoTotalCuotas = round($montoTotalCuotas, 2);
		}
	} else {
		$cuotas = $_SESSION['cuotas'] ?? [];
	}
	
	// Procesar productos desde JSON
	$detComprobante = [];
	if (isset($_POST['productosJSON'])) {
		$productosArray = json_decode($_POST['productosJSON'], true);
		if ($productosArray) {
			$idDetComprobante = 1;
			foreach ($productosArray as $productoItem) {
				$oDetComprobante = new detComprobante(
					$idDetComprobante,
					1,
					$productoItem['idProducto'],
					$productoItem['codigo'],
					$productoItem['descripcion'],
					$productoItem['unMedida'],
					$productoItem['codUM'],
					$productoItem['cantidad'],
					$productoItem['espesor'],
					$productoItem['ancho'],
					$productoItem['largo'],
					$productoItem['cantidadFinal'],
					$productoItem['precio'],
					$productoItem['precio'] * $productoItem['cantidadFinal']
				);
				$detComprobante[$idDetComprobante] = $oDetComprobante;
				$idDetComprobante++;
			}
		}
	} else {
		$detComprobante = $_SESSION['detComprobante'] ?? [];
	}
	
	$usuario              = $_SESSION['user'];
	$idComprobante        = "";
	$medioPago            = array();
	$montoPagado          = array();
	
    /** Condición de pago Crédito - inicio *
	$cuotasCredito        = $_POST['cuotasCredito'];
	$medioPagoCredito     = array();
	$montoPagadoCredito   = array();
    /** Condición de pago Crédito - fin **/
    
	$vMontoTotal          = 0.0;
	$respuesta['mensaje'] = "";
	$fechaActual          = date('d-m-Y H:i:s');
	
	//Inicio validaciones
	if (empty($tipoComprobante))
	{
		$respuesta['mensaje'] .= "-Debe escoger un tipo de comprobante.\n";
	}
	else
	{
		if (empty($idCliente))
		{
			$respuesta['mensaje'] .=  "-Debe ingresar un cliente.\n";
		}
		else
		{
            switch ($tipoComprobante) 
            {
                case 1:
                    $respuesta['mensaje'] .= strlen($numeroDocumento) != 11 ? "-El RUC debe tener 11 dígitos.\n" : "";
                    break;
                default:
                    $respuesta['mensaje'] .= (strlen($numeroDocumento) != 8 && strlen($numeroDocumento) != 12) ? "-El DNI debe tener 8 dígitos y CE 12 dígitos.\n" : "";
                    break;
            }
		}
	}
	
	if (empty($detComprobante))
	{
		$respuesta['mensaje'] .= "-Debe agregar al menos un producto.\n";
	}
	else 
	{
		foreach ($detComprobante as $item) 
		{
			$idProducto = $item->getIdProducto();
			
			$selectStockProducto = 
				"SELECT descripcion, stock 
					FROM productos pro 
					WHERE pro.id = '$idProducto'";
			
			if (!$resultSelectStockProducto = mysqli_query($con, $selectStockProducto)) 
			{
				exit(mysqli_error($con));
			}
			
			while ($rowSelectStockProducto = mysqli_fetch_assoc($resultSelectStockProducto)) 
			{
				$stock       = $rowSelectStockProducto['stock'];
				$descripcion = $rowSelectStockProducto['descripcion'];
			}
			
			$nuevoStock = $stock - $item->getProCantidadFinal();
			
			$respuesta['mensaje'] .= $nuevoStock < 0 ? "-No hay stock suficiente para el producto ".$descripcion.".\n": "";
		}
		unset($item);
	}
	
	$medioPago   = $_POST['medioPago'];
	$montoPagado = $_POST['montoPagado'];
	
	if ($condicionPago == 1)
	{
        if (empty($medioPago))
        {
            // Solo mostrar este error si no hay valores en los campos de pago
            $hayMontoPagado = false;
            for ($i = 1; $i <= 6; $i++) {
                if (isset($_POST['MP'.$i]) && !empty($_POST['MP'.$i])) {
                    $hayMontoPagado = true;
                    break;
                }
            }
            
            if (!$hayMontoPagado) {
    	        $respuesta['mensaje'] .= "-Debe ingresar al menos un medio de pago.\n";
            }
        }
    	else 
    	{
    		if ((count($montoPagado) == 1 && !empty($montoPagado[0])) || count($montoPagado) > 1) 
    		{
    			for ($i = 0; $i < count($montoPagado); $i++) 
    			{
    				$vMontoTotal += $montoPagado[$i];
    			}
    			
    			$respuesta['mensaje'] .= $vMontoTotal != $montoTotal ? "-El monto a pagar al contado (".number_format($vMontoTotal, 2, '.', '').") no corresponde con el calculado en el comprobante (".number_format($montoTotal, 2, '.', '').").\n" : "";
    		}
    	}
	} 
	else 
	{
		// Para ventas a crédito, verificar que haya cuotas y que el monto total coincida
		if (empty($cuotas)) {
			$respuesta['mensaje'] .= "-Debe agregar al menos una cuota para la venta a crédito.\n";
		} else {
			$respuesta['mensaje'] .= $montoTotalCuotas != $montoTotal ? "-El monto a pagar al crédito (".number_format($montoTotalCuotas, 2, '.', '').") no corresponde con el calculado en el comprobante (".number_format($montoTotal, 2, '.', '').").\n" : "";
		}
	}
	
	/* Inicio - actualización monto máximo sin documento - 2022.11.12*/
	$selectNumeroDocumento = 
		"SELECT * 
			FROM clientes cli 
			WHERE cli.id = '$idCliente'";
			
	if (!$resultSelectNumeroDocumento = mysqli_query($con, $selectNumeroDocumento)) 
	{
		exit(mysqli_error($con));
	}
	
	$clienteNumDocumento = ''; // Variable nueva para almacenar el documento del cliente
	while ($rowSelectNumeroDocumento = mysqli_fetch_assoc($resultSelectNumeroDocumento)) 
	{
		$clienteNumDocumento = $rowSelectNumeroDocumento['num_documento'];
	}
	
	if ($tipoComprobante == 2 && $montoTotal >= 700 && $clienteNumDocumento == '00000000') 
	{
	    $respuesta['mensaje'] .= "-Para boletas con montos mayores o iguales a S/ 700.00 es obligatorio el número de documento del cliente.\n";
	}
	/* Fin - actualización monto máximo sin documento - 2022.11.12*/
	
	/* Detracciones - inicio */
	if ($tipoComprobante == 1 && $montoTotal <= 700 && $aplicaDetraccion == 1) 
	{
	    $respuesta['mensaje'] .= "-La detracción solo es aplicable a facturas con monto mayor a S/ 700.00.\n";
	}
	
	$aplicaDetraccion = $aplicaDetraccion == 1 ? "true" : "false";
	
	/* Detracciones - fin*/

	/*if ($condicionPago !== 1)
	{
		foreach ($cuotas as $item) 
		{
			$respuesta['mensaje'] .= "- ".$item->getComFechaCuota()." 23:59:59"."\n";
		}
	}*/
	
	//Fin validaciones
	
	if (empty($respuesta['mensaje'])) //Consultar si existe alguna validación
	{
		$insertComprobante = "
			INSERT INTO comprobantes(id_cliente, tip_comprobante, fec_emision, fec_vencimiento, 
				condicion_pago, desc_medio_pago, observaciones, guia_remision, ord_compra, 
				fec_pago, mon_neto, mon_igv, mon_total, aplica_detraccion, 
				tip_detraccion, por_detraccion, mon_detraccion, cod_medio_pago_detraccion, 
				usu_creacion, fec_creacion) 
			VALUES('$idCliente', '$tipoComprobante', STR_TO_DATE('$fechaActual', '%d-%m-%Y %H:%i:%s'), 
				DATE_ADD(STR_TO_DATE('$fechaActual', '%d-%m-%Y %H:%i:%s'), INTERVAL '$condicionPago' DAY), 
				'$condicionPago', '$descMedioPago', '$observaciones', '$guiaRemision', '$ordenCompra', 
				'$fechaPago', '$montoNeto', '$montoIGV', '$montoTotal', '$aplicaDetraccion', 
				'$bienServicioDet', '$porcentajeDet', '$montoDet', '$medioPagoDet', 
				'$usuario', STR_TO_DATE('$fechaActual', '%d-%m-%Y %H:%i:%s'))";
		
		if (!$resultInsertComprobante = mysqli_query($con, $insertComprobante)) 
		{
			exit(mysqli_error($con));
		}
		
		$selectIdComprobante = "
			SELECT MAX(id) as idComprobante 
			FROM comprobantes";
		
		if (!$resultSelectIdComprobante = mysqli_query($con, $selectIdComprobante)) 
		{
			exit(mysqli_error($con));
		}
		
		while ($rowSelectIdComprobante = mysqli_fetch_assoc($resultSelectIdComprobante)) 
		{
			$idComprobante = $rowSelectIdComprobante['idComprobante'];
		}
		
		if ($condicionPago == 1)
	    {
	        // Si se recibe el array medioPago, usarlo. De lo contrario, procesar los campos MP1, MP2, etc.
    		if (!empty($medioPago)) {
        		for ($i = 0; $i < count($medioPago); $i++) 
        		{
        			$vMedioPago   = $medioPago[$i];
        			$vMontoPagado = count($montoPagado) == 1 ? $montoTotal : $montoPagado[$i];
        			
        			$insertPagos = "
    					INSERT INTO pagos (id_comprobante, codigo_medio_pago, monto) 
        				VALUES ('$idComprobante', '$vMedioPago', '$vMontoPagado')";
        			
        			if (!$resultInsertPagos = mysqli_query($con, $insertPagos)) 
        			{
        				exit(mysqli_error($con));
        			}
        		}
    		} else {
    		    // Procesar campos MP1, MP2, etc.
    		    for ($i = 1; $i <= 6; $i++) {
    		        if (isset($_POST['MP'.$i]) && !empty($_POST['MP'.$i])) {
    		            $vMedioPago = $i;
    		            $vMontoPagado = $_POST['MP'.$i];
    		            
    		            $insertPagos = "
        					INSERT INTO pagos (id_comprobante, codigo_medio_pago, monto) 
            				VALUES ('$idComprobante', '$vMedioPago', '$vMontoPagado')";
            			
            			if (!$resultInsertPagos = mysqli_query($con, $insertPagos)) 
            			{
            				exit(mysqli_error($con));
            			}
    		        }
    		    }
    		}
    	}
    	else 
    	{
    	    foreach ($cuotas as $item) 
    		{
    			$insertCuota = "
					INSERT INTO cuotas (id_comprobante, fecha, monto) 
    				VALUES ('".$idComprobante."', STR_TO_DATE('".$item->getComFechaCuota()." 23:59:59"."', '%Y-%m-%d %H:%i:%s'), '".$item->getComMontoCuota()."')";
    						
    			if (!$resultInsertCuota = mysqli_query($con, $insertCuota)) 
    			{
    				exit(mysqli_error($con));
    			}
    		}
    		unset($item); 
    	}
		
		foreach ($detComprobante as $item) 
		{
			$insertDetComprobante = "
				INSERT INTO det_comprobante (id_comprobante, id_producto, precio, cantidad, espesor, ancho, largo, cantidad_final) 
				VALUES ('".$idComprobante."', '".$item->getIdProducto()."', '".$item->getProPrecioUnitario()."', '".$item->getProCantidad()."', 
					'".$item->getProEspesor()."', '".$item->getProAncho()."', '".$item->getProLargo()."', '".$item->getProCantidadFinal()."')";
						
			if (!$resultInsertDetComprobante = mysqli_query($con, $insertDetComprobante)) 
			{
				exit(mysqli_error($con));
			}
		}
		unset($item);
		
		$_SESSION['cuotas']         = array();
		$_SESSION['detComprobante'] = array();
	
		$respuesta['estado']  = 0;
		$respuesta['mensaje'] = "Comprobante [$idComprobante] guardado correctamente.";
		
		// Opcional: devolver información para mostrar al usuario
		$respuesta['serieComprobante'] = "-";
		$respuesta['numeroComprobante'] = $idComprobante;
		$respuesta['tipoComprobante'] = $tipoComprobante;
	}
	else
	{
		$respuesta['estado'] = 200;
	}
	
	echo json_encode($respuesta);
?>