<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
//require 'vendor/autoload.php';

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (isset($_POST['lr-enviar'])) {

    if(isset($_POST['lr-nombre']) && isset($_POST['lr-correo']) && isset($_POST['lr-asunto']) && isset($_POST['lr-mensaje'])) { 

        $nombre = $_POST['lr-nombre'];
        $correo = $_POST['lr-correo'];
        $asunto = $_POST['lr-asunto'];
        $mensaje = $_POST['lr-mensaje'];

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug  = 0;                                      //Enable verbose debug output //0:se desactiva el debug, 2: se activa el debug
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'mainpa.com';                           //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'libro.reclamaciones@mainpa.com';       //SMTP username
            $mail->Password   = 'Libro2025.';                           //SMTP password
            $mail->SMTPSecure = 'ssl';                                  //Enable implicit TLS encryption //ssl: dominio con candado, tls: dominio sin candado
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('libro.reclamaciones@mainpa.com', 'Libro de reclamaciones Mainpa');
            //$mail->addAddress('gutenberg.alexis@gmail.com', 'Gutenberg Alexis');     //Add a recipient
            $mail->addAddress($correo, $nombre);     //Add a recipient
            /*$mail->addReplyTo('info@example.com', 'Information');
            $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/

            //Attachments
            /*$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');*/    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo "Enviado correctamente :)";
            echo '
                    <script type="text/javascript">
                        alert("Se envió correctamente su mensaje."); 
                    </script>
                ';
        } catch (Exception $e) {
            //echo "Error al enviar :(. Mailer Error: {$mail->ErrorInfo}";
            echo '
                    <script type="text/javascript">
                        alert("Todos los campos son obligatorios."); 
                    </script>
                ';
        }
    } 
}