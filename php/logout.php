<?php

session_start();

if(!isset ($_SESSION)){
    header('Location: ../index.php');
} else{
    session_unset();
    session_destroy();
    header('Location: ../index.php');
}

?>