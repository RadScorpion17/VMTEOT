<?php
require_once 'conexion.php';
session_start();   
$message = "";

try {
     if (isset($_POST["login"])) {
          if (empty($_POST["username"]) || empty($_POST["password"])) {
               $message = '<label>Rellene todos los campos</label>';
          } else {
               $usr = $_POST["username"];
               $pass = $_POST["password"];
               //$query = "SELECT users.id, roles.nombre AS role, password FROM eot.users INNER JOIN roles ON roles.id = users.role_id WHERE username = '$usr';";
               $query = "SELECT users.id, roles.nombre as role, users.password from users INNER JOIN roles ON roles.id = users.role_id WHERE username = '$usr';";
               $statement = $con->prepare($query);
               $statement->execute();
               $row = $statement->fetch(PDO::FETCH_ASSOC);
               //if (password_verify($row['password'], password_hash($pass, PASSWORD_BCRYPT))) 
               if ((password_verify($pass, $row['password'])) || password_verify($row['password'], password_hash($pass, PASSWORD_DEFAULT))) {
                    $startingPage = [
                         'admin' => 'admin.php',
                         'user' => 'index.php',
                         'ccm' => 'ccm.php'
                    ];
                    $nextPage = array_key_exists($row['role'], $startingPage) ? $startingPage['role'] : 'index.php';
                    if (array_key_exists($row['role'], $startingPage)) {
                         $nextPage = $startingPage[$row['role']];
                    } else {
                         $nextPage = $startingPage['user'];
                         error_log('There is no starting page for role ' . $row['role']);
                    }
                    $_SESSION["username"] = $_POST["username"];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['role'] = $row['role'];
                    header('Location: ' . $nextPage);
               } else {
                    header('Location: pdo_login.php');
               }
          }
     }
} catch (PDOException $error) {
     $message = $error->getMessage();
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
                         <input id="tab-1" type="radio" name="tab" class="sign-in" checked><label for="tab-1" class="tab">Iniciar Sesion</label>

                         <div class="login-form">
                              <form method="POST">
                                   <div class="group">
                                        <label for="user" class="label">Usuario</label>
                                        <input name="username" type="text" class="input">
                                   </div>
                                   <div class="group">
                                        <label for="pass" class="label">Contraseña</label>
                                        <input name="password" type="password" class="input" data-type="password">
                                   </div>
                                   <?php echo $message; ?>
                                   <div class="group">
                                        <input type="submit" name="login" class="button" value="Acceder">
                                   </div>
                                   <div class="hr"></div>
                              </form>
                              <a class="pass-reset" href="recuperacion.php">Recuperar Contraseña</a>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     </div>
</body>

</html>