<?php
	include('../x/detCompra.php');
	session_start();

    if(isset($_POST['idDetCompra'], $_POST['cantidad'], $_POST['costo']))
	{
		$idDetCompra = $_POST['idDetCompra'];
		$cantidad    = $_POST['cantidad'];
		$costo       = $_POST['costo'];
		
		$detCompra = $_SESSION['detCompra'][$idDetCompra];
		
		$detCompra->setProCantidad($cantidad);
		$detCompra->setProCostoUnitario($costo);
		$detCompra->setProCostoTotal($cantidad*$costo);
		
		$_SESSION['detCompra'][$idDetCompra] = $detCompra;
	}
?>