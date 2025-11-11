<?php
function getConnection() {
  $host = 'localhost';      
  $db   = 'programacion2';        
  $user = 'root';     
  $pass = '';    
  $charset = 'utf8mb4';
  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  return new PDO($dsn, $user, $pass);
}


function obtenerArticulos($filtro_nombre = "", $filtro_marca, $filtro_estado, $limite, $offset) {
    $pdo = getConnection();
    
   
    $sql = " SELECT A.*, C.nombre AS nombre_categoria 
             FROM articulos A 
             LEFT JOIN categorias C ON (A.id_categoria = C.id) 
             WHERE 1=1 ";
    
   
    $params = [];
    
 
    if (!empty($filtro_nombre)) {
      
        $sql .= " AND (A.nombre LIKE :nombre 
                      OR A.descripcion_corta LIKE :nombre_desc 
                      OR C.nombre LIKE :nombre_cat)";
        
        $params[':nombre'] = '%' . $filtro_nombre . '%';
        $params[':nombre_desc'] = '%' . $filtro_nombre . '%';
        $params[':nombre_cat'] = '%' . $filtro_nombre . '%';  
    }
    
    if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
        $sql .= " AND A.id_categoria = :marca";
        $params[':marca'] = (int)$filtro_marca;
    }
    

    if ($filtro_estado !== "") {
        $sql .= " AND A.activo = :activo"; 
        $params[':activo'] = (int)$filtro_estado; 
    }
    
  
    $sql .= " ORDER BY A.id DESC LIMIT :limite OFFSET :offset";
    $params[':limite'] = (int)$limite;
    $params[':offset'] = (int)$offset;
    
    $stmt = $pdo->prepare($sql);
    
    try {
        
        foreach ($params as $key => &$value) {
            
            if (in_array($key, [':limite', ':offset', ':marca', ':activo'])) {
                $type = PDO::PARAM_INT;
            } else {
                $type = PDO::PARAM_STR;
            }
            $stmt->bindValue($key, $value, $type);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error de BD al obtener artículos: " . $e->getMessage());
        return [];
    }
}






function guardarArticulo($array, $hayNuevaImagen = false) {
  $pdo = getConnection();
    if (isset($array["id"])) {
      
        $sql = "UPDATE articulos SET ";
        $updates = [
            "nombre = :nombre",
            "descripcion_corta = :descripcion_corta",
            "precio = :precio",
            "stock = :stock",
            "id_categoria = :id_categoria",
            "activo = :activo"
        ];
        $params = [
            ':nombre' => $array["nombre"],
            ':descripcion_corta' => $array["descripcion_corta"],
            ':precio' => $array["precio"],
            ':stock' => $array["stock"],
            ':id_categoria' => $array["id_categoria"],
            ':activo' => $array["activo"],
            ':id' => $array["id"]
        ];

        if ($hayNuevaImagen) {
            $updates[] = "imagen = :imagen";
            $params[':imagen'] = $array["imagen"];
        }

        $sql .= implode(', ', $updates);
        $sql .= " WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
     
        $stmt->execute($params);
  } else {
    $sql = "INSERT INTO articulos (nombre, descripcion_corta, precio, stock, id_categoria, imagen, activo) VALUES (:nombre, :descripcion_corta, :precio, :stock, :id_categoria, :imagen, :activo) ";
    $stmt = $pdo->prepare($sql);

$params = [
            ':nombre' => $array["nombre"],
            ':descripcion_corta' => $array["descripcion_corta"],
            ':precio' => $array["precio"],
            ':stock' => $array["stock"],
            ':id_categoria' => $array["id_categoria"],
            ':imagen' => $array["imagen"],
            ':activo' => $array["activo"]  
        ];
  $stmt->execute($params);
  }
}

function desactivarArticulo($id_articulo) {
    $pdo = getConnection();
    $sql = "UPDATE articulos SET activo = 0 WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ (int)$id_articulo ]);
        // Verificar si la fila fue afectada
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error de BD al desactivar el artículo: " . $e->getMessage());
        return false;
    }
}


function restaurarArticulo($id_articulo) {
    $pdo = getConnection();
    $sql = "UPDATE articulos SET activo = 1 WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ (int)$id_articulo ]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error de BD al restaurar el artículo: " . $e->getMessage());
        return false;
    }
}


