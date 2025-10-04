<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idDetComprobante'], $_POST['unidadMedida'], $_POST['cantidad'], $_POST['precio']))
	{
		if($_POST['unidadMedida'] == 3)
		{
			$cantidadFinal = round($_POST['cantidad']*$_POST['espesor']*$_POST['ancho']*$_POST['largo']/12, 2);
			
			$query = 
					"UPDATE det_comprobante dcom 
						SET dcom.cantidad = '" .$_POST['cantidad']. "', dcom.espesor = '" .$_POST['espesor']. "', 
						dcom.ancho = '" .$_POST['ancho']. "', dcom.largo = '" .$_POST['largo']. "', 
						dcom.precio = '" .$_POST['precio']. "', dcom.cantidad_final = '" .$cantidadFinal. "' 
						WHERE dcom.id = '" .$_POST['idDetComprobante']. "'";
		}
		else 
		{
			$query = 
					"UPDATE det_comprobante dcom 
						SET dcom.cantidad = '" .$_POST['cantidad']. "', dcom.precio = '" .$_POST['precio']. "', 
						dcom.cantidad_final =  '" .$_POST['cantidad']. "' 
						WHERE dcom.id = '" .$_POST['idDetComprobante']. "'";
		}
		
		if (!$result = mysqli_query($con, $query)) {
			exit(mysqli_error($con));
		}
	}
?>