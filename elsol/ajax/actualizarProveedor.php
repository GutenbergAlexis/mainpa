<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['idProveedor'], $_POST['ruc'], $_POST['razonSocial'], $_POST['direccion'], $_POST['contacto'], $_POST['telefono'], $_POST['correo']))
	{
		$idProveedor = $_POST['idProveedor'];
		$ruc         = $_POST['ruc'];
		$razonSocial = utf8_decode($_POST['razonSocial']);
		$direccion   = utf8_decode($_POST['direccion']);
		$contacto    = utf8_decode($_POST['contacto']);
		$telefono    = $_POST['telefono'];
		$correo      = $_POST['correo'];
		$usuario     = $_SESSION['user'];
		
		$query = 
				"UPDATE proveedores SET ruc = '$ruc', razon_social = '$razonSocial', direccion = '$direccion', 
					contacto = '$contacto', telefono = '$telefono', correo = '$correo', usu_modificacion = '$usuario', 
					fec_modificacion = NOW() WHERE id = '$idProveedor'";
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>