$(document).ready(function() {
 leerDetComprobante();
 leerDetCompra();
 leerOtrosIngresos();
 leerUsuarios();
 leerClientes();
 leerProductos();
 leerProveedores();
 leerCompras();
 leerComprobantes();
 buscarVentas();
 leerCotizaciones();
 //AGREGAR CUOTAS  
 leerCuota();
});

function buscarVentas() {
 var codVenta       = document.getElementById('codigo-venta')       ? document.getElementById('codigo-venta').value       : '';
 var numDocumento   = document.getElementById('numero-documento')   ? document.getElementById('numero-documento').value   : '';
 var fechaDesde     = document.getElementById('fecha-desde')        ? document.getElementById('fecha-desde').value        : '';
 var fechaHasta     = document.getElementById('fecha-hasta')        ? document.getElementById('fecha-hasta').value        : '';
 var numComprobante = document.getElementById('numero-comprobante') ? document.getElementById('numero-comprobante').value : '';
 
 $.post("../../ajax/ingresos/buscarVentas.php", {
  codVenta       : codVenta, 
  numDocumento   : numDocumento, 
  fechaDesde     : fechaDesde, 
  fechaHasta     : fechaHasta, 
  numComprobante : numComprobante
 }, function(data, status) {
  if (document.getElementById('ventas')) {
   document.getElementById('ventas').innerHTML = data;
  }
 });
}

function buscarCliente() {
 var numDocumento = document.getElementById('numero-documento').value;
 
 $.post("../../ajax/buscarCliente.php", {
   numDocumento : numDocumento
 }, function(data, status) {
  $("#buscar-cliente").html(data);
 });
 $("#buscar-cliente").modal("show");
}

function agregarCliente() {
 var idCliente = document.getElementById('cli-id-cliente').value;
 var nombre    = document.getElementById('cli-nombre-razon-social').value;
 var direccion = document.getElementById('cli-direccion').value;

 /*$.post("../../pages/ingresos/proceso-ventas.php", {*/
 $.post("", {
	 
 }, function(data, status) {
  $("#buscar-cliente").modal("hide");
  document.getElementById('id-cliente').value = idCliente;
  document.getElementById('nombre').value     = nombre;
  document.getElementById('direccion').value  = direccion;
 });
}

function agregarNoCliente() {
 $.post("", {
     
 }, function(data, status) {
  $("#cli-dni").prop("checked", false);
  $("#cli-ruc").prop("checked", false);
  $("#tipo-documento-final").val("");
  $("#nombre").val("");
  $("#direccion").val("");
 });
}

// Inicialización del estado de cuotas
let cuotas = JSON.parse(localStorage.getItem('cuotasTemporales')) || [];
let idCuota = cuotas.length > 0 ? Math.max(...cuotas.map(c => c.id)) + 1 : 1;

function agregarCuota() {
 var fechaCuota = $("#fecha-cuota").val();
 var montoCuota = parseFloat($("#monto-cuota").val());
 
 if (fechaCuota.length === 0 || isNaN(montoCuota)) {
  alert('Se debe ingresar la fecha y el monto de la cuota.');
 } else if (montoCuota <= 0) {
  alert('El monto de la cuota debe ser mayor a cero.');
 } else {
  const nuevaCuota = {
    id: idCuota++,
    fecha: fechaCuota,
    monto: montoCuota
  };
  
  cuotas.push(nuevaCuota);
  localStorage.setItem('cuotasTemporales', JSON.stringify(cuotas));
  
  renderizarCuotas();
   $("#mod-agregar-cuota").modal("hide");
  $("#fecha-cuota").val("");
  $("#monto-cuota").val("");
 }
}

function renderizarCuotas() {
  // Generar HTML para mostrar las cuotas
  let html = `<table class="table table-striped table-bordered">
              <thead>
                  <tr>
                      <th width="10%">N°</th>
                      <th width="35%">Fecha</th>
                      <th width="30%">Monto</th>
                      <th width="25%">&nbsp;</th>
                  </tr>
              </thead>
              <tbody>`;
  
  if (cuotas.length === 0) {
      html += `<tr><td colspan="4">Aún no se han agregado cuotas.</td></tr>`;
  } else {
      let montoTotal = 0;
      cuotas.forEach((cuota, index) => {
          montoTotal += cuota.monto;
          
          // Formatear la fecha para mostrarla en formato YYYY-MM-DD
          const fechaPartes = cuota.fecha.split('-');
          const fechaFormateada = fechaPartes.length === 3 ? 
              `${fechaPartes[0]}-${fechaPartes[1]}-${fechaPartes[2]}` : cuota.fecha;
          
          html += `<tr>
                      <td>${index + 1}</td>
                      <td style="text-align:right;">${fechaFormateada}</td>
                      <td style="text-align:right;">${cuota.monto.toFixed(2)}</td>
                      <td>
                          <label onclick="editarCuota(${cuota.id})" class="btn btn-default btn-circle" 
                                 data-toggle="tooltip" title="editar">
                              <i class="fa fa-pencil"></i>
                          </label>
                          <label onclick="eliminarCuota(${cuota.id})" class="btn btn-danger btn-circle" 
                                 data-toggle="tooltip" title="eliminar">
                              <i class="fa fa-times"></i>
                          </label>
                      </td>
                  </tr>`;
      });
      
      html += `</tbody>
              <tfoot>
                  <tr>
                      <td>&nbsp;</td>
                      <td style="text-align:right;font-weight:bold;">Monto total</td>
                      <td style="text-align:right;font-weight:bold;">${montoTotal.toFixed(2)}</td>
                      <td>&nbsp;</td>
                  </tr>
                  <input type="hidden" id="monto-total-cuotas" name="monto-total-cuotas" value="${montoTotal.toFixed(2)}">
              </tfoot>`;
  }
  
  html += `</table>`;
  $("#detalle-cuotas").html(html);
}

function editarCuota(id) {
  const cuota = cuotas.find(c => c.id === id);
  if (cuota) {
    $("#fecha-cuota").val(cuota.fecha);
    $("#monto-cuota").val(cuota.monto.toFixed(2));
    
    // Guardamos el ID de la cuota en edición temporalmente
    $("#mod-agregar-cuota").data("editando-id", id);
    
    // Cambiamos el texto del botón
    $("#btn-agregar-cuota").text("Actualizar").off("click").on("click", function() {
      actualizarCuota();
    });
    
    $("#mod-agregar-cuota").modal("show");
  }
}

function actualizarCuota() {
  const id = $("#mod-agregar-cuota").data("editando-id");
  const fechaCuota = $("#fecha-cuota").val();
  const montoCuota = parseFloat($("#monto-cuota").val());
  
  if (fechaCuota.length === 0 || isNaN(montoCuota)) {
    alert('Se debe ingresar la fecha y el monto de la cuota.');
    return;
  }
  
  const index = cuotas.findIndex(c => c.id === id);
  if (index !== -1) {
    cuotas[index].fecha = fechaCuota;
    cuotas[index].monto = montoCuota;
    localStorage.setItem('cuotasTemporales', JSON.stringify(cuotas));
    
    renderizarCuotas();
    
    // Resetear el modal
   $("#fecha-cuota").val("");
   $("#monto-cuota").val("");
    $("#mod-agregar-cuota").data("editando-id", null);
    $("#btn-agregar-cuota").text("Agregar").off("click").on("click", function() {
      agregarCuota();
    });
    
    $("#mod-agregar-cuota").modal("hide");
  }
}

