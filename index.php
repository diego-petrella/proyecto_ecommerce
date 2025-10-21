<?php
if (!defined('APP_INCLUDED')) {
    define('APP_INCLUDED', true);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//Controlador de Catálogo
require "./controllers/catalogo.php";

//Incluir las plantillas
require "./includes/header.php"; 
require "./views/catalogo.php";
require "./includes/footer.php"; 
?>