function buscarPorId($id) {
  $pdo = getConnection();
  $sql = "SELECT A.*, C.nombre AS nombre_categoria  FROM articulos A INNER JOIN categorias C on (A.id_categoria = C.id) WHERE A.id = ? ";
  
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(1, $id, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}




function buscarCategoriaPorId($id) {
    $pdo = getConnection(); 
    
    $sql = "SELECT id, nombre FROM categorias WHERE id = :id_categoria ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_categoria', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



function filtrarArticulos (){
$pdo = getConnection();
$query= "SELECT C.nombre, ";
$query.=" SUM(I.precio_unitario * I.cantidad) AS subtotal ";
$query.=" FROM items I INNER JOIN articulos A ON (I.id_articulo = A.id) ";
$query.=" INNER JOIN categorias C ON (A.id_categoria = C.id) GROUP BY C.nombre";

$stmt = $pdo->prepare($query);
$stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function verificarUsuario($email, $password_form) {
    $pdo = getConnection();
    
    try {
        
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $usuario_bd = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if (!$usuario_bd) {
            error_log("Fallo de login: Email no encontrado o inactivo - " . $email);
            return false;
        }
        if (password_verify($password_form, $usuario_bd['password_hash'])) {
            return $usuario_bd; 

        } else {
            error_log("Fallo de login: Contraseña incorrecta para - " . $email);
            return false;
        }

    } catch (PDOException $e) {
        error_log("Error de BD en verificarUsuario: " . $e->getMessage());
        return false;
    }
}



function obtenerProductosPorListaDeIds($product_ids) {
   $pdo = getConnection();

    if (empty($product_ids)) {
        return [];
    }
    
    // CONVERTIR ARRAY EN CADENA PARA LA QUERY
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    
    
    $sql = "
        SELECT 
            a.id, a.nombre, a.precio, a.id_categoria, a.stock, a.imagen,
            C.nombre AS nombre_categoria
        FROM 
            articulos a
        JOIN 
            categorias C ON a.id_categoria = C.id
        WHERE 
            a.id IN ({$placeholders})
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    
    $productos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productos[$row['id']] = $row;
    }
    
    return $productos;
}


function contarProductosCarrito() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    return array_sum($_SESSION['carrito']); 
}

function obtenerStockProducto($id) {
    $pdo = getConnection(); 
    
    $sql = "SELECT stock FROM articulos WHERE id = :id_producto";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado && isset($resultado['stock'])) {
            return (int)$resultado['stock'];
        }
        
    } catch (PDOException $e) {
        
        error_log("Error al obtener stock del producto ID {$id}: " . $e->getMessage());
    }
    //Devuelve 9999 por defecto si el producto no se encuentra
    return 9999;
}

function vaciarCarrito() {
    if (isset($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
    }
    //ELIMINO VARIABLES CARGADAS
    if (isset($_SESSION['total_items_carrito'])) {
        unset($_SESSION['total_items_carrito']);
    }

    return true;
}

function guardarCategoria($array) {
    $pdo = getConnection();
    
    if (isset($array["id"]) && $array["id"] > 0) {
        $sql = "UPDATE categorias SET nombre = :nombre WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $array["id"], PDO::PARAM_INT);
    } else {
        $sql = "INSERT INTO categorias (nombre) VALUES (:nombre)";
        $stmt = $pdo->prepare($sql);
    }
    
    $stmt->bindParam(':nombre', $array["nombre"], PDO::PARAM_STR);
    return $stmt->execute();
}

function obtenerTodasCategoriasActivas() {
    $pdo = getConnection();
    
    $sql = "SELECT id, nombre 
            FROM categorias 
            WHERE activo = 1 
            ORDER BY nombre ASC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error de BD al obtener todas las categorías activas: " . $e->getMessage());
        return [];
    }
}

