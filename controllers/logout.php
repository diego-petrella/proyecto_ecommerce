<?php

session_start();

session_unset();

session_destroy();

header("Location: /programacion2/articulos/index.php");
exit;
?>