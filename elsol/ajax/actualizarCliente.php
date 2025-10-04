<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idCliente'], $_POST['tipCliente'], $_POST['numDocumento'], $_POST['primerNombre'], $_POST['segundoNombre'], 
		$_POST['primerApellido'], $_POST['segundoNombre'], $_POST['direccion'], $_POST['telefono'], $_POST['celular'], $_POST['correo']))
	{
		$idCliente       = $_POST['idCliente'];
		$tipCliente      = $_POST['tipCliente'];
		$numDocumento    = $_POST['numDocumento'];
		$primerNombre    = utf8_decode(addslashes($_POST['primerNombre']));
		$segundoNombre   = utf8_decode(addslashes($_POST['segundoNombre']));
		$primerApellido  = utf8_decode(addslashes($_POST['primerApellido']));
		$segundoApellido = utf8_decode(addslashes($_POST['segundoApellido']));
		$direccion       = utf8_decode(addslashes($_POST['direccion']));
		$contacto        = utf8_decode(addslashes($_POST['contacto']));
		$telefono        = $_POST['telefono'];
		$celular         = $_POST['celular'];
		$correo          = $_POST['correo'];
		$usuario         = $_SESSION['user'];
		
		$updateCliente = 
				"UPDATE clientes 
					SET num_documento = '$numDocumento', tip_cliente = '$tipCliente', nombre_razon_social = '$primerNombre', 
					seg_nombre = '$segundoNombre', pri_apellido = '$primerApellido', seg_apellido = '$segundoApellido', 
					direccion = '$direccion', contacto = '$contacto', telefono = '$telefono', celular = '$celular', 
					correo = '$correo', usu_modificacion = '$usuario', fec_modificacion = NOW() 
					WHERE id = '$idCliente'";
		
		if (!$resultUpdateCliente = mysqli_query($con, $updateCliente)) {
			exit(mysqli_error($con));
		}
	}
?>