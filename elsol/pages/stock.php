<?php
	include('../util/database.php');
	include('../util/PHPExcel/PHPExcel.php');
	session_start();
	
	if (isset($_SESSION['user'])) 
	{
		if ($_SESSION['perfil'] == 3) 
		{
			$archivo       = "stock.xlsx";
			$inputFileType = PHPExcel_IOFactory::identify($archivo);
			$objReader     = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel   = $objReader->load($archivo);
			$sheet         = $objPHPExcel->getSheet(0);
			$highestRow    = $sheet->getHighestRow();
			for ($row = 1; $row <= $highestRow; $row++) 
			{
				$codigo      = $sheet->getCell("A".$row)->getValue();
				$descripcion = $sheet->getCell("B".$row)->getValue();
				$unMedida    = $sheet->getCell("C".$row)->getValue() == 'NIU' ? 1 : ($sheet->getCell("C".$row)->getValue() == 'KG' ? 2 : ($sheet->getCell("C".$row)->getValue() == 'PT' ? 3 : ($sheet->getCell("C".$row)->getValue() == 'ZZ' ? 4 : 0)));
				$precio      = $sheet->getCell("D".$row)->getValue();
				$stock       = $sheet->getCell("E".$row)->getValue();
				
				$insertStock = 
					"INSERT INTO productos(descripcion, unidad_medida, codigo, precio, stock, usu_creacion, fec_creacion) 
						VALUES('$descripcion', $unMedida, '$codigo', '$precio', '$stock', 'SYSTEM', NOW())";
				
				if (!$resultInsertStock = mysqli_query($con, $insertStock)) 
				{
					exit(mysqli_error($con));
				}
			}
		} 
		else 
		{
			header('Location: login.php');
		}
	} 
	else 
	{
		header('Location: login.php');
	}
?>