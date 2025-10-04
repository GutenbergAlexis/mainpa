<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idComprobante'], $_POST['idProducto'], $_POST['unMedida'], $_POST['codUM'], $_POST['codigo'], $_POST['descripcion'], 
		$_POST['cantidad'], $_POST['espesor'], $_POST['ancho'], $_POST['largo'], $_POST['precio']))
	{
		$idComprobante = $_POST['idComprobante'];
        $idProducto    = $_POST['idProducto'];
        $unMedida      = $_POST['unMedida'];
        $codUM         = $_POST['codUM'];
        $codigo        = $_POST['codigo'];
        $descripcion   = $_POST['descripcion'];
        $cantidad      = $_POST['cantidad'];
        $espesor       = $_POST['espesor'];
        $ancho         = $_POST['ancho'];
        $largo         = $_POST['largo'];
        $precio        = $_POST['precio'];
		
		$cantidadFinal = $codUM == 3 ? round($cantidad*$espesor*$ancho*$largo/12, 2) : $cantidad;
		
		$insertDetComprobante = 
			"INSERT INTO det_comprobante (id_comprobante, id_producto, precio, cantidad, espesor, ancho, largo, cantidad_final) 
				VALUES ('$idComprobante', '$idProducto', '$precio', '$cantidad', '$espesor', '$ancho', '$largo', '$cantidadFinal')";
		
		if (!$resultInsertDetComprobante = mysqli_query($con, $insertDetComprobante)) 
		{
			exit(mysqli_error($con));
		}
	}
?>