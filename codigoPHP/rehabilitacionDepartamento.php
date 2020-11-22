<?php
session_start();
require_once '../config/confArchivo.php';
if (isset($_REQUEST['cancelar'])) {
    $_SESSION['rehab']['ejecucion'] = true;
    $_SESSION['rehab']['mensaje'] = "No se ha rehabilitado el departamento";
    header("Location: " . rutaIndex . "?CodPagina=rehab");
    die();
}
require_once '../core/201109libreriaValidacion.php';
require_once '../config/confDBPDO.php';

$errores = array(
    "fecha" => null,
);

$formulario = array(
    "codigo" => null,
    "fecha" => null,
);

define("OBLIGATORIO", 1);


if (isset($_REQUEST['enviar'])) {
    $formulario['codigo'] = $_REQUEST['codigo'];
    $sql = <<<EOF
        update Departamento
        set FechaBaja = null
        where CodDepartamento = :codigo;
   EOF;

    try {
        $miDB = new PDO(DSN, USER, PASSWORD);
        $consulta = $miDB->prepare($sql);
        $consulta->bindParam(":codigo", $_REQUEST["codigo"]);
        $ejecucion = $consulta->execute();

        if ($ejecucion) {
            $_SESSION['rehab']['ejecucion'] = true;
            $_SESSION['rehab']['mensaje'] = "El departamento ha sido habilitado de nuevo";
        } else {
            throw new Exception("Error al hacer la busqueda \"" . $insercion->errorInfo()[2] . "\"", $insercion->errorInfo()[1]);
        }
    } catch (Exception $e) {
        $_SESSION['rehab']['ejecucion'] = false;
        $_SESSION['rehab']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    } finally {
        unset($conexion);
        header("Location: " . rutaIndex . "?CodPagina=rehab");
        die();
    }
} else {
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
        } else {
            throw new Exception("Error al hacer la busqueda \"" . $consulta->errorInfo()[2] . "\"", $consulta->errorInfo()[1]);
        }
    } catch (Exception $e) {
        echo "<p>Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")</p>";
    } finally {
        unset($miDB);
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Rehabilitar departamento</title>
            <link rel="stylesheet" type="text/css" href="../webroot/css/estilos.css">
        </head>
        <body>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="codigo">Codigo del departamento: </label>
                <input type="text" id="codigo" name="codigo"  readonly value="<?php if (isset($_REQUEST["codigo"])) echo $_REQUEST["codigo"]; ?>"><br>
                <label for="descripcion">Introduce una descripción del departamento: </label>
                <input type="text" id="descripcion" name="descripcion" readonly value="<?php if (isset($_REQUEST["descripcion"])) echo $_REQUEST["descripcion"]; ?>"><br>

                <label for="fecha">Seleciona una fecha</label>
                <input type="date" id="fecha" name="fecha" readonly value="<?php if (isset($_REQUEST["fechaBaja"])) echo $_REQUEST["fechaBaja"]; ?>"><br>
                <label for="volumen">Introduce el volumen de negocio: </label>
                <input type="text" id="volumen" name="volumen" readonly value="<?php if (isset($_REQUEST["volumen"])) echo $_REQUEST["volumen"]; ?>"><br>
                <input type="submit" value="cancelar" name="cancelar">
                <input type="submit" value="Editar" name="enviar">
            </form>
            <?php
        }
        ?>

    </body>
</html>
