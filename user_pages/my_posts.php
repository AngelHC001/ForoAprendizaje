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
    $userName = $arr[1];
    $userMat = $arr[2];
    $userProf = $arr[4];  //NORMAL
    $userType = $arr[5]; 
    
    $userData = $userMat."-".$userName; //FORMATO PARA POSTS
    $images = array("jpg","jpeg","png");

    //REDIRECCION ADMINISTRADOR
    if($userType == 1){
        $link = "../paginas_admin/admin_main.php";
    }
    else{
        $link = "../index.php";
    }

    if(isset($_POST["btnPost"]) && (!empty($_POST["titulo"]) || !empty($_POST["contenido"])) )
    {
        $id1 = $_GET['btnEdit']; 
        $nuevoTitulo = $_POST["titulo"];
        $nuevoConten = $_POST["contenido"];
        $nuevafecha = date("Y-m-d h:i:s", time());
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
        $titulo = "";
        $cont = "";
    }


    //FUNCIONES BASICAS
    if(isset($_GET['btnLike']))
    {       
        $idPost = $_GET['btnLike'];
        $likeSign = likePost($idPost, $conn); 
        if(!$likeSign){
            echo "Algo salio mal :(";
        }
    }

    //BORRADO EXCLUSIVO DEL USUARIO
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
        <title>Comunidad|Mis Post</title>
    </head>
    <body>
        <nav class="navbar navbar-primary bg-primary text-light border-bottom ">
            <div class="container">
                <img src="../assets/community.png" width="80" height="80" class="align-top" alt="">
                <h2>Habilidades del Pensamiento</h2>
            </div>    
        </nav>
        
        <div class="container-fluid">
            <div class="row">

                <!--LADO IZQUIERDO--> 
                <div id="leftbar" class="col-md-4">   
                    <!--AREA DE PERFIL--> 
                    <div class="row text-center">   
                        <div class="col">
                            <h3><?php echo $userName; ?></h3>
                            <img src="<?php echo $userProf?>" class="rounded" alt="fotoPerfil" width="80" height="80">
                            <p><?php echo $userMat; ?></p>
                        </div>

                        <div class="col border-start d-flex align-items-center justify-content-center">
                            <div class="mt-1">
                                <form class="mt-2" action="">                                 
                                    <button class="btn btn-warning mt-2" type="submit" formaction="<?php echo $link;?>">
                                        <i class="bi bi-arrow-left-circle"></i> Regresar
                                    </button>     

                                    <button class="btn btn-danger mt-2" type="submit" formaction="../utils/close.php">
                                        <i class="bi bi-box-arrow-left"></i> Cerrar Sesion
                                    </button>
                                </form>     
                                              
                            </div>
                        </div>      
                    </div>    <!--AREA DE PERFIL--> 
                
                    <hr>

                    <!--PANEL DE EDITAR-->
                    <div class="row">  
                        <h4 class="text-center">Editar Publicacion</h4>
                        <div class="col">
                            <form action="" method="post">                              
                                <input id="txt1" name="titulo" type="text" class="form-control mb-3" 
                                    placeholder="Titulo" value="<?php echo $titulo;?>">
                                <textarea id="txt2" name="contenido" class="form-control"  placeholder="Escribe algo..."><?php echo $cont;?></textarea>
                         
                                <!--COLOCAR NOMBRE DE ARCHIVO LIMITE HASTA 5 ARCHIVOS-->
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

                    </div> <!--EDITAR-->      
                </div> <!--LADO IZQUIERDO-->
                
                <!--LADO DERECHO AREA DEL POST -->
                <div id="rightbar" class="col-md-8 text-light admin-col-2 rounded">
                    <div class="row p-2 bg-dark rounded">
                        <h2>Mis Publicaciones</h2>
                    </div>

                    <!--MIS PUBLICACIONES-->
                    <div class="row p-2 mt-2">
                        <div class="d-grid gap-2">
                            <?php
                                //MOTOR DEL AREA DE COMENTARIOS DE LA PUBLICACION
                                $queryComm = "SELECT * FROM PUBLICACION WHERE USUARIO = '$userData'";     
                                $res1 = mysqli_query($conn,$queryComm);
                                if($res1)
                                {
                                    while (($row = mysqli_fetch_array($res1)) != NULL) //OBTENER REGLONES DE LAS CONSULTAS
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
                                            
                                            <div class="d-flex flex-row p-2 justify-content-center">
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
                                                                        <img class="rounded" src="uploaded_files/<?php echo $f;?>" width="80" height="80">
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
                                            

                                            <!--BOTONES DE POST-->
                                            <div class="p-2 border-top">
                                                <form action="" method="get">
                                                    <button class="btn btn-success btn-sm" name="btnLike" 
                                                    value=<?php echo $row['idPublicacion'];  ?> type="submit">
                                                        <i class="bi bi-check-circle"></i>   
                                                    </button>
                                                    <span><?php echo $row['nLikes']; ?></span>

                                                    <button class="btn btn-primary btn-sm" name="btnComment"
                                                        value=<?php echo $row['idPublicacion'];?> 
                                                        type="submit" formaction="comentario.php">
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
                                            <!--BOTONES DE post-->
                                        </div> <!--FORMATO DE POSTS-->
                                                    
                                        <?php
                                        //REANUDA PHP
                                    }
                                }
                                else
                                    echo "0 RESULTS";
                            ?>
                        </div> <!--AREA COMENTARIOS-->                           
                </div>  <!--LADO DERECHO-->   
                       
            </div> <!--RENGLON GENERAL  -->
        </div> <!-- CONTENEDOR -->

        <script src="./extra/funciones.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>