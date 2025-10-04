<?php
	include('../util/database.php');
	session_start();
	
    /*if(isset($_POST['idProducto'], $_POST['cantidad'], $_POST['precio']))
    {*/
		$numDocumento = $_POST['numDocumento'];
		$priNombre    = utf8_decode($_POST['priNombre']);
		$segNombre    = utf8_decode($_POST['segNombre']);
		$priApellido  = utf8_decode($_POST['priApellido']);
		$segApellido  = utf8_decode($_POST['segApellido']);
		$direccion    = utf8_decode($_POST['direccion']);
		$telefono     = $_POST['telefono'];
		$celular      = $_POST['celular'];
		$correo       = $_POST['correo'];
		$user         = $_POST['user'];
		$pass         = $_POST['pass'];
		$perfil       = $_POST['perfil'];
		$usuario      = $_SESSION['user'];
		
        $query = "INSERT INTO usuarios(num_documento, pri_nombre, seg_nombre, pri_apellido, seg_apellido, direccion, telefono, celular, correo, user, pass, perfil, usu_creacion, fec_creacion) VALUES('$numDocumento', '$priNombre', '$segNombre', '$priApellido', '$segApellido', '$direccion', '$telefono', '$celular', '$correo', '$user', '$pass', '$perfil', '$usuario', NOW())";
		
        if (!$result = mysqli_query($con, $query)) 
		{
            exit(mysqli_error($con));
        }
		
    //}
?>