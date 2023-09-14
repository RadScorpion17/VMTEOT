<?php 

    require_once "conexion.php";
    $username = $_SESSION["username"];
    $ideot = $_SESSION['user_id'];

    $start = 0;  $per_page = 8;
    $page_counter = 0;
    $next = $page_counter + 1;
    $previous = $page_counter - 1;


try {
    if (isset($_GET['start'])) {
        $start = $_GET['start'];
        $page_counter = $_GET['start'];
        $start = $start * $per_page;
        $next = $page_counter + 1;
        $previous = $page_counter - 1;
    }
    $query = $con->prepare("SELECT id, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura WHERE eotruc=:username ORDER BY fechaevento DESC OFFSET $start LIMIT $per_page");
    $query->bindParam(':username', $username);
    $query->execute();

    if ($query->rowCount() > 0) {
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    }
    $count_query = "SELECT id FROM t_factura WHERE eotruc=:username";
    $query = $con->prepare($count_query);
    $query->bindParam(':username', $username);
    $query->execute();
    $count = $query->rowCount();
    $paginations = ceil($count / $per_page);
}catch(PDOException $e){
    echo "Error: ".$e->getCode();
}
?>