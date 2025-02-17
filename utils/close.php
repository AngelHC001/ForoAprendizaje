<?php
    session_start();
    session_unset(); //borra variables de sesion
    session_destroy();
    
    header("location:../login.php");
    exit;
?>