function eliminarCuota(id) {
 // Prevenir cualquier posible propagación de evento
 if (event) {
  event.preventDefault();
  event.stopPropagation();
 }
 
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar la cuota?");

 if (confirmacion === true) {
  $.post("../../ajax/eliminarCuota.php", {
   idCuota : id
  }, function(data, status) {
   var response = JSON.parse(data);
   if (response.success) {
    // Actualizar la tabla de cuotas
    $("#act-detalle-cuotas tbody").html(response.html);
    // Actualizar el monto total de cuotas
    $("#monto-total-cuotas").val(response.montoTotalRaw);
    
    // Actualizar los montos del comprobante
    var montoTotal = parseFloat(response.montoTotalRaw);
    var montoNeto = Math.round((montoTotal / 1.18) * 100) / 100;
    var montoIGV = Math.round((montoTotal - montoNeto) * 100) / 100;
    
    // Actualizar los montos en la tabla
    $("td:contains('Monto neto') + td").text(montoNeto.toFixed(2));
    $("td:contains('IGV') + td").text(montoIGV.toFixed(2));
    $("td:contains('Monto total (S/)') + td").text(montoTotal.toFixed(2));
    $("#monto-total").val(montoTotal);
   }
  });
 }
 
 return false;
}

function limpiarCuotas() {
  cuotas = [];
  idCuota = 1;
  localStorage.removeItem('cuotasTemporales');
  renderizarCuotas();
}

function leerCuota() {
  renderizarCuotas();
}

// Inicialización del estado de productos
let productos = JSON.parse(localStorage.getItem('productosTemporales')) || [];
let idProductoTemp = productos.length > 0 ? Math.max(...productos.map(p => p.id)) + 1 : 1;

function agregarDetComprobante() {
 var idProducto  = $("#producto").val();
 var unMedida    = $("#un_medida").val();
 var codUM       = $("#cod_um").val();
 var codigo      = $("#codigo").val();
 var descripcion = $("#descripcion").val();
 var cantidad    = $("#cantidad").val();
 var espesor     = $("#espesor").val() || "0";
 var ancho       = $("#ancho").val() || "0";
 var largo       = $("#largo").val() || "0";
 var precio      = $("#precio").val();

 if (!idProducto || !precio || !cantidad || (cantidad <= 0)) {
   alert('Debe completar todos los campos requeridos con valores válidos.');
   return;
 }

 if ((idProducto != 247) && (precio <= 0)) {
   alert('El precio del producto debe ser mayor a S/ 0.00.');
   return;
 }

 // Crear objeto de producto
 const nuevoProducto = {
   id: idProductoTemp++,
   idProducto: idProducto,
   codigo: codigo,
   descripcion: descripcion,
   unMedida: unMedida,
   codUM: codUM,
   cantidad: cantidad,
   espesor: espesor,
   ancho: ancho,
   largo: largo,
   precio: precio,
   cantidadFinal: calcularCantidadFinal(cantidad, espesor, ancho, largo, codUM)
 };
 
 // Agregar el producto al array
 productos.push(nuevoProducto);
 
 // Guardar en localStorage
 localStorage.setItem('productosTemporales', JSON.stringify(productos));
 
 // Actualizar la vista
 renderizarProductos();
 
 // Cerrar modal y limpiar campos
 $("#mod-agregar-producto").modal("hide");
 $("#producto").val("");
 $("#cantidad").val("");
 $("#precio").val("");
 $("#un_medida").val("");
 $("#espesor").val("");
 $("#ancho").val("");
 $("#largo").val("");
 $("#codigo").val("");
 $("#descripcion").val("");
 $("#producto").selectpicker("refresh");
}

// Función auxiliar para calcular la cantidad final según la unidad de medida
function calcularCantidadFinal(cantidad, espesor, ancho, largo, codUM) {
  if (codUM == 3) { // Planchas o material que use dimensiones
    return (parseFloat(cantidad) * parseFloat(espesor) * parseFloat(ancho) * parseFloat(largo) / 12).toFixed(2);
  } else {
    return parseFloat(cantidad).toFixed(2);
  }
}

function renderizarProductos() {
  if (productos.length === 0) {
    $("#detalle-comprobante").html('<p>Aún no se han agregado productos.</p>');
    return;
  }

  let html = `<table class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th class="tabla-cabecera" width="5%">N°</th>
                  <th class="tabla-cabecera" width="10%">Código</th>
                  <th class="tabla-cabecera" width="25%">Descripción</th>
                  <th class="tabla-cabecera" width="10%">Unidad de medida</th>
                  <th class="tabla-cabecera" width="10%">Cantidad</th>
                  <th class="tabla-cabecera" width="10%">Precio</th>
                  <th class="tabla-cabecera" width="15%">Subtotal</th>
                  <th class="tabla-cabecera" width="15%">&nbsp;</th>
                </tr>
              </thead>
              <tbody>`;
  
  let montoTotalAcumulado = 0;
  
  productos.forEach((producto, index) => {
    const cantidadFinal = producto.cantidadFinal;
    const precioFinal = producto.precio;
    const subtotal = (precioFinal * cantidadFinal).toFixed(2);
    montoTotalAcumulado = Number(montoTotalAcumulado) + Number(subtotal);
    
    // Crear la descripción incluyendo dimensiones para productos tipo 3
    let descripcionMostrar = producto.descripcion;
    if (producto.codUM == 3) {
      descripcionMostrar = `${producto.descripcion} - ${producto.cantidad} - ${producto.espesor}X${producto.ancho}X${producto.largo}`;
    }
    
    html += `<tr>
              <td class="centrar tabla-detalle">${index + 1}</td>
              <td class="centrar tabla-detalle">${producto.codigo}</td>
              <td class="izquierda tabla-detalle">${descripcionMostrar}</td>
              <td class="centrar tabla-detalle">${producto.unMedida}</td>
              <td class="derecha tabla-detalle">${cantidadFinal}</td>
              <td class="derecha tabla-detalle">${precioFinal}</td>
              <td class="derecha tabla-detalle">${subtotal}</td>
              <td class="centrar tabla-detalle">
                <label onclick="cargarModalEditarProducto(${producto.id})" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar">
                  <i class="fa fa-pencil"></i>
                </label>
                <label onclick="eliminarDetComprobante(${producto.id})" class="btn btn-danger btn-circle" data-toggle="tooltip" title="eliminar">
                  <i class="fa fa-times"></i>
                </label>
              </td>
            </tr>`;
  });
  
  // Calcular montos finales correctamente
  let montoTotal = (montoTotalAcumulado).toFixed(2);
  let montoNeto = (montoTotal / 1.18).toFixed(2);
  let montoIGV = (montoTotal - montoNeto).toFixed(2);
  
  // Redondear a 2 decimales
  /*montoNeto = Math.round(montoNeto * 100) / 100;
  montoIGV = Math.round(montoIGV * 100) / 100;*/
  
  html += `</tbody>
          <tfoot>
            <tr>
              <td colspan="5">&nbsp;</td>
              <td style="text-align:right;font-weight:bold;">Monto neto</td>
              <td style="text-align:right;font-weight:bold;">${montoNeto}</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="5">&nbsp;</td>
              <td style="text-align:right;font-weight:bold;">IGV</td>
              <td style="text-align:right;font-weight:bold;">${montoIGV}</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td colspan="5">&nbsp;</td>
              <td style="text-align:right;font-weight:bold;">Monto total</td>
              <td style="text-align:right;font-weight:bold;">${montoTotal}</td>
              <td>&nbsp;</td>
            </tr>
          </tfoot>
          </table>
          <input type="hidden" id="monto-neto" name="monto-neto" value="${montoNeto}">
          <input type="hidden" id="monto-igv" name="monto-igv" value="${montoIGV}">
          <input type="hidden" id="monto-total" name="monto-total" value="${montoTotal}">`;
  
  $("#detalle-comprobante").html(html);
}

