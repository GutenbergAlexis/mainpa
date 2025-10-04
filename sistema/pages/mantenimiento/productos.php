<?php
	date_default_timezone_set('America/Lima');

	include('../../util/database.php');
	include('../../util/numerosALetras.php');
	session_start();
    
    // Configuración de paginación
    $productos_por_pagina = 50;
    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($pagina_actual - 1) * $productos_por_pagina;
	
	if (isset($_SESSION['user'])) 
	{
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Productos - Mainpasoft</title>
	<link rel="shortcut icon" href="../../img/favicon.ico">
    <!-- Bootstrap Core CSS -->
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../vendor/bootstrap/css/bootstrap-datepicker3.standalone.min.css" rel="stylesheet">
	<!-- Latest compiled and minified CSS -->
	<link href="../../vendor/bootstrap/css/bootstrap-select.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../../estilos/estilos.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="../../img/logo.jpg" width="230"></a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-top-links navbar-right">
				<li id="usuario">Bienvenido <?php echo strtolower($_SESSION['user']); ?> - <?php echo strtolower($_SESSION['desPerfil']); ?></li>
				<li class="divider"></li>
				<li><a href="../logout.php"><i class="fa fa-sign-out fa-fw"></i> Salir</a>
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Ingresos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../ingresos/proceso-ventas.php">Proceso de ventas</a>
                                </li>
                                <li>
                                    <a href="../ingresos/buscar-cotizaciones.php">Buscar cotizaciones</a>
                                </li>
                                <li>
                                    <a href="../ingresos/buscar-ventas.php">Buscar ventas</a>
                                </li>
                                <li>
                                    <a href="../ingresos/otros-ingresos.php">Otros</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Egresos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../egresos/compras.php">Compras</a>
                                </li>
                                <li>
                                    <a href="../egresos/servicios.php">Servicios</a>
                                </li>
                                <li>
                                    <a href="../egresos/suministros.php">Suministros</a>
                                </li>
                                <li>
                                    <a href="../egresos/bancos.php">Bancos</a>
                                </li>
                                <li>
                                    <a href="../egresos/personal.php">Personal</a>
                                </li>
                                <li>
                                    <a href="../egresos/otros-egresos.php">Otros</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Reportes<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../reportes/detalle-diario.php">Detalle diario</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-ventas-producto.php">Reporte de ventas por producto</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-ventas-general.php">Reporte de ventas general</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-compras-producto.php">Reporte de compras por producto</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-compras-general.php">Reporte de compras general</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-ingresos-mensual.php">Reporte de ingresos mensual</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-egresos-mensual.php">Reporte de egresos mensual</a>
                                </li>
                                <li>
                                    <a href="../reportes/reporte-resumen-mensual.php">Reporte resumen mensual</a>
                                </li>
                            </ul>
                        </li>
                        <li class="active">
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Mantenimiento<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="usuarios.php">Usuarios</a>
                                </li>
                                <li>
                                    <a href="clientes.php">Clientes</a>
                                </li>
                                <li>
                                    <a class="active" href="productos.php">Productos</a>
                                </li>
                                <li>
                                    <a href="proveedores.php">Proveedores</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Productos</h1>
					</div>
<?php
		if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 2 || $_SESSION['perfil'] == 3) 
		{
?>
                    <div class="col-lg-3 col-mb-6 col-sm-12">
                        <input class="form-control" type="text" id="producto" name="producto" placeholder="Nombre del producto" />
                    </div>
					<div class="panel-body">
						<button type="button" class="btn btn-custom-buscar" data-toggle="modal" onclick="buscarProducto()" title="buscar"><i class="fa fa-search"></i> Buscar</button>
					</div>
<?php   
			if ($_SESSION['perfil'] == 3) 
			{
?>
					<div class="panel-body col-lg-12 col-mb-12 col-sm-12">
						<button type="button" class="btn btn-custom-agregar" data-toggle="modal" data-target="#mod-agregar" title="agregar"><i class="fa fa-plus"></i> Agregar</button>
					</div>
<?php
			}
?>
					<div class="panel-body col-lg-12 col-mb-12 col-sm-12">
						<div id="productos" class="centrar"></div>
					</div>
<?php
		} 
		else 
		{
?>
					<div class="panel-body">
						<h4>No tiene permisos para ver esta sección.</h4>
					</div>
<?php
		}
?>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
	
	<!-- MODAL AGREGAR -->
	<div class="modal fade" id="mod-agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Agregar producto</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos del producto</h4>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="descripcion">Descripción</label>
								<input class="form-control" type="text" id="descripcion" name="descripcion" placeholder="Descripción" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="codigo">Código</label>
								<input class="form-control" type="text" id="codigo" name="codigo" placeholder="Código" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="unidad-medida">Unidad de medida</label>
								<select class="form-control selectpicker" id="unidad-medida" data-live-search="true" title="-- seleccionar --">
									<?php 
										$query = 'SELECT * FROM parametros WHERE padre = 29';
										$result = mysqli_query($con, $query);
										while ($rows = mysqli_fetch_assoc($result)) {
											echo '<option value=' .$rows['codigo']. ' data-tokens=' .utf8_encode($rows['descripcion']). '>' .utf8_encode($rows['descripcion']).' - '.utf8_encode($rows['abreviatura']). '</option>';
										}
									?>
								</select>
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="precio">Precio</label>
								<input class="form-control" type="text" id="precio" name="precio" placeholder="Precio" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="guardarProducto();" title="guardar">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- MODAL ACTUALIZAR -->
	<div class="modal fade" id="mod-actualizar-producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	</div>
    <!-- jQuery -->
    <script src="../../vendor/jquery/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap-datepicker.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="../../vendor/bootstrap/js/bootstrap-select.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../../vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../../dist/js/sb-admin-2.js"></script>
	<!-- Custom JS file -->
	<script type="text/javascript" src="../../js/script.js"></script>
	<script type="text/javascript">
		$('.datepicker').datepicker({
			format: 'dd/mm/yyyy'
		});
	</script>

<script>
// Función para cargar productos con paginación
function cargarProductos(pagina = 1) {
    $.ajax({
        url: '../../ajax/mantenimiento/leerProductos.php',
        type: 'GET',
        data: { pagina: pagina },
        success: function(response) {
            $('#productos').html(response);
        },
        error: function() {
            $('#productos').html('<p>Error al cargar los productos.</p>');
        }
    });
}

// Cargar productos al iniciar
$(document).ready(function() {
    cargarProductos(1);
});

// Manejar clic en paginación (delegación de eventos)
$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var pagina = $(this).attr('href').split('pagina=')[1];
    cargarProductos(pagina);
    
    // Opcional: Actualizar URL en el navegador
    history.pushState(null, null, '?pagina=' + pagina);
});
</script>

</body>
</html>
<?php
	} 
	else 
	{
		header('Location: ../login.php');
	}
?>