<?php
	include('../util/database.php');
	session_start();
	
    /*if(isset($_POST['idProducto'], $_POST['cantidad'], $_POST['precio']))
    {*/
		$tipoIngreso   = $_POST['tipoIngreso'];
		$descripcion   = utf8_decode($_POST['descripcion']);
		$monto         = $_POST['monto'];
		$fecha         = $_POST['fecha'];
		$observaciones = utf8_decode($_POST['observaciones']);
		$usuario       = $_SESSION['user'];
		
        $insertOtrosIngresos = 
			"INSERT INTO otros_ingresos(tip_ingreso, descripcion, monto, fecha, observaciones, usu_creacion, fec_creacion) 
				VALUES('$tipoIngreso', '$descripcion', '$monto', str_to_date('$fecha', '%d/%m/%Y'), '$observaciones', '$usuario', NOW())";
		
        if (!$resultOtrosIngresos = mysqli_query($con, $insertOtrosIngresos)) 
		{
            exit(mysqli_error($con));
        }
		
    //}
?>