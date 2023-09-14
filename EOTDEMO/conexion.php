<?php
    $host='localhost';
    $bd = 'facturas';
    $usuario = 'postgres';
    $password = '258017';

    try
    {
        $con = new PDO("pgsql:host=$host;dbname=$bd",$usuario,$password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        $e->getMessage();
    }
?>