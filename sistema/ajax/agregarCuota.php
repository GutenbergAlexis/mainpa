<?php
	include('../x/cuotas.php');
	
	/** Condición de pago Crédito - inicio *
	include('../x/cuotasCredito.php');
	/** Condición de pago Crédito - fin **/
	
	session_start();

    if(isset($_POST['fechaCuota'], $_POST['montoCuota']))
	{
        $fechaCuota = $_POST['fechaCuota'];
        $montoCuota = $_POST['montoCuota'];
		
		$oCuotas = new cuotas($_SESSION['idCuota'], 1, $fechaCuota, $montoCuota);
		
		$_SESSION['cuotas'][$_SESSION['idCuota']] = $oCuotas;
		
		$_SESSION['idCuota'] = $_SESSION['idCuota'] + 1;
		
		/** Condición de pago Crédito - inicio *
		$cuotasCredito = new cuotasCredito($_SESSION['idCuotaCredito'], 1, $fechaCuota, $montoCuota);
		
		$_POST['cuotasCredito'][$_SESSION['idCuotaCredito']] = $cuotasCredito;
		
		$_SESSION['idCuotaCredito'] = $_SESSION['idCuotaCredito'] + 1;
		/** Condición de pago Crédito - fin **/
	}
?>