<?php
require_once '../config/confArchivo.php';
if (isset($_REQUEST['volver'])) {
    header("Location: " . rutaIndex);
    die();
}
require_once '../core/201109libreriaValidacion.php';
require_once '../config/confDBPDO.php';


try {
    $miDB = new PDO(DSN, USER, PASSWORD);
    $consulta = $miDB->prepare("Select * from Departamento where CodDepartamento = :codigo limit 1");
    $consulta->bindParam(":codigo", $_REQUEST['codigo']);
    $eje = $consulta->execute();
    if ($eje) {
        $departamento = $consulta->fetchObject();
        $_REQUEST["descripcion"] = $departamento->DescDepartamento;
        $_REQUEST["fechaBaja"] = $departamento->FechaBaja;
        $_REQUEST["volumen"] = $departamento->VolumenNegocio;
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="UTF-8">
                <title>Mostrar departamento</title>
                <link rel="stylesheet" type="text/css" href="../webroot/css/estilos.css">
            </head>
            <body>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <label for="codigo">Codigo del departamento: </label>
                    <input type="text" id="codigo" name="codigo"  readonly value="<?php if (isset($_REQUEST["codigo"])) echo $_REQUEST["codigo"]; ?>"><br>
                    <label for="descripcion">Introduce una descripción del departamento: </label>
                    <input type="text" id="descripcion" name="descripcion" readonly value="<?php if (isset($_REQUEST["descripcion"])) echo $_REQUEST["descripcion"]; ?>"><br>
                    <label for="fecha">Fecha de baja </label>
                    <input type="text" id="fecha" name="fecha" readonly value="<?php if (isset($_REQUEST["fechaBaja"])) echo $_REQUEST["fechaBaja"]; ?>"><br>
                    <label for="volumen">Introduce el volumen de negocio: </label>
                    <input type="text" id="volumen" name="volumen" readonly value="<?php if (isset($_REQUEST["volumen"])) echo $_REQUEST["volumen"]; ?>"><br>
                    <input type="submit" value="Volver" name="volver">
                </form>
            </body>
        </html>
        <?php
    } else {
        throw new Exception("Error al hacer la busqueda \"" . $consulta->errorInfo()[2] . "\"", $consulta->errorInfo()[1]);
    }
} catch (Exception $e) {
    session_start();
    $_SESSION['mostar']['ejecucion'] = false;
    $_SESSION['mostar']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    header("Location: " . rutaIndex . "?CodPagina=mostar");
} finally {
    unset($miDB);
    die();
}
