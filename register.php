<?php 
    require("utils/config.php");
    

    if(isset($_POST['register']))
    {
        $nom = $_POST['nombre'];
        $mat = $_POST['matricula'];
        $pass1 = $_POST['contraseña1']; //** Y CONFIRMA CONTRASEÑA */
        $pass2 = $_POST['contraseña2'];
        
        $nom = strtr($nom," ","-");
        
        //INTEGRAR A LA BD
        if($pass1 == $pass2)
        {
            $pass = password_hash($pass1,PASSWORD_DEFAULT); 
            $consulta = "INSERT INTO usuario (nombre,matricula,contraseña,tipo,respuestas) 
                            VALUES('$nom','$mat','$pass',0,0)";

            $res = mysqli_query($conn,$consulta);
            if($res)
                header("location:login.php");
            else
                echo("Error: Algo salio mal :(");            
            //YA REGISTRADO PERMANECE EN EL MISMO SITIO
        }
        else
        {
            echo("CONTRASEÑAS NO COINCIDEN");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="extra/aux1.css">
    <title>Comunidad | Registro</title>
</head>
<body id="bg1">
    <nav class="navbar navbar-primary bg-primary text-light border-bottom ">
        <div class="container">
            <img src="assets/community.png" width="80" height="80" class="align-top" alt="">
            <h2>Comunidad de aprendizaje: Habilidades del Pensamiento</h2>
        </div>    
    </nav>

    <div class="container">
        <div class="row">
            
            <div class="card-signup mt-3">
                <div class="card text-center">
                    <div class="card-header bg-primary text-light">
                        <h3>Bienvenido</h3>
                    </div>

                    <div class="card-body card-padding">                   
                        <form action="" method="post" class="g-3">
                            <div class="form-group row mb-3">
                                <label for="nombre" class="col-form-label col-sm-2" >Nombre: </label>
                                <div class="col-sm-10">
                                    <input name="nombre" type="text" class="form-control" required>
                                </div>                  
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="matricula" class="col-form-label col-sm-2">Matricula: </label>
                                <div class="col-sm-10">
                                    <input name="matricula" type="text" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="contraseña"  class="col-form-label col-sm-2">Contraseña: </label>
                                <div class="col-sm-10">
                                    <input name="contraseña1" type="password" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="contraseña2"  class="col-form-label col-sm-2">Confirmar Contraseña: </label>
                                <div class="col-sm-10">
                                    <input name="contraseña2" type="password" class="form-control" required>
                                </div>
                            </div>

                            <input name="register" type="submit" value="Registrarse" class="btn btn-primary">                                                      
                        </form>     
                    </div>

                    <div class="card-footer">
                            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></p>    
                    </div>

                </div> <!--CARD-->
            </div><!--CARD-->
            
        </div> <!--ROW-->
    </div> <!--CONTAINER-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>