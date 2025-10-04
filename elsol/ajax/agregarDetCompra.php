<?php
	include('../x/detCompra.php');
	session_start();

    if(isset($_POST['idProducto'], $_POST['codigo'], $_POST['descripcion'], $_POST['cantidad'], $_POST['costo']))
	{
        $idProducto  = $_POST['idProducto'];
        $codigo      = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        $unMedida    = $_POST['unMedida'];
        $cantidad    = $_POST['cantidad'];
        $costo       = $_POST['costo'];
		
		$oDetCompra = new detCompra($_SESSION['idDetCompra'], 1, $idProducto, $codigo, $descripcion, $unMedida, '', $cantidad, $costo, $cantidad*$costo);
				
		$_SESSION['detCompra'][$_SESSION['idDetCompra']] = $oDetCompra;
		
		$_SESSION['idDetCompra'] = $_SESSION['idDetCompra'] + 1;
	}
?>