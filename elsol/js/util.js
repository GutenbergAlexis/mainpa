$('.datepicker').datepicker({
	format: 'dd/mm/yyyy',
    firstDay: 1, 
	todayHighlight: true
});
function mostrarTipo(codigo) {
	if (codigo == 3) {
		$('#esp').show();
		$('#espesor').show();
		$('#anc').show();
		$('#ancho').show();
		$('#lar').show();
		$('#largo').show();
	} else {
		$('#esp').hide();
		$('#espesor').hide();
		$('#anc').hide();
		$('#ancho').hide();
		$('#lar').hide();
		$('#largo').hide();
	}
};

var timeout;
document.onmousemove = function(){ 
    clearTimeout(timeout); 
    contadorSesion(); //aqui cargamos la funcion de inactividad
};
function contadorSesion() {
    timeout = setTimeout(function () { 
        $.confirm({
            title: 'Alerta',
            content: 'La sesión expiró por inactividad.',
            autoClose: 'expirar|1', //cuanto tiempo necesitamos para cerrar la sesion automaticamente
            type: 'red',
            icon: 'fa fa-spinner fa-spin',
            buttons: {
                expirar: {
                    text: 'Ir a la pantalla inicial',
                    btnClass: 'btn-red',
                    action: function () {
                        window.location.href = '../login.php';
                    }
                }
            }
        });
    }, 300000); //3 segundos para no demorar tanto 
};

document.addEventListener('DOMContentLoaded', function() {
    localStorage.removeItem('cuotasTemporales');
    localStorage.removeItem('productosTemporales');
    console.log('LocalStorage de cuotas y productos inicializado');
});

document.getElementById('condicion-pago').addEventListener('change', function() {
    if (this.value == 2) {
        document.getElementById('div-medio-pago').style.display = 'none';
        document.getElementById('div-cuotas').style.display = '';
        document.getElementById('div-agregar-cuotas').style.display = '';
        // Inicializa la vista de cuotas
        if (typeof renderizarCuotas === 'function') {
            renderizarCuotas();
        }
    } else {
        document.getElementById('div-medio-pago').style.display = '';
        document.getElementById('div-cuotas').style.display = 'none';
        document.getElementById('div-agregar-cuotas').style.display = 'none';
        // Limpia las cuotas si existe la función
        if (typeof limpiarCuotas === 'function') {
            limpiarCuotas();
        }
    }
    console.log('¿Es pago al contado? ', this.value);
});

//Detracciones - inicio - 2024.07.21

document.getElementById('tipo-comprobante').addEventListener('change', function() {
    if (this.value == 1) {
        document.getElementById('div-detracciones').style.display = '';
    } else {
        document.getElementById('div-detracciones').style.display = 'none';
    }
    console.log('¿Es factura y puede aplicar detracción?: ', this.value);
});
var montoTotal;
document.getElementById('aplica-detraccion').addEventListener('change', function() {
    montoTotal = document.getElementById('monto-total').value;
    if (this.value == 1) {
        document.getElementById('div-detalle-detracciones').style.display = '';
    } else {
        document.getElementById('div-detalle-detracciones').style.display = 'none';
        document.getElementById('bien-servicio-detraccion').value = '0';
        document.getElementById('medio-pago-detraccion').value = '0';
        document.getElementById('porcentaje-detraccion-visual').value = '';
        document.getElementById('porcentaje-detraccion').value = '';
        document.getElementById('monto-detraccion').value = '';
    }
    console.log('¿Aplica detracción? ', this.value);
});
document.getElementById('bien-servicio-detraccion').addEventListener('change', function() {
    montoTotal = document.getElementById('monto-total').value;
    if (this.value == 8) {
        document.getElementById('porcentaje-detraccion-visual').value = '4%';
        document.getElementById('porcentaje-detraccion').value = '4';
        document.getElementById('monto-detraccion').value = (0.04 * montoTotal).toFixed(2);
    } else if (this.value == 20) {
        document.getElementById('porcentaje-detraccion-visual').value = '12%';
        document.getElementById('porcentaje-detraccion').value = '12';
        document.getElementById('monto-detraccion').value = (0.12 * montoTotal).toFixed(2);
    }
    console.log('¿Es factura y puede aplicar detracción?: ', this.value);
});

//Detracciones - fin - 2024.07.21