function eliminarDetComprobante(id) {
  var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el producto?");

  if (confirmacion == true) {
    productos = productos.filter(producto => producto.id !== id);
    localStorage.setItem('productosTemporales', JSON.stringify(productos));
    renderizarProductos();
  }
}

function cargarModalEditarProducto(id) {
  const producto = productos.find(p => p.id == id);
  if (!producto) return;

  let modalHtml = `
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Actualizar</h4>
        </div>
        <div class="modal-body">
          <div class="panel-body">
            <div class="col-lg-12 col-mb-12 col-sm-12">
              <label for="act-producto">Producto</label>
              <input type="text" id="act-producto" placeholder="Producto" class="form-control" value="${producto.descripcion}" readonly />
            </div>
            <div class="col-lg-6 col-mb-6 col-sm-12">
              <label for="act-unidad-medida">Uni. Med.</label>
              <input type="text" id="act-unidad-medida" placeholder="Unidad de medida" class="form-control" value="${producto.unMedida}" readonly />
            </div>`;

  // Agregar campos de dimensiones si es que el producto lo requiere
  if (producto.codUM == 3) {
    modalHtml += `
            <div class="col-lg-6 col-mb-6 col-sm-12">
              <label for="act-cantidad">Cantidad</label>
              <input type="number" id="act-cantidad" placeholder="Cantidad" class="form-control" value="${producto.cantidad}" />
            </div>
            <div class="col-lg-2 col-mb-2 col-sm-12">
              <label for="act-espesor">Espesor</label>
              <input type="number" id="act-espesor" placeholder="Espesor" class="form-control" value="${producto.espesor}" />
            </div>
            <div class="col-lg-2 col-mb-2 col-sm-12">
              <label for="act-ancho">Ancho</label>
              <input type="number" id="act-ancho" placeholder="Ancho" class="form-control" value="${producto.ancho}" />
            </div>
            <div class="col-lg-2 col-mb-2 col-sm-12">
              <label for="act-largo">Largo</label>
              <input type="number" id="act-largo" placeholder="Largo" class="form-control" value="${producto.largo}" />
            </div>`;
  } else {
    modalHtml += `
            <div class="col-lg-6 col-mb-6 col-sm-12">
              <label for="act-cantidad">Cantidad</label>
              <input type="number" id="act-cantidad" placeholder="Cantidad" class="form-control" value="${producto.cantidad}" />
            </div>`;
  }

  modalHtml += `
            <div class="col-lg-6 col-mb-6 col-sm-12">
              <label for="act-precio">Precio</label>
              <input type="number" id="act-precio" placeholder="Precio" class="form-control" value="${producto.precio}" />
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" onclick="actualizarDetComprobante()">Actualizar</button>
          <input type="hidden" id="id-det-comprobante" value="${producto.id}">
          <input type="hidden" id="cod-unidad-medida" value="${producto.codUM}">
        </div>
      </div>
    </div>
  `;

  $("#mod-actualizar-det-comprobante").html(modalHtml);
  $("#mod-actualizar-det-comprobante").modal("show");
}

function actualizarDetComprobante() {
  var idDetComprobante = $("#id-det-comprobante").val();
  var espesor          = $("#act-espesor").val();
  var ancho            = $("#act-ancho").val();
  var largo            = $("#act-largo").val();
  var cantidad         = $("#act-cantidad").val();
  var precio           = $("#act-precio").val();
  
  if (!cantidad || !precio || cantidad <= 0 || precio <= 0) {
    alert('Debe ingresar valores válidos para cantidad y precio.');
    return;
  }

  // Encontrar el producto en el array
  const index = productos.findIndex(p => p.id == idDetComprobante);
  if (index !== -1) {
    // Actualizar los valores
    productos[index].espesor = espesor;
    productos[index].ancho = ancho;
    productos[index].largo = largo;
    productos[index].cantidad = cantidad;
    productos[index].precio = precio;
    productos[index].cantidadFinal = calcularCantidadFinal(
      cantidad, espesor, ancho, largo, productos[index].codUM
    );
    
    // Guardar en localStorage
    localStorage.setItem('productosTemporales', JSON.stringify(productos));
    
    // Actualizar vista
    renderizarProductos();
    
    // Cerrar modal
    $("#mod-actualizar-det-comprobante").modal("hide");
  }
}

function limpiarProductos() {
  productos = [];
  idProductoTemp = 1;
  localStorage.removeItem('productosTemporales');
  renderizarProductos();
}

function leerDetComprobante() {
  renderizarProductos();
}

function calcularDetraccion() {
 alert('Hola detracción!');
}

function guardarComprobante() {
 var tipoComprobante  = $("#tipo-comprobante").val();
 var idCliente        = $("#id-cliente").val();
 var numeroDocumento  = $("#numero-documento").val();
 var ordenCompra      = $("#orden-compra").val();
 var guiaRemision     = $("#guia-remision").val();
 var condicionPago    = $("#condicion-pago").val();
 var descMedioPago    = $("#desc-medio-pago").val();
 var observaciones    = $("#observaciones").val();
 var montoNeto        = $("#monto-neto").val();
 var montoIGV         = $("#monto-igv").val();
 var montoTotal       = $("#monto-total").val();
 var montoTotalCuotas = $("#monto-total-cuotas").val();
 
 /** Detracción - inicio **/
 var aplicaDetraccion = $("#aplica-detraccion").val();
 var bienServicioDet  = $("#bien-servicio-detraccion").val();
 var medioPagoDet     = $("#medio-pago-detraccion").val();
 var porcentajeDet    = $("#porcentaje-detraccion").val();
 var montoDet         = $("#monto-detraccion").val();
 /** Detracción - fin **/
 
 var medioPago        = [];
 var montoPagado      = [];

 for (i = 1; i <= 10; i++) {
  if (document.getElementById('CB'+i)) {
   var monto = document.getElementById('MP'+i).value;
   if (document.getElementById('CB'+i).checked) {
	medioPago[medioPago.length]     = i;
	montoPagado[montoPagado.length] = monto;
   }
  }
 }
 
 /** Condición de pago Crédito - inicio *
 var medioPagoCredito   = [];
 var montoPagadoCredito = [];
 for (i = 1; i <= 10; i++) {
  if (document.getElementById('CB'+i)) {
   var monto = document.getElementById('MP'+i).value;
   if (document.getElementById('CB'+i).checked) {
	medioPago[medioPago.length]     = i;
	montoPagado[montoPagado.length] = monto;
   }
  }
 }
 /** Condición de pago Crédito - fin **/
 
 //alert(aplicaDetraccion + ' ' + bienServicioDet + ' ' + medioPagoDet + ' ' + porcentajeDet + ' ' + montoDet)
 
 $.post('../../ajax/guardarComprobante.php', {
  tipoComprobante : tipoComprobante, 
  idCliente       : idCliente, 
  numeroDocumento : numeroDocumento, 
  ordenCompra     : ordenCompra, 
  guiaRemision    : guiaRemision, 
  condicionPago   : condicionPago, 
  descMedioPago   : descMedioPago, 
  observaciones   : observaciones, 
  montoNeto       : montoNeto, 
  montoIGV        : montoIGV, 
  montoTotal      : montoTotal, 
  montoTotalCuotas: montoTotalCuotas, 
 
  /** Detracción - inicio **/
  aplicaDetraccion: aplicaDetraccion, 
  bienServicioDet : bienServicioDet, 
  medioPagoDet    : medioPagoDet, 
  porcentajeDet   : porcentajeDet, 
  montoDet        : montoDet, 
  /** Detracción - fin **/
  
  medioPago       : medioPago, 
   montoPagado     : montoPagado,
   
   // Agregar los datos de localStorage
   cuotasJSON      : JSON.stringify(cuotas),
   productosJSON   : JSON.stringify(productos)
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
    // Limpiar las cuotas y productos después de guardar
    limpiarCuotas();
    limpiarProductos();
   window.location.reload(true);
  }
 });
}