function obtenerCategorias($filtro_estado, $limite, $offset) {
    $pdo = getConnection();

    /* Esta consulta usa LEFT JOIN para incluir categorías con 0 artículos.
     Solo cuenta artículos donde a.activo = 1.
    */
    $sql = "SELECT
                c.id, c.nombre, c.activo,
                COUNT(a.id) AS conteo_articulos
            FROM
                categorias c
            LEFT JOIN
                articulos a ON c.id = a.id_categoria AND a.activo = 1
            WHERE
                1=1"; 

    if ($filtro_estado !== "") {
        $sql .= " AND c.activo = :activo";
    }

    $sql .= " GROUP BY c.id, c.nombre, c.activo";

    $sql .= " ORDER BY c.nombre ASC";

    $sql .= " LIMIT :limite OFFSET :offset"; 

    try {
        $stmt = $pdo->prepare($sql);

        if ($filtro_estado !== "") {
            $stmt->bindValue(':activo', (int)$filtro_estado, PDO::PARAM_INT);
        }
   
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error de BD al obtener categorías con conteo: " . $e->getMessage());
        return [];
    }
   
}


function desactivarCategoria($id_categoria) {
    $pdo = getConnection();
    $sql = "UPDATE categorias SET activo = 0 WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ (int)$id_categoria ]);
        // Verificar si la fila fue afectada
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error de BD al desactivar la categoría: " . $e->getMessage());
        return false;
    }
}

function restaurarCategoria($id_categoria) {
    $pdo = getConnection();
    $sql = "UPDATE categorias SET activo = 1 WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ (int)$id_categoria ]);
        // Verificar si la fila fue afectada
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error de BD al restaurar la categoría: " . $e->getMessage());
        return false;
    }
}

function contarTotalCategorias($filtro_estado) {
    $pdo = getConnection();
    $sql = "SELECT COUNT(id) FROM categorias WHERE 1=1"; 
    $params = [];
    
    if ($filtro_estado !== "") {
        $sql .= " AND activo = :activo";
        $params[':activo'] = (int)$filtro_estado;
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn(); 
    } catch (PDOException $e) {
        error_log("Error de BD al contar categorías: " . $e->getMessage());
        return 0;
    }
}



function contarProductosPorCategoria($id_categoria) {
    $pdo = getConnection();
    $sql = "SELECT COUNT(id) FROM articulos WHERE id_categoria = ? AND activo = 1";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_categoria]);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error de BD al contar productos por categoría: " . $e->getMessage());
        return 0; 
    }
}


function obtenerArticulosCarrito($carrito) {
    if (empty($carrito)) {
        return [];
    }

    $pdo = getConnection();
    
    //Extraer solo los IDs de los productos del carrito
    $ids_articulos = array_keys($carrito);
    
    $placeholders = str_repeat('?,', count($ids_articulos) - 1) . '?';

    $sql = "SELECT 
                a.id, 
                a.nombre, 
                a.precio, 
                a.imagen, 
                C.nombre AS nombre_categoria 
            FROM articulos a
            LEFT JOIN categorias C ON a.id_categoria = C.id
            WHERE a.id IN ($placeholders)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids_articulos);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $articulos_finales = [];
        foreach ($resultados as $articulo) {
            $articulos_finales[$articulo['id']] = $articulo;
        }

        return $articulos_finales;

    } catch (PDOException $e) {
        error_log("Error de BD al obtener artículos del carrito: " . $e->getMessage());
        return [];
    }
}

