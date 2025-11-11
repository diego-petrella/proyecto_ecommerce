<?php
// ¡IMPORTANTE! Reemplaza estos valores con tus credenciales reales de Mercado Pago
// Estas credenciales se obtienen en el Panel de Desarrolladores de Mercado Pago.
// Para pruebas, usa las credenciales de 'Test'.

// Access Token: Clave de acceso para la API de Mercado Pago
define('MP_ACCESS_TOKEN', 'APP_USR-1225567301912849-102209-92de25a83c2180b1cfe2319f888f55ef-1803836373');

// URL base de la aplicación (Necesaria para notificaciones)
// Debe coincidir con la BASE_URL que definiste en index.php
//define('MP_BASE_URL', 'http://localhost/programacion2/articulos/');
define('MP_BASE_URL', 'https://heterogynous-billye-oceanographically.ngrok-free.dev/programacion2/articulos/');
// URL de la API de Mercado Pago
//define('MP_API_BASE', 'https://api.mercadopago.com');
define('MP_API_BASE', 'https://api.mercadopago.com/checkout/preferences');
?>