<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("location:pdo_login.php");
} else {
  require_once "conexion.php";
  $username = $_SESSION["username"];
  $ideot = $_SESSION['user_id'];
  setcookie($username);
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="headerstyle.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <title>VMT FACTURAS</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
  </script>
</head>

<body>
  <nav class="navbar">
    <div class="navbar-container container">
      <input type="checkbox" name="" id="" />
      <div class="hamburger-lines">
        <span class="line line1"></span>
        <span class="line line2"></span>
        <span class="line line3"></span>
      </div>
      <ul class="menu-items">
        <li><a href="resetpw.php">Cambiar contraseña</a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
      <a class="logo" href=<?php if ($_SESSION['role'] == 'admin') {
                              echo "admin.php";
                            } else {
                              echo "index.php";
                            } ?>> <img src='/imagenes/VMT.png' alt="HTML5 Icon" width="190" height="55"></a>
    </div>
  </nav>
</body>
