<?php
	include('../util/database.php');
	session_start();
	
    /*if(isset($_POST['idProducto'], $_POST['cantidad'], $_POST['precio']))
    {*/
		$descripcion = utf8_decode($_POST['descripcion']);
		$unMedida    = $_POST['unMedida'];
		$codigo      = $_POST['codigo'];
		$precio      = $_POST['precio'];
		$usuario     = $_SESSION['user'];
		
        $query = "INSERT INTO productos(descripcion, unidad_medida, codigo, precio, usu_creacion, fec_creacion) VALUES('$descripcion', $unMedida, '$codigo', '$precio', '$usuario', NOW())";
		
        if (!$result = mysqli_query($con, $query)) 
		{
            exit(mysqli_error($con));
        }
		
    //}
?>