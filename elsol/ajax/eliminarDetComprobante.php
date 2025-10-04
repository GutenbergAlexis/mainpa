<?php
	include('../x/detComprobante.php');
	session_start();

    if(isset($_POST['idDetComprobante']))
	{
		$idDetComprobante = $_POST['idDetComprobante'];
		
		unset($_SESSION['detComprobante'][$idDetComprobante]);
	}
?>