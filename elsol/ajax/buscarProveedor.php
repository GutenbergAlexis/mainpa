<?php
	include('../util/database.php');

	if(isset($_POST['ruc']) && isset($_POST['ruc']) != "")
	{
		$ruc      = $_POST['ruc'];
		$response = array();

		$selectProveedor = 
			"SELECT prv.id, prv.ruc, prv.razon_social, prv.direccion 
				FROM proveedores prv 
				WHERE prv.ruc = '$ruc'";
		
		if (!$resultSelectProveedor = mysqli_query($con, $selectProveedor)) 
		{
			exit(mysqli_error($con));
		}
		
		if(mysqli_num_rows($resultSelectProveedor) > 0) 
		{
			while ($rowSelectProveedor = mysqli_fetch_assoc($resultSelectProveedor)) 
			{
				$response = $rowSelectProveedor;
			}
		}
		else
		{
			$response['status']  = 200;
			$response['message'] = "Data not found!";
		}
	}
	else
	{
		$response['status']  = 200;
		$response['message'] = "Invalid Request!";
	}
	
	echo json_encode($response);
?>