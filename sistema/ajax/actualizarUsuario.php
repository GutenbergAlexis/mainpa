<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idUsuario'], $_POST['numDocumento'], $_POST['priNombre'], $_POST['segNombre'], $_POST['priApellido'], $_POST['segApellido'], $_POST['direccion'], $_POST['telefono'], $_POST['celular'], $_POST['correo'], $_POST['user'], $_POST['pass'], $_POST['perfil']))
	{
		$idUsuario    = $_POST['idUsuario'];
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
				
		$query = 
				"UPDATE usuarios SET num_documento = '$numDocumento', pri_nombre = '$priNombre', 
					seg_nombre = '$segNombre', pri_apellido = '$priApellido', seg_apellido = '$segApellido', 
					direccion = '$direccion', telefono = '$telefono', celular = '$celular', correo = '$correo', 
					user = '$user', pass = '$pass', perfil = '$perfil', usu_modificacion = '$usuario', fec_modificacion = NOW() 
				WHERE id = '$idUsuario'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>