function leerComprobantes() {
 var codVenta       = document.getElementById('codigo-venta')       ? document.getElementById('codigo-venta').value       : '';
 var numDocumento   = document.getElementById('numero-documento')   ? document.getElementById('numero-documento').value   : '';
 var fechaDesde     = document.getElementById('fecha-desde')        ? document.getElementById('fecha-desde').value        : '';
 var fechaHasta     = document.getElementById('fecha-hasta')        ? document.getElementById('fecha-hasta').value        : '';
 var numComprobante = document.getElementById('numero-comprobante') ? document.getElementById('numero-comprobante').value : '';
 
 $.post("../../ajax/ingresos/leerComprobantes.php", {
  codVenta       : codVenta, 
  numDocumento   : numDocumento, 
  fechaDesde     : fechaDesde, 
  fechaHasta     : fechaHasta, 
  numComprobante : numComprobante
 }, function(data, status) {
  if (document.getElementById('comprobantes')) {
   document.getElementById('comprobantes').innerHTML = data;
  }
 });
}

function cargarComprobante(id) {
 $.post('../../ajax/cargarComprobante.php', {
  id: id
 }, function(data, status) {
  $('#mod-cargar-comprobante').html(data);
 });
 $('#mod-cargar-comprobante').modal('show');
}

function pagarComprobante(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea pagar este comprobante?");
 if (confirmacion == true) {
  $.post("../../ajax/pagarComprobante.php", {
    id : id
  }, function(data, status) {
    leerComprobantes();
  });
 }
}

function verComprobante(id) {
 $.post("../../ajax/verComprobante.php", {
  id : id
 }, function(data, status) {
  $("#mod-ver-comprobante").html(data);
 });
}

function editarComprobante(id) {
 $.post("../../ajax/editarComprobante.php", {
  id: id
 }, function(data, status) {
  $("#mod-editar-comprobante").html(data);
  $("#id-comprobante").val(id);
 });
 $("#mod-editar-comprobante").modal("show");
}

function actualizarComprobante() {
 var idComprobante    = $("#id-comprobante").val();
 var tipoComprobante  = $("#tipo-comprobante").val();
 var numeroDocumento  = $("#numero-documento").val();
 var idCliente        = $("#id-cliente").val();
 var guiaRemision     = $("#guia-remision").val();
 var observaciones    = $("#observaciones").val();
 var ordenCompra      = $("#orden-compra").val();
 var montoNeto        = $("#monto-neto").val();
 var montoIGV         = $("#monto-igv").val();
 var montoTotal       = $("#monto-total").val();
 var entregado        = document.getElementById('cb-entregado').checked ? 1 : 0;

 /** Detracción - inicio **/
 var aplicaDetraccion = $("#aplica-detraccion").val();
 var bienServicioDet  = $("#bien-servicio-detraccion").val();
 var medioPagoDet     = $("#medio-pago-detraccion").val();
 var porcentajeDet    = $("#porcentaje-detraccion").val();
 var montoDet         = $("#monto-detraccion").val();
 /** Detracción - fin **/

 var medioPago        = [];
 var montoPagado      = [];

 for (i = 1; i <= 10; i++) {
  if (document.getElementById('CB'+i)) {
   var monto = document.getElementById('MP'+i).value;
   if (document.getElementById('CB'+i).checked) {
	medioPago[medioPago.length]     = i;
	montoPagado[montoPagado.length] = monto;
   }
  }
 }
 
 $.post("../../ajax/actualizarComprobante.php", {
  idComprobante   : idComprobante, 
  tipoComprobante : tipoComprobante,
  numeroDocumento : numeroDocumento, 
  idCliente       : idCliente, 
  guiaRemision    : guiaRemision, 
  observaciones   : observaciones, 
  ordenCompra     : ordenCompra, 
  montoNeto       : montoNeto, 
  montoIgv        : montoIGV, 
  montoTotal      : montoTotal, 
  entregado       : entregado, 
 
  /** Detracción - inicio **/
  aplicaDetraccion: aplicaDetraccion, 
  bienServicioDet : bienServicioDet, 
  medioPagoDet    : medioPagoDet, 
  porcentajeDet   : porcentajeDet, 
  montoDet        : montoDet, 
  /** Detracción - fin **/
  
  medioPago       : medioPago, 
  montoPagado     : montoPagado
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
   window.location.reload(true);
  }
 });
 //window.location.href = "../../pages/ingresos/buscar-ventas.php";
 buscarVentas();
}

function anularComprobante(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea anular el comprobante?");
 
 if (confirmacion == true) {
  $.post("../../ajax/comprobantes/anularComprobante.php", {
   idComprobante : id
  }, function(data, status) {
   var respuesta = JSON.parse(data);
   alert(respuesta['mensaje']);
   if(respuesta['estado']==0) {
    window.location.reload(true);
   }
  });
 }
 //window.location.href = "../../pages/ingresos/buscar-ventas.php";
 //buscarVentas();
}

function guardarOtrosIngresos() {
 var tipoIngreso   = document.getElementById('rb-jicamarca').checked ? 1 : 
						(document.getElementById('rb-alquiler').checked ? 2 : 
						(document.getElementById('rb-pucallpa').checked ? 3 : 
						(document.getElementById('rb-iquitos').checked ? 4 : 
						5)));
 var descripcion   = $("#descripcion").val();
 var monto         = $("#monto").val();
 var fecha         = $("#fecha").val();
 var observaciones = $("#observaciones").val();

 $.post("../../ajax/guardarOtrosIngresos.php", {
  tipoIngreso   : tipoIngreso, 
  descripcion   : descripcion, 
  monto         : monto, 
  fecha         : fecha, 
  observaciones : observaciones
 }, function(data, status) {
  alert("Se guardó correctamente.");
  location.reload();
 });
}

function leerOtrosIngresos() {
 $.get("../../ajax/leerOtrosIngresos.php", {
 }, function(data, status) {
  $("#otros-ingresos").html(data);
 });
}

