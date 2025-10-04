<?php
	include('../util/database.php');
	include('../x/detCompra.php');
	session_start();
	
	$idProveedor          = $_POST['idProveedor'];
	$fechaCompra          = $_POST['fechaCompra'];
	$medioPago            = $_POST['medioPago'];
	$observaciones        = utf8_decode($_POST['observaciones']);
	$montoNeto            = $_POST['montoNeto'];
	$montoIGV             = $_POST['montoIGV'];
	$montoTotal           = $_POST['montoTotal'];
	$detCompra            = $_SESSION['detCompra'];
	$usuario              = $_SESSION['user'];
	$updateStockProducto  = "";
	$respuesta['mensaje'] = "";
	
	//Validaciones
	
	$respuesta['mensaje'] .= empty($idProveedor) ? "-Debe ingresar un proveedor.\n"       : "";
	
	$respuesta['mensaje'] .= empty($fechaCompra) ? "-Debe ingresar la fecha de compra.\n" : "";
	
	$respuesta['mensaje'] .= empty($medioPago)   ? "-Debe ingresar un medio de pago.\n"   : "";
	
	if (empty($detCompra))
	{
		$respuesta['mensaje'] .= "-Debe agregar al menos un producto.\n";
	}
	else 
	{
		foreach ($detCompra as $item) 
		{
			$idProducto = $item->getIdProducto();
			
			$selectStockProducto = 
				"SELECT pro.stock 
					FROM productos pro 
					WHERE pro.id = '$idProducto'";
			
			if (!$resultSelectStockProducto = mysqli_query($con, $selectStockProducto)) 
			{
				exit(mysqli_error($con));
			}
			
			while ($rowSelectStockProducto = mysqli_fetch_assoc($resultSelectStockProducto)) 
			{
				$stock = $rowSelectStockProducto['stock'];
			}
			
			$nuevoStock = $stock + $item->getProCantidad();
			
			$updateStockProducto = 
				"UPDATE productos pro 
					SET pro.stock = '$nuevoStock' 
					WHERE pro.id = '$idProducto'";
		}
		unset($item);
	}
	
	if (empty($respuesta['mensaje'])) //Consultar si existe alguna validación
	{
		$insertCompra = "INSERT INTO compras(id_proveedor, fec_compra, par_medio_pago, observaciones, mon_neto, mon_igv, mon_total, usu_creacion, fec_creacion) 
			VALUES('$idProveedor', str_to_date('$fechaCompra', '%d/%m/%Y'), '$medioPago', '$observaciones', '$montoNeto', '$montoIGV', '$montoTotal', '$usuario', NOW())";
		
		if (!$resultInsertCompra = mysqli_query($con, $insertCompra)) 
		{
			exit(mysqli_error($con));
		}
		
		$selectIdCompra = "SELECT MAX(id) as idCompra FROM compras";
		
		if (!$resultSelectIdCompra = mysqli_query($con, $selectIdCompra)) 
		{
			exit(mysqli_error($con));
		}
		
		while ($rowsSelectIdCompra = mysqli_fetch_assoc($resultSelectIdCompra)) 
		{
			$idCompra = $rowsSelectIdCompra['idCompra'];
		}
		
		foreach ($detCompra as $item) 
		{
			$idProducto    = $item->getIdProducto();
			$costoUnitario = $item->getProCostoUnitario();
			$cantidad      = $item->getProCantidad();
			
			$insertDetCompra = "INSERT INTO det_compra (id_compra, id_producto, costo, cantidad) 
				VALUES ('$idCompra', '$idProducto', '$costoUnitario', '$cantidad')";
						
			if (!$resultInsertDetCompra = mysqli_query($con, $insertDetCompra)) 
			{
				exit(mysqli_error($con));
			}
		}
		unset($item);
		
		if (!$resultUpdateStockProducto = mysqli_query($con, $updateStockProducto)) 
		{
			exit(mysqli_error($con));
		}
		
		$respuesta['estado']  = 0;
		$respuesta['mensaje'] = "-Compra guardada correctamente.";
	}
	else
	{
		$respuesta['estado'] = 200;
	}
	
	echo json_encode($respuesta);
?>