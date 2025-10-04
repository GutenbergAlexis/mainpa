<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script languaje="javascript">
function funcion_javascript(){
   alert ("Esto es javascript");
   window.location.href="login.php";
}
function contadorSesion() {
    timeout = setTimeout(function () {
        $.confirm({
            title: 'Alerta',
            content: 'La sesión expiró por inactividad.',
            autoClose: 'expirar|5000',//cuanto tiempo necesitamos para cerrar la sess automaticamente
            type: 'red',
            icon: 'fa fa-spinner fa-spin',
            buttons: {
                expirar: {
                    text: 'Ir a la pantalla inicial',
                    btnClass: 'btn-red',
                    action: function () {
                        window.location.href = "login.php";
                    }
                }
            }
        });
    }, 5000);//3 segundos para no demorar tanto 
}
</script>
</head>
<body>
<? 
?>
<script languaje="javascript">
var timeout;
document.onmousemove = function(){ 
    clearTimeout(timeout); 
    contadorSesion(); //aqui cargamos la funcion de inactividad
} 
</script>
<? 
?>
</body>
</html>