function buscarOtrosIngresos() {
 var tipoIngreso = $("#tipo-ingreso").val();
 var fechaDesde  = $("#fecha-desde").val();
 var fechaHasta  = $("#fecha-hasta").val();
 
 $.post("../../ajax/buscarOtrosIngresos.php", {
  tipoIngreso : tipoIngreso, 
  fechaDesde  : fechaDesde, 
  fechaHasta  : fechaHasta
 }, function(data, status) {
  $("#otros-ingresos").html(data);
 });
}
/* Inicio Usuario */
function guardarUsuario() {
 var numDocumento = $("#numero-documento").val();
 var priNombre    = $("#primer-nombre").val();
 var segNombre    = $("#segundo-nombre").val();
 var priApellido  = $("#primer-apellido").val();
 var segApellido  = $("#segundo-apellido").val();
 var direccion    = $("#direccion").val();
 var telefono     = $("#telefono").val();
 var celular      = $("#celular").val();
 var correo       = $("#correo").val();
 var user         = $("#user").val();
 var pass         = $("#pass").val();
 var perfil       = $("#perfil").val();
 
 $.post("../../ajax/guardarUsuario.php", {
  numDocumento : numDocumento, 
  priNombre    : priNombre, 
  segNombre    : segNombre, 
  priApellido  : priApellido, 
  segApellido  : segApellido, 
  direccion    : direccion, 
  telefono     : telefono, 
  celular      : celular, 
  correo       : correo, 
  user         : user, 
  pass         : pass, 
  perfil       : perfil
 }, function(data, status) {
  alert("Se guardó correctamente.");
  location.reload();
 });
}

function leerUsuarios() {
 $.get("../../ajax/leerUsuarios.php", {
 }, function(data, status) {
  $("#usuarios").html(data);
 });
}

function eliminarUsuario(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el usuario?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarUsuario.php", {
   idUsuario : id
  }, function(data, status) {
   leerUsuarios();
  });
 }
}

function cargarUsuario(id) {
 $.post("../../ajax/cargarUsuario.php", {
  idUsuario : id
 }, function(data, status) {
  $("#mod-actualizar-usuario").html(data);
 });
 $("#mod-actualizar-usuario").modal("show");
}

function actualizarUsuario() {
 var idUsuario    = $("#id-usuario").val();
 var numDocumento = $("#act-numero-documento").val();
 var priNombre    = $("#act-primer-nombre").val();
 var segNombre    = $("#act-segundo-nombre").val();
 var priApellido  = $("#act-primer-apellido").val();
 var segApellido  = $("#act-segundo-apellido").val();
 var direccion    = $("#act-direccion").val();
 var telefono     = $("#act-telefono").val();
 var celular      = $("#act-celular").val();
 var correo       = $("#act-correo").val();
 var user         = $("#act-user").val();
 var pass         = $("#act-pass").val();
 var perfil       = $("#act-perfil").val();
 
 $.post("../../ajax/actualizarUsuario.php", {
  idUsuario    : idUsuario, 
  numDocumento : numDocumento, 
  priNombre    : priNombre, 
  segNombre    : segNombre, 
  priApellido  : priApellido, 
  segApellido  : segApellido, 
  direccion    : direccion, 
  telefono     : telefono, 
  celular      : celular, 
  correo       : correo, 
  user         : user, 
  pass         : pass, 
  perfil       : perfil
 }, function(data, status) {
  $("#mod-actualizar-usuario").modal("hide");
  leerUsuarios();
 });
}
/* Inicio Cliente */
function guardarCliente() {
 var tipCliente   = document.getElementById('agr-rb-natural').checked ? 1 : (document.getElementById('agr-rb-juridica').checked ? 2 : 0);
 var tipDocumento = document.getElementById('agr-tipo-documento').value;
 var numDocumento = document.getElementById('agr-numero-documento').value;
 var priNombre    = document.getElementById('agr-primer-nombre').value;
 var segNombre    = document.getElementById('agr-segundo-nombre').value;
 var priApellido  = document.getElementById('agr-primer-apellido').value;
 var segApellido  = document.getElementById('agr-segundo-apellido').value;
 var direccion    = document.getElementById('agr-direccion').value;
 var contacto     = document.getElementById('agr-contacto').value;
 var telefono     = document.getElementById('agr-telefono').value;
 var celular      = document.getElementById('agr-celular').value;
 var correo       = document.getElementById('agr-correo').value;
 
 $.post("../../ajax/guardarCliente.php", {
  tipCliente   : tipCliente, 
  tipDocumento : tipDocumento, 
  numDocumento : numDocumento, 
  priNombre    : priNombre, 
  segNombre    : segNombre, 
  priApellido  : priApellido, 
  segApellido  : segApellido, 
  direccion    : direccion, 
  contacto     : contacto, 
  telefono     : telefono, 
  celular      : celular, 
  correo       : correo
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
   window.location.reload(true);
  }
 });
}

function guardarClienteVenta() {
 var tipCliente   = document.getElementById('agr-rb-natural').checked ? 1 : (document.getElementById('agr-rb-juridica').checked ? 2 : 0);
 var tipDocumento = document.getElementById('agr-tipo-documento').value;
 var numDocumento = document.getElementById('agr-numero-documento').value;
 var priNombre    = document.getElementById('agr-primer-nombre').value;
 var segNombre    = document.getElementById('agr-segundo-nombre').value;
 var priApellido  = document.getElementById('agr-primer-apellido').value;
 var segApellido  = document.getElementById('agr-segundo-apellido').value;
 var direccion    = document.getElementById('agr-direccion').value;
 var contacto     = document.getElementById('agr-contacto').value;
 var telefono     = document.getElementById('agr-telefono').value;
 var celular      = document.getElementById('agr-celular').value;
 var correo       = document.getElementById('agr-correo').value;
 
 $.post("../../ajax/guardarCliente.php", {
  tipCliente   : tipCliente, 
  tipDocumento : tipDocumento, 
  numDocumento : numDocumento, 
  priNombre    : priNombre, 
  segNombre    : segNombre, 
  priApellido  : priApellido, 
  segApellido  : segApellido, 
  direccion    : direccion, 
  contacto     : contacto, 
  telefono     : telefono, 
  celular      : celular, 
  correo       : correo
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
   document.getElementById('numero-documento').value = numDocumento;
   document.getElementById('nombre').value           = priNombre;
   document.getElementById('direccion').value        = direccion;
   document.getElementById('id-cliente').value       = respuesta['idCliente'];
   $("#mod-agregar-cliente").modal("hide");
  }
 });
}

function leerClientes() {
 $.get("../../ajax/leerClientes.php", {
	 
 }, function(data, status) {
  $("#clientes").html(data);
 });
}

function eliminarCliente(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el cliente?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarCliente.php", {
   idCliente : id
  }, function(data, status) {
   leerClientes();
  });
 }
}

function cargarCliente(id) {
 $.post("../../ajax/cargarCliente.php", {
  idCliente : id
 }, function(data, status) {
  $("#mod-actualizar-cliente").html(data);
 });
 $("#mod-actualizar-cliente").modal("show");
}

