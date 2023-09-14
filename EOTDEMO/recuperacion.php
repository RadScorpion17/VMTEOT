<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\Mailtemplate;

require $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailer.php';
require 'PHPMailer\Exception.php';
require 'PHPMailer\SMTP.php';
require 'PHPMailer\Mailtemplate.php';
require_once "conexion.php";



//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
if (isset($_REQUEST['Enviar'])) {
  if (isset($_REQUEST['txt_email'])) {
    $eotemail = $_REQUEST['txt_email'];
    $user = $_REQUEST['txt_user'];
    try {
      $selectstmt = $con->prepare('SELECT ruc, email FROM public.eot WHERE ruc=:username and email=:email');
      $selectstmt->bindParam(':username', $user);
      $selectstmt->bindParam(':email', $eotemail);
      $selectstmt->execute();
      $rowcount = $selectstmt->rowCount();
      if ($rowcount > 0) {
        try {
          $newpass = generatePassword(10);
          //Server settings
          $mail->SMTPDebug = 0;                                       //Enable verbose debug output
          $mail->isSMTP();                                           //Send using SMTP
          $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                  //Enable SMTP authentication
          $mail->Username   = 'vmtdeveloperpy@gmail.com';             //SMTP username
          $mail->Password   = 'isugoybbvmfgymhu';                    //SMTP password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           //Enable implicit TLS encryption
          $mail->Port       = 465;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

          //Recipients
          $mail->setFrom('vmtdeveloperpy@gmail.com', 'ADMINISTRACION');
          $mail->addAddress($eotemail);     //Add a recipient

          //Attachments
          //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

          $mail->isHTML(true);                                  //Set email format to HTML
          $mail->Subject = 'Recuperacion de contraseña';
          $mail->Body    = mailtemplate($user, $newpass);
          $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          if($mail->send()){
            $updatestmt = $con->prepare('UPDATE users SET password=:newpass WHERE username=:user');
            $updatestmt -> bindParam(':newpass',$newpass);
            $updatestmt -> bindParam(':user',$user);
            $updatestmt ->execute();
            $message = "Revise su buzon!";
          }
        } catch (Exception $e) {
          echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      } else {
        $message = "Las credenciales ingresadas no coinciden";
        die;
      }
    } catch (PDOException $pdo) {
      echo $pdo;
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>VMT</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
  <div class="vh-100 d-flex justify-content-center align-items-center">
    <div class="container">

      <link rel="StyleSheet" href="newstyles.css" type="text/css">


      <div class="login-wrap">
        <div class="login-html">
          <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Recuperar contraseña</label>

          <div class="login-form">
            <form method="POST">
              <div class="group">
                <label for="txt_user" class="label">Usuario</label>
                <input type="text" name="txt_user" class="input" required />
              </div>
              <div class="group">
                <label for="txt_email" class="label">Email</label>
                <input type="email" name="txt_email" class="input" required />
              </div>
              <div class="group">
                <input type="submit" name="Enviar" class="button" value="Enviar">
                <?php
                if (isset($message)) {
                  echo "<p class=\"tab-2\">".$message."</p>";
                }
                ?>
              </div>
              <div class="hr"></div>
            </form>
            <a class="tab-2" href="pdo_login.php">Volver al inicio</a>
          </div>
        </div>

      </div>
    </div>
  </div>
  </div>
</body>

</html>