function buscarDatosUsuario($id_usuario) {
    $pdo = getConnection();
    $sql = "SELECT 
                nombre, 
                apellido, 
                direccion, 
                telefono, 
                email 
            FROM usuarios 
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function guardarOrdenLocal($id_usuario, $datos_envio, $carrito, $subtotal, $total_final, $metodo_pago) {
    $pdo = getConnection();
    $pdo->beginTransaction(); 
    $costo_envio = 0.00; 

    try {
        $articulos_bd = obtenerArticulosCarrito($carrito);
        $subtotal_calculado = 0;
        foreach ($carrito as $id_articulo => $cantidad) {
            if (!isset($articulos_bd[$id_articulo])) {
                throw new Exception("Artículo {$id_articulo} no existe o no tiene datos de BD.");
            }
            $subtotal_calculado += $articulos_bd[$id_articulo]['precio'] * $cantidad;
        }

        // CONTROL DE DIFERENCIAS
        if (abs($subtotal - $subtotal_calculado) > 0.01 || abs($total_final - $subtotal_calculado) > 0.01) {
             throw new Exception("Discrepancia en el total del pedido."); 
        }

        $sql_pedido = "INSERT INTO pedidos (
            id_usuario, fecha_pedido, total, subtotal, costo_envio, metodo_pago,
            nombre_cliente, direccion_envio, telefono_contacto
        ) VALUES (
            :id_u, NOW(), :total, :subtotal, :envio, :metodo,
            :nombre_c, :direccion, :telefono
        )";
        $stmt_pedido = $pdo->prepare($sql_pedido);
        $stmt_pedido->execute([
            ':id_u' => $id_usuario,
            ':total' => $total_final,
            ':subtotal' => $subtotal_calculado,
            ':envio' => $costo_envio,
            ':metodo' => $metodo_pago, 
            ':nombre_c' => $datos_envio['nombre'] . ' ' . $datos_envio['apellido'],
            ':direccion' => $datos_envio['direccion'],
            ':telefono' => $datos_envio['telefono']
        ]);
        
        $id_pedido = $pdo->lastInsertId();
        
       
        $sql_detalle = "INSERT INTO detalles_pedido (id_pedido, id_articulo, cantidad, precio_unitario) 
                        VALUES (:idp, :ida, :cant, :precio)";
        $stmt_detalle = $pdo->prepare($sql_detalle);

        $sql_stock = "UPDATE articulos SET stock = stock - :cantidad WHERE id = :ida AND stock >= :cantidad";
        $stmt_stock = $pdo->prepare($sql_stock);

        foreach ($carrito as $id_articulo => $cantidad) {
            $articulo_data = $articulos_bd[$id_articulo];
            
            // DESCUENTO STOCK
            $stmt_stock->execute([
                ':cantidad' => $cantidad, 
                ':ida' => $id_articulo
            ]);
            
            if ($stmt_stock->rowCount() === 0) {
                throw new Exception("Stock insuficiente para el artículo ID: {$id_articulo}.");
            }

            // EJECUTO LA QUERY DETALLE
            $stmt_detalle->execute([
                ':idp' => $id_pedido,
                ':ida' => $id_articulo,
                ':cant' => $cantidad,
                ':precio' => $articulo_data['precio']
            ]);
        }
        
        $pdo->commit();
        return (int)$id_pedido;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error de Venta Local (ID Usuario: {$id_usuario}): " . $e->getMessage());
        return false;
    }
}


//FUNCION MERCADOPAGO

function guardarOrdenDeVentaConfirmada($id_usuario, $datos_envio, $total, $items, $mp_preference_id) {
    $pdo = getConnection();
    $pdo->beginTransaction();
    
    try {
        $subtotal_calculado = 0;
        foreach ($items as $item) {
            $subtotal_calculado += (float)$item['unit_price'] * (int)$item['quantity'];
        }

        $sql_pedido = "INSERT INTO pedidos 
                            (id_usuario, fecha_pedido, total, subtotal, metodo_pago, mp_preference_id, 
                            nombre_cliente, direccion_envio, telefono_contacto) 
                        VALUES 
                            (?, NOW(), ?, ?, ?, ?, ?, ?, ?)"; 
        
        $stmt_pedido = $pdo->prepare($sql_pedido);
        
        $stmt_pedido->execute([
            $id_usuario,
            $total, 
            $subtotal_calculado, 
            'MercadoPago', 
            $mp_preference_id, 
            $datos_envio['nombre'] . ' ' . $datos_envio['apellido'], 
            $datos_envio['direccion'],
            $datos_envio['telefono'],
        ]);
        
        $id_pedido = $pdo->lastInsertId();
        
    
        $sql_descontar_stock = "UPDATE articulos SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $sql_detalle = "INSERT INTO detalles_pedido 
                            (id_pedido, id_articulo, cantidad, precio_unitario) 
                        VALUES 
                            (?, ?, ?, ?)";
        
        $stmt_descontar = $pdo->prepare($sql_descontar_stock);
        $stmt_detalle = $pdo->prepare($sql_detalle);
        
        foreach ($items as $item) {
            $item_id = (int)$item['id'];
            $cantidad = (int)$item['quantity'];
            $precio = (float)$item['unit_price'];
            $stmt_descontar->execute([$cantidad, $item_id, $cantidad]);
            
            if ($stmt_descontar->rowCount() === 0) {
                // VEMOS SI SE ACTUALIZA UNA FILA SINO NO HAY STOCK
                throw new Exception("Stock insuficiente para el artículo ID: {$item_id}");
            }
            $stmt_detalle->execute([
                $id_pedido,
                $item_id,
                $cantidad,
                $precio,
            ]);
        }
        
        //FINALIZAR TRANSACCION
        $pdo->commit();
        
        return $id_pedido;

    } catch (Exception $e) {
        $pdo->rollBack(); //ROLLBACK SI FALLO LA OPERACION
        error_log("Error de BD al guardar pedido (MP Webhook): " . $e->getMessage());
        return false;
    }
}


