<?php
    require_once '../config/confArchivo.php';
    if(isset($_REQUEST["volver"])){
        header("Location: ".rutaIndex);
        die();
    } 
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Mostrar Codigo</title>
        <style>
            input{
                border: 2px solid black;
                border-radius: 2px;
                background-color: aquamarine;
                height: 50px;
                width: 100px;
            }
        </style>
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="submit" value="volver" name="volver">
        </form>
    </body>
</html>
<?php
    
    echo "<h1>Mostar Codigo</h1>";
    echo "<h2>mtoDepartamentos</h2>";
    highlight_file('../mtoDepartamentos.php');
    echo "<h2>Alta Departamento</h2>";
    highlight_file('altaDepartamento.php');
    echo "<h2>Baja Departamento</h2>";
    highlight_file('./bajaDepartamento.php');
    echo "<h2>Baja lógica Departamento</h2>";
    highlight_file('./bajaLogicaDepartamento.php');
    echo "<h2>Editar Departamento</h2>";
    highlight_file('./editarDepartamento.php');
    echo "<h2>Exportar Departamento</h2>";
    highlight_file('./exportarDepartamento.php');
    echo "<h2>Importar</h2>";
    highlight_file('./importarDepartamento.php');
    echo "<h2>Mostrar Departamento</h2>";
    highlight_file('./mostrarDepartamento.php');
    