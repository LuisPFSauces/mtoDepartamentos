<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
require_once './config/confArchivo.php';
if (isset($_REQUEST["volver"])) {
    header('Location: ' . rutaIndex . "/");
}
if (isset($_REQUEST["importar"])) {
    header('Localtion: ');
}
if (isset($_REQUEST["exportar"])) {
    
}
if (isset($_REQUEST["anadir"])) {
    
}
if (isset($_REQUEST["volver"])) {
    
}
if (isset($_REQUEST["volver"])) {
    
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="botones">
                <input type="submit" value="importar" >
                <input type="submit" value="exportar" >
                <input type="submit" value="añadir" >
            </div>
            <div class="busqueda">
                <label for="busqueda">Introduce el codigo departamento</label>
                <input type="text" id="busqueda" name="busqueda">
                <input type="submit" value="buscar" name="buscar">
            </div>
        </form>
<?php
if (isset($_REQUEST["buscar"]) && !empty($_REQUEST["busqueda"])) {
    $sql = "Select * from Departamento where DescDepartamento like :descripcion";
    $valores = array(
        ":descripcion" => "%" . $_REQUEST["busqueda"] . "%"
    );
} else {
    $sql = "Select * from Departamaneto";
}
require_once './config/confDBPDO.php';
try {
    $miDB = new PDO(DSN, USER, PASSWORD);
    
    $departamentos = $miDB->prepare($sql);
    $eje = $departamentos->execute($valores);

    if ($eje) {
        $departamentos->fetchObject();
        while ($departamentos) {

            $departamentos->fetchObject();
        }
    } else {
        throw new Exception("Error al hacer la busqueda " . $departamentos->errorInfo(), $departamentos->errorCode());
    }
} catch (Exception $e) {
    echo "<p>Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")</p>";
}
?>
    </body>
</html>