function obtenerTodosLosPedidos($fecha_inicio = null, $fecha_fin = null) {
    $pdo = getConnection(); 
    
    $sql = "SELECT 
                p.id AS pedido_id,
                p.fecha_pedido,
                p.total,
                p.nombre_cliente,
                p.direccion_envio,
                p.telefono_contacto,
                u.email AS email_cliente
            FROM pedidos p
            JOIN usuarios u ON p.id_usuario = u.id";
    $params = []; 
    $clausulas_where = [];

    if (!empty($fecha_inicio)) {
        
        $clausulas_where[] = "DATE(p.fecha_pedido) >= :fecha_inicio";
        $params[':fecha_inicio'] = $fecha_inicio;
    }

    if (!empty($fecha_fin)) {
        $clausulas_where[] = "DATE(p.fecha_pedido) <= :fecha_fin";
        $params[':fecha_fin'] = $fecha_fin;
    }

    if (!empty($clausulas_where)) {
        $sql .= " WHERE " . implode(" AND ", $clausulas_where);
    }

    $sql .= " ORDER BY p.fecha_pedido DESC";

    try {
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute($params); 
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    } catch (PDOException $e) {
        
        error_log("Error en obtenerTodosLosPedidos (PDO): " . $e->getMessage());
        return []; // Retorna un array vacío en caso de error
    }
}


function obtenerDetallePedido($id_pedido) {
    $pdo = getConnection();

    
    $sql_pedido = "SELECT id, id_usuario, fecha_pedido, total, subtotal, costo_envio, 
                        nombre_cliente, direccion_envio, telefono_contacto, mp_preference_id, 
                        metodo_pago 
                    FROM pedidos 
                    WHERE id = :id";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->bindParam(':id', $id_pedido, PDO::PARAM_INT);
    $stmt_pedido->execute();
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        return false;
    }

    
    $sql_detalles = "SELECT 
                        dp.cantidad,
                        dp.precio_unitario,
                        a.nombre, 
                        a.imagen
                    FROM detalles_pedido dp
                    JOIN articulos a ON dp.id_articulo = a.id
                    WHERE dp.id_pedido = :id_pedido";
    
    $stmt_detalles = $pdo->prepare($sql_detalles);
    $stmt_detalles->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
    $stmt_detalles->execute();
    $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

    $pedido['detalles'] = $detalles;
    return $pedido;
}

function contarTotalArticulos($filtro_nombre,$filtro_marca , $filtro_estado) {
    $pdo = getConnection();
    $sql = "SELECT COUNT(a.id) FROM articulos a 
           LEFT JOIN categorias m ON a.id_categoria = m.id
            WHERE 1=1"; 

    $params = [];
    
    
    if (!empty($filtro_nombre)) {
        $sql .= " AND a.nombre LIKE :nombre";
        $params[':nombre'] = '%' . $filtro_nombre . '%';
    }

    if ($filtro_marca > 0) {
        $sql .= " AND a.id_categoria = :marca";
        $params[':marca'] = $filtro_marca;
    }
    //FILTRO ESTADO
    if ($filtro_estado !== "") {
        $sql .= " AND a.activo = :activo";
        $params[':activo'] = $filtro_estado;
    }
    

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        
        return (int)$stmt->fetchColumn(); 

    } catch (PDOException $e) {
        error_log("Error de BD al contar artículos: " . $e->getMessage());
        return 0;
    }
}

