<?php
    session_start();
    include("../utils/config.php");
    include("../utils/queries.php");
    date_default_timezone_set('America/Mexico_City'); //datetime

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
    
    $images = array("jpg","jpeg","png");

    if(isset($_POST["btnPost"]) && (!empty($_POST["titulo"]) || !empty($_POST["contenido"])) )
    {
        $id1 = $_GET['btnEdit']; 
        $nuevoTitulo = $_POST["titulo"];
        $nuevoConten = $_POST["contenido"];
        $nuevafecha = date("d-m-Y h:i:s", time());
        $signal = editPost($conn, $id1,$userData,$nuevoTitulo,$nuevoConten,$nuevafecha);
        if(!$signal){
            echo "ALGO SALIO MAL :(";
        }

    }

    //LLEVAR VALORES A LOS CAMPOS
    if(isset($_GET["btnEdit"])){
        $post = searchPost($conn,$_GET['btnEdit']);
        $cad = explode("-",$post);
        //OBTENER DATOS
        $titulo = $cad[0];
        $cont = $cad[1];
    }
    else{
        $titulo = "";
        $cont = "";
    }

    //VACIAR TEXTOS
    if(isset($_POST['cancel'])){
        unset($_GET['btnEdit']);
        $titulo = "";
        $cont = "";
    }

    //COLOCAR LOS DATOS COMO IDpUSUARIO
    //SEPARARLOS EN ARREGLO Y OBTENERLOS UNO POR UNO
    if(isset($_GET['btnLike']))
    {       
        $idPost = $_GET['btnLike'];
        $likeSign = likePost($idPost, $conn); 
        if(!$likeSign){
            echo "Algo salio mal :(";
        }
    }

    //BORRADO EXCLUSIVO POST ADMIN
    if(isset($_GET['btnErase'])){            
        $id = $_GET['btnErase'];

        if(!deletePost($conn,$userData,$id)){
            echo "ALGO SALIO MAL :(";
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
                <div class="row-sm list-group p-1">
                    <a href="#" class="list-group-item list-group-item-action active" aria-current="true">Mis Publicaciones</a>
                    <a href="admin_stud.php" class="list-group-item list-group-item-action">Publicaciones de Alumnos</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action">Ver Usuarios</a>
                    <a href="admin_control.php" class="list-group-item list-group-item-action">Administrador</a>
                    <a href="admin_files.php" class="list-group-item list-group-item-action">Archivos Enviados</a>
                    <a href="admin_prof.php" class="list-group-item list-group-item-action">Ver Perfil</a>                   
                    <a href="admin_main.php" class="list-group-item list-group-item-action">Volver al Grupo</a>
                </div>    

                <div class="row-sm p-1">
                    <h5>Editar Publicacion</h5>
                       
                    <form action="" method="post">
                        <input name="titulo" class="form-control mb-2" type="text" placeholder="Titulo" value="<?php echo $titulo; ?>">
                        <textarea name="contenido" class="form-control" placeholder="Escribe algo..."><?php echo $cont;?></textarea>   
                                                                            
                        <div class="mt-2 text-end"> <!--BOTONES-->
                            <button name="cancel" class="btn btn-outline-secondary" title="Limpiar Texto">
                                <i class="bi bi-x-square"></i> 
                            </button>
                                                                            
                            <button name="btnPost" class="btn btn-outline-primary" type="submit"  title="Enviar">
                                <i class="bi bi-send"></i>
                            </button>
                        </div> <!--BOTONES-->
                    </form>   
                </div>  
            </div>
            <!--BARRA DE HERRAMIENTAS-->

            <!--BARRA DE CRUD-->
            <div id="rightbar" class="col-md-8 text-light">
                <div class="row p-2 bg-dark rounded">
                    <h2>Mis Publicaciones</h2>
                </div>

                <div class="row p-2 admin-col-2 d-grid gap-2">
                    <?php //PUBLICACIONES DEL ADMIN
                        $queryPost = "SELECT * FROM PUBLICACION WHERE USUARIO = '$userData'";              
                        $res = mysqli_query($conn,$queryPost);
                        if($res)
                        {
                            while (($row = mysqli_fetch_array($res)) != NULL) //OBTENER REGLONES DE LAS CONSULTAS
                            {       
                                //PAUSA PHP?>
                                
                                <!--FORMATO DE POSTS-->
                                <div class="container-sm border border-2 border-light rounded bg-dark">
                                    <div class="row p-auto border-bottom">
                                        <div class="col-sm-4"> 
                                            <h4><?php echo $row['titulo']; ?></h4> 
                                        </div>

                                        <div class="col-sm-8"> 
                                            <p class="text-end">
                                                <?php  echo "<b>".$row['usuario']. "</b>\n"; 
                                                        echo $row['fecha_hora']; ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <p><?php echo $row['contenido'] ?></p>
                                    </div>
                                    
                                    <div class="d-flex p-2 flex-row justify-content-center">
                                        <?php 
                                            if($row['archivo'] != null)
                                            {   
                                                $fileStrings = $row['archivo'];    //cadena de strings de 1 a 5 archivos  
                                                $cut = explode("-",$fileStrings); //partir cadena
                                                
                                                foreach($cut as $f)
                                                { 
                                                    $format = strtolower(substr($f, -3));
                                                    if(in_array($format,$images))
                                                    { 
                                                        ?>
                                                        <div class="flex-column me-2 bg-light text-center rounded">
                                                            <a href="../uploaded_files/<?php echo $f;?>" download="<?php echo $f;?>" >
                                                                <img class="rounded" src="../uploaded_files/<?php echo $f;?>" width="80" height="80">
                                                            </a>
                                                        </div> 
                                                    <?php 
                                                    }
                                                    else
                                                    { ?>
                                                        <div class="flex-column me-2 bg-light text-center rounded">
                                                            <a href="../uploaded_files/<?php echo $f;?>" download="<?php echo $f;?>" >
                                                                <?php echo $f?>
                                                            </a>
                                                        </div>
                                                    <?php 
                                                    }
                                                }                   
                                            }
                                        ?> 
                                    </div>
                                     
                                    <div class="p-2 border-top text-start">
                                        <form action="" method="get">
                                            <button class="btn btn-success btn-sm" name="btnLike" 
                                            value=<?php echo $row['idPublicacion'];  ?> type="submit">
                                                <i class="bi bi-check-circle"></i>   
                                            </button>
                                            <span><?php echo $row['nLikes']; ?></span>

                                            <button class="btn btn-primary btn-sm" name="btnComment"
                                                value=<?php echo $row['idPublicacion'];?> 
                                                type="submit" formaction="../user_pages/comentario.php">
                                                <i class="bi bi-chat"></i>       
                                            </button>
                                            <span><?php echo $row['nComentarios']; ?></span>

                                            <button class="btn btn-secondary btn-sm" name="btnEdit"
                                                value=<?php echo $row['idPublicacion'];?> 
                                                type="submit" formaction="" title="Editar">
                                                <i class="bi bi-pen"></i>       
                                            </button>

                                            <button class="btn btn-danger btn-sm" name="btnErase"
                                                value=<?php echo $row['idPublicacion'];?> 
                                                type="submit" title="Borrar">
                                                <i class="bi bi-eraser"></i>       
                                            </button>
                                            
                                        </form>

                                    </div>
                                </div>  <!--FORMATO DE POSTS-->
                                <?php
                                //REANUDA PHP
                            }
                        }
                        else
                            echo "0 RESULTS";
                    ?>
                </div>       
            </div>
            <!--BARRA DE CRUD-->

        </div> <!--ROW-->
    </div> <!--CONTAINER-FLUID-->

    <script type="text/javascript" src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>