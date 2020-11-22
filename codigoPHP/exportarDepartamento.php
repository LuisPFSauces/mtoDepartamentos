<?php

require_once '../config/confDBPDO.php';
require_once '../config/confArchivo.php';

function crearHijo($nombre, $dom, &$nodo, $valor = null) {
    if ($dom instanceof DOMDocument && $nodo instanceof DOMElement) {
        if (is_null($valor)) {
            $elemento = $dom->createElement($nombre);
        } else {
            $elemento = $dom->createElement($nombre, $valor);
        }

        $nodo->appendChild($elemento);
        return $elemento;
    } else {
        return null;
    }
}

try {

    $miDB = new PDO(DSN, USER, PASSWORD);
    $consulta = $miDB->prepare("Select * from Departamento");
    $ejecucion = $consulta->execute();
    if($ejecucion){
    $dom = new DOMDocument("1.0", "UTF-8");
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = true;


    $root = $dom->createElement("Departamentos");
    $dom->appendChild($root);

    $oDepartamento = $consulta->fetchObject();
    while ($oDepartamento) {
        $departamento = crearHijo("Departamento", $dom, $root);
        crearHijo('CodDepartamento', $dom, $departamento, $oDepartamento->CodDepartamento);
        crearHijo('DescDepartamento', $dom, $departamento, $oDepartamento->DescDepartamento);
        crearHijo('FechaBaja', $dom, $departamento, $oDepartamento->FechaBaja);
        crearHijo('Volumen', $dom, $departamento, $oDepartamento->VolumenNegocio);

        $oDepartamento = $consulta->fetchObject();
    }
    $dom->save("../tmp/SQL.xml");
    header('Content-Disposition: attachment;filename="SQL.xml"');
    header('Content-Type: text/xml');
    readfile("../tmp/SQL.xml");
    
    } else{
         throw new Exception("Error al hacer la busqueda \"" . $consulta->errorInfo()[2] . "\"", $consulta->errorInfo()[1]);
    }
} catch (Exception $e) {
    session_start();
    $_SESSION['exportar']['ejecucion'] = false;
    $_SESSION['exportar']['mensaje'] = "Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")";
    header("Location: " . rutaIndex . "?CodPagina=exportar");
} finally {
    unset($miDB);
    session_commit();
}