function buscarUsuarioPorEmail($email) {
    $pdo = getConnection();
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        error_log("Error de BD al buscar email: " . $e->getMessage());
        return false;
    }
}

function registrarUsuario($email, $password_hash, $rol_id) {
    $pdo = getConnection();
    $sql = "INSERT INTO usuarios (email, password_hash, id_rol) VALUES (?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $password_hash, (int)$rol_id]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error de BD al registrar usuario: " . $e->getMessage());
        return false;
    }
}

function actualizarPerfilUsuario($id_usuario, $datos_perfil) {
    $pdo = getConnection();
    
    $campos_permitidos = ['nombre', 'apellido', 'direccion', 'telefono'];
    $updates = [];
    $params = [];
    
    foreach ($campos_permitidos as $campo) {
        if (isset($datos_perfil[$campo])) { 
            $updates[] = "$campo = ?"; 
            $params[] = $datos_perfil[$campo];
        }
    }

    if (empty($updates)) {
        return true; 
    }

    $sql = "UPDATE usuarios SET " . implode(', ', $updates) . " WHERE id = ?";
    $params[] = (int)$id_usuario;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return true; 

    } catch (PDOException $e) {
        error_log("Error de BD al actualizar perfil: " . $e->getMessage());
        return false;
    }
}

function obtenerPedidosPorUsuario($id_usuario) {
    $pdo = getConnection();
    
    
    $sql = "SELECT p.id, p.fecha_pedido, p.total 
            FROM pedidos p
            WHERE p.id_usuario = ?
            ORDER BY p.fecha_pedido DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([ (int)$id_usuario ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error de BD al obtener pedidos: " . $e->getMessage());
        return [];
    }
}

function obtenerDetalleDePedido($id_pedido, $id_usuario = null) {
    $pdo = getConnection();
    $params = [(int)$id_pedido];
    
    $sql = "SELECT p.* FROM pedidos p
            WHERE p.id = ?";

    if ($id_usuario !== null) {
        $sql .= " AND p.id_usuario = ?";
        $params[] = (int)$id_usuario;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            return false;
        }

        $sql_detalles = "SELECT dp.cantidad, dp.precio_unitario, a.nombre, a.imagen 
                         FROM detalles_pedido dp
                         LEFT JOIN articulos a ON dp.id_articulo = a.id
                         WHERE dp.id_pedido = ?";
                         
        $stmt_detalles = $pdo->prepare($sql_detalles);
        $stmt_detalles->execute([(int)$id_pedido]);
        $pedido['detalles'] = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

        return $pedido;
        
    } catch (PDOException $e) {
        error_log("Error de BD al obtener detalle de pedido: " . $e->getMessage());
        return false;
    }
}

function actualizarPasswordUsuario($id_usuario, $nuevo_hash) {
    $pdo = getConnection();
    $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nuevo_hash, $id_usuario]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Error de BD al actualizar password: " . $e->getMessage());
        return false;
    }
}

function obtenerHashPasswordPorId($id_usuario) {
    $pdo = getConnection();
    $sql = "SELECT password_hash FROM usuarios WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ? $resultado['password_hash'] : false;
        
    } catch (PDOException $e) {
        error_log("Error de BD al obtener hash: " . $e->getMessage());
        return false;
    }
}


function obtenerUsuarios($filtro_email = "", $filtro_rol = "", $filtro_estado = "", $limite, $offset) {
    $pdo = getConnection();
    
    $sql = "SELECT u.id, u.email, u.nombre, u.apellido, u.activo, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id
            WHERE 1=1";
    
    $params = []; 

    if (!empty($filtro_email)) {
        $sql .= " AND u.email LIKE :email";
        $params[':email'] = '%' . $filtro_email . '%';
    }

    if ($filtro_rol !== "") {
        $sql .= " AND u.id_rol = :id_rol";
        $params[':id_rol'] = (int)$filtro_rol;
    }

    if ($filtro_estado !== "") {
        $sql .= " AND u.activo = :activo";
        $params[':activo'] = (int)$filtro_estado;
    }

    $sql .= " ORDER BY u.id ASC LIMIT :limite OFFSET :offset";
    
    try {
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error de BD al obtener usuarios: " . $e->getMessage());
        return [];
    }
}

