<?php
	include('../util/database.php');
	include('../util/fpdf/fpdf.php');
	session_start();
	
    if(isset($_POST))
	{
		$idComprobante = $_POST['id-comprobante'];
		$nombre        = $_POST['nombre'];
		$direccion     = $_POST['direccion'];
		$guiaRemision  = $_POST['guia-remision'];
		
		$rows          = $_SESSION['rows'];
		$numRegistros  = $_SESSION['num-registros'];
		
		$i = 0;
		
		/*************************************/
		$generado = 1;
		$usuario  = $_SESSION['user'];
		
        $query = "UPDATE comprobantes SET generado = '$generado', usu_modificacion = '$usuario', fec_modificacion = NOW() WHERE id = '$idComprobante'";
		
        if (!$result = mysqli_query($con, $query)) 
		{
            exit(mysqli_error($con));
        }
		/*************************************/
		
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(40, 10, $nombre);
		$pdf->Cell(40, 10, $nombre);
		$pdf->Ln();
		$pdf->Cell(40, 10, '');
		$pdf->Cell(40, 10, $direccion);
		$pdf->Ln();
		$pdf->Cell(40, 10, $guiaRemision);
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 10);
		
		for ($i = 1; $i < $numRegistros; $i++)
		{
			$pdf->Cell(10, 10, $i);
			$pdf->Cell(10, 10, $rows[$i]['cantidad']);
			$pdf->Cell(60, 10, $rows[$i]['des_producto']);
			$pdf->Cell(15, 10, number_format($rows[$i]['precio_unitario'], 2, '.', ''), 0, 0, 'R');
			$pdf->Cell(15, 10, number_format($rows[$i]['precio_total'], 2, '.', ''), 0, 0, 'R');
			$pdf->Ln();
		}
		
		$pdf->Output();
	}
?>