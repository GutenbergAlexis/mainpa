<?php
	include('../x/detCompra.php');
	session_start();

    if(isset($_POST['idDetCompra']))
	{
		$idDetCompra = $_POST['idDetCompra'];
		
		unset($_SESSION['detCompra'][$idDetCompra]);
	}
?>