function contarTotalUsuarios($filtro_email = "", $filtro_rol = "", $filtro_estado = "") {
    $pdo = getConnection();
    
    $sql = "SELECT COUNT(u.id) 
            FROM usuarios u
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtro_email)) {
        $sql .= " AND u.email LIKE :email";
        $params[':email'] = '%' . $filtro_email . '%';
    }
    
    
    if ($filtro_rol !== "") {
        $sql .= " AND u.id_rol = :id_rol";
        $params[':id_rol'] = (int)$filtro_rol;
    }
    
    
    if ($filtro_estado !== "") {
        $sql .= " AND u.activo = :activo";
        $params[':activo'] = (int)$filtro_estado;
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error de BD al contar usuarios: " . $e->getMessage());
        return 0;
    }
}

function obtenerRoles() {
        $pdo = getConnection();
        $sql = "SELECT id, nombre FROM roles ORDER BY nombre ASC";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error de BD al obtener roles: " . $e->getMessage());
            return [];
        }
    }

    function guardarUsuario($datos_usuario) {
    $pdo = getConnection();
    $modo_edicion = !empty($datos_usuario['id']);
    
    // HASH
    $password_hash = null;
    if (!empty($datos_usuario['password'])) {
        $password_hash = password_hash($datos_usuario['password'], PASSWORD_BCRYPT);
    }

    try {
        if ($modo_edicion) {
            $sql = "UPDATE usuarios SET 
                        email = :email, 
                        id_rol = :id_rol, 
                        nombre = :nombre, 
                        apellido = :apellido, 
                        direccion = :direccion, 
                        telefono = :telefono, 
                        activo = :activo";
            
            $params = [
                ':email' => $datos_usuario['email'],
                ':id_rol' => $datos_usuario['id_rol'],
                ':nombre' => $datos_usuario['nombre'],
                ':apellido' => $datos_usuario['apellido'],
                ':direccion' => $datos_usuario['direccion'],
                ':telefono' => $datos_usuario['telefono'],
                ':activo' => $datos_usuario['activo'],
                ':id' => $datos_usuario['id']
            ];

            if ($password_hash) {
                $sql .= ", password_hash = :password_hash";
                $params[':password_hash'] = $password_hash;
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return true; 

        } else {
            
            $sql = "INSERT INTO usuarios 
                        (email, id_rol, nombre, apellido, direccion, telefono, activo, password_hash) 
                    VALUES 
                        (:email, :id_rol, :nombre, :apellido, :direccion, :telefono, :activo, :password_hash)";
            
            $params = [
                ':email' => $datos_usuario['email'],
                ':id_rol' => $datos_usuario['id_rol'],
                ':nombre' => $datos_usuario['nombre'],
                ':apellido' => $datos_usuario['apellido'],
                ':direccion' => $datos_usuario['direccion'],
                ':telefono' => $datos_usuario['telefono'],
                ':activo' => $datos_usuario['activo'],
                ':password_hash' => $password_hash 
            ];
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $pdo->lastInsertId(); // Devolvemos el ID del nuevo usuario
        }

    } catch (PDOException $e) {
        error_log("Error de BD en guardarUsuario: " . $e->getMessage());
        return false; 
    }
}

function eliminarUsuario($id) {
    $pdo = getConnection(); 
    try {
        
        $sql = "UPDATE usuarios SET activo = 0 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute(); 
        
    } catch (PDOException $e) {
        
        error_log("Error de BD al desactivar usuario ID {$id}: " . $e->getMessage());
        return false; 
    }
}

function buscarUsuarioPorIdAdmin($id) {
    $pdo = getConnection();
    $sql = "SELECT id, email, id_rol, nombre, apellido, direccion, telefono, activo 
            FROM usuarios WHERE id = ?";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        error_log("Error de BD al buscar usuario por ID: " . $e->getMessage());
        return false;
    }
}

function restaurarUsuario($id_usuario) {
    $pdo = getConnection();
    $sql = "UPDATE usuarios SET activo = 1 WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_usuario]);
        return true; 
    } catch (PDOException $e) {
        error_log("Error de BD al restaurar usuario ID {$id_usuario}: " . $e->getMessage());
        return false;
    }
}
