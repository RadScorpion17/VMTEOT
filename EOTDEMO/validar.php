<?php
include "header.php";

if (isset($_REQUEST['update_id'])) {
    try {
        $id = $_REQUEST['update_id'];                                         //
        $select_stmt = $con->prepare('SELECT * FROM t_factura WHERE id=:id'); // Cargar los resultados del query en la varible
        $select_stmt->bindParam(':id', $id);                                  // $row. Ej: si se quiere acceder a la columna
        $select_stmt->execute();                                              // Emblema == $row['proveedor_ruc']
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);                         //
        extract($row);
    } catch (PDOException $e) {
        $e->getMessage();
    }
    if (isset($_REQUEST['btn_guardar'])) {
        try {
            if (isset($_REQUEST['check_valido'])) {
                $estado = $_REQUEST['check_valido'];
            } else {
                $estado = null;
                $errormensaje = "";
            }

            if (!isset($errormensaje)) //Ejecutar el codigo si la variable $errormensaje esta vacia
            {
                $query = "UPDATE t_factura SET estado=:estado_new
                                              WHERE id=:id";
                $actualizar_stmt = $con->prepare($query);
                $actualizar_stmt->bindParam(':estado_new', $estado);
                $actualizar_stmt->bindParam(':id', $id);
                if ($actualizar_stmt->execute()) {
                    $actualizarmensaje = "Validado!";
                    header("refresh:2;admin.php");
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
<div class="row h-100 justify-content-left align-items-center">
    <div class="col-10 col-md-8 col-lg-6">
        <br>
        <h2 class="display-6">Validar Factura</h2>
    </div>
    <form method="POST" class="form-horizontal justify-content-center" enctype="multipart/form-data">
        <div class="container-fluid ">
            <div class="row h-100 justify-content-left">
                </br>


                <div class='col-md-4'>
                    <div class="row">
                        <div class="col">
                            <label class="col-sm-3 control-label">Emblema</label>
                            <input type="text" name="txt_proveedor_ruc" class="form-control" value="<?php echo $row['proveedor_ruc']; ?>" disabled />
                        </div>

                        <div class="col">
                            <label class="col-sm-2 control-label">Empresa</label>
                            <input type="text" name="txt_eotruc" class="form-control" value="<?php echo $row['eotruc']; ?>" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">N°Factura</label>
                        <input type="text" name="txt_numfactura" class="form-control" value="<?php echo $row['numfactura']; ?>" disabled />
                    </div>

                    <div class="row">
                        <div class="col">
                            <label class="col-sm-6 control-label">Diesel Tipo I</label>
                            <input type="text" name="txt_t1_cantidad" class="form-control" value="<?php echo $row['t1_cantidad']; ?>" disabled />
                        </div>
                        <div class="col">
                            <label class="col-sm-3 control-label">Precio</label>
                            <input type="text" name="txt_t1_precio" class="form-control" value="<?php echo $row['t1_precio']; ?>" disabled />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label class="col-sm-8 control-label">Diesel Tipo III</label>
                            <input type="text" name="txt_t3_cantidad" class="form-control" value="<?php echo $row['t3_cantidad']; ?>" disabled />
                        </div>
                        <div class="col">
                            <label class="col-sm-3 control-label">Precio</label>
                            <input type="text" name="txt_t3_precio" class="form-control" value="<?php echo $row['t3_precio']; ?>" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Fecha</label>
                        <input type="date" name="date_fechafactura" class="form-control" value="<?php echo $row['fechafactura']; ?>" disabled />
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Creación</label>
                        <input type="text" name="txt_creacion" class="form-control" value="<?php echo $row['fechaevento']; ?>" disabled />
                    </div>

                    <div class="form-group">
                    <label for="txt_observacion" class="col-sm-6 form-label">Observación</label>
                    <textarea maxlength="200" class="form-control" name="txt_observacion" id="txt_observacion" placeholder="Observaciones adcionales de la factura (Max: 200 caracteres)" rows="3" disabled><?php echo $row['observacion']; ?></textarea>
                    </div>

                    </br>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="check_valido" id="exampleRadios1" value="Válido">
                        <label class="form-check-label" for="exampleRadios1">
                            Valido
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="check_valido" id="exampleRadios2" value="Denegado">
                        <label class="form-check-label" for="exampleRadios2">
                            Denegado
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-offset-3 col-sm-9 m-t-15">
                            <input type="submit" name="btn_guardar" class="btn btn-secondary" value="Guardar">
                            <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='embed-responsive' style='padding-bottom:150%'>
                        <object data="<?php echo '/upload/' . $row['rutaarchivo']; ?>" type='application/pdf' width='700px' height='700px'></object>
                    </div>
                </div>
            </div>
        </div>

        <?php include('footer.html'); ?>