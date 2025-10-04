<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Gutenberg">
    <meta name="generator" content="http://www.mainpa.com">
    <title>Mainpa - Fabricación y servicios</title>
	<link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/dist/css/style.css" rel="stylesheet">
    <link href="assets/dist/css/modal.css" rel="stylesheet">
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet'>
	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/brands.js" integrity="sha384-sCI3dTBIJuqT6AwL++zH7qL8ZdKaHpxU43dDt9SyOzimtQ9eyRhkG3B7KMl6AO19" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>
    <style>
      body {
        font-family:'Comfortaa';
      }
    </style>
  </head>
  <body>
    <?php include 'bloques/cabecera.php'; ?>
    <div class="container d-flex align-items-center justify-content-center">
      <main class="pagina">
	    <article class="articulo mainpa-fondo-arboles">
          <h1 class="txt-center txt-blanco">Fabricación y servicios</h1>
          <div class="row">
            <div class="col-6 col-sm-4 col-md-3 div-producto txt-center pad-10"><a href="#"><div class="div-border pad-20 div-border-radius bg-blanco open-modal" onclick="window.modfabparihuelas.showModal();"><img class="img-producto div-border-radius" src="img/productos/laminas-tableros/fabricacion-parihuelas.jpg" alt="fabricacion-parihuelas" title="fabricacion-parihuelas"/>Fabricación de parihuelas</div></a></div>
            <div class="col-6 col-sm-4 col-md-3 div-producto txt-center pad-10"><a href="#"><div class="div-border pad-20 div-border-radius bg-blanco open-modal" onclick="window.modfabpaneles.showModal();"><img class="img-producto div-border-radius" src="img/productos/laminas-tableros/fabricacion-paneles.jpg" alt="fabricacion-paneles" title="fabricacion-paneles"/>Fabricación de paneles</div></a></div>
            <div class="col-6 col-sm-4 col-md-3 div-producto txt-center pad-10"><a href="#"><div class="div-border pad-20 div-border-radius bg-blanco open-modal" onclick="window.modoptcorte.showModal();"><img class="img-producto div-border-radius" src="img/productos/laminas-tableros/optimizacion-corte.jpg" alt="optimizacion-corte" title="optimizacion-corte"/>Optimización de paneles</div></a></div>
            <div class="col-6 col-sm-4 col-md-3 div-producto txt-center pad-10"><a href="#"><div class="div-border pad-20 div-border-radius bg-blanco open-modal" onclick="window.moddimmadera.showModal();"><img class="img-producto div-border-radius" src="img/productos/laminas-tableros/dimensionado-madera.jpg" alt="dimensionado-madera" title="dimensionado-madera"/>Domensionado de madera</div></a></div>
            <div class="col-6 col-sm-4 col-md-3 div-producto txt-center pad-10"><a href="#"><div class="div-border pad-20 div-border-radius bg-blanco open-modal" onclick="window.moddimtableros.showModal();"><img class="img-producto div-border-radius" src="img/productos/laminas-tableros/dimensionado-tableros.jpg" alt="dimensionado-tableros" title="dimensionado-tableros"/>Domensionado de tableros</div></a></div>
          </div>
		</article>
      </main>
    </div>
    <?php include 'bloques/pie-pagina.php'; ?>
    <!-- inicio productos -->
    <dialog id="modfabparihuelas" class="col-12 bg-blanco mod-producto"><div class="row"><div class="col-md-12 pad-10"><h4>Fabricación de parihuelas</h4></div></div><div class="row"><div class="col-md-6 div-border txt-center"><img class="img-modal" src="img/productos/laminas-tableros/techo-klar.jpg" alt="techo-klar" title="techo-klar"/></div><div class="col-md-6"><p class="mod-titulo">Parihuelas de madera</p></div></div><div class="txt-right"><button class="close-modal" onclick="window.modfabparihuelas.close();">Cerrar</button></div></dialog>
    <dialog id="modfabpaneles" class="col-12 bg-blanco mod-producto"><div class="row"><div class="col-md-12 pad-10"><h4>Fabricación de paneles</h4></div></div><div class="row"><div class="col-md-6 div-border txt-center"><img class="img-modal" src="img/productos/laminas-tableros/techo-klar.jpg" alt="techo-klar" title="techo-klar"/></div><div class="col-md-6"><p class="mod-titulo">Panel fenólico marrón<br><label class="mod-descripcion">Medidas: <br>- 122 cm x 244 cm</label></p><p class="mod-titulo">Panel triplay<br><label class="mod-descripcion">Medidas: <br>- 122 cm x 244 cm</label></p></div></div><div class="txt-right"><button class="close-modal" onclick="window.modfabpaneles.close();">Cerrar</button></div></dialog>
    <dialog id="modoptcorte" class="col-12 bg-blanco mod-producto"><div class="row"><div class="col-md-12 pad-10"><h4>Optimización de corte</h4></div></div><div class="row"><div class="col-md-6 div-border txt-center"><img class="img-modal" src="img/productos/laminas-tableros/techo-klar.jpg" alt="techo-klar" title="techo-klar"/></div><div class="col-md-6"><p class="mod-titulo">Plataforma de madera</p></div></div><div class="txt-right"><button class="close-modal" onclick="window.modoptcorte.close();">Cerrar</button></div></dialog>
    <dialog id="moddimmadera" class="col-12 bg-blanco mod-producto"><div class="row"><div class="col-md-12 pad-10"><h4>Dimensionado de madera</h4></div></div><div class="row"><div class="col-md-6 div-border txt-center"><img class="img-modal" src="img/productos/laminas-tableros/techo-klar.jpg" alt="techo-klar" title="techo-klar"/></div><div class="col-md-6"><p class="mod-titulo">Dimensionado de madera</p></div></div><div class="txt-right"><button class="close-modal" onclick="window.moddimmadera.close();">Cerrar</button></div></dialog>
    <dialog id="moddimtableros" class="col-12 bg-blanco mod-producto"><div class="row"><div class="col-md-12 pad-10"><h4>Dimensionado de tableros</h4></div></div><div class="row"><div class="col-md-6 div-border txt-center"><img class="img-modal" src="img/productos/laminas-tableros/techo-klar.jpg" alt="techo-klar" title="techo-klar"/></div><div class="col-md-6"><p class="mod-titulo">Dimensionado de tableros</p></div></div><div class="txt-right"><button class="close-modal" onclick="window.moddimtableros.close();">Cerrar</button></div></dialog>
    <!-- fin productos -->
  </body>
</html>
