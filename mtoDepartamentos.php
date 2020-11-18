<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
require_once './config/confArchivo.php';
if (isset($_REQUEST["volver"])) {
    header('Location: ' . rutaCodigo . "//");
}
if (isset($_REQUEST["importar"])) {
    
}
if (isset($_REQUEST["exportar"])) {
    //exec("php codigoPHP/exportar.php");
    //shell_exec("php codigoPHP/exportar.php");
    require 'codigoPHP/exportar.php';
    /* header('Content-Disposition: attachment;filename="SQL.xml"');
      header('Content-Type: text/xml');
      readfile(rutaTmp."/SQL.xml"); */
}
if (isset($_REQUEST["anadir"])) {
    header('Location:' . rutaCodigo . '/anadir.php');
}
if (isset($_REQUEST["volver"])) {
    
}
if (isset($_REQUEST["mostrarCodigo"])) {
    
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <style>
            a{
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="botones">
                <input type="submit" value="importar" name="importar">
                <input type="submit" value="exportar" name="exportar">
                <input type="submit" value="anadir" name="anadir">
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
            $sql = "Select * from Departamento";
        }
        require_once './config/confDBPDO.php';
        try {
            $miDB = new PDO(DSN, USER, PASSWORD);

            $departamentos = $miDB->prepare($sql);
            if (isset($valores)) {
                $eje = $departamentos->execute($valores);
            } else {
                $eje = $departamentos->execute();
            }

            if ($eje) {
                $oDepartamento = $departamentos->fetchObject();
                echo <<<EOF
                    <table>
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Descripcion</th>
                                <th>Fecha de Baja</th>
                                <th>Volumen Negocio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                EOF;
                while ($oDepartamento) {
                    echo "<tr>\n\t<th>" . $oDepartamento->CodDepartamento . "</th>";
                    echo "\t<td>" . $oDepartamento->DescDepartamento . "</td>";
                    echo "\t<td>" . $oDepartamento->FechaBaja . "</td>";
                    echo "\t<td>" . $oDepartamento->VolumenNegocio . "</td>";
                    echo "<td><a href=\"".rutaCodigo."/editar?codigo=".$oDepartamento->CodDepartamento."\">&#9999;&#65039;</a>	<a href=\"".rutaCodigo."/borrar?codigo=".$oDepartamento->CodDepartamento."\">&#128465;&#65039;</a> <a href=\"".rutaCodigo."/buscar?codigo=".$oDepartamento->CodDepartamento."\">&#128270;</a> </td>\n<tr>";

                    $oDepartamento = $departamentos->fetchObject();
                }
                echo "</tbody>\n\t</table>";
            } else {
                throw new Exception("Error al hacer la busqueda \"" . $departamentos->errorInfo()[2] . "\"", $departamentos->errorInfo()[1]);
            }
        } catch (Exception $e) {
            echo "<p>Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")</p>";
        } finally {
            unset($miDB);
        }
        ?>
    </body>
</html>
