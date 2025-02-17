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
    $userProf = $arr[4];  //NORMAL 
    $userData = $userMat."-".$userName; //FORMATO DE COMENTARIOS Y POSTS
    
    $nombreCompleto = $arr[6];
    
    //FUNCION DE EDITAR DATOS DEL PERFIL    
    if(isset($_POST['btnForm']))
    {
        //Nuevo usuario
        $nuevoNom = strtr($_POST['nom']," ","-");
        $nuevaMat = $_POST['mat'];
        $cadNom = explode("-",$nuevoNom)[0];

        //ACTUALIZAR TODO ANTES DE CAMBIAR AL NOMBRE OFICIAL
        //COMENTARIOS
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
            }//if POSTS
        }
        else{
            echo "CAMBIOS NO AFECTADOS";
        }
    }
       

    //FUNCION DE SUBIR IMAGEN
    if(isset($_POST['btnSave']))
    {
        $img = $_FILES["subirFoto"]["name"];
        $imgtmp = $_FILES["subirFoto"]["tmp_name"];
        $action = profileImg($img,$imgtmp,$userId,$conn);
        
        if($action){
            header("refresh:0");
        }
        else{
            echo "Algo salio mal :(";
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
    <title>Editar Perfil</title>
</head>
<body class="admin-col-2"> 
    <nav class="navbar navbar-primary bg-primary text-light border-bottom ">
        <div class="container">
            <img src="../assets/community.png" class="align-top" width="60" height="60" alt="">
            <h2>Comunidad de aprendizaje: Habilidades del Pensamiento</h2>
        </div>    
    </nav>
   
    <div class="container mt-5 bg-light rounded">
        <h1>Mi Perfil</h1>
        <hr>
        <div class="row">
            <!--LADO IZQUIERDO-->
            <div class="col-md-6 border-end text-center">
                <h2><?php echo $userName ?></h2>
                
                <div class="mb-3">
                    <img src="<?php echo $userProf?>" class="rounded" width="160" height="160">
                </div>
               
                <div class="mb-3">
                    <form action="" method="post" enctype="multipart/form-data">
                        <button class="btn btn-warning" type="submit" formaction="../index.php">
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

                        <p> <i class="bi bi-info-circle"></i> Elige primero una foto y despues pulsa Guardar.</p>   
                    </form>              
                </div>
            </div>
            <!--LADO IZQUIERDO-->

            <!--LADO DERECHO-->
            <div class="col-md-6">
                <h2>Editar</h2>
                
                <form action="" method="post">
                    <div class="row mb-2">
                        <label for="" class="col-sm-2 col-form-label">Nombre: </label>
                        <div class="col-sm-10">
                            <input type="text" value=<?php echo $nombreCompleto; ?> name="nom"  class="form-control" required>
                        </div>                   
                    </div>

                    <div class="row mb-2">
                        <label for="" class="col-sm-2 col-form-label">Matricula: </label>
                        <div class="col-sm-10">
                            <input type="text" value=<?php echo $userMat ?> name="mat" class="form-control" required>
                        </div>                   
                    </div>

                    <div class="row mb-2">
                        <label for="" class="col-sm-2 col-form-label">Renovar Contraseña: </label>
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
            <!--LADO IZQUIERDO-->


        </div>
    </div>

    <script src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>