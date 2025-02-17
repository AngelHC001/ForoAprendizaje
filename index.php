<?php 
    session_start();
    include("utils/config.php");
    include("utils/queries.php");
    date_default_timezone_set('America/Mexico_City'); //datetime

    if(!isset($_SESSION["usuario"]))
        header("location:login.php");
    
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
    $userProf = substr($arr[4],3);  //QUITAR ../ ESTA ES PAGINA SUPERFICIAL
    $userData = $userMat."-".$userName; //FORMATO PARA POSTS

    //ATENCION HAY LIMITE DE ARCHIVOS SUBIDOS EN EL FOLDER PROFILE PICS TALVEZ 10
    
    //FUNCIONES DE PUBLICAR ARCHIVO
    $valid_extensions = array("jpg","jpeg","png","pdf","docx","pptx","xlsx");
    $images = array("jpg","jpeg","png");
    
    if(isset($_POST['btnPost']) && (!empty($_POST['titulo']) || !empty($_POST['contenido'])))
    {
        //Extraer datos principales TITULO, CONTENIDO, FECHA
        $title = $_POST['titulo'];
        $content = $_POST['contenido'];
        $date1 = date("Y-m-d h:i:s", time());

        
        //PROCEDIMIENTO DE PUBLICACION CON ARCHIVOS
        if($_FILES["archivo"]["size"][0] != 0) //SI EL ESPACIO NO ESTA VACIO
        {
            $cadena = ""; //NOMBRES DE ARCHIVOS
            $totalFileUploaded = 0;
            $countfiles = count($_FILES['archivo']['name']);
            if($countfiles <= 5)
            {
                for($i=0;$i<$countfiles;$i++)
                {
                    $filename = $_FILES['archivo']['name'][$i];
                    $cadena .= $filename."-";

                    ## Location
                    $location = "uploaded_files/".$filename;
                    $extension = pathinfo($location,PATHINFO_EXTENSION);
                    $extension = strtolower($extension);
                
                    ## Check file extension
                    if(in_array(strtolower($extension), $valid_extensions)) {
                        ## Upload file
                        if(move_uploaded_file($_FILES['archivo']['tmp_name'][$i],$location))
                        {                          
                            $totalFileUploaded++;
                        }
                    }
                }
                $fq1 = insertPost($conn,$title,$content,$date1,$userData,$cadena);
                if(!$fq1){
                    echo "Algo Salio Mal";
                }
            }
            else
            {
                echo "<script> alert('No se publicó tu post, limite de archivos: 5'); </script>";
            }
        }
        else //EL ESPACIO ESTA VACIO
        { 
            $fq2 = insertPost($conn,$title,$content,$date1,$userData," ");
            if(!$fq2){
                echo "Algo Salio Mal";
            }
        }

    }//PUBLICAR ARCHIVO

    //FUNCION LIKE
    if(isset($_GET['btnLike']))
    {
        $idPost = $_GET['btnLike'];
        if(!likePost($idPost,$conn)){
            echo("Algo salio mal :(");
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
    <link rel="stylesheet" href="./extra/aux1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Comunidad| Inicio</title>
</head>
<body>
    <nav class="navbar navbar-primary bg-primary text-light border-bottom ">
        <div class="container">
            <img src="assets/community.png" width="60" height="60" class="align-top" alt="">
            <h2>Inicio</h2>
        </div>    
    </nav>

    <div class="container-fluid">
        <div class="row">

            <div id="leftbar" class="col-md-4"> <!--LADO IZQUIERDO-->   
                <!--AREA DE PERFIL--> 
                <div class="row text-center">   
                    <div class="col border-end">
                        <h3><?php echo $userName; ?></h3>
                        <img src="<?php echo $userProf; ?>" class="rounded"  width="80" height="80">
                        <p><?php echo $userMat; ?></p>
                    </div>

                    <div class="col d-flex align-items-center justify-content-center">
                        <form class="mt-3">    
                            <button class="btn btn-success mb-2" type="submit" formaction="user_pages/my_posts.php">
                                <i class="bi bi-sticky-fill"></i> Mis Posts
                            </button>

                            <button class="btn btn-secondary" type="submit"  formaction="user_pages/profile.php"> 
                                <i class="bi bi-pencil-square"></i> Editar Perfil
                            </button>

                            <button class="btn btn-danger mt-2" type="submit" formaction="utils/close.php">
                                <i class="bi bi-box-arrow-left"></i> Cerrar Sesion
                            </button>
                        </form>      
                    </div>     
                </div>    <!--AREA DE PERFIL--> 
               
                <hr>

                 <!--CREAR POST-->
                <div class="row">
                    <h5>Publicar</h5>
                    
                    <div class="col mb-3">
                        <form action="" method="post" enctype="multipart/form-data">
                            <input name="titulo" class="form-control mb-2" type="text" placeholder="Titulo">
                            <textarea name="contenido" class="form-control" placeholder="Escribe algo..."></textarea>   
                              
                            <!--COLOCAR NOMBRE DE ARCHIVO LIMITE HASTA 5 ARCHIVOS-->
                            <div id="archivos"></div>
                            
                            <div class="mt-2 text-end"> <!--BOTONES-->
                                <button class="btn btn-outline-secondary" title="Retirar Archivos" onclick="cancelaArchivos()">
                                    <i class="bi bi-x-square"></i> 
                                </button>
                                        
                                <label class="btn btn-outline-secondary">
                                    <i class="bi bi-paperclip"></i>    
                                    <input id="fichero" name="archivo[]" type="file" multiple title="Adjuntar Archivo"/>
                                </label>
            
                                <button class="btn btn-outline-primary" type="submit"  title="Enviar" name="btnPost">
                                    <i class="bi bi-send"></i>
                                </button>
                            </div> <!--BOTONES-->
                        </form>
                    </div>

                </div> <!--CREAR POST-->      
            </div> <!--LADO IZQUIERDO-->
            
          
            <!--LADO DERECHO AREA DE POSTS -->
            <div id="rightbar" class="col-md-8 text-light rounded">
                <div class="row p-2 bg-dark rounded">
                    <h2>Publicaciones</h2>
                </div>

                <div class="row p-2 admin-col-2 d-grid gap-2">
                    <?php //MOTOR DEL AREA DE PUBLICACIONES 
                    $queryPost = "SELECT * FROM PUBLICACION";     //TODAS LAS PUBLICACIONES            
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
                                                { ?>
                                                    <div class="flex-column me-2 bg-light text-center rounded">
                                                        <a href="uploaded_files/<?php echo $f;?>" download="<?php echo $f;?>" >
                                                            <img class="rounded" src="uploaded_files/<?php echo $f;?>" width="80" height="80">
                                                        </a>
                                                    </div> 
                                                <?php 
                                                }
                                                else
                                                { ?>
                                                    <div class="flex-column me-2 bg-light text-center rounded">
                                                        <a href="uploaded_files/<?php echo $f;?>" download="<?php echo $f;?>" >
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
                                            type="submit" formaction="user_pages/comentario.php">
                                            <i class="bi bi-chat"></i>       
                                        </button>
                                        <span><?php echo $row['nComentarios']; ?></span>
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
                </div>
            </div>  <!--LADO DERECHO-->
        
        </div> <!--ROW-->
    </div> <!-- CONTENEDOR -->
    <script type="text/javascript" src="extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>