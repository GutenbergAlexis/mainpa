<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idProveedor']))
	{
		$idProveedor = $_POST['idProveedor'];
		
		$query = 
				"DELETE FROM proveedores WHERE id = '$idProveedor'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>