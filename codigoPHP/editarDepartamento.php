<!DOCTYPE html>
<?php
session_start();
require_once '../config/confArchivo.php';
if (isset($_REQUEST['cancelar'])) {
    $_SESSION['editar']['ejecucion'] = true;
    $_SESSION['editar']['mensaje'] = "No se ha modificado el departamento";
    header("Location: " . rutaIndex . "?CodPagina=editar");
    die();
}
require_once '../core/201109libreriaValidacion.php';
require_once '../config/confDBPDO.php';

$errores = array(
    "descripcion" => null,
    "volumen" => null,
);

$formulario = array(
    "codigo" => null,
    "descripcion" => null,
    "volumen" => null
);

define("OBLIGATORIO", 1);
$entradaOK = true;


if (isset($_REQUEST['enviar'])) {
    $errores["descripcion"] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['descripcion'], 255, 5, OBLIGATORIO);
    $errores["volumen"] = validacionFormularios::comprobarFloat($_REQUEST['volumen'], PHP_FLOAT_MAX, 0, OBLIGATORIO);

    foreach ($errores as $clave => $error) {
        if ($error != null) {
            $_REQUEST[$clave] = "";
            $entradaOK = false;
        }
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
        $entradaOK = false;
    }
}

if ($entradaOK) {

    $formulario['codigo'] = $_REQUEST['codigo'];
    $formulario['descripcion'] = $_REQUEST['descripcion'];
    $formulario['volumen'] = $_REQUEST['volumen'];

    $sql = <<<EOF
        update Departamento
        set DescDepartamento = :descripcion,
        VolumenNegocio = :volumen
        where CodDepartamento = :codigo;
   EOF;

    try {
        $miDB = new PDO(DSN, USER, PASSWORD);
        $consulta = $miDB->prepare($sql);
        $ejecucion = $consulta->execute(array(":codigo" => $formulario['codigo'], ":descripcion" => $formulario['descripcion'], ":volumen" => $formulario['volumen']));

        if ($ejecucion) {
            $_SESSION['editar']['ejecucion'] = true;
            $_SESSION['editar']['mensaje'] = "El departamento ha sido modificado";
        } else {
            throw new Exception("Error al hacer la busqueda \"" . $insercion->errorInfo()[2] . "\"", $insercion->errorInfo()[1]);
        }
    } catch (Exception $e) {
        $_SESSION['editar']['ejecucion'] = false;
        $_SESSION['editar']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    } finally {
        unset($conexion);
        header("Location: " . rutaIndex . "?CodPagina=editar");
        die();
    }
} else {
    ?>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Editar departamento</title>
            <link rel="stylesheet" type="text/css" href="../webroot/css/estilos.css">
        </head>
        <body>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="codigo">Codigo del departamento: </label>
                <input type="text" id="codigo" name="codigo"  readonly value="<?php if (isset($_REQUEST["codigo"])) echo $_REQUEST["codigo"]; ?>"><br>
                <label for="descripcion">Introduce una descripción del departamento: </label>
                <input type="text" id="descripcion" name="descripcion" value="<?php if (isset($_REQUEST["descripcion"])) echo $_REQUEST["descripcion"]; ?>"><br>
                <?php
                echo!empty($errores['descripcion']) ? "<p class=\"error\">" . $errores['descripcion'] . "</p>" : "";
                ?>
                <label for="fecha">Fecha de baja </label>
                <input type="text" id="fecha" name="fecha" readonly value="<?php if (isset($_REQUEST["fechaBaja"])) echo $_REQUEST["fechaBaja"]; ?>"><br>
                <label for="volumen">Introduce el volumen de negocio: </label>
                <input type="text" id="volumen" name="volumen" value="<?php if (isset($_REQUEST["volumen"])) echo $_REQUEST["volumen"]; ?>"><br>
                <?php
                echo!empty($errores['volumen']) ? "<p class=\"error\">" . $errores['volumen'] . "</p>" : "";
                ?>
                <input type="submit" value="cancelar" name="cancelar">
                <input type="submit" value="Editar" name="enviar">
            </form>
            <?php
        }
        ?>
    </body>
</html>
