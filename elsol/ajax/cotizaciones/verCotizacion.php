<?php
include('../../util/database.php');
include('../../util/numerosALetras.php');
require('../../util/fpdf/fpdf.php');
session_start();

class PDF extends FPDF
{
    function Header()
    {
		// Logo
		$this->Image('../../img/logoCotizacion.png', 18, 10, 70); // Ajusta la ruta y dimensiones según tu logo
		// Salto de línea
		$this->Ln(5);

		// Lista de productos a la derecha
		$this->SetFont('Arial', '', 7);
		$this->SetTextColor(5, 62, 137);
		$this->SetXY(100, 10); // Ajusta las coordenadas según sea necesario
		$this->Cell(0, 5, chr(149) . utf8_decode(' TRIPLAY FENÓLICO'), 0, 1, 'L');
		$this->SetXY(145, 10);
		$this->Cell(0, 5, chr(149) . ' TRIPLAY', 0, 1, 'L');
		$this->SetXY(170, 10);
		$this->Cell(0, 5, chr(149) . ' SELLADORA', 0, 1, 'L');
		$this->SetXY(100, 16); 
		$this->Cell(0, 5, chr(149) . ' SOLERAS', 0, 1, 'L');
		$this->SetXY(145, 16);
		$this->Cell(0, 5, chr(149) . ' MADERAS', 0, 1, 'L');
		$this->SetXY(170, 16);
		$this->Cell(0, 5, chr(149) . ' THINNER', 0, 1, 'L');
		$this->SetXY(100, 22); 
		$this->Cell(0, 5, chr(149) . ' VIGAS', 0, 1, 'L');
		$this->SetXY(145, 22);
		$this->Cell(0, 5, chr(149) . ' CLAVOS', 0, 1, 'L');
		$this->SetXY(170, 22);
		$this->Cell(0, 5, chr(149) . ' PRESERVANTES', 0, 1, 'L');
		$this->SetXY(100, 28); 
		$this->Cell(0, 5, chr(149) . utf8_decode(' TABLAS DE CONSTRUCCIÓN'), 0, 1, 'L');
		$this->SetXY(145, 28);
		$this->Cell(0, 5, chr(149) . ' COLA', 0, 1, 'L');
		$this->SetXY(170, 28);
		$this->Cell(0, 5, chr(149) . ' TORNILLOS', 0, 1, 'L');

		// Línea de separación
		//$this->Line(30, 40, 210, 40); // Ajusta las coordenadas según sea necesario
		$this->Ln(5);
    }

    function Footer()
    {
		$this->SetFont('Arial', '', 8);
		// Posición a 1.5 cm del final
		$this->SetY(-24);
		// Información de la empresa
		$this->Cell(0, 5, utf8_decode('Principal: Av. Próceres de la Independencia N° 2975 Urb. Canto Grande S.J.L. - Whatsapp 942902647 - Correo: ventas.mainpa@gmail.com'), 0, 1, 'C');
		$this->SetY(-18);
		$this->Cell(0, 5, utf8_decode('Sucursal 1: Av. Prolongación Pachacútec S/N Mz. AW Lote 13 - JICAMARCA - Whatsapp 997926805 - Correo: comercialelsol.mainpa@gmail.com'), 0, 1, 'C');
		$this->SetY(-12);
		$this->Cell(0, 5, utf8_decode('Sucursal 2: Mz. UC lote 10227 Asociación de Vivienda Gallinazos KM 28.5 - PUENTE PIEDRA'), 0, 1, 'C');
		// Número de página (opcional)
		// $this->Cell(0, 10, 'Página '.$this->PageNo(), 0, 0, 'C');
    }
}
	
$idCotizacion = $_POST['id-ver-cotizacion'];

$setLocale = 
	"SET lc_time_names = 'es_PE'"; 
	
