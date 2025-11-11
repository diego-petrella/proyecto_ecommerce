<?php
session_start();
require '../models/funciones.php';
require '../includes/config_mercadopago.php'; 

// NOTIFICACION MP
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// VERIFICAR NOTIFICACION TIPO 'payment'
if (!isset($data['type']) || $data['type'] !== 'payment') {
    http_response_code(400); 
    exit;
}

//ID DEL PAGO DE MP
$payment_id = $data['data']['id'] ?? null;

if (!$payment_id) {
    http_response_code(400);
    exit;
}

// Consultar a Mercado Pago para obtener el estado real del pago (por seguridad)
$ch = curl_init("https://api.mercadopago.com/v1/payments/" . $payment_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . MP_ACCESS_TOKEN,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$payment_info = json_decode($response, true);

// PROCESAR INFO PAGO
if ($http_code === 200 && $payment_info['status'] === 'approved') {
    // Obtener la referencia externa (el ID de usuario que enviamos al generar la preferencia)
    $user_id = $payment_info['external_reference'];
    $total_paid = $payment_info['transaction_amount'];
    $mp_preference_id = $payment_info['id'];
    
    // Obtener los ítems y los datos del usuario para el registro en la BD
    $items = $payment_info['additional_info']['items'] ?? [];
    
    $datos_usuario_db = buscarDatosUsuario((int)$user_id); 
    
    $datos_envio_webhook = [
        'nombre' => $datos_usuario_db['nombre'] ?? '',
        'apellido' => $datos_usuario_db['apellido'] ?? '',
        'direccion' => $datos_usuario_db['direccion'] ?? 'N/A',
        'telefono' => $datos_usuario_db['telefono'] ?? 'N/A',
    ];

    // 6. Guardar el pedido confirmado en tu BD
    $id_pedido = guardarOrdenDeVentaConfirmada(
        (int)$user_id, 
        $datos_envio_webhook, 
        (float)$total_paid, 
        $items, 
        (string)$mp_preference_id
    );

    if ($id_pedido) {
        // Éxito. Si todo sale bien, respondemos 200 a MP.
    }
    
    http_response_code(200); // Responder 200 OK es obligatorio para Mercado Pago
    exit;

} else {
    // Pago pendiente, rechazado, o error de consulta.
    // Solo registramos y respondemos 200 para evitar reintentos infinitos de MP.
    error_log("MP Webhook: Pago ID $payment_id no aprobado o error al consultar.");
    http_response_code(200); 
    exit;
}
?>