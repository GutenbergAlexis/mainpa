<?php
	include('../../util/database.php');
	include('../../util/numerosALetras.php');
	include('../../x/detComprobante.php');
	session_start();
	
	if (isset($_SESSION['user'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Proceso de ventas - Mainpasoft</title>
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
                        <li class="active">
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Egresos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a class="active" href="compras.php">Compras</a>
                                </li>
                                <li>
                                    <a href="servicios.php">Servicios</a>
                                </li>
                                <li>
                                    <a href="suministros.php">Suministros</a>
                                </li>
                                <li>
                                    <a href="bancos.php">Bancos</a>
                                </li>
                                <li>
                                    <a href="personal.php">Personal</a>
                                </li>
                                <li>
                                    <a href="otros-egresos.php">Otros</a>
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
                        <li>
                            <a href="#"><i class="fa fa-sitemap fa-fw"></i> Mantenimiento<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../mantenimiento/usuarios.php">Usuarios</a>
                                </li>
                                <li>
                                    <a href="../mantenimiento/clientes.php">Clientes</a>
                                </li>
                                <li>
                                    <a href="../mantenimiento/productos.php">Productos</a>
                                </li>
                                <li>
                                    <a href="../mantenimiento/proveedores.php">Proveedores</a>
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
						<h1 class="page-header">Agregar compra</h1>
					</div>
<?php
		if ($_SESSION['perfil'] == 2 || $_SESSION['perfil'] == 3) 
		{
			$_SESSION['detCompra'] = array();
			$_SESSION['idDetCompra'] = 1;
?>
					<div class="panel-body">
						<h4>RUC del proveedor</h4>
						<div class="col-lg-3 col-mb-6 col-sm-12">
							<input class="form-control" type="text" id="ruc" name="ruc" placeholder="RUC" />
						</div>
						<div class="col-lg-2 col-mb-6 col-sm-12">
							<button type="button" class="btn btn-default" data-toggle="modal" onclick="buscarProveedor()" title="buscar">
							<i class="fa fa-search"></i> Buscar</button>
						</div>
					</div>
					<div class="panel-body">
						<h4>Datos del proveedor</h4>
						<div class="col-lg-8 col-mb-8 col-sm-12">
							<input type="hidden" id="id-proveedor" />
							<label for="razon-social">Razón Social</label>
							<input class="form-control" type="text" id="razon-social" name="razon-social" placeholder="Razón Social" readonly />
							<label for="direccion">Dirección</label>
							<input class="form-control" type="text" id="direccion" placeholder="Dirección" readonly />
							<label for="observaciones">Observaciones</label>
							<textarea class="form-control" rows="4" id="observaciones" name="observaciones" placeholder="Observaciones"></textarea>
						</div>
						<div class="col-lg-4 col-mb-4 col-sm-12">
							<label for="fecha-compra">Fecha de compra</label>
							<input class="form-control datepicker" id="fecha-compra" name="fecha-compra" data-provide="datepicker-inline" />
							<label for="medio-pago">Medio de pago</label>
							<select class="form-control selectpicker" id="medio-pago" data-live-search="true" title="-- seleccionar --">
								<?php 
									$query = 'SELECT * FROM parametros WHERE padre = 4';
									$result = mysqli_query($con, $query);
									while ($rows = mysqli_fetch_assoc($result)) {
								?>
								<option value='<?php echo $rows['codigo']; ?>' data-tokens='<?php echo $rows['codigo'].utf8_encode($rows['descripcion']); ?>'><?php echo $rows['codigo'].'. '.utf8_encode($rows['descripcion']); ?></option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
					<div class="panel-body">
						<button type="button" class="btn btn-success" data-toggle="modal" data-target="#mod-agregar-producto" title="agregar productos">Agregar productos</button>
					</div>
					<!-- /.col-lg-12 -->
					<div class="panel-body">
						<h4>Resumen de la compra</h4>
						<div id="detalle-compra"></div>
					</div>
					<div class="panel-body">
						<center>
							<button type="button" class="btn btn-primary" onclick="guardarCompra();" title="guardar">Guardar</button>
						</center>
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
	
	<!-- MODAL AGREGAR -->
	<div class="modal fade" id="mod-agregar-producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Agregar producto</h4>
				</div>
				<div class="modal-body">
					<div class="panel-body">
						<div class="col-lg-12 col-mb-12 col-sm-12">
							<label for="producto">Producto</label>
							<select class="form-control selectpicker" id="producto" data-live-search="true" onchange=
							"$('#codigo').val(this.options[this.selectedIndex].getAttribute('codigo'));
							 $('#descripcion').val(this.options[this.selectedIndex].getAttribute('descripcion'));
							 $('#un_medida').val(this.options[this.selectedIndex].getAttribute('un_medida'));" title="-- seleccionar --">
								<?php 
									$query = 'SELECT pro.*, par.descripcion AS un_medida, par.codigo AS cod_um FROM productos pro JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29';
									$result = mysqli_query($con, $query);
									while ($rows = mysqli_fetch_assoc($result)) {
								?>
								<option codigo='<?php echo $rows['codigo']; ?>' descripcion='<?php echo utf8_encode($rows['descripcion']); ?>' un_medida='<?php echo utf8_encode($rows['un_medida']); ?>' value='<?php echo $rows['id']; ?>' data-tokens='<?php echo $rows['codigo'].utf8_encode($rows['descripcion']); ?>'><?php echo $rows['codigo'].' - '.utf8_encode($rows['descripcion']); ?></option>
								<?php
									}
								?>
							</select>
						</div>
						<div class="col-lg-6 col-mb-12 col-sm-12">
							<label for="un_medida">Unidad de medida</label>
							<input class="form-control" type="text" id="un_medida" placeholder="Unidad de medida" disabled />
						</div>
						<div class="col-lg-6 col-mb-12 col-sm-12">
							<label for="cantidad">Cantidad</label>
							<input type="text" id="cantidad" placeholder="Cantidad" class="form-control" />
						</div>
						<div class="col-lg-6 col-mb-12 col-sm-12">
							<label for="costo">Costo</label>
							<input type="text" id="costo" placeholder="Costo" class="form-control" />
						</div>
						<input type="hidden" id="codigo">
						<input type="hidden" id="descripcion">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="agregarDetCompra()">Agregar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- MODAL ACTUALIZAR PRODUCTO -->
	<div class="modal fade" id="mod-actualizar-producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	</div>
	
	<!-- MODAL PROVEEDOR -->
	<div class="modal fade" id="buscar-proveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Datos del proveedor</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="cli-nombre-razon-social">RUC</label>
						<input type="text" id="prv-ruc" class="form-control" readonly />
					</div>
					<div class="form-group">
						<label for="prv-razon-social">Nombre/Razón Social</label>
						<input type="text" id="prv-razon-social" class="form-control" readonly />
					</div>
					<div class="form-group">
						<label for="prv-direccion">Dirección</label>
						<input type="text" id="prv-direccion" class="form-control" readonly />
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" onclick="agregarProveedor()">Agregar</button>
					<input type="hidden" id="prv-id">
				</div>
			</div>
		</div>
	</div>
	
	<!-- MODAL NO PROVEEDOR -->
	<div class="modal fade" id="buscar-no-proveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">No se encontró el proveedor</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>No se encontró el proveedor</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>
    <!-- /#wrapper -->
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
			format: 'dd/mm/yyyy',
            firstDay: 1, 
			todayHighlight: true
		}).datepicker("setDate", new Date());
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