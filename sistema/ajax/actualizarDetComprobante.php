<?php
	include('../x/detComprobante.php');
	session_start();

    if(isset($_POST['idDetComprobante'], $_POST['cantidad'], $_POST['precio']))
	{
		$idDetComprobante = $_POST['idDetComprobante'];
		$espesor          = $_POST['espesor'];
		$ancho            = $_POST['ancho'];
		$largo            = $_POST['largo'];
		$cantidad         = $_POST['cantidad'];
		$precio           = $_POST['precio'];
		$cantidadFinal    = "";
		
		$detComprobante = $_SESSION['detComprobante'][$idDetComprobante];
		
		if ($detComprobante->getProCodigoUnidadMedida() == 3)
		{
			$detComprobante->setProEspesor($espesor);
			$detComprobante->setProAncho($ancho);
			$detComprobante->setProLargo($largo);
			$cantidadFinal = round($espesor*$largo*$ancho*$cantidad/12, 2);
		}
		else
		{
			$cantidadFinal = $cantidad;
		}
		
		$detComprobante->setProCantidad($cantidad);
		$detComprobante->setProCantidadFinal($cantidadFinal);
		$detComprobante->setProPrecioUnitario($precio);
		$detComprobante->setProPrecioTotal($cantidadFinal*$precio);
		
		$_SESSION['detComprobante'][$idDetComprobante] = $detComprobante;
	}
?>