function actualizarCliente() {
 var idCliente       = $("#id-cliente").val();
 var tipCliente      = document.getElementById('act-rb-natural').checked ? 1 : 
						(document.getElementById('act-rb-juridica').checked ? 2 : 0);
 var numDocumento    = $("#act-numero-documento").val();
 var primerNombre    = $("#act-primer-nombre").val();
 var segundoNombre   = $("#act-segundo-nombre").val();
 var primerApellido  = $("#act-primer-apellido").val();
 var segundoApellido = $("#act-segundo-apellido").val();
 var direccion       = $("#act-direccion").val();
 var contacto        = $("#act-contacto").val();
 var telefono        = $("#act-telefono").val();
 var celular         = $("#act-celular").val();
 var correo          = $("#act-correo").val();

 $.post("../../ajax/actualizarCliente.php", {
  idCliente       : idCliente, 
  tipCliente      : tipCliente, 
  numDocumento    : numDocumento, 
  primerNombre    : primerNombre, 
  segundoNombre   : segundoNombre, 
  primerApellido  : primerApellido, 
  segundoApellido : segundoApellido, 
  direccion       : direccion, 
  contacto        : contacto, 
  telefono        : telefono, 
  celular         : celular, 
  correo          : correo
 }, function(data, status) {
  $("#mod-actualizar-cliente").modal("hide");
  leerClientes();
 });
}
/* Inicio Producto */
function guardarProducto() {
 var descripcion = $("#descripcion").val();
 var unMedida    = $("#unidad-medida").val();
 var codigo      = $("#codigo").val();
 var precio      = $("#precio").val();
 
 $.post("../../ajax/guardarProducto.php", {
  descripcion : descripcion, 
  unMedida    : unMedida, 
  codigo      : codigo, 
  precio      : precio
 }, function(data, status) {
  alert("Se guardó correctamente.");
  location.reload();
 });
}

function buscarProducto() {
  var producto = document.getElementById('producto') ? document.getElementById('producto').value : '';

  $.post("../../ajax/mantenimiento/buscarProducto.php", {
    producto : producto
  }, function(data, status) {
    if (document.getElementById('productos')) {
      document.getElementById('productos').innerHTML = data;
    }
  });
}

function leerProductos() {
 $.get("../../ajax/mantenimiento/leerProductos.php", {
	 
 }, function(data, status) {
  $("#productos").html(data);
 });
}

function eliminarProducto(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el producto?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarProducto.php", {
   idProducto : id
  }, function(data, status) {
   leerProductos();
  });
 }
}

function cargarProducto(id) {
 $.post("../../ajax/cargarProducto.php", {
  idProducto : id
 }, function(data, status) {
  $("#mod-actualizar-producto").html(data);
 });
 $("#mod-actualizar-producto").modal("show");
}

function actualizarProducto() {
 var idProducto  = $("#id-producto").val();
 var descripcion = $("#act-descripcion").val();
 var codigo      = $("#act-codigo").val();
 var precio      = $("#act-precio").val();
 var stock       = $("#act-stock").val();

 $.post("../../ajax/actualizarProducto.php", {
  idProducto  : idProducto, 
  descripcion : descripcion, 
  codigo      : codigo, 
  precio      : precio, 
  stock       : stock
 }, function(data, status) {
  $("#mod-actualizar-producto").modal("hide");
  leerProductos();
 });
}
/* Inicio Proveedor */
function guardarProveedor() {
 var ruc         = $("#ruc").val();
 var razonSocial = $("#razon-social").val();
 var direccion   = $("#direccion").val();
 var contacto    = $("#contacto").val();
 var telefono    = $("#telefono").val();
 var correo      = $("#correo").val();
 
 $.post("../../ajax/guardarProveedor.php", {
  ruc         : ruc, 
  razonSocial : razonSocial, 
  direccion   : direccion, 
  contacto    : contacto, 
  telefono    : telefono, 
  correo      : correo
 }, function(data, status) {
  alert("Se guardó correctamente.");
  location.reload();
 });
}

function leerProveedores() {
 $.get("../../ajax/leerProveedores.php", {
	 
 }, function(data, status) {
  $("#proveedores").html(data);
 });
}

function eliminarProveedor(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el proveedor?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarProveedor.php", {
   idProveedor : id
  }, function(data, status) {
   leerProveedores();
  });
 }
}

function cargarProveedor(id) {
 $.post("../../ajax/cargarProveedor.php", {
  idProveedor : id
 }, function(data, status) {
  $("#mod-actualizar-proveedor").html(data);
 });
 $("#mod-actualizar-proveedor").modal("show");
}

function actualizarProveedor() {
 var idProveedor = $("#id-proveedor").val();
 var ruc         = $("#act-ruc").val();
 var razonSocial = $("#act-razon-social").val();
 var direccion   = $("#act-direccion").val();
 var contacto    = $("#act-contacto").val();
 var telefono    = $("#act-telefono").val();
 var correo      = $("#act-correo").val();

 $.post("../../ajax/actualizarProveedor.php", {
  idProveedor : idProveedor, 
  ruc         : ruc, 
  razonSocial : razonSocial, 
  direccion   : direccion, 
  contacto    : contacto, 
  telefono    : telefono, 
  correo      : correo
 }, function(data, status) {
  $("#mod-actualizar-proveedor").modal("hide");
  leerProveedores();
 });
}
/* Inicio Compras */
function leerCompras() {
 var idProveedor = $("#proveedor").val();
 var fechaDesde  = $("#fecha-desde").val();
 var fechaHasta  = $("#fecha-hasta").val();
 
 $.post("../../ajax/leerCompras.php", {
  idProveedor : idProveedor, 
  fechaDesde  : fechaDesde, 
  fechaHasta  : fechaHasta
 }, function(data, status) {
  $("#compras").html(data);
 });
}

function cargarCompra(id) {
 $("#id-compra").val(id);

 $.post("../../ajax/cargarCompra.php", {
  id: id
 }, function(data, status) {
  $("#mod-cargar-compra").html(data);
  $("#id-compra").val(id);
 });
 $("#mod-cargar-compra").modal("show");
}

function buscarProveedor() {
 $.post("../../ajax/buscarProveedor.php", {
  ruc: $("#ruc").val()
 }, function(data, status) {
  var proveedor = JSON.parse(data);
  if(proveedor['status'] == 200) {
   $("#prv-id").val("0");
   $("#buscar-no-proveedor").modal("show");
  } else {
   $("#prv-id").val(proveedor.id);
   $("#prv-ruc").val(proveedor.ruc);
   $("#prv-razon-social").val(proveedor.razon_social);
   $("#prv-direccion").val(proveedor.direccion);
   $("#buscar-proveedor").modal("show");
  }
 });
}

function agregarProveedor() {
 var id          = $("#prv-id").val();
 var ruc         = $("#prv-ruc").val();
 var razonSocial = $("#prv-razon-social").val();
 var direccion   = $("#prv-direccion").val();

 $.post("../../pages/egresos/agregar-compra.php", {
	 
 }, function(data, status) {
  $("#buscar-proveedor").modal("hide");

  $("#id-proveedor").val(id);
  $("#ruc").val(ruc);
  $("#razon-social").val(razonSocial);
  $("#direccion").val(direccion);
 });
}

function agregarDetCompra() {
 var idProducto  = $("#producto").val();
 var codigo      = $("#codigo").val();
 var descripcion = $("#descripcion").val();
 var unMedida    = $("#un_medida").val();
 var cantidad    = $("#cantidad").val();
 var costo       = $("#costo").val();

 $.post("../../ajax/agregarDetCompra.php", {
  idProducto  : idProducto, 
  codigo      : codigo, 
  descripcion : descripcion, 
  unMedida    : unMedida, 
  cantidad    : cantidad, 
  costo       : costo
 }, function(data, status) {
  $("#mod-agregar-producto").modal("hide");
  
  leerDetCompra();
  
  $("#producto").val("");
  $("#cantidad").val("");
  $("#costo").val("");
 });
}

function leerDetCompra() {
 $.get("../../ajax/leerDetCompra.php", {
	 
 }, function(data, status) {
  $("#detalle-compra").html(data);
 });
}

