<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Gutenberg">
    <meta name="generator" content="http://www.mainpa.com">
    <title>Mainpa - Libro de reclamaciones</title>
	  <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/dist/css/style.css" rel="stylesheet">
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
          <h1 class="txt-center txt-blanco">Libro de reclamaciones</h1>
          <div class="row">
            <div class="col-12 col-md-12 pad-10">
              <div class="div-border-radius bg-blanco txt-justified pad-20">
              <form method="post">
                  <p>
                    <label for="lr-nombre" class="form-label">Nombre</label>
                    <input id="lr-nombre" name="lr-nombre" class="form-control" type="text" placeholder="nombre">
                  </p>
                  <p>
                    <label for="lr-correo" class="form-label">Correo electrónico</label>
                    <input id="lr-correo" name="lr-correo" class="form-control" type="email" placeholder="correo@mail.com">
                  </p>
                  <p>
                    <label for="lr-asunto" class="form-label">Asunto</label>
                    <input id="lr-asunto" name="lr-asunto" class="form-control" type="text" placeholder="asunto">
                  </p>
                  <p>
                    <label for="lr-mensaje" class="form-label">Reclamo/observación</label>
                    <textarea id="lr-mensaje" name="lr-mensaje" class="form-control" placeholder="mensaje" rows="3"></textarea>
                  </p>
                  <p>
                    <input id="lr-enviar" class="btn btn-success" name="lr-enviar" type="submit"></input>
                  </p>
                </form>
              </div>
            </div>
          </div>
        </article>
      </main>
    </div>
    <?php include 'bloques/envio-correo.php'; ?>
    <?php include 'bloques/pie-pagina.php'; ?>
  </body>
</html>
