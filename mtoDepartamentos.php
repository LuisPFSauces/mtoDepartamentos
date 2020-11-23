<?php
require_once 'config/confArchivo.php';
require_once './config/confDBPDO.php';

if (isset($_REQUEST["volver"])) {
    session_commit();
    header('Location: ' . rutaCodigo . "//");
}
if (isset($_REQUEST["importar"])) {
    session_commit();
    header('Location:' . rutaCodigo . '/importarDepartamento.php?importar=' . $_REQUEST["importar"]);
}
if (isset($_REQUEST["exportar"])) {
    session_commit();
    header('Location:' . rutaCodigo . '/exportarDepartamento.php');
}
if (isset($_REQUEST["alta"])) {
    session_commit();
    header('Location:' . rutaCodigo . '/altaDepartamento.php');
}
if (isset($_REQUEST["volver"])) {
    session_commit();
    header("Location: " . rutaAtras);
}
if (isset($_REQUEST["codigo"])) {
    session_commit();
    header("Location: " . rutaCodigo . "/mostrarCodigo.php");
}

session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Mantenimiento Departamentos</title>
        <link type="text/css" rel="stylesheet" href="webroot/css/estilo.css">
    </head>
    <body>
        <h1>Luis Puente Fernandez</h1>
        <?php
        if (isset($_REQUEST["buscar"])) {
            $_SESSION['fecha'] = $_REQUEST['fecha'];
        }
        ?>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" class="buscador">
            <div class="busqueda">
                <label for="busqueda">Introduce el codigo departamento</label>
                <input type="text" id="busqueda" name="busqueda" value="<?php echo isset($_REQUEST["buscar"]) ? $_REQUEST["busqueda"] : ''; ?>">
                <input type="submit" value="buscar" name="buscar">
            </div>
            <div class="estado">
                <p>Estado del Departamento: </p>
                <label for="todos">Todos: </label>
                <input type="radio" id="todos" name="fecha" value="todos" <?php
                if (isset($_SESSION['fecha'])) {
                    echo $_SESSION['fecha'] == "todos" ? 'checked' : '';
                } else {
                    echo 'checked';
                    $_SESSION['fecha'] = "todos";
                }
                ?>>
                <label for="alta">Alta: </label>
                <input type="radio" id="alta" name="fecha" value="alta" <?php echo isset($_SESSION['fecha']) && $_SESSION['fecha'] == "alta" ? 'checked' : ''; ?>>
                <label for="baja">Baja: </label>
                <input type="radio" id="baja" name="fecha" value="baja" <?php echo isset($_SESSION['fecha']) && $_SESSION['fecha'] == "baja" ? 'checked' : ''; ?>>
            </div>
        </form>
        <?php
        define("MAX_CONSULTAS", 5);
        if (isset($_REQUEST['anterior']) && $_SESSION['pagina'] > 1) {
            $_SESSION['pagina']--;
        } else if (isset($_REQUEST['siguiente']) && $_SESSION['pagina'] < $_SESSION['total_paginas']) {
            $_SESSION['pagina']++;
        } else if (isset($_REQUEST['final'])) {
            $_SESSION['pagina'] = $_SESSION['total_paginas'];
        } else {
            $_SESSION['pagina'] = 1;
        }
        $limite = "limit " . ($_SESSION['pagina'] > 0 ? ($_SESSION['pagina'] - 1) * MAX_CONSULTAS . "," . MAX_CONSULTAS : $_SESSION['pagina'] * MAX_CONSULTAS . "," . MAX_CONSULTAS);

        if (isset($_REQUEST["buscar"]) && !empty($_REQUEST["busqueda"])) {
            if ($_SESSION['fecha'] == "baja") {
                $sql = "Select * from Departamento where DescDepartamento like :descripcion and FechaBaja is not null";
            } else if ($_SESSION['fecha'] == "alta") {
                $sql = "Select * from Departamento where DescDepartamento like :descripcion and FechaBaja is null";
            } else {
                $sql = "Select * from Departamento where DescDepartamento like :descripcion ";
            }
            $valores = array(
                ":descripcion" => "%" . $_REQUEST["busqueda"] . "%"
            );
        } else {
            if ($_SESSION['fecha'] == "baja") {
                $sql = "Select * from Departamento where FechaBaja is not null";
            } else if ($_SESSION['fecha'] == "alta") {
                $sql = "Select * from Departamento where FechaBaja is null";
            } else {
                $sql = "Select * from Departamento";
            }
        }



        try {
            $miDB = new PDO(DSN, USER, PASSWORD);

            if (!isset($_SESSION['total_paginas']) || isset($_REQUEST['buscar'])) {
                $departamentos = $miDB->prepare($sql);

                if (isset($valores)) {
                    $eje = $departamentos->execute($valores);
                } else {
                    $eje = $departamentos->execute();
                }
                $total = $departamentos->rowCount();
                if ($total == 0) {
                    $_SESSION['total_paginas'] = 1;
                } else if ($total % MAX_CONSULTAS == 0) {
                    $_SESSION['total_paginas'] = $total / MAX_CONSULTAS;
                } else {
                    $_SESSION['total_paginas'] = floor($total / MAX_CONSULTAS) + 1;
                }
            }
            $sql .= " " . $limite;
            $departamentos = $miDB->prepare($sql);

            if (isset($valores)) {
                $eje = $departamentos->execute($valores);
            } else {
                $eje = $departamentos->execute();
            }

            if ($eje) {
                if ($departamentos->rowCount() > 0) {


                    $oDepartamento = $departamentos->fetchObject();
                    echo "
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
                        <tbody>";

                    while ($oDepartamento) {
                        is_null($oDepartamento->FechaBaja) ? $clase = "alta" : $clase = "baja";
                        echo "<tr>\n\t<th class=\"$clase\">" . $oDepartamento->CodDepartamento . "</th>";
                        echo "\t<td class=\"$clase\">" . $oDepartamento->DescDepartamento . "</td>";
                        echo "\t<td class=\"$clase\">" . $oDepartamento->FechaBaja . "</td>";
                        echo "\t<td class=\"$clase\">" . $oDepartamento->VolumenNegocio . "</td>";
                        echo "<td><a href=\"" . rutaCodigo . "/editarDepartamento.php?codigo=" . $oDepartamento->CodDepartamento . "\">&#9999;&#65039;</a>	<a href=\"" . rutaCodigo . "/bajaDepartamento.php?codigo=" . $oDepartamento->CodDepartamento . "\">&#128465;&#65039;</a> <a href=\"" . rutaCodigo . "/mostrarDepartamento.php?codigo=" . $oDepartamento->CodDepartamento . "\">&#128270;</a>";
                        if (is_null($oDepartamento->FechaBaja)) {
                            echo "<a href=\"" . rutaCodigo . "/bajaLogicaDepartamento.php?codigo=" . $oDepartamento->CodDepartamento . "\">&#128234;</a></td>\n</tr>";
                        } else {
                            echo "<a href=\"" . rutaCodigo . "/rehabilitacionDepartamento.php?codigo=" . $oDepartamento->CodDepartamento . "\">&#128235;</a></td>\n</tr>";
                        }
                        $oDepartamento = $departamentos->fetchObject();
                    }
                    echo "</tbody>\n\t</table>";
                } else {
                    echo "<p class=\"error\">No hay ningun departamento que coincida con las caracteristicas introducidas</p>";
                }
            } else {
                throw new Exception("Error al hacer la busqueda \"" . $departamentos->errorInfo()[2] . "\"", $departamentos->errorInfo()[1]);
            }
        } catch (Exception $e) {
            echo "<p class=\"error\" >Se ha producido un error al conectar con la base de datos( " . $e->getMessage() . ", " . $e->getCode() . ")</p>";
        } finally {
            unset($miDB);
        }
        ?>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" class="opciones">
            <div class="botones">
                <input type="submit" value="importar" name="importar">
                <input type="submit" value="exportar" name="exportar">
                <input type="submit" value="Añadir" name="alta">
            </div>
            <div class="paginas">
                <?php if ($_SESSION['pagina'] != 1) { ?>
                    <input type="submit" name="inicio" value="&#8678;" class="movimiento">
                    <input type="submit" name="anterior" value="&#8672;" class="movimiento">

                <?php } ?>
                <span><?php echo $_SESSION['pagina'] . " de " . $_SESSION['total_paginas']; ?></span>
                <?php if ($_SESSION['pagina'] != $_SESSION['total_paginas']) { ?>
                    <input type="submit" name="siguiente" value="&#8674;" class="movimiento">
                    <input type="submit" name="final" value="&#8680;" class="movimiento">
                <?php } ?>
            </div>

            <div class="opciones2">
                <input type="submit" value="Volver" name="volver">
                <input type="submit" value="Mostrar Codigo" name="codigo">
            </div>
        </form>
        <div class="info">
            <?php
            if (isset($_REQUEST['CodPagina'])) {
                $codigo = $_REQUEST['CodPagina'];
                if ($_SESSION[$codigo]['ejecucion']) {
                    echo "<p class=\"sesion\">" . $_SESSION[$codigo]['mensaje'] . "</p>";
                } else {
                    echo "<p class=\"error\">" . $_SESSION[$codigo]['mensaje'] . "</p>";
                }
            }
            ?>
        </div>
    </body>
</html>