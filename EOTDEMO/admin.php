<?php
include('header.php');
$roladmitido = ['admin'];
if (!array_key_exists('role', $_SESSION) || !in_array($_SESSION['role'], $roladmitido)) {
    header('Location: pdo_login.php');
    die;
}

$start = 0;
$per_page = 8;
$page_counter = 0;
$next = $page_counter + 1;
$previous = $page_counter - 1;

if (isset($_GET['start'])) {
    $start = $_GET['start'];
    $page_counter =  $_GET['start'];
    $start = $start *  $per_page;
    $next = $page_counter + 1;
    $previous = $page_counter - 1;

}

$q = "SELECT id, eotruc, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura ORDER BY fechaevento DESC OFFSET $start LIMIT $per_page";
$count_query = "SELECT id FROM t_factura";
if (isset($_REQUEST['btn_buscar'])) {
    $where = array();
    $params = array();

        if (isset($_REQUEST['txt_estado']) and !empty($_REQUEST['txt_estado'])) {
        if($_REQUEST['txt_estado']=="Porvalidar"){
            $where[] = "estado IS NULL";
            //$params['estado'] = "IS NULL";
        } else {
            $_SESSION['estado'] = $_GET['txt_estado'];
            $where[] = "estado = :estado";
            $params['estado'] = $_SESSION['estado'];
        }
        }

        if (isset($_REQUEST['txt_search']) and !empty($_REQUEST['txt_search'])) {
            $_SESSION['search'] = $_GET['txt_search'];
            $where[] = "eotruc LIKE :search OR numfactura LIKE :search OR proveedor_ruc LIKE :search";
            $params['search'] = $_SESSION['search']."%";
        }

        if (!empty($where)) {
            $q = "SELECT id, eotruc, numfactura, fechafactura, proveedor_ruc, estado, rutaarchivo FROM t_factura WHERE" . " " . join(' AND ', $where)." ORDER BY fechaevento DESC OFFSET $start LIMIT $per_page";
            $count_query = "SELECT id FROM t_factura WHERE" . " " . join(' AND ', $where);
            $filterparams = http_build_query($params);
            $_SESSION['params'] = $filterparams;
        }
}
if(isset($_REQUEST['btn_limpiar'])){
    $_SESSION['params'] = null;
    $_SESSION['search'] = null;
    $_SESSION['estado'] = null;
}
$query = $con->prepare($q);
if (!empty($params)) {
    $query->execute($params);
} else {
    $query->execute();
}
if ($query->rowCount() > 0) {
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
}

$querycount = $con->prepare($count_query);
if (!empty($params)) {
    $querycount->execute($params);
} else {
    $querycount->execute();
}
$count = $querycount->rowCount();
$paginations = ceil($count / $per_page);

