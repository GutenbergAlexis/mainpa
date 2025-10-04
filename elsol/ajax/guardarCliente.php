<?php
	include('../util/database.php');
	session_start();

	$tipCliente           = $_POST['tipCliente'];
	$tipDocumento         = $_POST['tipDocumento'];
	$numDocumento         = $_POST['numDocumento'];
	$priNombre            = utf8_decode(addslashes($_POST['priNombre']));
	$segNombre            = utf8_decode(addslashes($_POST['segNombre']));
	$priApellido          = utf8_decode(addslashes($_POST['priApellido']));
	$segApellido          = utf8_decode(addslashes($_POST['segApellido']));
	$direccion            = utf8_decode(addslashes($_POST['direccion']));
	$contacto             = utf8_decode(addslashes($_POST['contacto']));
	$telefono             = $_POST['telefono'];
	$celular              = $_POST['celular'];
	$correo               = $_POST['correo'];
	$usuario              = $_SESSION['user'];
	$respuesta['mensaje'] = "";
	
	//Validaciones
	$respuesta['mensaje'] .= $tipCliente == 0 ? "-Debe ingresar un Tipo de Cliente.\n" : "";
	
	/*if ($tipDocumento == 1) 
	{
		$respuesta['mensaje'] .= strlen($numDocumento) != 8 ? "-El Número de DNI debe tener 8 dígitos.\n" : "";
		
		$respuesta['mensaje'] .= ($correo != "" && $correo != "-" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) ? "-Debe ingresar un correo válido.\n" : "";
	} 
	else if ($tipDocumento == 6) 
	{
		$respuesta['mensaje'] .= strlen($numDocumento) != 11 ? "-El Número de RUC debe tener 11 dígitos.\n" : "";
		
		$respuesta['mensaje'] .= !filter_var($correo, FILTER_VALIDATE_EMAIL) ? "-Debe ingresar un correo válido.\n" : "";
	}
	else
	{
		$respuesta['mensaje'] .= "-Debe ingresar un Tipo de Documento.\n";
	}*/
	
    /*********************/
    switch ($tipDocumento) 
    {
        case 1:
            $respuesta['mensaje'] .= strlen($numDocumento) != 8 ? "-El Número de DNI debe tener 8 dígitos.\n" : "";
	
	        $respuesta['mensaje'] .= $priNombre == "" ? "-Debe ingresar el Primer Nombre.\n" : "";
	
	        $respuesta['mensaje'] .= $priApellido == "" ? "-Debe ingresar el Apellido Paterno.\n" : "";
            
            $respuesta['mensaje'] .= ($correo != "" && $correo != "-" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) ? "-Debe ingresar un correo válido.\n" : "";
            
            break;
        case 4:
		    $respuesta['mensaje'] .= strlen($numDocumento) != 12 ? "-El Número de Carnet de Extranjería debe tener 12 dígitos.\n" : "";
	
	        $respuesta['mensaje'] .= $priNombre == "" ? "-Debe ingresar el Primer Nombre.\n" : "";
	
	        $respuesta['mensaje'] .= $priApellido == "" ? "-Debe ingresar el Apellido Paterno.\n" : "";
		
	    	$respuesta['mensaje'] .= ($correo != "" && $correo != "-" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) ? "-Debe ingresar un correo válido.\n" : "";
        
            break;
        case 6:
		    $respuesta['mensaje'] .= strlen($numDocumento) != 11 ? "-El Número de RUC debe tener 11 dígitos.\n" : "";
	
	        $respuesta['mensaje'] .= $priNombre == "" ? "-Debe ingresar la Razón Social.\n" : "";
		
		    $respuesta['mensaje'] .= !filter_var($correo, FILTER_VALIDATE_EMAIL) ? "-Debe ingresar un correo válido.\n" : "";
            
            break;
        default: 
		    $respuesta['mensaje'] .= "-Debe ingresar un Tipo de Documento.\n";
		    
		    break;
    }
    /*********************/
	
	$respuesta['mensaje'] .= $direccion == "" ? "-Debe ingresar una Dirección.\n" : "";
	
	$selectIdCliente = 
		"SELECT id 
			FROM clientes 
			WHERE num_documento = '$numDocumento'";
	
	if (!$resultSelectIdCliente = mysqli_query($con, $selectIdCliente)) 
	{
		exit(mysqli_error($con));
	}
		
	$respuesta['mensaje'] .= mysqli_num_rows($resultSelectIdCliente) > 0 ? "-Cliente ya existe.\n" : "";
	
	if (empty($respuesta['mensaje'])) //Consultar si existe alguna validación
	{
		$insertCliente = "INSERT INTO clientes(tip_cliente, tip_documento, num_documento, nombre_razon_social, seg_nombre, pri_apellido, 
				seg_apellido, direccion, contacto, telefono, celular, correo, usu_creacion, fec_creacion) 
			VALUES('$tipCliente', '$tipDocumento', '$numDocumento', '$priNombre', '$segNombre', '$priApellido', 
				'$segApellido', '$direccion', '$contacto', '$telefono', '$celular', '$correo', '$usuario', NOW())";
		
		if (!$resultInsertCliente = mysqli_query($con, $insertCliente)) 
		{
			exit(mysqli_error($con));
		}
		
		$selectIdCliente = 
			"SELECT id 
				FROM clientes 
				WHERE num_documento = '$numDocumento'";
		
		if (!$resultSelectIdCliente = mysqli_query($con, $selectIdCliente)) 
		{
			exit(mysqli_error($con));
		}
		
		while ($rowSelectIdCliente = mysqli_fetch_assoc($resultSelectIdCliente)) 
		{
			$idCliente = $rowSelectIdCliente['id'];
		}
		
		$respuesta['estado']    = 0;
		$respuesta['mensaje']   = "-Cliente guardado correctamente.";
		$respuesta['idCliente'] = $idCliente;
	}
	else
	{
		$respuesta['estado']  = 200;
	}
	
	echo json_encode($respuesta);
?>