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
    $userName = $arr[1];
    $userMat = $arr[2];
    $userPass = $arr[3];
    $userProf = $arr[4]; 
    $userData = $userMat."-".$userName;
    $nombreCompleto = $arr[6];

    //SUBIR FOTO
    if(isset($_POST['btnSave']))
    {
        $img = $_FILES['subirFoto']['name'];
        $imgtmp = $_FILES['subirFoto']['tmp_name'];
        $prof1 = profileImg($img,$imgtmp,$userId,$conn);
        
        if($prof1){
            header("refresh:0");
        }
        else{
            echo "SOMETHING WENT WRONG";
        }                       
    }

    //ACTUALIZAR DATOS
    if(isset($_POST['btnForm']))
    {
        //Nuevo usuario
        $nuevoNom = strtr($_POST['nom']," ","-");
        $nuevaMat = $_POST['mat'];
        $cadNom = explode("-",$nuevoNom)[0]; //OBTENER PRIMER NOMBRE
        
        $cambios = changeData($nuevaMat."-".$cadNom,$userData,$conn);
        if($cambios)
        {
            if(empty($_POST['pass1']) && empty($_POST['pass2']))
            {
                $res = updateUser($nuevoNom,$nuevaMat," ",$userId,$conn); //sin cambiar pass
                if($res){
                    //RENOVAR sesiones 
                    $_SESSION['usuario'] = $nuevoNom;
                    $_SESSION['contraseña'] = $userPass;
                    header("refresh:0");
                }
                else
                {
                    echo "ALGO SALIO MAL";
                }
            }
            else
            {
                $nuevoPass = $_POST['pass1'];
                $hashed = password_hash($nuevoPass,PASSWORD_DEFAULT);
                $res = updateUser($nuevoNom,$nuevaMat,$hashed,$userId,$conn); //sin cambiar pass
            
                if($res){
                    //RENOVAR sesiones 
                    $_SESSION['usuario'] = $nuevoNom;
                    $_SESSION['contraseña'] = $hashed;
                    header("refresh:0");
                } 
                else{
                    echo "Algo salio mal :(";
                }
            }

        }
        else{
            echo "CAMBIOS NO AFECTADOS";
        }
    }

    //RENOVAR PUBLICACIONES            
    //$updatePosts = "UPDATE PUBLICACION SET USUARIO = '$nuevaMat $nuevoNom' WHERE USUARIO = '$username'";
    //$res3 = mysqli_query($conn,$updatePosts);

    //echo $datos;
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
            <div class="col-md-2 text-light bg-dark border-end-2">
                <div class="row border border-2 p-1">
                    <h3>Opciones</h3>
                </div>
            
                <div class="row list-group p-1">
                    <a href="admin_post.php" class="list-group-item list-group-item-action" aria-current="true">Mis Publicaciones</a>
                    <a href="admin_stud.php" class="list-group-item list-group-item-action">Publicaciones de Alumnos</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action">Ver Usuarios</a>
                    <a href="admin_control.php" class="list-group-item list-group-item-action">Administrador</a>
                    <a href="admin_files.php" class="list-group-item list-group-item-action">Archivos Enviados</a>
                    <a href="#" class="list-group-item list-group-item-action active">Ver Perfil</a>
                    <a href="admin_main.php" class="list-group-item list-group-item-action">Volver al Grupo</a>
                </div>    
            </div>
            <!--BARRA DE HERRAMIENTAS-->

            <!--PANEL PERFIL-->
            <div class="col-md-10 admin-col-2">
                <div class="container-sm mt-4 bg-light rounded">
                    <h1>Mi Perfil</h1>
                    <hr>
                    <div class="row">
                        <!--LADO IZQUIERDO-->
                        <div class="col-sm-6 border-end text-center">
                            <h2><?php echo $nombreCompleto ?></h2>
                            
                            <div class="mb-3">
                                <img src="<?php echo $userProf ?>" width="80" height="80">
                            </div>
                        
                            <div class="mb-3">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <button class="btn btn-warning" type="submit" formaction="admin_main.php">
                                        <i class="bi bi-arrow-left-circle"></i> Regresar
                                    </button>
                                
                                    <label class="btn btn-primary">
                                        <i class="bi bi-camera"></i> Cambiar Foto
                                        <input id="fichero" type="file" name="subirFoto">
                                    </label>
                                    
                                    <button class="btn btn-success" type="submit" name="btnSave">
                                        <i class="bi bi-floppy"></i> Guardar
                                    </button>

                                    <div class="mb-3" id="archivos"></div>

                                    <p> <i class="bi bi-info-circle"></i> 
                                        Elige primero una foto y despues pulsa Guardar.
                                    </p>
                                    
                                </form>              
                            </div>
                        </div>
                        <!--LADO IZQUIERDO-->

                         <!--LADO DERECHO-->
                        <div class="col-sm-6">
                            <h2>Editar</h2>
                            
                            <form action="" method="post">
                                <div class="row mb-2">
                                    <label for="" class="col-sm-2 col-form-label">Nombre: </label>
                                    <div class="col-sm-10">
                                        <input type="text" value=<?php echo $userName ?> name="nom"  class="form-control" required>
                                    </div>                   
                                </div>

                                <div class="row mb-2">
                                    <label for="" class="col-sm-2 col-form-label">Matricula: </label>
                                    <div class="col-sm-10">
                                        <input type="text" value=<?php echo $userMat ?> name="mat" class="form-control" required>
                                    </div>                   
                                </div>

                                <div class="row mb-2">
                                    <label for="" class="col-sm-2 col-form-label">Contraseña: </label>
                                    <div class="col-sm-10">
                                        <input type="password" name="pass1"  class="form-control">
                                    </div>                   
                                </div>

                                <div class="row">
                                    <label for="" class="col-sm-2 col-form-label">Confirmar Contraseña: </label>
                                    <div class="col-sm-10">
                                        <input type="password" name="pass2" class="form-control">
                                    </div>                   
                                </div>

                                <div class="mb-3 text-end">
                                    <button class="btn btn-primary" name="btnForm">
                                        <i class="bi bi-pencil"></i> Cambiar datos
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!--LADO DERECHO-->

                    </div>  <!--ROW-->
                </div> <!--CONTAINER-->

                <br>
                <br>
                <br>
                <br>
                <br>
                
            </div> <!--PANEL PERFIL-->

        </div> <!--ROW-->
    </div> <!--CONTAINER-->

    
   

    <script type="text/javascript" src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>