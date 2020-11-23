<?php
session_start();
require_once '../config/confArchivo.php';
if (isset($_REQUEST['cancelar'])) {
    $_SESSION['importar']['ejecucion'] = true;
    $_SESSION['importar']['mensaje'] = "No se ha creado el departamento";
    header("Location: " . rutaIndex . "?CodPagina=importar");
    die();
}
?>

<?php
require_once '../config/confDBPDO.php';
$entradaOK = true;
$archivo = null;
$error = null;
if (isset($_REQUEST["importar"])) {
    if (!empty($_FILES["archivo"]["name"])) {
        $archivo = $_FILES['archivo']['tmp_name'];
    } else {
        $error = "Introduce un archivo";
        $entradaOK = false;
    }
} else {
    $entradaOK = false;
}
if ($entradaOK) {
    try {
        $dom = new DOMDocument;
        $dom->load($archivo);
        $miDB = new PDO(DSN, USER, PASSWORD);

        $prepare = $miDB->prepare("Insert into Departamento values (:codigo, :descripcion, :fecha, :volumen)");

        $departamento = $dom->getElementsByTagName('Departamento');
        $miDB->beginTransaction();
        foreach ($departamento as $dep) {
            $valores = $dep->childNodes;

            $aValores = array(
                ":codigo" => $valores->item(1)->nodeValue,
                ":descripcion" => $valores->item(3)->nodeValue,
                ":fecha" => empty($valores->item(5)->nodeValue) ? null : $valores->item(5)->nodeValue,
                ":volumen" => $valores->item(7)->nodeValue
            );

            $eje = $prepare->execute($aValores);

            if (!$eje) {
                throw new Exception("Error al insertar en la base de datos " . $prepare->errorCode());
            }
        }
        $miDB->commit();
        $_SESSION['importar']['ejecucion'] = true;
        $_SESSION['importar']['mensaje'] = "Todos los datos han sido importados";
    } catch (Exception $e) {
        session_start();
        $_SESSION['importar']['ejecucion'] = false;
        $_SESSION['importar']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    } finally {
        unset($miDB);
        session_commit();
        header("Location: " . rutaIndex . "?CodPagina=importar");
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Importar</title>
            <link rel="stylesheet" type="text/css" href="../webroot/css/estilos.css">
        </head>
        <body>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"  enctype = "multipart/form-data">
                <input type="file" name="archivo" >
                <?php
                if (is_null($error)) {
                    echo "<span style=\"color:blue;\">$error</span>";
                }
                ?>
                <br>
                <input type="submit" name="importar" value="Importar">
                <input type="submit" name="cancelar" value="Cancelar">
            </form>
        </body>
    </html>
<?php } 
