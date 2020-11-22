<!DOCTYPE html>
<?php
require_once '../config/confArchivo.php';
session_start();
if (isset($_REQUEST['cancelar'])) {
    $_SESSION['baja']['ejecucion'] = true;
    $_SESSION['baja']['mensaje'] = "No se ha borrado el departamento";
    header("Location: " . rutaIndex . "?CodPagina=baja");
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
    
    try {
        $miDB = new PDO(DSN, USER, PASSWORD);
        $consulta = $miDB->prepare("delete from Departamento where CodDepartamento = :codigo");
        $consulta->bindParam(":codigo", $_REQUEST["codigo"]);
        $ejecucion = $consulta->execute();

        if ($ejecucion) {
            $_SESSION['baja']['ejecucion'] = true;
            $_SESSION['baja']['mensaje'] = "El departamento ha sido borrado";
        } else {
            throw new Exception("Error al hacer la busqueda \"" . $consulta->errorInfo()[2] . "\"", $consulta->errorInfo()[1]);
        }
    } catch (Exception $e) {
        $_SESSION['baja']['ejecucion'] = false;
        $_SESSION['baja']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    } finally {
        unset($conexion);
        header("Location: " . rutaIndex . "?CodPagina=baja");
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
            ?>

            <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Buscar departamento</title>
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
                        <input type="submit" value="cancelar" name="cancelar">
                        <input type="submit" value="Borrar" name="enviar">
                    </form>
                    <?php
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
        ?>
    </body>
</html>
