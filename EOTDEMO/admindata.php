<?php 
    require_once "conexion.php";

    $start = 0;  $per_page = 8;
    $page_counter = 0;
    $next = $page_counter + 1;
    $previous = $page_counter - 1;

    $q = "SELECT id, eotruc, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura ORDER BY fechaevento DESC OFFSET $start LIMIT $per_page";

    if (isset($_REQUEST['search'])) {
    $busqueda = $_REQUEST['search'];
    $q = "SELECT id, eotruc, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura WHERE eotruc =:valor ORDER BY fechaevento DESC OFFSET $start LIMIT $per_page";
}
    
    if(isset($_GET['start'])){
     $start = $_GET['start'];
     $page_counter =  $_GET['start'];
     $start = $start *  $per_page;
     $next = $page_counter + 1;
     $previous = $page_counter - 1;
    }
    $query = $con->prepare($q);
    if(isset($busqueda)){
        $query->bindParam(':valor', $busqueda);
    }
    $query->execute();

    if($query->rowCount() > 0){
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    $count_query = "SELECT id FROM t_factura";
    $query = $con->prepare($count_query);
    $query->execute();
    $count = $query->rowCount();
    $paginations = ceil($count / $per_page);
?>
     