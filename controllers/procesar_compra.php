<?php
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/programacion2/articulos/');
}


require '../models/funciones.php';

require '../includes/config_mercadopago.php'; 


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header('Location: ' . BASE_URL . 'views/carrito.php');
    exit;
}

$id_usuario = (int)$_SESSION['usuario_id'];
$carrito = $_SESSION['carrito']; 
$total_final_form = (float)($_POST['total_final_visible'] ?? 0); 
$subtotal_form = (float)($_POST['subtotal'] ?? 0);
$metodo_pago = $_POST['metodo_pago'] ?? ''; 

$datos_envio = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'apellido' => trim($_POST['apellido'] ?? ''),
    'direccion' => trim($_POST['direccion'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
];

//VERIFICACION BASICA
if (empty($metodo_pago) || $total_final_form <= 0) {
    $_SESSION['error_compra'] = 'Método de pago o carrito inválido.';
    header('Location: ' . BASE_URL . 'views/checkout.php');
    exit;
}

// ACTUALIZACION DEL PERFIL DE USUARIO SI NO CARGO SUS DATOS ANTES
$datos_para_actualizar_perfil = [
    'nombre' => $datos_envio['nombre'], 'apellido' => $datos_envio['apellido'],
    'direccion' => $datos_envio['direccion'], 'telefono' => $datos_envio['telefono'],
];
actualizarPerfilUsuario($id_usuario, $datos_para_actualizar_perfil);

// ACCIONES DEPENDIENDO DEL TIPO DE PAGO
switch ($metodo_pago) {
    case 'Efectivo':
    case 'Transferencia':
        //GUARDA PEDIDOS SIMPLE
        $id_pedido = guardarOrdenLocal(
            $id_usuario, 
            $datos_envio, 
            $carrito, 
            $subtotal_form, 
            $total_final_form, 
            $metodo_pago
        );

        if (!$id_pedido) {
            $_SESSION['error_compra'] = 'Fallo al registrar la orden local o stock insuficiente.';
            header('Location: ' . BASE_URL . 'views/checkout.php');
            exit;
        }

        //LIMPIA EL CARRITO DESPUES DE GUARDAR
        unset($_SESSION['carrito']); 
        header('Location: ' . BASE_URL . 'views/confirmacion.php?pedido_id=' . $id_pedido);
        exit;

    case 'Tarjeta':
        break;
        //SIGUE A LA LOGICA DE GUARDADO DE MP
    default:
        $_SESSION['error_compra'] = 'Método de pago no válido.';
        header('Location: ' . BASE_URL . 'views/checkout.php');
        exit;
}


//FLUJO MP
$items_mp = [];
$articulos_bd = obtenerArticulosCarrito($carrito); 

foreach ($carrito as $id_articulo => $cantidad) {
    if (isset($articulos_bd[$id_articulo])) {
        $item_data = $articulos_bd[$id_articulo];
        $items_mp[] = [
            'id' => (string)$id_articulo,
            'title' => $item_data['nombre'],
            'description' => $item_data['descripcion_corta'] ?? $item_data['nombre'],
            'quantity' => (int)$cantidad,
            'unit_price' => (float)$item_data['precio'],
            'currency_id' => 'ARS', 
        ];
    }
}

$preference_data = [
    'items' => $items_mp,
    'payer' => [
        'name' => $datos_envio['nombre'],
        'surname' => $datos_envio['apellido'],
        'email' => $datos_envio['email'],
    ],
    
    'external_reference' => (string)$id_usuario, 
    'back_urls' => [
        'success' => MP_BASE_URL . 'views/compra_exitosa.php?status=success',
        'pending' => MP_BASE_URL . 'views/compra_exitosa.php?status=pending',
        'failure' => MP_BASE_URL . 'views/compra_exitosa.php?status=failure',
    ],
    'notification_url' => MP_BASE_URL . 'controllers/mercadopago_webhook.php', 
    'auto_return' => 'approved',
];

// CONEXXION API MP
$ch = curl_init(MP_API_BASE); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preference_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . MP_ACCESS_TOKEN, 
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$mp_response = json_decode($response, true);

// RESPUESTA
if ($http_code === 201 && isset($mp_response['init_point'])) {
    header('Location: ' . $mp_response['init_point']);
    exit;

} else {
    // FALLO: Redirigir con error.
    error_log("MP API Error (Code: $http_code): " . ($mp_response['message'] ?? $response));
    $_SESSION['error_compra'] = 'Error al conectar con el medio de pago. Intente más tarde.';
    header('Location: ' . BASE_URL . 'views/checkout.php'); 
    exit;
}
?>