?>
<br />
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading text-left">
            <h2 class="display-6">FACTURAS</h2>
        </div>
        <form>
            <div class="col-sm-3">
                <div class="form-group">
                    <input type="text" name="txt_search" id="txt_search" class="form-control" <?php if (!empty($_SESSION['search'])) {
                                                                                        echo 'value="' . $_SESSION['search']. '"';
                                                                                    } ?> placeholder="Buscar">
                </div>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="radio" name="txt_estado" id="flexRadioDefault1" value="Válido">
                <label class="form-check-label" for="flexRadioDefault1">
                    Válido
                </label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="radio" name="txt_estado" id="flexRadioDefault2" value="Denegado">
                <label class="form-check-label" for="flexRadioDefault2">
                    Denegado
                </label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="radio" name="txt_estado" id="flexRadioDefault3" value="Porvalidar">
                <label class="form-check-label" for="flexRadioDefault3">
                    Por validar
                </label>
                </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <button type="submit" name="btn_buscar" id="btn_buscar" value="Search" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-search"></i>Buscar</button>
            <button type="submit" name="btn_limpiar" id="btn_limpiar" value="Clean" class="btn btn-primary btn-sm"><i class="fa fa-fw fa-search"></i>Limpiar</button>
        </div>
    </div>
    </form>

    <div class="table-responsive ">
        <table class="table table-hover">
            <thead class="thead-inverse">
                <tr>
                    <th>Eot</th>
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
                if (isset($result)) {
                    foreach ($result as $data) {
                        if ($data['estado'] == 'Válido') {
                            echo "<tr class=\"table-success\">";
                        } else if ($data['estado'] == 'Denegado') {
                            echo "<tr class=\"table-danger\">";
                        }
                        echo '<td>' . $data['eotruc'] . '</td>';
                        echo '<td>' . $data['numfactura'] . '</td>';
                        echo '<td>' . $data['fechafactura'] . '</td>';
                        echo '<td>' . $data['proveedor_ruc'] . '</td>';
                        echo '<td>' . $data['estado'] . '</td>';
                        echo "<td><a href=\"/upload/" . $data['rutaarchivo'] . "\">" . $data['rutaarchivo'] . "</a></td>";
                        echo "<td><a href=\"validar.php?update_id=" . $data['id'] . "\"class=\"btn btn-secondary\">Validar</a>";
                        echo '</tr>';
                    }
                } else {
                    echo "<p></p>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <ul class="pagination">
        <?php
        if (isset($_SESSION['params'])) {
            if ($count > 0) {
                if ($page_counter == 0) {
                    echo "<li class=\"page-item disabled\"><a class=\"page-link\" href=?start='0' class='active'><<</a></li>";
                    echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"?start=0\" class='active'>0</a></li>";
                    for ($j = 1; $j < $paginations; $j++) {
                        echo '<li class="page-item"><a class="page-link" href=?start=' . $j . "&" . $_SESSION['params'] . '>' . $j . '</a></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="?start=' . $next . "&" . $_SESSION['params'] . '"> >> </a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href=?start=' . $previous . "&" . $_SESSION['params'] . '> << </a></li>';
                    for ($j = 0; $j < $paginations; $j++) {
                        if ($j == $page_counter) {
                            echo '<li class="page-item active"><a class="page-link" href="?start=' . $j . "&" . $_SESSION['params'] . '"class="active">' . $j . '</a></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="?start=' . $j . "&" . $_SESSION['params'] . '">' . $j . '</a></li>';
                        }
                    }
                    if ($j != $page_counter + 1)
                        echo '<li class="page-item"><a class="page-link" href=?start=' . $next . "&" . $_SESSION['params'] . '>>></a></li>';
                }
            } else {
                if ($page_counter == 0) {
                    echo "<li class=\"page-item disabled\"><a class=\"page-link\" href=?start='0' class='active'><<</a></li>";
                    echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"?start=0\" class='active'>0</a></li>";
                    for ($j = 1; $j < $paginations; $j++) {
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$j>" . $j . "</a></li>";
                    }
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?start=$next\"> >> </a></li>";
                } else {
                    echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$previous><<</a></li>";
                    for ($j = 0; $j < $paginations; $j++) {
                        if ($j == $page_counter) {
                            echo "<li class=\"page-item active\"><a class=\"page-link\" href=?start=$j class=\"active\">" . $j . "</a></li>";
                        } else {
                            echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$j>" . $j . "</a></li>";
                        }
                    }
                    if ($j != $page_counter + 1)
                        echo "<li class=\"page-item\"><a class=\"page-link\" href=?start=$next>>></a></li>";
                }
            }
        }
        ?>
    </ul>
</div>
</body>

<?php include('footer.html'); ?>


<!--</br>
    <div class="vh-20 d-flex justify-content-center align-items-center">
    <div class="container">
	<h2 class="display-6">Facturas</h2>
    <form method="post">
<div class="col-sm-3">
    <div class="form-group">
        <label>Buscar RUC</label>
        <input type="text" name="username" id="username" class="form-control" placeholder="Numero de ruc" required>
    </div>
</div>
<div class="col-sm-3">
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
  <label class="form-check-label" for="flexCheckDefault">
    Aprobados
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
  <label class="form-check-label" for="flexCheckChecked">
    Denegados
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
  <label class="form-check-label" for="flexCheckChecked">
    Por validar
  </label>
</div>
</div>

    <div class="clearfix"></div>
<div class="col-sm-3">
    <div class="form-group">
		<button type="submit" name="btn_buscar" id="btn_buscar" value="Search" class="btn btn-primary"><i class="fa fa-fw fa-search"></i> Buscar </button>
		<a href="admin.php" class="btn btn-danger"><i class="fa fa-fw fa-sync"></i> Limpiar </a>
	</div>
</div>
    </form>
    <div class = table-responsive>
    <table class="table table-hover">
        <thead class="thead-inverse">
            <tr>
                <th>eotruc</th>
                <th>N°</th>
                <th>Fecha</th>
                <th>Emblema</th>
				<th>Creación</th>
                <th>Estado</th>
                <th>Archivo</th>
                <th></th>
            </tr>
        </thead>
    <tbody>
    <?php
    /*try {
        if ($queryvar != null) {
            $select_stmt = $con->prepare($queryvar);
            $select_stmt->bindParam(':valor', $valor);
            $select_stmt->execute();
            while ($row = $select_stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
            <tr <?php if (($row['estado']) == "Válido") {
                    echo 'class="table-success"';
                } else if (($row['estado']) == "Denegado") {
                    echo 'class="table-danger"';
                } ?>>
                <td scope="row" class="align-middle"><?php echo $row['eotruc']; ?></td>
                <td scope="row" class="align-middle"><?php echo $row['numfactura']; ?></td>
                <td class="align-middle"><?php echo $row['fechafactura']; ?></td>
                <td class="align-middle"><?php echo $row['proveedor_ruc']; ?></td>
            	<td class="align-middle"><?php echo $row['fechaevento']; ?></td>
                <td class="align-middle"><?php echo $row['estado']; ?></td>
                <td class="align-middle" style="width: 1% !important;"><a  href="<?php echo '/upload/' . $row['rutaarchivo']; ?>"><?php echo $row['rutaarchivo']; ?></a></td>
                <td class="align-middle"><a href="validar.php?update_id=<?php echo $row['id']; ?>" class="btn btn-secondary">Validar</a>
            </tr>
         <?php
            }
        }
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }
        ?>
    </tbody>
    </table>
    </div>
    </div>
    </div>
<?php include('footer.html');?> -->*/
