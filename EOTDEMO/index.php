<?php
include 'header.php';
include_once 'data.php';

if (isset($_REQUEST['borrar_id'])) {
    $id = $_REQUEST['borrar_id'];
    try {
        $select_stmt = $con->prepare('SELECT * FROM t_factura WHERE eotruc=:username and id=:id');
        $select_stmt->bindParam(':id', $id);
        $select_stmt->bindParam(':username', $username);
        $select_stmt->execute();
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
        if (!isset($row['estado'])) {
            unlink("upload/" . $row['rutaarchivo']);

            $borrar_stmt = $con->prepare('DELETE FROM t_factura WHERE id=:id');
            $borrar_stmt->bindParam(':id', $id);
            $borrar_stmt->execute();

            header("refresh:1;index.php");
        } else {
            $idrow = $row['id'];
            $url = "editar.php?update_id=$idrow";
            header("Location: $url");
        }
    }catch(PDOException $e){
        echo $e->getCode();
    }
}
?>

<br/>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading text-left"><h2 class="display-6">FACTURAS</h2></div>
        <div class="justify-content-center align-items-center"><a class="btn btn-light btn-outline-dark" href="agregar.php">Agregar Facturas</a></div>
        <div class="table table-responsive">
        <table class="table table-hover">
            <thead class="thead-inverse">
            <tr>
                <th>Factura</th>
                <th>Fecha</th>
                <th>Proveedor</th>
                <th>Estado</th>
                <th>Archivo</th>
                <th style="width: 200px;"></th>
            </tr>
            </thead>
                <tbody>
                <?php
                if(isset($result)){
                try {
                    foreach ($result as $data) {
                        echo '<tr>';
                        echo '<td>' . $data['numfactura'] . '</td>';
                        echo '<td>' . $data['fechafactura'] . '</td>';
                        echo '<td>' . $data['proveedor_ruc'] . '</td>';
                        echo '<td>' . $data['estado'] . '</td>';
                        echo "<td><a href=\"/upload/" . $data['rutaarchivo'] . "\">" . $data['rutaarchivo'] . "</a></td>";
                        echo "<td class=\"align-middle\">
                        <a type=\"submit\" href=\"editar.php?update_id=" . $data['id'] . "\" class=\"btn btn-secondary btn-sm\">Editar</a>
                        <a href=\"?borrar_id=" . $data['id'] . "\" class=\"btn btn-secondary btn-sm\">Eliminar</a></td>";
                        echo '</tr>';
                    }
                }catch(Exception $p){
                    echo $p->getMessage();
                }}
                 ?>
                </tbody>
            </table>
                </div>
            <ul class="pagination pagination-sm justify-content-center">
            <?php
                if($page_counter == 0){
                    echo "<li class=\"page-item disabled\"><a class=\"page-link\" href=?start='0' class='active'><<</a></li>";
                    echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"?start=0\" class='active'>0</a></li>";
                    for($j=1; $j < $paginations; $j++) { 
                      echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$j>".$j."</a></li>";
                   }
                   echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$next>>></a></li>";
                }else{
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$previous><<</a></li>"; 
                    for($j=0; $j < $paginations; $j++) {
                     if($j == $page_counter) {
                        echo "<li class=\"page-item active\"><a class=\"page-link\" href=?start=$j class=\"active\">".$j."</a></li>";
                     }else{
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$j>".$j."</a></li>";
                     } 
                  }if($j != $page_counter+1)
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$next>>></a></li>"; 
                } 
            ?>
            </ul>   
        </div>  
    </body>

<?php include('footer.html'); ?>