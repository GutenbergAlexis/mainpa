<?php
	include('../util/database.php');
	session_start();
	
    /*if(isset($_POST['idProducto'], $_POST['cantidad'], $_POST['precio']))
    {*/
		$ruc         = $_POST['ruc'];
		$razonSocial = utf8_decode($_POST['razonSocial']);
		$direccion   = utf8_decode($_POST['direccion']);
		$contacto    = utf8_decode($_POST['contacto']);
		$telefono    = $_POST['telefono'];
		$correo      = $_POST['correo'];
		$usuario     = $_SESSION['user'];
		
        $query = "INSERT INTO proveedores(ruc, razon_social, direccion, contacto, telefono, correo, usu_creacion, fec_creacion) VALUES('$ruc', '$razonSocial', '$direccion', '$contacto', '$telefono', '$correo', '$usuario', NOW())";
		
        if (!$result = mysqli_query($con, $query)) 
		{
            exit(mysqli_error($con));
        }
		
    //}
?>