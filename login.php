<?php
        require("utils/config.php");
        session_start();

        if(isset($_POST['start']))
        {
            $user = $_POST["matricula"]; //asumiendo que es la matricula
            $pass = $_POST["contraseña"];
            $_SESSION["validate"] = false;
   
            $consulta = "SELECT * FROM USUARIO WHERE MATRICULA = '$user'";
            $res = mysqli_query($conn,$consulta);
 
            if($res)
            {
                $row = mysqli_fetch_array($res);
                $passObtenida = $row["contraseña"];
                
                if(password_verify($pass,$passObtenida))
                {
                    $_SESSION["usuario"] = $row["nombre"];
                    $_SESSION["contraseña"] = $row["contraseña"];
                    $_SESSION["validate"] = true;

                    if($row['tipo'] == 1)
                        header("location:paginas_admin/admin_main.php");
                    else
                        header("location:index.php");
                }
                else
                {
                    echo "<script> alert('Datos incorrectos'); </script>";
                }

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
    <title>Comunidad| Log in</title>
</head>
<body id="bg1">
    <nav class="navbar navbar-primary bg-primary text-light border-bottom">
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
                        <h3>Inicia Sesión para entrar</h3>
                    </div>
                    
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group row mb-3">
                                <label for="usuario" class="col-sm-2 col-form-label">Matricula:</label>
                                <div class="col-sm-10">
                                    <input name="matricula" type="text" class="form-control" required>
                                </div>  
                            </div>

                            <div class="form-group row mb-3">
                                <label for="contraseña" class="col-sm-2 col-form-label">Contraseña:</label>
                                <div class="col-sm-10">
                                    <input name="contraseña" type="password" class="form-control" required>
                                </div>
                            </div>

                            <input name="start" type="submit" class="btn btn-primary">
                        </form>
                    </div>

                    <div class="card-footer">
                        <p>¿No tienes una cuenta?</p>
                        <a href="register.php">Click Aqui</a>
                    </div>

                </div>
            </div>  <!--CARD-->

        </div> <!--ROW-->
    </div> <!--CONTAINER-->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>
</html>