$selectCotizacion = "
	SELECT cot.id, cot.id_cliente, cli.tip_documento, cli.num_documento, 
		par2.descripcion AS des_tipo_documento, cli.direccion, cli.contacto, 
		CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social, 
		cot.tip_comprobante, par1.descripcion AS des_comprobante, par3.descripcion AS des_med_pago, 
		cot.guia_remision, DATE_FORMAT(cot.fec_emision, 'Lima, %W %d de %M del %Y') AS fecha, cot.ord_compra, 
		cot.observaciones, cot.par_medio_pago, cot.usu_creacion AS vendedor, cot.condicion_pago, cot.desc_medio_pago, 
		usu.pri_nombre, usu.pri_apellido, cot.aplica_detraccion, par4.descripcion AS desc_aplica_detraccion, 
		cot.tip_detraccion, par5.descripcion AS desc_tip_detraccion, cot.por_detraccion, cot.mon_detraccion, 
		cot.cod_medio_pago_detraccion, par6.descripcion AS desc_medio_pago_detraccion 
	FROM cotizaciones cot 
	JOIN clientes cli ON cli.id = cot.id_cliente 
	JOIN usuarios usu ON usu.user = cot.usu_creacion 
	JOIN parametros par1 ON par1.codigo = cot.tip_comprobante AND par1.padre = 8 
	JOIN parametros par2 ON par2.codigo = cli.tip_documento AND par2.padre = 12 
	LEFT JOIN parametros par3 ON par3.codigo = cot.par_medio_pago AND par3.padre = 4 
	LEFT JOIN parametros par4 ON par4.abreviatura = cot.aplica_detraccion AND par4.padre = 42 /*aplica detracción*/
	LEFT JOIN parametros par5 ON par5.codigo = cot.tip_detraccion AND par5.padre = 68 /*tipo de detracción*/
	LEFT JOIN parametros par6 ON par6.codigo = cot.cod_medio_pago_detraccion AND par6.padre = 45 /*medio pago detracción*/
	WHERE cot.id = '$idCotizacion'";

$selectDetCotizacion = "
	SELECT dcot.id_producto, pro.codigo AS cod_producto, pro.descripcion AS des_producto, dcot.cantidad, 
		dcot.precio AS precio_unitario, dcot.precio*dcot.cantidad_final precio_total, pro.unidad_medida, 
		dcot.espesor, dcot.ancho, dcot.largo, dcot.cantidad_final, par.abreviatura AS abr_unidad_medida 
	FROM det_cotizacion dcot 
	JOIN productos pro ON pro.id = dcot.id_producto 
	JOIN parametros par ON par.codigo = pro.unidad_medida 
	WHERE par.padre = 29 AND dcot.id_cotizacion = '$idCotizacion'";

$selectCuotas = "
	SELECT cuo.id_comprobante, DATE_FORMAT(cuo.fecha, '%d-%m-%Y') as fecha, FORMAT(cuo.monto, 2) as monto
	FROM cuotas cuo 
	WHERE cuo.id_comprobante = '$idCotizacion' 
	ORDER BY cuo.fecha ASC";

if (!$resultSetLocale = mysqli_query($con, $setLocale)) 
{
	exit(mysqli_error($con));
}

if (!$resultSelectCotizacion = mysqli_query($con, $selectCotizacion)) 
{
	exit(mysqli_error($con));
}

if (!$resultSelectDetCotizacion = mysqli_query($con, $selectDetCotizacion)) 
{
	exit(mysqli_error($con));
}

