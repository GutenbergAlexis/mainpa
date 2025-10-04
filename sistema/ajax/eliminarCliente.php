<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idCliente']))
	{
		$idCliente = $_POST['idCliente'];
		
		$query = 
				"DELETE FROM clientes WHERE id = '$idCliente'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>