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

    //FUNCION ADMINISTRADOR
    if($userType == 1){
        $link = "../paginas_admin/admin_main.php";
    }
    else{
        $link = "../index.php";
    }


    //OBTENER POST
    if(isset($_GET['btnComment']))
    { 
        $idPost = $_GET['btnComment'];
        
        $query = "SELECT * FROM PUBLICACION WHERE IDPUBLICACION = $idPost";
        $res = mysqli_query($conn,$query); 
        if($res)
        {
            while($row = mysqli_fetch_assoc($res))
            {
                $title = $row['titulo'];
                $content = $row['contenido'];  
                $remitant = $row['usuario'];
                $date = $row['fecha_hora'];
                $files = $row['archivo'];
            }
        }
        else{
            return "0 Results";
        }
    }


    //FUNCIONES DE PUBLICAR COMENTARIO
    $valid_extensions = array("jpg","jpeg","png","pdf","docx","pptx","xlsx"); 
    $images = array("jpg","jpeg","png");

    if(isset($_POST['btnPost']) && !empty($_POST['contenido']))
    {
        //Extraer contenido de comentario
        $idPost = $_GET['btnComment'];
        $content2 = $_POST['contenido']; 
        $date1 = date("Y-m-d h:i:s", time());

        if($_FILES['archivo']['size'][0] != 0)
        {
            $cadena = ""; //NOMBRES DE ARCHIVOS
            $countfiles = count($_FILES['archivo']['name']);
            $totalFileUploaded = 0;

            $countfiles = count($_FILES['archivo']['name']);
            if($countfiles <= 5)
            {
                for($i=0;$i<$countfiles;$i++)
                {
                    $filename = $_FILES['archivo']['name'][$i];
                    $cadena .= $filename."-";

                    ## Location
                    $location = "../uploaded_files/".$filename;
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

                $fq1 = commentPost($conn,$content2,$date1,$userData,$idPost,$cadena);
                if(!$fq1){
                    echo "Algo Salio Mal";
                }
            }
            else
            {
                echo "<script> alert('No se publicó tu comentario, limite de archivos: 5'); </script>";
            }
           
        }
        else
        {
            $res1 = commentPost($conn,$content2,$date1,$userData,$idPost," ");
            if(!$res1){
                echo "Algo salio mal :(";
            }
        }
    }

    if(isset($_POST['btnDelete']))
    {
        $idPost = $_GET['btnComment'];

        $data = explode("-",$_POST['btnDelete']); //datos del comentario
        $idcom = $data[0]; //id comentario
        $user = $data[1];  //usuario del comentario
        $mat = $data[2];
        
        $user_ex = $user."-".$mat;

        if($userType == 1)
        {
            $delete = "DELETE FROM COMENTARIO WHERE IDCOMENTARIO = $idcom AND USUARIO = '$user_ex'"; 
            $delcomm = mysqli_query($conn,$delete);
        }
        else
        {
            $delete = "DELETE FROM COMENTARIO WHERE IDCOMENTARIO = $idcom AND USUARIO = '$userData'"; 
            $delcomm = mysqli_query($conn,$delete);
            
        }
        
        //ACTUALIZAR COMENTARIOS Y RESPUESTAS DEL USUARIO
        $resta1 = "UPDATE PUBLICACION SET NCOMENTARIOS = NCOMENTARIOS - 1 WHERE IDPUBLICACION = $idPost";
        $resta2 = "UPDATE USUARIO SET RESPUESTAS = RESPUESTAS - 1 WHERE NOMBRE = '$name' AND MATRICULA = '$mat'";

        if($delcomm){
            $restaCom = mysqli_query($conn,$resta1);
            $restaRes = mysqli_query($conn,$resta2);
        }
        else{
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
    <title>Comunidad|Comentario</title>
</head>
<body>
    <nav class="navbar navbar-primary bg-primary text-light border-bottom ">
        <div class="container">
            <img src="../assets/community.png" width="80" height="80" class="align-top" alt="">
            <h2>Comunidad de aprendizaje: Habilidades del Pensamiento</h2>
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
                        <img src="<?php echo $userProf;?>" class="rounded" alt="fotoPerfil" width="80" height="80">
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

                <!--PANEL DE COMENTARIOS-->
                 <div class="row">  
                    <h4 class="text-center">COMENTARIOS</h4>
                    <div class="col">
                        <form action="" method="post" enctype="multipart/form-data">
                            <textarea name="contenido" class="form-control" placeholder="Escribe algo..."></textarea>   
                        
                            <!--COLOCAR NOMBRE DE ARCHIVO LIMITE HASTA 5 ARCHIVOS-->
                            <div id="archivos"></div>
                            
                            <div class="mt-2 text-end"> <!--BOTONES-->
                                <button class="btn btn-outline-secondary" name="cancel" 
                                        title="Retirar Archivos" onclick="cancelaArchivos()">
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
            
            <!--LADO DERECHO AREA DEL POST -->
            <div id="rightbar" class="col-md-8 text-light bg-secondary rounded ">
                <div class="row p-2 bg-dark rounded">
                    <h2>Comentar Publicacion</h2>
                </div>

                <div class="row p-2 mt-2">
                    <!--POST ELEGIDO -->
                    <div class="border border-light border-4 rounded bg-dark">
                        <div class="row p-2">
                            <div class="col-sm-4"> 
                                <h3><?php echo $title; ?></h3> 
                            </div>

                            <div class="col-sm-8"> 
                                <p class="text-end">
                                    <?php  echo $remitant. "\n"; 
                                            echo $date;  //FRAGMENTO PHP?>
                                </p>
                            </div>
                        </div>

                        <div class="row p-2">
                            <p class="text-justify">
                                <?php echo $content ?> 
                            </p>
                        </div>                

                        <div class="d-flex flex-row p-2 justify-content-center">
                            <?php 
                                if($files != null)
                                {   
                                    $cut = explode("-",$files); //partir cadena                                 
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
                    </div>
                    <!--POST ELEGIDO -->
                    
                    <h3 class="text-center">Respuestas</h3>

                    <div class="d-grid gap-0">
                        <?php
                            //MOTOR DEL AREA DE COMENTARIOS DE LA PUBLICACION
                            $queryComm = "SELECT * FROM COMENTARIO WHERE IDPUBLICACION = $idPost";     
                            $res3 = mysqli_query($conn,$queryComm);
                            if($res3)
                            {
                                while (($row3 = mysqli_fetch_array($res3)) != NULL) //OBTENER REGLONES DE LAS CONSULTAS
                                {       
                                    $matPost = explode("-",$row3['usuario'])[0];
                                    //PAUSA PHP?>
                                    
                                    <!--FORMATO DE COMENTARIO-->
                                    <div class="row bg-dark border-bottom">
                                        <div class="row-sm text-end">                                      
                                            <?php echo $row3['usuario']." ".$row3['fecha_hora'];  ?>                                                                     
                                        </div>

                                        <p class="col-10"><?php echo $row3['contenido'];?></p>

                                        <?php
                                            if($matPost === $userMat || $userType == 1)
                                            { ?>
                                                <form class="col-2" action="" method="post">
                                                    <button class="btn btn-danger btn-sm" name="btnDelete" 
                                                        value="<?php echo $row3['idComentario']."-".$row3['usuario']; ?>" type="submit">
                                                        <i class="bi bi-eraser"></i>   
                                                    </button>
                                                </form> 
                                            <?php 
                                            }
                                        ?>           

                                        <div class="d-flex flex-row p-2 justify-content-center">
                                            <?php 
                                                if($row3['archivo'] != null)
                                                {   
                                                    $fileStrings = $row3['archivo'];    //cadena de strings de 1 a 5 archivos  
                                                    $cut = explode("-",$fileStrings); //partir cadena
                                                    
                                                    foreach($cut as $f)
                                                    { 
                                                        $format = strtolower(substr($f, -3));
                                                        if(in_array($format,$images))
                                                        { ?>
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
                                    </div>    
                                    <?php                                       
                                }
                            }
                            else{
                                echo "0 RESULTS";  
                            }
                        ?>                         
                </div> <!--AREA COMENTARIOS-->                         
            </div>  <!--LADO DERECHO-->   
                     
        </div> <!--RENGLON GENERAL  -->
    </div> <!-- CONTENEDOR -->

    <script src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>



