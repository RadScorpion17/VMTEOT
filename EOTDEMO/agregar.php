<?php
include('header.php');
require_once "conexion.php";

$loadstmt = $con->prepare("SELECT * FROM public.emblemas");
$loadstmt->execute();
$row = $loadstmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_REQUEST['btn_agregar'])) {
    try {
        $numfactura = $_REQUEST['txt_numfactura'];                  //Obtener los inputs del formulario HTML
        $totalfactura = $_REQUEST['txt_totalfactura'];              //
        $fechafactura = $_REQUEST['date_fechafactura'];             //
        $proveedor_ruc = $_REQUEST['txt_proveedor_ruc'];            //
        $observacion = $_REQUEST['txt_observacion'];                //
        $emblema = $_REQUEST['txt_emblema'];
        $idemblema = null;
        foreach($row as $data){
            if($data['emblema']==$emblema){
                $idemblema = $data['id_emblema'];
            }
        }
        if(!isset($idemblema)){
            $insertemblema = $con->prepare("INSERT INTO public.emblemas(emblema) VALUES (?) RETURNING id_emblema");
            $insertemblema->bindParam(1,$emblema);
            if($insertemblema->execute()){
                $insertemblema->fetch(PDO::FETCH_ASSOC);
                $idemblema = $insertemblema['id_emblema'];
            }
        }


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

        $timestamp = $_SERVER['REQUEST_TIME'];                         //Devolver fecha/hora al momento del REQUEST
        $timezone = "America/Asuncion";                                //Establecer zona horaria
        $dt = new DateTime();                                          //Crear objeto Date()
        $dt->setTimestamp($timestamp);                                 //
        $dt->setTimezone(new DateTimeZone($timezone));                 //Convertir GMT a hora local
        $datetime = $dt->format('Y-m-d H:i:s');                        //Formatear de UNIX a UMT

        $pdf_archivo = $_FILES["txt_file"]["name"]; //nombre 
        $tipo = $_FILES["txt_file"]["type"]; //tipo de archivo
        $tamanho = $_FILES["txt_file"]["size"]; //tamaño
        $temp = $_FILES["txt_file"]["tmp_name"]; //ubicacion temporal

        $ruta = dirname(__FILE__) . '/upload/' . $username . "_" . $numfactura ."_". $proveedor_ruc ."_" . $fechafactura . ".pdf"; //ruta del archivo
                
        if ($tipo == "application/pdf") {
            if (!file_exists($ruta)) {
                if ($tamanho < 2000000) {
                    $file = $username . "_" . $numfactura ."_". $proveedor_ruc ."_" . $fechafactura . ".pdf";
                } else {
                    $errormensaje = "Archivo muy grande (>2MB)";
                }
            } else {
                $errormensaje = "El registro o el archivo ya existe";
            }
        } else {
            $errormensaje = "El archivo no es del formato PDF";
        }

        if (!isset($errormensaje)) {
            $insertar_stm = $con->prepare('INSERT INTO t_factura(eotruc,numfactura,totalfactura,fechafactura,proveedor_ruc,rutaarchivo,fechaevento,t1_cantidad,t1_precio,t3_cantidad,t3_precio,observacion, id_emblema) 
            VALUES(:veotruc,:vnumfactura,:vtotalfactura,:vfechafactura,:vproveedor_ruc,:vpdf_archivo,:vfechaevento,:vt1_Cantidad,:vt1_Precio,:vt3_Cantidad,:vt3_Precio,:observacion,:id_emblema)');
            $insertar_stm->bindParam(':veotruc', $username);
            $insertar_stm->bindParam(':vnumfactura', $numfactura);
            $insertar_stm->bindParam(':vtotalfactura', $totalfactura);
            $insertar_stm->bindParam(':vfechafactura', $fechafactura);
            $insertar_stm->bindParam(':vproveedor_ruc', $proveedor_ruc);
            $insertar_stm->bindParam(':vpdf_archivo', $file);
            $insertar_stm->bindParam(':vfechaevento', $datetime);
            $insertar_stm->bindParam(':vt1_Cantidad', $t1_Cantidad);
            $insertar_stm->bindParam(':vt1_Precio', $t1_Precio);
            $insertar_stm->bindParam(':vt3_Cantidad', $t3_Cantidad);
            $insertar_stm->bindParam(':vt3_Precio', $t3_Precio);
            $insertar_stm->bindParam(':observacion', $observacion);
            $insertar_stm->bindParam(':id_emblema', $idemblema);
            
            if ($insertar_stm->execute()) {
                $insertarmensaje = "Archivo guardado exitosamente";
                header("refresh:1;index.php");
                move_uploaded_file($temp, dirname(__FILE__) . '/upload/' . $username . "_" . $numfactura ."_". $proveedor_ruc ."_" . $fechafactura . ".pdf");
            }
        }
    } catch (PDOException $e) {
        $e->getMessage();
        $errormensaje = "El registro o el archivo ya existe".$e;
    }
}
?>
<div class="row h-100 justify-content-center align-items-center">
    <div class="col-10 col-md-8 col-lg-6">
        <br>
        <h2 class="display-6">Añadir Facturas</h2>
        <form method="POST" class="form-horizontal justify-content-center" enctype="multipart/form-data">

            <div class="row">
                <div class="col-sm-8">
                    <label for="txt_numfactura" class="col-sm-3 form-label">N°.Factura</label>
                    <input type="text" name="txt_numfactura" class="form-control" placeholder="001-001-0000001" required />
                </div>
                <div class="col-sm-4">
                    <label for="date_fechafactura" class="col-sm-6 form-label">Fecha Factura</label>
                    <input type="date" name="date_fechafactura" class="form-control" required />
                </div>
            </div>
<br>
            <div class="row">
            <div class="col">
                    <label for="txt_proveedor_ruc" class="col-sm-8 form-label">RUC Proveedor</label>
                    <input type="text" name="txt_proveedor_ruc" class="form-control" placeholder="123456-8" required />
            </div>
            <div class="col">
                    <label for="emblema" class="col-sm-8 form-label">Emblema Proveedor</label>
                    <input class="form-control" name="txt_emblema" list="emblemas" placeholder="PETROPAR" required>
                    <datalist id="emblemas">
                        <?php
                            foreach($row as $data){
                                echo '<option>'.$data['emblema'].'</option>';
                            }
                        ?>
                    </datalist>
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
                                    <label for="txt_t1_Cantidad" class="col-sm-3 form-label">Cantidad</label>
                                    <input type="number" step="any" name="txt_t1_Cantidad" id="txt_t1_Cantidad" class="form-control" placeholder="Litros combustible" oninput="calcular();"/>
                                </div>
                                <div class="col">
                                    <label for="txt_t1_Precio" class="col-sm-3 form-label">Precio</label>
                                    <input type="number" step="any" name="txt_t1_Precio" id="txt_t1_Precio" class="form-control" placeholder="Precio unitario" oninput="calcular();"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                            Diesel Tipo III
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col">
                                    <label for="txt_t3_Cantidad" class="col-sm-3 form-label">Cantidad</label>
                                    <input type="number" step="any" name="txt_t3_Cantidad" id="txt_t3_Cantidad" class="form-control" placeholder="Litros combustible" oninput="calcular();"/>
                                </div>
                                <div class="col">
                                    <label for="txt_t3_Precio" class="col-sm-3 form-label">Precio</label>
                                    <input type="number" step="any" name="txt_t3_Precio" id="txt_t3_Precio" class="form-control" placeholder="Precio unitario" oninput="calcular();"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
</div>
</br>
                <div class="form-group">
                    <label for="txt_totalfactura" class="col-sm-3 form-label">Total</label>
                    <input type="number" step="any" name="txt_totalfactura" id="txt_totalfactura" class="form-control" placeholder="Costo total" required />
                </div>
<br>
                <div class="form-group">
                    <label for="txt_totalfactura" class="col-sm-6 form-label">Observación</label>
                    <textarea maxlength="200" class="form-control" name="txt_observacion" id="txt_observacion" placeholder="Observaciones adcionales de la factura (Max: 200 caracteres)" rows="3"></textarea>
                </div>
            <br>
                <div class="form-group">
                    <label for="txt_file" class="col-sm-3 form-label">Archivo</label>
                    <input type="file" name="txt_file" id="upload" class="form-control" accept="application/pdf" required />
                    <div style="margin-top:20px">Size: <span id="size">0</span> bytes</div>
                    <?php if (isset($errormensaje)) {
                        echo '<label class="text-danger">' . $errormensaje . '</label>';
                    } else if (isset($insertarmensaje)) {
                        echo '<label class="text-success">' . $insertarmensaje . '</label>';
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"></label>
                    <div class="col-sm-offset-3 col-sm-9 m-t-15">
                        <input type="submit" name="btn_agregar" class="btn btn-secondary" value="Agregar">
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
        </form>
    </div>
</div>
<script>
    function calcular(){
        var t1cant = document.getElementById("txt_t1_Cantidad").value;
        var t1precio = document.getElementById("txt_t1_Precio").value;
        var t3cant = document.getElementById("txt_t3_Cantidad").value;
        var t3precio = document.getElementById("txt_t3_Precio").value;
        var total1 = t1cant*t1precio;
        var total3 = t3cant*t3precio;
        var total = total1 + total3;
        document.getElementById("txt_totalfactura").value = total;
    }
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