if (!$resultSelectCuotas = mysqli_query($con, $selectCuotas)) 
{
	exit(mysqli_error($con));
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetMargins(16, 0);

if(mysqli_num_rows($resultSelectCotizacion) > 0) 
{
	while ($rowSelectCotizacion = mysqli_fetch_assoc($resultSelectCotizacion)) 
	{
		$pdf->SetFont('Arial', '', 10);
		$pdf->MultiCell(0, 1, ' ');
		$pdf->MultiCell(0, 7, utf8_decode($rowSelectCotizacion['fecha']), 0, 1, '');
		$pdf->SetFont('Arial', 'BU', 11);
		$pdf->MultiCell(0, 8, utf8_decode('COTIZACIÓN C001-'.str_pad($rowSelectCotizacion['id'], 8, "0", STR_PAD_LEFT)), 0, 1, '');
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->MultiCell(0, 6, utf8_decode('SEÑOR(ES): '.$rowSelectCotizacion['nombre_razon_social']), 0, 1, '');
		$pdf->MultiCell(0, 6, utf8_decode($rowSelectCotizacion['des_tipo_documento'].': '.$rowSelectCotizacion['num_documento']), 0, 1, '');
		$pdf->MultiCell(0, 6, utf8_decode('DIRECCIÓN: '.$rowSelectCotizacion['direccion']), 0, 1, '');
		$pdf->SetFont('Arial', '', 10);
		$pdf->MultiCell(0, 8, utf8_decode('Por medio de la presente le hacemos llegar la siguiente cotización según su solicitud:'));

		//Cabecera de la tabla
		$pdf->SetFillColor(5, 62, 137);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(64, 64, 64);
		$pdf->SetLineWidth(.3);
		$pdf->SetFont('Arial', 'B', 10);
		//Datos cabecera
		$pdf->Cell(12, 7, 'Cant.', 1, 0, 'C', 1);
		$pdf->Cell(18, 7, 'U.M.', 1, 0, 'C', 1);
		$pdf->Cell(100, 7, utf8_decode('Descripción'), 1, 0, 'C', 1);
		$pdf->Cell(25, 7, 'P.U.', 1, 0, 'C', 1);
		$pdf->Cell(25, 7, 'Importe', 1, 0, 'C', 1);
		$pdf->Ln();
		//Detalle
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial', '', 9);
		//Datos detalle 
		$montoTotal    = 0;
		$montoEnLetras = '-';
			
		if(mysqli_num_rows($resultSelectDetCotizacion) > 0) 
		{
			while ($rowSelectDetCotizacion = mysqli_fetch_assoc($resultSelectDetCotizacion)) 
			{
				$pdf->Cell(12, 6, $rowSelectDetCotizacion['cantidad_final'], 1, 0, 'C', 1);
				$pdf->Cell(18, 6, $rowSelectDetCotizacion['abr_unidad_medida'], 1, 0, 'C', 1);
				
				if ($rowSelectDetCotizacion['unidad_medida'] == 3) 
				{
					$pdf->Cell(100, 6, utf8_decode($rowSelectDetCotizacion['des_producto'].'-'.$rowSelectDetCotizacion['cantidad'].'-'.$rowSelectDetCotizacion['espesor'].'x'.$rowSelectDetCotizacion['ancho'].'x'.$rowSelectDetCotizacion['largo']), 1, 0, 'L', 1);
				} 
				else 
				{
					$pdf->Cell(100, 7, $rowSelectDetCotizacion['des_producto'], 1, 0, 'L', 1);
				}
				
				$pdf->Cell(25, 6, number_format($rowSelectDetCotizacion['precio_unitario'], 2, '.', ''), 1, 0, 'R', 1);
				$pdf->Cell(25, 6, number_format($rowSelectDetCotizacion['precio_total'], 2, '.', ''), 1, 0, 'R', 1);
				$pdf->Ln();
				
				$montoTotal += $rowSelectDetCotizacion['precio_total'];
			}
			
			$montoNeto = round($montoTotal/1.18, 2);
			$montoIGV  = $montoTotal - $montoNeto;
		
			$metodoReflex  = new ReflectionMethod('numerosALetras', 'to_word');
			$montoEnLetras = $metodoReflex->invoke(new numerosALetras(), $montoTotal, 'PEN');
			
			if ($rowSelectCotizacion['tip_comprobante'] != 5) 
			{
				$pdf->Cell(130, 6, ' ', 1, 0, 'L', 1);
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->Cell(25, 6, 'SUB TOTAL', 1, 0, 'R', 1);
				$pdf->SetFont('Arial', '', 9);
				$pdf->Cell(25, 6, 'S/ '.number_format($montoNeto, 2, '.', ''), 1, 0, 'R', 1);
				$pdf->Ln();
				$pdf->Cell(130, 6, ' ', 1, 0, 'L', 1);
				$pdf->SetFont('Arial', 'B', 9);
				$pdf->Cell(25, 6, 'IGV', 1, 0, 'R', 1);
				$pdf->SetFont('Arial', '', 9);
				$pdf->Cell(25, 6, 'S/ '.number_format($montoIGV, 2, '.', ''), 1, 0, 'R', 1);
				$pdf->Ln();
			}
			
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(130, 6, 'SON: '.$montoEnLetras, 1, 0, 'L', 1);
			$pdf->Cell(25, 6, 'TOTAL', 1, 0, 'R', 1);
			$pdf->Cell(25, 6, 'S/ '.number_format($montoTotal, 2, '.', ''), 1, 0, 'R', 1);
			$pdf->Ln();
		}
			
		//Detracciones 
		if($rowSelectCotizacion['aplica_detraccion'] == 'true') 
		{
			$pdf->SetFont('Arial', '', 9);
			$pdf->Cell(130, 6, '', 1, 0, '', 1);
			$pdf->Cell(25, 6, 'DETRAC. ' . $rowSelectCotizacion['por_detraccion'].'%' , 1, 0, 'R', 1);
			$pdf->Cell(25, 6, 'S/ '.number_format($rowSelectCotizacion['mon_detraccion'], 2, '.', ''), 1, 0, 'R', 1);
			$pdf->Ln();
		}
		
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(180, 8, utf8_decode('Condiciones generales de la venta'), 0, 1, '');
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(180, 6, utf8_decode('Validez de la Oferta: 10 DÍAS - Sujeto a stock'), 0, 1, '');
		/*$pdf->Cell(180, 6, utf8_decode('Entrega: En toda Lima Metropolitana'), 0, 1, '');*/
		$pdf->Cell(180, 6, utf8_decode('VENTA SUJETA A STOCK'), 0, 1, '');
		/*$pdf->Cell(0, 5, chr(149) . ' Madera roble', 0, 1, '');
		$pdf->Cell(0, 5, chr(149) . ' Medida comercial', 0, 1, '');
		$pdf->Cell(0, 5, chr(149) . ' Madera mojada', 0, 1, '');
		$pdf->Cell(0, 5, chr(149) . utf8_decode(' Entrega 1 día hábil'), 0, 1, '');
		$pdf->Cell(0, 5, utf8_decode('(No hay cambio ni devolución por defectos de la madera causados por su naturaleza)'), 0, 1, '');*/
		if (strlen(trim($rowSelectCotizacion['observaciones'])) > 0)
		{
			$pdf->SetFont('Arial', 'B', 10);
			$pdf->Cell(180, 7, utf8_decode('Observaciones'), 0, 1, '');
			$pdf->SetFont('Arial', '', 9);
			$pdf->MultiCell(180, 6, utf8_decode($rowSelectCotizacion['observaciones']), 0, 1, '');
		}
		$pdf->Ln();
		$pdf->Cell(180, 6, utf8_decode('Atentamente'), 0, 1, '');
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(180, 6, utf8_decode($rowSelectCotizacion['pri_nombre'] . ' ' . $rowSelectCotizacion['pri_apellido']), 0, 1, '');
		//$pdf->Cell(180, 6, utf8_decode('942902647'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('Representante de ventas'), 0, 1, '');
		$pdf->Ln();
		$pdf->SetFont('Arial', '', 9);
		$pdf->Cell(180, 6, utf8_decode('CUENTA CORRIENTE SOLES'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('BBVA: 0011-0832-01-00011917'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('BCP: 191-2514172-0-76'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('CCI BCP: 00219100251417207650'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('CUENTA DETRACCIÓN'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('BANCO DE LA NACIÓN: 00-062-092416'), 0, 1, '');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(180, 6, utf8_decode('COMERCIAL MADERERA EL SOL E.I.R.L.'), 0, 1, '');
		$pdf->Cell(180, 6, utf8_decode('RUC 20510094876'), 0, 1, '');
	}
}

$pdf->Output();

?>