<?php
	date_default_timezone_set('America/Lima');

	include('../../util/database.php');
	include('../../util/numerosALetras.php');
	session_start();
	
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
    <title>Usuarios - Mainpasoft</title>
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
                                    <a class="active" href="#">Usuarios</a>
                                </li>
                                <li>
                                    <a href="clientes.php">Clientes</a>
                                </li>
                                <li>
                                    <a href="productos.php">Productos</a>
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
						<h1 class="page-header">Usuarios</h1>
					</div>
<?php
		if ($_SESSION['perfil'] == 3) 
		{
?>
					<div class="panel-body col-lg-12 col-mb-12 col-sm-12">
						<button type="button" class="btn btn-success" data-toggle="modal" data-target="#mod-agregar" title="agregar">Agregar</button>
					</div>
					<div class="panel-body col-lg-12 col-mb-12 col-sm-12">
						<div id="usuarios"></div>
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
						<h4 class="modal-title" id="myModalLabel">Agregar usuario</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos de usuario</h4>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="numero-documento">Número de documento</label>
								<input class="form-control" type="text" id="numero-documento" name="numero-documento" placeholder="Número de documento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
						</div>
						<div class="panel-body">
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="primer-nombre">Primer nombre</label>
								<input class="form-control" type="text" id="primer-nombre" name="primer-nombre" placeholder="Primer nombre" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="segundo-nombre">Segundo nombre</label>
								<input class="form-control" type="text" id="segundo-nombre" name="segundo-nombre" placeholder="Segundo nombre" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="primer-apellido">Primer apellido</label>
								<input class="form-control" type="text" id="primer-apellido" name="primer-apellido" placeholder="Primer apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="segundo-apellido">Segundo apellido</label>
								<input class="form-control" type="text" id="segundo-apellido" name="segundo-apellido" placeholder="Segundo apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="direccion">Dirección</label>
								<input class="form-control" type="text" id="direccion" name="direccion" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="telefono">Teléfono</label>
								<input class="form-control" type="text" id="telefono" name="telefono" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="celular">Celular</label>
								<input class="form-control" type="text" id="celular" name="celular" placeholder="Celular" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="correo">Correo electrónico</label>
								<input class="form-control" type="text" id="correo" name="correo" placeholder="Correo electrónico" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="user">Usuario</label>
								<input class="form-control" type="text" id="user" name="user" placeholder="Usuario" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="pass">Contraseña</label>
								<input class="form-control" type="password" id="pass" name="pass" placeholder="Contraseña" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="perfil">Perfil</label>
								<select class="form-control selectpicker" id="perfil" name="perfil" data-live-search="true" title="-- seleccionar --" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
									<?php 
										$query = 'SELECT * FROM parametros WHERE padre = 15';
										$result = mysqli_query($con, $query);
										while ($rows = mysqli_fetch_assoc($result)) {
											echo '<option value=' .$rows['codigo']. ' data-tokens=' .$rows['codigo']. '.' .$rows['descripcion']. '>' .$rows['codigo']. '. ' .$rows['descripcion']. '</option>';
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="guardarUsuario();" title="guardar">Guardar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- MODAL ACTUALIZAR -->
	<div class="modal fade" id="mod-actualizar-usuario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
</body>
</html>
<?php
	} 
	else 
	{
		header('Location: ../login.php');
	}
?>