function eliminarDetCompra(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el producto?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarDetCompra.php", {
   idDetCompra : id
  }, function(data, status) {
   leerDetCompra();
  });
 }
}

function cargarDetCompra(id) {
 $.post("../../ajax/cargarDetCompra.php", {
  idDetCompra : id
 }, function(data, status) {
  $("#mod-actualizar-det-compra").html(data);
 });
 $("#mod-actualizar-det-compra").modal("show");
}

function actualizarDetCompra() {
 var idDetCompra = $("#id-det-compra").val();
 var cantidad    = $("#act-cantidad").val();
 var costo       = $("#act-costo").val();

 $.post("../../ajax/actualizarDetCompra.php", {
  idDetCompra : idDetCompra,
  cantidad    : cantidad,
  costo       : costo
 }, function(data, status) {
  $("#mod-actualizar-det-compra").modal("hide");
  leerDetCompra();
 });
}

function guardarCompra() {
 var idProveedor   = $("#id-proveedor").val();
 var fechaCompra   = $("#fecha-compra").val();
 var medioPago     = $("#medio-pago").val();
 var observaciones = $("#observaciones").val();
 var montoNeto     = $("#monto-neto").val();
 var montoIGV      = $("#monto-igv").val();
 var montoTotal    = $("#monto-total").val();

 $.post("../../ajax/guardarCompra.php", {
  idProveedor   : idProveedor, 
  fechaCompra   : fechaCompra, 
  medioPago     : medioPago, 
  observaciones : observaciones, 
  montoNeto     : montoNeto, 
  montoIGV      : montoIGV, 
  montoTotal    : montoTotal
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
   window.location.reload(true);
  }
 });
}

function guardarCotizacion() {
 var tipoComprobante  = $("#tipo-comprobante").val();
 var idCliente        = $("#id-cliente").val();
 var numeroDocumento  = $("#numero-documento").val();
 var ordenCompra      = $("#orden-compra").val();
 var guiaRemision     = $("#guia-remision").val();
 var condicionPago    = $("#condicion-pago").val();
 var descMedioPago    = $("#desc-medio-pago").val();
 var observaciones    = $("#observaciones").val();
 var montoNeto        = $("#monto-neto").val();
 var montoIGV         = $("#monto-igv").val();
 var montoTotal       = $("#monto-total").val();
 var montoTotalCuotas = $("#monto-total-cuotas").val();
 
 /** Detracción - inicio **/
 var aplicaDetraccion = $("#aplica-detraccion").val();
 var bienServicioDet  = $("#bien-servicio-detraccion").val();
 var medioPagoDet     = $("#medio-pago-detraccion").val();
 var porcentajeDet    = $("#porcentaje-detraccion").val();
 var montoDet         = $("#monto-detraccion").val();
 /** Detracción - fin **/
 
 var medioPago        = [];
 var montoPagado      = [];

 for (i = 1; i <= 10; i++) {
  if (document.getElementById('CB'+i)) {
   var monto = document.getElementById('MP'+i).value;
   if (document.getElementById('CB'+i).checked) {
	medioPago[medioPago.length]     = i;
	montoPagado[montoPagado.length] = monto;
   }
  }
 }

 $.post('../../ajax/cotizaciones/guardarCotizacion.php', {
  tipoComprobante : tipoComprobante, 
  idCliente       : idCliente, 
  numeroDocumento : numeroDocumento, 
  ordenCompra     : ordenCompra, 
  guiaRemision    : guiaRemision, 
  condicionPago   : condicionPago, 
  descMedioPago   : descMedioPago, 
  observaciones   : observaciones, 
  montoNeto       : montoNeto, 
  montoIGV        : montoIGV, 
  montoTotal      : montoTotal, 
  montoTotalCuotas: montoTotalCuotas, 
 
  /** Detracción - inicio **/
  aplicaDetraccion: aplicaDetraccion, 
  bienServicioDet : bienServicioDet, 
  medioPagoDet    : medioPagoDet, 
  porcentajeDet   : porcentajeDet, 
  montoDet        : montoDet, 
  /** Detracción - fin **/
  
  medioPago       : medioPago, 
  montoPagado     : montoPagado,
  
  // Agregar los datos de localStorage
  cuotasJSON      : JSON.stringify(cuotas),
  productosJSON   : JSON.stringify(productos)
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
    // Limpiar las cuotas y productos después de guardar
    limpiarCuotas();
    limpiarProductos();
   window.location.reload(true);
  }
 });
}

function leerCotizaciones() {
 var codCotizacion = document.getElementById('cot-codigo-cotizacion') ? document.getElementById('cot-codigo-cotizacion').value : '';
 var numDocumento  = document.getElementById('cot-numero-documento')  ? document.getElementById('cot-numero-documento').value  : '';
 var fechaDesde    = document.getElementById('cot-fecha-desde')       ? document.getElementById('cot-fecha-desde').value       : '';
 var fechaHasta    = document.getElementById('cot-fecha-hasta')       ? document.getElementById('cot-fecha-hasta').value       : '';
 
 $.post("../../ajax/cotizaciones/leerCotizaciones.php", {
  codCotizacion : codCotizacion, 
  numDocumento  : numDocumento, 
  fechaDesde    : fechaDesde, 
  fechaHasta    : fechaHasta
 }, function(data, status) {
  if (document.getElementById('cotizaciones')) {
   document.getElementById('cotizaciones').innerHTML = data;
  }
 });
}

function venderCotizacion() {
 var idCotizacion    = $("#id-cotizacion").val();
 var montoTotal      = $("#monto-total").val();
 var medioPago       = [];
 var montoPagado     = [];
 for (i = 1; i <= 10; i++) {
  if (document.getElementById('CB'+i)) {
   var monto = document.getElementById('MP'+i).value;
   if (document.getElementById('CB'+i).checked) {
	medioPago[medioPago.length]     = i;
	montoPagado[montoPagado.length] = monto;
   }
  }
 }

 $.post('../../ajax/cotizaciones/venderCotizacion.php', {
  idCotizacion : idCotizacion, 
  montoTotal   : montoTotal, 
  medioPago    : medioPago, 
  montoPagado  : montoPagado
 }, function(data, status) {
  var respuesta = JSON.parse(data);
  alert(respuesta['mensaje']);
  if(respuesta['estado']==0) {
   window.location.reload(true);
  }
 });
}

/* REPORTES */ 

/*Reporte de Ventas Producto */
function buscarReporteVentasProducto() {
 var tipoComprobante = $("#tip-comprobante").val();
 var fechaDesde      = $("#fecha-desde").val();
 var fechaHasta      = $("#fecha-hasta").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteVentasProducto.php", {
  tipoComprobante : tipoComprobante, 
  fechaDesde      : fechaDesde, 
  fechaHasta      : fechaHasta
 }, function(data, status) {
  $("#reporte-ventas-producto").html(data);
  $("#rep-tipo-comprobante").val(tipoComprobante);
  $("#rep-fecha-desde").val(fechaDesde);
  $("#rep-fecha-hasta").val(fechaHasta);
 });
}

