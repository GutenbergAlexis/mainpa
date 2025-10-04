<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idProducto']))
	{
		$idProducto = $_POST['idProducto'];
		
		$query = 
				"DELETE FROM productos WHERE id = '$idProducto'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>