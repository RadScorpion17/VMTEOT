<?php

session_start();
if (!isset($_SESSION["username"])) {
  header("location:login.php");
} else {
  require_once "conexion.php";
  $username = $_SESSION["username"];
  $ideot = $_SESSION['user_id'];
  setcookie($username);
}

$row_limit = 5;

if (isset($_POST["page"])) {
    $page_no = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
    if(!is_numeric($page_no))
        die("Error fetching data! Invalid page number!!!");
} else {
    $page_no = 1;
}
$username = $_SESSION["username"];
// get record starting position
$start = (($page_no-1) * $row_limit);

$results = $con->prepare("SELECT id, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura WHERE eotruc=:username ORDER BY fechaevento OFFSET $start LIMIT $row_limit");
$results->bindParam(':username', $username);
$results->execute();

while($row = $results->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>" . 
    "<td>" . $row['numfactura'] . "</td>" . 
    "<td>" . $row['fechafactura'] . "</td>" . 
    "<td>" . $row['proveedor_ruc'] . "</td>" . 
    "<td>" . $row['estado'] . "</td>" . 
    "<td><a href=\"/upload/" . $row['rutaarchivo'] ."\">".$row['rutaarchivo']."</a></td>" .
    "<td class=\"align-middle\">
        <a type=\"submit\" href=\"editar.php?update_id=".$row['id']."\" class=\"btn btn-secondary\">Editar</a>
        <a href=\"?borrar_id=".$row['id']."\" class=\"btn btn-secondary\">Eliminar</a></td>".
    "</tr>";
}
?>
