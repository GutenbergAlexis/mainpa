<?php
	include('../util/database.php');
	session_start();

    if(isset($_POST['id'])) {
		$idComprobante = $_POST['id'];
		$usuario       = $_SESSION['user'];
		
		$updatePagado = 
			"UPDATE comprobantes 
				SET pagado = '1', usu_modificacion = '$usuario', fec_modificacion = NOW() 
				WHERE id = '$idComprobante'";
		
		if (!$resultUpdatePagado = mysqli_query($con, $updatePagado)) {
			exit(mysqli_error($con));
		}
	}
?>