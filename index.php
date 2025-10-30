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

//DIVIDIR FRONT DEL BACK
//ARREGLAR EL ELIMINADO DE PRODUCTOS/CATEGORIAS, BORRADO LOGICO..
//AGREGAR GESTION CUENTA DE USUARIO FINAL, LISTADO DE PEDIDOS REALIZADOS.
//AGREGAR REGISTRO USUARIOS FINALES
//AGREGAR CREACION USUARIOS DESDE PANEL ADMIN
//AGREGAR INTEGRACION CON MERCADOPAGO

?>
