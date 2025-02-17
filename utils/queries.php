<?php
    include("config.php");
   
    //DATOS DE USUARIO RECIBE NOMBRE DE LA SESION
    function userData($userName,$connection)
    {
        $query = "SELECT * FROM USUARIO WHERE nombre = '$userName'"; 
        $res = mysqli_query($connection,$query); 

        if ($res->num_rows > 0)  
        { 
            while($row = $res->fetch_assoc()) // OUTPUT DATA OF EACH ROW 
            { 
                $userId = $row["idUsuario"]." ";
                $nombre = $row["nombre"];
                $nombreCompleto = explode("-",$nombre);
                $primer_nombre = $nombreCompleto[0];

                $userData = $primer_nombre." ".$row["matricula"]." ";
                $userPass = $row['contraseña']." ";
                $userImage = $row['nombrefoto'];
                $userType = $row['tipo']." ";
            }

            if($userImage == null)
                $userProf = "../assets/user.png";
            else  
                $userProf ="../profile_pics/".$userImage; //ruta de foto de perfil
        }  
        else 
        { 
            return "0 results"; 
        }

        $string = $userId.$userData.$userPass.$userProf." ".$userType.$nombre;
        return $string;
    }


    //ATENCION MOVER LOS DEMAS ARCHIVOS EN UNA CARPETA
    //PAGINA DE PERFIL SUBIR FOTO DE PERFIL RETORNA BOOL
    function profileImg($file,$filetemp,$idUser,$connection)
    {
        $destination = "../profile_pics/";
        $filename = $file;        //$_FILES["subirFoto"]["name"];
        $tempname = $filetemp;    //$_FILES["subirFoto"]["tmp_name"];
        $target = $destination.basename($filename);
        
        $sql = "UPDATE USUARIO SET NOMBREFOTO = '$filename' WHERE IDUSUARIO = $idUser"; 
        $res = mysqli_query($connection,$sql);        
        if($res){
            if (move_uploaded_file($tempname, $target)){
                return true;    //header("refresh:0"); //echo "<h3>&nbsp; Image uploaded successfully!</h3>";
            }      
            else{
                return false;    //echo "<h3>&nbsp; Failed to upload image!</h3>";
            }
        }
        else{
            return false;
        }

        
    }


    //ACTUALIZAR DATOS USUARIO
    function updateUser($newName,$newMat,$newPass,$idUser,$connection)
    {
        if($newPass == " ")
        {
            $upt = "UPDATE USUARIO SET NOMBRE = '$newName', MATRICULA = '$newMat'
                    WHERE IDUSUARIO = $idUser";
        }
        else
        {
            $upt = "UPDATE USUARIO SET NOMBRE = '$newName', MATRICULA = '$newMat', CONTRASEÑA = '$newPass'
                    WHERE IDUSUARIO = $idUser";
        }

        return mysqli_query($connection,$upt);
    }

    //ACTUALIZAR COMENTARIOS Y POSTS RECIBE <<PARAMETROS DADOS COMO MAT-USER>>
    function changeData($newUser,$oldUser,$connection)
    {
        $cgd1 = "UPDATE COMENTARIO SET USUARIO = '$newUser' WHERE USUARIO = '$oldUser'";
        $cgd2 = "UPDATE PUBLICACION SET USUARIO = '$newUser' WHERE USUARIO = '$oldUser'";

        if(mysqli_query($connection,$cgd1)){
            return mysqli_query($connection,$cgd2);
        }
    }




    //-------------------------------------
    //          FUNCIONES DE POSTS
    //-------------------------------------

    function likePost($id_post,$connection)
    {
        $queryUpt = "UPDATE PUBLICACION SET NLIKES = NLIKES + 1 WHERE IDPUBLICACION = $id_post";
        $update = mysqli_query($connection,$queryUpt);
        return $update; 
    }


    function insertPost($connection,$txtHead,$txtCont,$date,$user,$file_string)
    {
        $insertPost = " ";
        if($file_string != " ")
        {
            $insertPost = "INSERT INTO PUBLICACION (titulo,contenido,archivo,fecha_hora,nComentarios,nLikes,usuario)
                                VALUES ('$txtHead','$txtCont','$file_string','$date',0,0,'$user')";
        }
        else
        {
            //PROCEDIMIENTO DE PUBLICACION SIN ARCHIVOS
            $insertPost = "INSERT INTO PUBLICACION (titulo,contenido,fecha_hora,nComentarios,nLikes,usuario)
                              VALUES ('$txtHead','$txtCont','$date',0,0,'$user')";
        }

        return mysqli_query($connection,$insertPost);
    }

    function deletePost($connection, $user, $id_post){
        $delete1 = "DELETE FROM COMENTARIO WHERE IDPUBLICACION = $id_post";
        $delete2 = "DELETE FROM PUBLICACION WHERE USUARIO = '$user' AND IDPUBLICACION = $id_post";
        
        if(mysqli_query($connection,$delete1)){
            if(mysqli_query($connection,$delete2)){
                return true;
            }  
        }
        return false;       
    }

    
    //-------------------------------------
    //         FUNCIONES DE COMENTARIO 
    //-------------------------------------
    
    function commentPost($connection,$txtCont,$date,$user,$id_post,$file_string)
    {
        $data = explode("-",$user);
        $mat = $data[0];
        $comment = " ";
        if($file_string != " ")
        {
            $comment = "INSERT INTO COMENTARIO (CONTENIDO,FECHA_HORA,USUARIO,ARCHIVO,IDPUBLICACION) 
                                VALUES ('$txtCont','$date','$user','$file_string',$id_post)";
        }
        else{ //COMENTARIO SIN ARCHIVOS
            $comment = "INSERT INTO COMENTARIO (CONTENIDO,FECHA_HORA,USUARIO,IDPUBLICACION) 
                        VALUES ('$txtCont','$date','$user',$id_post)";         
        }

        $res = mysqli_query($connection,$comment);
        if($res)
        {
            $update1 = "UPDATE PUBLICACION SET NCOMENTARIOS = NCOMENTARIOS + 1 WHERE IDPUBLICACION = $id_post";
            $update2 = "UPDATE USUARIO SET RESPUESTAS = RESPUESTAS + 1 WHERE matricula = '$mat'";
            if(mysqli_query($connection,$update1)){
                return mysqli_query($connection,$update2);
            }
            else
                return false;
        }
        else
            return false;
    }

    //BUSCAR POST
    function searchPost($connection,$id_post)
    {
        $query = "SELECT TITULO,CONTENIDO FROM PUBLICACION WHERE idPublicacion = $id_post";
        $res = mysqli_query($connection,$query); 
        if ($res)  
        { 
            while($row = mysqli_fetch_array($res)) // OUTPUT DATA OF EACH ROW 
            { 
                $title = $row[0];
                $content = $row[1];               
            }
            $cadena = $title."-".$content;
            return $cadena;
        }  
        return "0"."-"."results";
    }

    function editPost($connection,$id_post,$user,$nTitle,$nContent,$ndate)
    {
        $upt = "UPDATE PUBLICACION SET TITULO = '$nTitle',CONTENIDO = '$nContent',fecha_hora = '$ndate' 
                    WHERE IDPUBLICACION = $id_post AND USUARIO = '$user'";
        
        return mysqli_query($connection,$upt);  
    }


    //DIRECTORIOS 
    function dir_is_empty($dir) 
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            closedir($handle);
            return false;
          }
        }
        closedir($handle);
        return true;
    }





?>