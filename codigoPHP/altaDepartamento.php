<?php
session_start();
require_once '../config/confArchivo.php';
if (isset($_REQUEST['cancelar'])) {
    $_SESSION['alta']['ejecucion'] = true;
    $_SESSION['alta']['mensaje'] = "No se ha creado el departamento";
    header("Location: " . rutaIndex . "?CodPagina=alta");
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Añadir Departamento</title>
        <link rel="stylesheet" type="text/css" href="../webroot/css/estilos.css">
        <script>
            function elementoAMayusculas(elemento) {
                elemento.value = elemento.value.toUpperCase();
            }
        </script>
    </head>
    <body>
        <?php
        require_once '../core/201109libreriaValidacion.php';
        require_once '../config/confDBPDO.php';

        $errores = array(
            "codigo" => null,
            "descripcion" => null,
            "volumen" => null,
            "conexion" => null
        );

        $formulario = array(
            "codigo" => null,
            "descripcion" => null,
            "volumen" => null
        );

        define("OBLIGATORIO", 1);
        $entradaOK = true;

        if (isset($_REQUEST['enviar'])) {

            $errores["codigo"] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['codigo'], 3, 3, OBLIGATORIO);
            $errores["descripcion"] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['descripcion'], 255, 5, OBLIGATORIO);
            $errores["volumen"] = validacionFormularios::comprobarFloat($_REQUEST['volumen'], PHP_FLOAT_MAX, 0, OBLIGATORIO);

            foreach ($errores as $clave => $error) {
                if ($error != null) {
                    $_REQUEST[$clave] = "";
                    $entradaOK = false;
                }
            }
            if (isset($_REQUEST['codigo'])) {
                try {
                    $conexion = new PDO(DSN, USER, PASSWORD);
                    $prepare = $conexion->prepare("Select CodDepartamento from Departamento where CodDepartamento = :codigo");
                    $prepare->bindParam(":codigo", $_REQUEST['codigo']);
                    $ejecucion = $prepare->execute();
                    if ($ejecucion) {
                        if ($prepare->rowCount() > 0) {
                            $entradaOK = false;
                            $_REQUEST['codigo'] = "";
                            $errores["codigo"] .= " El codigo de departamento ya existe por favor introduce otro";
                        }
                    } else {
                        throw new ErrorException("Error al ejecutar la sentencia");
                    }
                } catch (Exception $e) {
                    $errores['conexion'] = "Error al realizar la conexion ( " . $e->getMessage() . " )";
                    $entradaOK = false;
                } finally {
                    unset($conexion);
                    unset($prepare);
                    unset($ejecucion);
                }
            }
        } else {
            $entradaOK = false;
        }

        if ($entradaOK) {

            $formulario['codigo'] = $_REQUEST['codigo'];
            $formulario['descripcion'] = $_REQUEST['descripcion'];
            $formulario['volumen'] = $_REQUEST['volumen'];
            try {
                $miDB = new PDO(DSN, USER, PASSWORD);
                $insercion = $miDB->prepare("Insert into Departamento (CodDepartamento,DescDepartamento,VolumenNegocio) values (:codigo, :descripcion, :volumen)");
                $ejecucion = $insercion->execute(array(":codigo" => $formulario['codigo'], ":descripcion" => $formulario['descripcion'], ":volumen" => $formulario['volumen']));

                if ($ejecucion) {
                    $_SESSION['alta']['ejecucion'] = true;
                    $_SESSION['alta']['mensaje'] = "El departamento ha sido dado de alta";
                } else {
                    throw new Exception("Error al hacer la busqueda \"" . $insercion->errorInfo()[2] . "\"", $insercion->errorInfo()[1]);
                }
            } catch (Exception $e) {
                $_SESSION['alta']['ejecucion'] = false;
                $_SESSION['alta']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
            } finally {
                unset($conexion);
                header("Location: " . rutaIndex . "?CodPagina=alta");
                die();
            }
        } else {
            ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <label for="codigo">Introduce el codigo del departamento: </label>
                <input type="text" id="codigo" name="codigo" onblur="elementoAMayusculas(this)" value="<?php if (isset($_REQUEST["codigo"])) echo $_REQUEST["codigo"]; ?>">
                <?php
                echo!empty($errores['codigo']) ? "<span class=\"error\">" . $errores['codigo'] . "</span>" : "";
                ?><br>
                <label for="descripcion">Introduce una descripción del departamento: </label>
                <input type="text" id="descripcion" name="descripcion" value="<?php if (isset($_REQUEST["descripcion"])) echo $_REQUEST["descripcion"]; ?>">
                <?php
                echo!empty($errores['descripcion']) ? "<span class=\"error\">" . $errores['descripcion'] . "</span>" : "";
                ?><br>
                <label for="volumen">Introduce el volumen de negocio: </label>
                <input type="text" id="volumen" name="volumen" value="<?php if (isset($_REQUEST["volumen"])) echo $_REQUEST["volumen"]; ?>">
                <?php
                echo!empty($errores['volumen']) ? "<span class=\"error\">" . $errores['volumen'] . "</span>" : "";
                echo!empty($errores['conexion']) ? "<span class=\"error\">" . $errores['conexion'] . "</span>" : "";
                ?><br>
                <input type="submit" value="Cancelar" name="cancelar">
                <input type="submit" value="Crear" name="enviar">
            </form>
            <?php
        }
        ?>
    </body>
</html>
