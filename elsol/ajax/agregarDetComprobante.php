<?php
	include('../x/detComprobante.php');
	session_start();

  if(isset($_POST['idProducto'], $_POST['unMedida'], $_POST['codUM'], $_POST['codigo'], $_POST['descripcion'], 
    $_POST['cantidad'], $_POST['espesor'], $_POST['ancho'], $_POST['largo'], $_POST['precio']))
	{
        $idProducto  = $_POST['idProducto'];
        $unMedida    = $_POST['unMedida'];
        $codUM       = $_POST['codUM'];
        $codigo      = $_POST['codigo'];
        $descripcion = $_POST['descripcion'];
        $cantidad    = $_POST['cantidad'];
        $espesor     = $_POST['espesor'];
        $ancho       = $_POST['ancho'];
        $largo       = $_POST['largo'];
        $precio      = $_POST['precio'];
		
		$cantidadFinal = $codUM == 3 ? round($cantidad*$espesor*$ancho*$largo/12, 2) : $cantidad;
		
		$oDetComprobante = new detComprobante($_SESSION['idDetComprobante'], 1, $idProducto, $codigo, $descripcion, 
		  $unMedida, $codUM, $cantidad, $espesor, $ancho, $largo, $cantidadFinal, $precio, $cantidadFinal*$precio);
				
		$_SESSION['detComprobante'][$_SESSION['idDetComprobante']] = $oDetComprobante;
		
		$_SESSION['idDetComprobante'] = $_SESSION['idDetComprobante'] + 1;
	}
?>