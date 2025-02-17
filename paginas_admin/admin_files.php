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

    $path = "../uploaded_files";  //Asignar ruta
    $directorio = dir($path);     //Crear Objeto dir con la ruta

    //FUNCION GUARDAR TODO CREANDO UN ZIP
    if(isset($_POST['saveAll']))
    {
        if(!dir_is_empty($path))
        {
            $zipfile = "../repos/entregas.zip"; 
            touch($zipfile);

            //abrir zip
            $zip = new ZipArchive;
            $this_zip = $zip->open($zipfile);

            if($this_zip)
            {
                $folder = opendir('../uploaded_files');
                while(false !==  ($file = readdir($folder)) )
                {
                    if($file !== "." && $file !== ".."){
                        $file_with_path = "../uploaded_files/".$file;
                        $zip->addFile($file_with_path,$file);
                    }
                }
                closedir($folder);
            }
            
            //descargar
            if(file_exists($zipfile))
            {
                $demo_name = "archivos_entregados.zip";
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename='.$demo_name.'');
                readfile($zipfile);
                unlink($zipfile); //borrar archivo temporal
            }
        }
        else{
            echo "El directorio esta vacio";
        }
        
    }




    //FUNCION BORRAR TODOS LOS ARCHIVOS
    if(isset($_POST['cleanAll']))
    {
        if(!dir_is_empty($path))
        {
            while ($archivo = $directorio->read())
            {
                $dir1 = $path."/".$archivo;
                if($archivo != "." && $archivo!="..")
                {
                    if(file_exists($dir1)){
                        unlink($dir1);
                    }  
                }

                
            }       
        }
        else{
            echo "El directorio esta vacio";
        }
    }
    

    if(isset($_POST['delete'])){       
        $dirA = $_POST['delete'];
        if(file_exists($_POST['delete'])){
            unlink($dirA);
        }
        else{
            echo "ALGO SALIO MAL";
        }        
    }

    $docs = array("pdf","docx","pptx","xlsx");

   
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
                    <a href="admin_control.php" class="list-group-item list-group-item-action">Administrador</a>
                    <a href="#" class="list-group-item list-group-item-action active">Archivos Enviados</a>
                    <a href="admin_prof.php" class="list-group-item list-group-item-action">Ver Perfil</a>                   
                    <a href="admin_main.php" class="list-group-item list-group-item-action">Volver al Grupo</a>
                </div>
                
                <div class="row list-group p-3 d-grid gap-3">
                    <h3>Opciones para archivos</h3>
                    <form action="" method="post">
                        <button name="saveAll" class="btn btn-success">
                            <i class="bi bi-download"></i> Guardar todo
                        </button>
                        <button name="cleanAll" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Limpiar Carpeta
                        </button>
                    </form>
                   
                </div>
            </div>
            <!--BARRA DE HERRAMIENTAS-->

            <!--BARRA DE CRUD-->
            <div id="rightbar" class="col-md-8 text-light text-center">
                <div class="row p-2 bg-dark rounded text-between">
                    <h2>Archivos Enviados</h2>
                    
                </div>

                <div class="row p-2 admin-col-2">
                    <?php
                        //LEER DIRECTORIO
                        //$path = "../uploaded_files";  //Asignar ruta
                        //$directorio = dir($path);     //Crear Objeto dir con la ruta

                        //Con Dir podemos Leer directorio hasta el final
                        while ($archivo = $directorio->read())
                        {
                            if($archivo != "." && $archivo!="..")
                            {
                                $dir1 = $path."/".$archivo;
                                $format = strtolower(substr($archivo, -3)); //SABER EXTENSION DEL ARCHIVO
                                ?>
                                    <div class="col-sm-4 p-2">
                                        <div class="card">
                                            <div class="card-header">
                                                <?php     
                                                    //CHECAR PREVIEW DE PPT PDF Y DOC 
                                                    //BOTON DE DESCARGA
                                                    //BOTON DE BORRAR EN LA RUTA                                      
                                                    if ($format == "jpg" || $format == "png")
                                                    {   ?>
                                                        <img class="card-img-top" src="<?php echo $dir1;?>" width="287" height="160">
                                                        <?php
                                                    }
                                                ?>
                                            </div>
                                           
                                            <div class="card-body">
                                                <?php echo $archivo."<br>";?>
                                            </div>

                                            <div class="card-footer">
                                                <form action="" method="post">
                                                    <a class="btn btn-primary" href="<?php echo $dir1;?>" download="<?php echo $dir1;?>" >
                                                        Descargar
                                                    </a>
                                                   
                                                    <button name="delete" class="btn btn-danger" type="submit" value="<?php echo $dir1;?>" >
                                                        Borrar
                                                    </button>                
                                                </form>
                                            </div>

                                        </div> <!--CARD-->
                                    </div> <!--COL-->

                                <?php
                            }
                        }
                        $directorio->close();
                    ?>
                </div>       
            </div> <!--BARRA DE CRUD-->
            
        </div> <!--ROW-->
    </div> <!--CONTAINER-FLUID-->

    <script type="text/javascript" src="../extra/funciones.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>