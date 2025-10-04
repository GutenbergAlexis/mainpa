<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idProducto'], $_POST['descripcion'], $_POST['codigo'], $_POST['precio']))
	{
		$idProducto  = $_POST['idProducto'];
		$descripcion = utf8_decode($_POST['descripcion']);
		$codigo      = $_POST['codigo'];
		$precio      = $_POST['precio'];
		$stock       = $_POST['stock'];
		$usuario     = $_SESSION['user'];
		
		$query = 
				"UPDATE productos SET descripcion = '$descripcion', codigo = '$codigo', precio = '$precio', 
					stock = '$stock', usu_modificacion = '$usuario', fec_modificacion = NOW() 
					WHERE id = '$idProducto'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>