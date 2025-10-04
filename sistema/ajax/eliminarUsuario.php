<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idUsuario']))
	{
		$idUsuario = $_POST['idUsuario'];
		
		$query = 
				"DELETE FROM usuarios WHERE id = '$idUsuario'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>