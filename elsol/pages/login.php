<?php
	include('../util/database.php');
	session_start();
 
    if (!empty($_POST)) 
	{
        $userError  = null;
        $passError  = null;
        $loginError = null;
        
        // keep track post values
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        
        // validate input
        $valid = true;
		
        if (empty($user)) 
		{
            $userError = 'Por favor ingrese su usuario.';
            $valid = false;
        }
		
        if (empty($pass)) 
		{
            $passError = 'Por favor ingrese su contraseña';
            $valid = false;
        }
		
        // insert data
        if ($valid) 
		{
			$query = "SELECT usu.perfil perfil, par.descripcion desPerfil FROM usuarios usu
						JOIN parametros par ON par.codigo = usu.perfil AND par.padre = 15
						WHERE usu.user = '$user' AND usu.pass = '$pass'";

			if (!$result = mysqli_query($con, $query)) 
			{
				exit(mysqli_error($con));
			}

			if (mysqli_num_rows($result) == 1) 
			{
				$_SESSION['user'] = $user;
				while ($rows = mysqli_fetch_assoc($result)) 
				{
					$_SESSION['perfil']    = $rows['perfil'];
					$_SESSION['desPerfil'] = $rows['desPerfil'];
				}
				
				/*if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 2) Lenin Montenegro 
				{*/
					header("Location: ingresos/proceso-ventas.php");
				/*} 
				else if ($_SESSION['perfil'] == 3) 
				{
					header("Location: usuarios.php");
				}*/
			}
			else
			{
				$loginError = 'Usuario o contraseña errados, por favor verifique sus datos y vuelva a intentarlo.';
			}	
		}
    }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="euc-jp">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mainpasoft</title>
	<link rel="shortcut icon" href="../img/favicon.ico">
    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-body">
						<div class="text-center" style="height:120px;line-height:120px;">
						  <img src="../img/logo.jpg" style="vertical-align:middle;">
						</div>
						<form role="form" action="login.php" method="post">
                            <fieldset>
								<div class="form-group">
									<input class="form-control" name="user" type="text" placeholder="Usuario" value="<?php echo !empty($user)?$user:''; ?>" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus>
									<?php if (!empty($userError)): ?>
									<span class="help-inline"><?php echo $userError;?></span>
									<?php endif; ?>
								</div>
								<div class="form-group">
									<input class="form-control" name="pass" type="password" placeholder="Contraseña" value="<?php echo !empty($pass)?$pass:''; ?>">
									<?php if (!empty($passError)): ?>
									<span class="help-inline"><?php echo $passError;?></span>
									<?php endif; ?>
								</div>
								<?php if (!empty($loginError)): ?>
								<span class="help-inline"><?php echo $loginError;?></span>
								<?php endif; ?>
								<button type="submit" class="btn btn-lg btn-primary btn-block">Ingresar</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>
	<!--script type="text/javascript"> 
		var device = navigator.userAgent;
		if (device.match(/Iphone/i) || device.match(/Ipod/i) || device.match(/Android/i) || device.match(/J2ME/i) || device.match(/BlackBerry/i) || device.match(/iPhone|iPad|iPod/i) || device.match(/Opera Mini/i) || device.match(/IEMobile/i) || device.match(/Mobile/i) || device.match(/Windows Phone/i) || device.match(/windows mobile/i) || device.match(/windows ce/i) || device.match(/webOS/i) || device.match(/palm/i) || device.match(/bada/i) || device.match(/series60/i) || device.match(/nokia/i) || device.match(/symbian/i) || device.match(/HTC/i))
		{
			window.location = "http://www.mainpa.com";
		}
	</script-->
</body>
</html>