/*Reporte de Ventas General */
function buscarReporteVentasGeneral() {
 var tipoComprobante = $("#tip-comprobante").val();
 var fechaDesde      = $("#fecha-desde").val();
 var fechaHasta      = $("#fecha-hasta").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteVentasGeneral.php", {
  tipoComprobante : tipoComprobante, 
  fechaDesde      : fechaDesde, 
  fechaHasta      : fechaHasta
 }, function(data, status) {
  $("#reporte-ventas-general").html(data);
  $("#rep-tipo-comprobante").val(tipoComprobante);
  $("#rep-fecha-desde").val(fechaDesde);
  $("#rep-fecha-hasta").val(fechaHasta);
 });
}

/*Reporte de Compras Producto */
function buscarReporteComprasProducto() {
 var proveedor  = $("#proveedor").val();
 var fechaDesde = $("#fecha-desde").val();
 var fechaHasta = $("#fecha-hasta").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteComprasProducto.php", {
  proveedor  : proveedor, 
  fechaDesde : fechaDesde, 
  fechaHasta : fechaHasta
 }, function(data, status) {
  $("#reporte-compras-producto").html(data);
  $("#rep-proveedor").val(proveedor);
  $("#rep-fecha-desde").val(fechaDesde);
  $("#rep-fecha-hasta").val(fechaHasta);
 });
}

/*Reporte de Compras General */
function buscarReporteComprasGeneral() {
 var proveedor  = $("#proveedor").val();
 var fechaDesde = $("#fecha-desde").val();
 var fechaHasta = $("#fecha-hasta").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteComprasGeneral.php", {
  proveedor  : proveedor, 
  fechaDesde : fechaDesde, 
  fechaHasta : fechaHasta
 }, function(data, status) {
  $("#reporte-compras-general").html(data);
  $("#rep-proveedor").val(proveedor);
  $("#rep-fecha-desde").val(fechaDesde);
  $("#rep-fecha-hasta").val(fechaHasta);
 });
}

/*Reporte de Ingresos Mensual */
function buscarReporteIngresosMensual() {
 var mesPeriodo  = $("#mes-periodo").val();
 var anioPeriodo = $("#anio-periodo").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteIngresosMensual.php", {
  mesPeriodo  : mesPeriodo, 
  anioPeriodo : anioPeriodo
 }, function(data, status) {
  $("#reporte-ingresos-mensual").html(data);
  $("#rep-mes-periodo").val(mesPeriodo);
  $("#rep-anio-periodo").val(anioPeriodo);
 });
}

/*Reporte Detalle Diario */
function buscarReporteDetalleDiario() {
 var fechaDetalleDiario  = $("#fecha-detalle-diario").val();
 
 $.post("../../ajax/reportes/buscar/buscarReporteDetalleDiario.php", {
  fechaDetalleDiario : fechaDetalleDiario 
 }, function(data, status) {
  $("#reporte-detalle-diario").html(data);
  $("#rep-fecha-detalle-diario").val(fechaDetalleDiario);
 });
}

// Funciones para manejar cuotas desde el modal de comprobante
function cargarCuota(id) {
 // Prevenir cualquier posible propagación de evento
 if (event) {
  event.preventDefault();
  event.stopPropagation();
 }
 
 $.post("../../ajax/cargarCuota.php", {
  idCuota : id
 }, function(data, status) {
  $("#mod-actualizar-cuota").html(data);
  $("#mod-actualizar-cuota").modal("show");
 });
 
 return false;
}

function actualizarCuotaComprobante() {
 // Prevenir cualquier posible propagación de evento
 if (event) {
  event.preventDefault();
  event.stopPropagation();
 }
 
 var idCuota = $("#id-cuota").val();
 var fecha = $("#act-fecha-cuota").val();
 var monto = $("#act-monto-cuota").val();
 var idComprobante = $("#id-comprobante-cuota").val();

 $.post("../../ajax/actualizarCuota.php", {
  idCuota : idCuota,
  fecha : fecha,
  monto : monto,
  idComprobante : idComprobante
 }, function(data, status) {
  var response = JSON.parse(data);
  if (response.success) {
   // Actualizar la tabla de cuotas
   $("#act-detalle-cuotas tbody").html(response.html);
   // Actualizar el monto total de cuotas
   $("#monto-total-cuotas").val(response.montoTotalRaw);
   
   // Actualizar los montos del comprobante
   var montoTotal = parseFloat(response.montoTotalRaw);
   var montoNeto = Math.round((montoTotal / 1.18) * 100) / 100;
   var montoIGV = Math.round((montoTotal - montoNeto) * 100) / 100;
   
   // Actualizar los montos en la tabla
   $("td:contains('Monto neto') + td").text(montoNeto.toFixed(2));
   $("td:contains('IGV') + td").text(montoIGV.toFixed(2));
   $("td:contains('Monto total (S/)') + td").text(montoTotal.toFixed(2));
   $("#monto-total").val(montoTotal);
   
   // Cerrar el modal
   $("#mod-actualizar-cuota").modal("hide");
  }
 });
 
 return false;
}

function eliminarDetComprobante2(id) {
 var confirmacion = confirm("Esta opción no se puede deshacer, ¿desea eliminar el producto?");

 if (confirmacion == true) {
  $.post("../../ajax/eliminarDetComprobante2.php", {
    idDetComprobante : id
  }, function(data, status) {
   alert("Se eliminó correctamente el producto.");
   location.reload();
  });
 }
}

function cargarDetComprobante2(id) {
 $.post("../../ajax/cargarDetComprobante2.php", {
  idDetComprobante : id
 }, function(data, status) {
  $("#mod-actualizar-det-comprobante").html(data);
 });
 $("#mod-actualizar-det-comprobante").modal("show");
}

function agregarDetComprobante2() {
 var idProducto = $("#producto").val();
 var unMedida = $("#un_medida").val();
 var codUM = $("#cod_um").val();
 var codigo = $("#codigo").val();
 var descripcion = $("#descripcion").val();
 var cantidad = $("#cantidad").val();
 var espesor = $("#espesor").val() || "0";
 var ancho = $("#ancho").val() || "0";
 var largo = $("#largo").val() || "0";
 var precio = $("#precio").val();
 var idComprobante = $("#id-comprobante").val();

 if (!idProducto || !precio || !cantidad || (cantidad <= 0) || (precio <= 0)) {
   alert('Debe completar todos los campos requeridos con valores válidos.');
   return;
 }

 $.post("../../ajax/agregarDetComprobante2.php", {
  idProducto: idProducto,
  unMedida: unMedida,
  codUM: codUM,
  codigo: codigo,
  descripcion: descripcion,
  cantidad: cantidad,
  espesor: espesor,
  ancho: ancho,
  largo: largo,
  precio: precio,
  idComprobante: idComprobante
 }, function(data, status) {
  alert("Producto agregado correctamente.");
  $("#mod-agregar-producto").modal("hide");
  location.reload();
 });
}

function actualizarDetComprobante2() {
  var idDetComprobante = $("#id-det-comprobante").val();
  var unidadMedida     = $("#act-unidad-medida").val();
  var cantidad         = $("#act-cantidad").val();
  var espesor          = $("#act-espesor").val();
  var ancho            = $("#act-ancho").val();
  var largo            = $("#act-largo").val();
  var precio           = $("#act-precio").val();

  $.post("../../ajax/actualizarDetComprobante2.php", {
   idDetComprobante : idDetComprobante, 
   unidadMedida     : unidadMedida, 
   cantidad         : cantidad, 
   espesor          : espesor, 
   ancho            : ancho, 
   largo            : largo, 
   precio           : precio
  }, function(data, status) {
   $("#mod-actualizar-det-comprobante").modal("hide");
   location.reload();
  });
}