<?php
include "header.php";
require_once "conexion.php";

if (isset($_REQUEST['update_id'])) {
    try {
        $id = $_REQUEST['update_id'];
        $select_stmt = $con->prepare('SELECT * FROM t_factura WHERE id=:id');
        $select_stmt->bindParam(':id', $id);
        $select_stmt->execute();
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        if ($row['eotruc'] != $username) {
            header('Location:index.php');
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
    if ((isset($_REQUEST['btn_actualizar'])) && (!isset($row['estado']))) {
        try {
            $numfactura = $_REQUEST['txt_numfactura'];                  //Obtener los inputs del formulario HTML
            $totalfactura = $_REQUEST['txt_totalfactura'];              //
            $fechafactura = $_REQUEST['date_fechafactura'];             //
            $proveedor_ruc = $_REQUEST['txt_proveedor_ruc'];            //
            $observacion = $_REQUEST['txt_observacion'];                //
            
            $t1_Cantidad = $_REQUEST['txt_t1_Cantidad'];                //
            if(empty($t1_Cantidad)){
                $t1_Cantidad = null;
            }
            $t3_Cantidad = $_REQUEST['txt_t3_Cantidad'];                //
            if(empty($t3_Cantidad)){
                $t3_Cantidad = null;
            }
            $t1_Precio = $_REQUEST['txt_t1_Precio'];                    //
            if(empty($t1_Precio)){
                $t1_Precio = null;
            }
            $t3_Precio = $_REQUEST['txt_t3_Precio'];                    //
            if(empty($t3_Precio)){
                $t3_Precio = null;
            }

            $pdf_archivo = $_FILES["txt_file"]["name"];
            $tipo = $_FILES["txt_file"]["type"]; //tipo de archivo
            $tamanho = $_FILES["txt_file"]["size"];
            $temp = $_FILES["txt_file"]["tmp_name"];

            $ruta = dirname(__FILE__) . '/upload/' . $username . "_" . $numfactura . "_" . $fechafactura . ".pdf"; //ruta del archivo
            $directorio = dirname(__FILE__) . '/upload/';                                                          //ruta del archivo para actualizacion

            if ($pdf_archivo != null) {
                if ($tipo == "application/pdf") {
                    if ($tamanho < 2000000) {
                        unlink("upload/" . $row['rutaarchivo']);                        //remueve el archivo del directorio
                        move_uploaded_file($temp, $ruta);                               //carga el nuevo archivo
                        $file = $username . "_" . $numfactura . "_" . $fechafactura . ".pdf";
                        $cargamensaje = "Registro editado!";
                    } else {
                        $errormensaje = "Archivo muy grande (>2MB)";
                    }
                } else {
                    $errorpdf = "Inserte un archivo PDF";
                }
            } else {
                $file = $row['rutaarchivo'];
                $cargamensaje = "Registro editado!";
            }

            if (!isset($errormensaje)) {
                $query = "UPDATE t_factura SET eotruc=:eotruc_new, 
                                             numfactura=:numfactura_new,
                                             totalfactura=:totalfactura_new, 
                                             fechafactura=:fechafactura_new, 
                                             proveedor_ruc=:proveedor_ruc_new,
                                             rutaarchivo=:rutaarchivo_new,
                                             t1_cantidad=:t1_cantidad_new,
                                             t1_precio=:t1_precio_new,
                                             t3_cantidad=:t3_cantidad_new,
                                             t3_precio=:t3_precio_new,
                                             observacion=:observacion
                                              WHERE id=:id";

                $actualizar_stmt = $con->prepare($query);
                $actualizar_stmt->bindParam(':eotruc_new', $username);
                $actualizar_stmt->bindParam(':numfactura_new', $numfactura);
                $actualizar_stmt->bindParam(':totalfactura_new', $totalfactura);
                $actualizar_stmt->bindParam(':fechafactura_new', $fechafactura);
                $actualizar_stmt->bindParam(':proveedor_ruc_new', $proveedor_ruc);
                $actualizar_stmt->bindParam(':rutaarchivo_new', $file);
                $actualizar_stmt->bindParam(':t1_cantidad_new', $t1_Cantidad);
                $actualizar_stmt->bindParam(':t3_cantidad_new', $t3_Cantidad);
                $actualizar_stmt->bindParam(':t1_precio_new', $t1_Precio);
                $actualizar_stmt->bindParam(':t3_precio_new', $t3_Precio);
                $actualizar_stmt->bindParam(':observacion', $observacion);
                $actualizar_stmt->bindParam(':id', $id);

                if ($actualizar_stmt->execute()) {
                    $actualizarmensaje = "Archivo subido exitosamente";
                    header("refresh:1;index.php");
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } else if (isset($row['estado'])) {
        $estadomensaje = "El registro fue validado, no se puede modificar";
    }
}
?>
<div class="row h-100 justify-content-center align-items-center">
    <div class="col-10 col-md-8 col-lg-6">
        <br>
        <h2 class="display-6">Editar Factura</h2>
        <form method="POST" class="form-horizontal justify-content-center" enctype="multipart/form-data">

            <div class="row">
                <div class="col">
                    <label class="col-sm-3 control-label">Emblema</label>
                    <input type="text" name="txt_proveedor_ruc" class="form-control" value=<?php echo $row['proveedor_ruc']; ?> required <?php if (isset($estadomensaje)) {
                                                                                                                                                echo 'disabled';
                                                                                                                                            } ?> />
                </div>
                <div class="col">
                    <label class="col-sm-2 control-label">N°Factura</label>
                    <input type="text" name="txt_numfactura" class="form-control" <?php if (isset($estadomensaje)) {
                                                                                        echo 'disabled';
                                                                                    } ?> required value='<?php echo $row['numfactura']; ?>' />
                </div>
                <div class="col">
                    <label class="col-sm-3 control-label">Fecha</label>
                    <input type="date" name="date_fechafactura" class="form-control" value=<?php echo $row['fechafactura']; ?> required <?php if (isset($estadomensaje)) {
                                                                                                                                            echo 'disabled';
                                                                                                                                        } ?> />
                </div>
            </div>

            </br>

            <div class="accordion" id="accordionPanelsStayOpenExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                            Diesel Tipo I
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                        <div class="accordion-body">

                            <div class="row">
                                <div class="col">
                                    <label class="col-sm-3 control-label">Cantidad</label>
                                    <input type="number" step="any" name="txt_t1_Cantidad" class="form-control" value="<?php echo $row['t1_cantidad']; ?>" <?php if (isset($estadomensaje)) {
                                                                                                                                                            echo 'disabled';
                                                                                                                                                        } ?> />
                                </div>
                                <div class="col">
                                    <label class="col-sm-3 control-label">Precio</label>
                                    <input type="number" step="any" name="txt_t1_Precio" class="form-control" value="<?php echo $row['t1_precio']; ?>" <?php if (isset($estadomensaje)) {
                                                                                                                                                        echo 'disabled';
                                                                                                                                                    } ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                            Diesel Tipo III
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">


                            <div class="row">
                                <div class="col">
                                    <label class="col-sm-3 control-label">Cantidad</label>
                                    <input type="number" step="any" name="txt_t3_Cantidad" class="form-control" value="<?php echo $row['t3_cantidad']; ?>" <?php if (isset($estadomensaje)) {
                                                                                                                                                            echo 'disabled';
                                                                                                                                                        } ?> />
                                </div>
                                <div class="col">
                                    <label class="col-sm-3 control-label">Precio</label>
                                    <input type="number" step="any" name="txt_t3_Precio" class="form-control" value="<?php echo $row['t3_precio']; ?>" <?php if (isset($estadomensaje)) {
                                                                                                                                                        echo 'disabled';
                                                                                                                                                    } ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </br>
            <div class="form-group">
                <label class="col-sm-3 control-label">Total</label>
                <input type="number" step="any" name="txt_totalfactura" class="form-control" value=<?php echo $row['totalfactura']; ?> required <?php if (isset($estadomensaje)) {
                                                                                                                                        echo 'disabled';
                                                                                                                                    } ?> />
            </div>
            <br>
            <div class="form-group">
                    <label for="txt_observacion" class="col-sm-6 form-label">Observación</label>
                    <textarea maxlength="200" class="form-control" name="txt_observacion" id="txt_observacion" placeholder="Observaciones adcionales de la factura (Max: 200 caracteres)" rows="3" <?php if (isset($estadomensaje)) {
                                                                                                                                        echo 'disabled';
                                                                                                                                    } ?> ><?php echo $row['observacion']; ?></textarea>
                </div>                                                                                                                                

            <div class="form-group">
                <label class="col-sm-3 control-label">Archivo</label>
                <input type="file" id="upload" name="txt_file" class="form-control" accept="application/pdf" <?php if (isset($estadomensaje)) {
                                                                                                                    echo 'disabled';
                                                                                                                } ?> />
                <?php if (isset($cargamensaje)) {
                    echo '<label class="text-success">' . $cargamensaje . '</label>';
                } else if (isset($errormensaje)) {
                    echo '<label class="text-danger">' . $errormensaje . '</label>';
                } else if (isset($estadomensaje)) {
                    echo '<label class="text-danger">' . $estadomensaje . '</label>';
                }
                ?>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-offset-3 col-sm-9 m-t-15">
                    <input type="submit" name="btn_actualizar" class="btn btn-secondary" value="Actualizar">
                    <a href="index.php" class="btn btn-secondary">Cancelar</a>
                </div>

        </form>
    </div>
</div>
<script>
    $(function() {
        $('#upload').on('change', function(e) {
            let size = this.files[0].size;

            $('#size').text(size);

            if (size > 2000000) {
                alert('El archivo es muy grande!. El documento no debe pesar mas de 2MB');
            }
        });
    })
</script>
<?php include('footer.html'); ?>