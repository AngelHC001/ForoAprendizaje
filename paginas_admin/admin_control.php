<?php 
    session_start();
    include("../utils/config.php");
    include("../utils/queries.php");

    if(!isset($_SESSION["usuario"]))
        header("location:../login.php");
    
    //DATOS DE SESION
    $name = $_SESSION["usuario"];
    $pass = $_SESSION["contraseña"];

    //Obtener datos de una cadena
    $datos = userData($name,$conn);
    $arr = explode(" ",$datos); 
    
    //Datos por separado
    $userId = $arr[0];
    $userData = $arr[2]."-".$arr[1];
    $userMat = $arr[2];


    $queryPost = "SELECT IDUSUARIO,NOMBRE,MATRICULA,RESPUESTAS FROM USUARIO WHERE tipo = 1";              
    $res = mysqli_query($conn,$queryPost);
    //GUARDAR EN EXCEL
    if(isset($_POST["export"])) {	

        $fileName = "act_grupo".date('Ymd').".xls";			
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$fileName\"");	
    
        $verColumna = false;
        if(!empty($res)) {
          foreach($res as $row1) {
            if(!$verColumna) {		 
              echo implode("\t", array_keys($row1))."\n";
              $verColumna = true;
            }
            echo implode("\t", array_values($row1))."\n";
          }
        }
        exit;  
    }

    //AGREGAR ADMIN
    if(isset($_POST['nuevoAdmin']))
    {
        $nom_Admin = $_POST['aNombre']; 
        $mat_Admin = $_POST['aMatricula'];
        $pass1 = $_POST['aPass1']; 
        $pass2 = $_POST['aPass2'];

        $nom = strtr($nom_Admin," ","-");
        
        if($pass1 === $pass2)
        {
            $pass = password_hash($pass1,PASSWORD_DEFAULT); 
            $consulta = "INSERT INTO usuario (nombre,matricula,contraseña,tipo,respuestas) 
                            VALUES('$nom','$mat_Admin','$pass',1,0)";

            $res1 = mysqli_query($conn,$consulta);
            header("refresh: 0");

            if(!$res1)
                echo("Error: Algo salio mal :(");     

        }
        else
        {
            echo "<script> alert('Contraseñas no coinciden'); <script/>";
        }
    }

    //BORRAR USUARIO
    if(isset($_POST['delete_user'])){
        $id = $_POST['delete_user'];
        $query = "DELETE FROM USUARIO WHERE IDUSUARIO = $id";
        $del = mysqli_query($conn,$query);
        if(!$del){
            echo "ALGO SALIO MAL"; 
        }
    }
    
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../extra/aux1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Comunidad| Inicio</title>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark text-light border-bottom">
        <div class="container">
            <img src="../assets/teacher1.jpg" width="60" height="60" class="align-top" alt="">
            <h2>Administrar Foro</h2>
        </div>    
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!--BARRA DE HERRAMIENTAS-->
            <div class="col-md-4 text-light bg-dark border-end-2">
                <div class="row border border-2 p-1">
                    <h3>Opciones</h3>
                </div>
            
                <div class="row list-group p-1">
                    <a href="admin_post.php" class="list-group-item list-group-item-action" aria-current="true">Mis Publicaciones</a>
                    <a href="admin_stud.php" class="list-group-item list-group-item-action">Publicaciones de Alumnos</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action">Ver Usuarios</a>
                    <a href="#" class="list-group-item list-group-item-action active">Administrador</a>
                    <a href="admin_files.php" class="list-group-item list-group-item-action">Archivos Enviados</a>
                    <a href="admin_prof.php" class="list-group-item list-group-item-action">Ver Perfil</a>
                    <a href="admin_main.php" class="list-group-item list-group-item-action">Volver al Grupo</a>
                </div>    

                <div class="row list-group p-3 d-grid gap-3">
                    <h3>Opciones de Administrador</h3>
                    <form action="" method="post">
                        <button class="btn btn-success" name="export" type="submit">
                            <i class="bi bi-download"></i> Guardar Actividad
                        </button>
                    </form>
                    
                </div>
            </div>
            <!--BARRA DE HERRAMIENTAS-->


            <!--BARRA DE CRUD-->
            <div id="rightbar" class="col-md-8 text-light text-center bg-secondary">
                <div class="row p-1 bg-dark rounded">
                    <h2>Administradores</h2>
                </div>

                <table class="table table-bordered mt-1">
                    <tr class="table-active">
                        <th>Nombre</th>
                        <th>Matricula</th>
                        <th>Respuestas</th>
                        <th>Opciones</th>
                    </tr>    
                
                    <?php
                       
                        if($res)
                        {                      
                            while (($row = mysqli_fetch_array($res)) != NULL) //OBTENER REGLONES DE LAS CONSULTAS
                            {
                                 //PAUSA PHP?>
                                    <tr>
                                        <td><?php echo $row[1]; ?></td>
                                        <td><?php echo $row[2]; ?></td>
                                        <td><?php echo $row[3]; ?></td>
                                        <td>
                                            <?php
                                                if($row[2] != $userMat)
                                                { ?>            
                                                    <form action="" method="post">
                                                        <button name="delete_user" class="btn btn-sm btn-danger" 
                                                                value="<?php echo $row[0]; ?>" title="Borrar">
                                                            <i class="bi bi-dash-circle-fill"></i>
                                                        </button>
                                                    </form>
                                                <?php
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                            }
                        }
                        else
                            echo "0 RESULTS";
                    ?>
                    <tr>
                        <th colspan="4">Nuevo Administrador</th>
                    </tr>

                    <tr>
                        <td colspan="4">
                            <form action="" method="post">
                                <div class="input-group mb-3">
                                    <input name="aNombre" class="form-control" type="text" placeholder="Nombre" required>
                                    <input name="aMatricula" class="form-control" type="text" placeholder="Matricula" required>
                                    <input name="aPass1" class="form-control" type="password" placeholder="Contraseña" required>
                                    <input name="aPass2" class="form-control" type="password" placeholder="Confirma Contraseña" required>
                                </div>
                                <button name="nuevoAdmin" class="btn btn-primary btn-lg" type="submit">Registrar</button>                   
                            </form>
                            
                        </td>
                    </tr>
                </table>    
            </div>
            <!--BARRA DERECHA-->

        </div> <!--ROW-->
    </div> <!--CONTAINER-FLUID-->

    <script type="text/javascript" src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>