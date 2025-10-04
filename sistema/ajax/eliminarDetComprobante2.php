<?php
	include('../util/database.php');
	session_start();
	
	$idDetComprobante = $_POST['idDetComprobante'];

	$deleteDetComprobante = 
		"DELETE FROM det_comprobante WHERE id = $idDetComprobante";
	
	if (!$resultDeleteDetComprobante = mysqli_query($con, $deleteDetComprobante)) 
	{
		exit(mysqli_error($con));
	}
?>