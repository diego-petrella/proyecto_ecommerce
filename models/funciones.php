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

 function obtenerArticulos($nombre = "", $filtro_marca, $limite, $offset) {
     $pdo = getConnection();
    
     $sql = " SELECT A.*, M.nombre AS nombre_categoria 
              FROM articulos A 
              INNER JOIN categorias M ON (A.id_categoria = M.id) 
              WHERE 1=1 ";
    
     $params = [];
    
      //FILTRO NOMBRE
     if (!empty($nombre)) {
         $sql .= " AND A.nombre LIKE :nombre";
         $params[':nombre'] = '%' . $nombre . '%';
     }

      //FILTRO CATEGORIA
     if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
         $sql .= " AND A.id_categoria = :marca";
         $params[':marca'] = $filtro_marca;
     }
  
     
      //LIMIT y OFFSET
     $sql .= " ORDER BY A.id DESC LIMIT :limite OFFSET :offset";
    
     $stmt = $pdo->prepare($sql);
    
      //AGREGO PARAMETROS SIN NO ESTAN VACIOS
     if (!empty($nombre)) {
         $stmt->bindValue(':nombre', $params[':nombre'], PDO::PARAM_STR);
     }
     if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
         $stmt->bindValue(':marca', $params[':marca'], PDO::PARAM_INT);
     }

      //AGREGO LOS PARAMETROS DE LA PAGINACION
     $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
     $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
     try {
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     } catch (PDOException $e) {
         error_log("Error de BD al obtener artículos con paginación: " . $e->getMessage());
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
            "id_categoria = :id_categoria"
        ];
        $params = [
            ':nombre' => $array["nombre"],
            ':descripcion_corta' => $array["descripcion_corta"],
            ':precio' => $array["precio"],
            ':stock' => $array["stock"],
            ':id_categoria' => $array["id_categoria"],
            ':id' => $array["id"]
        ];

        if ($hayNuevaImagen) {
            $updates[] = "imagen = :imagen";
            $params[':imagen'] = $array["imagen"];
        }

        $sql .= implode(', ', $updates);
        $sql .= " WHERE id = :id";
        $params[':id'] = $array["id"];
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $array["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(':descripcion_corta', $array["descripcion_corta"], PDO::PARAM_STR);
        $stmt->bindParam(':precio', $array["precio"], PDO::PARAM_INT);
        $stmt->bindParam(':stock', $array["stock"], PDO::PARAM_INT);
        $stmt->bindParam(':id_categoria', $array["id_categoria"], PDO::PARAM_INT);
        $stmt->bindParam(':id', $array["id"], PDO::PARAM_INT);

        if ($hayNuevaImagen) {
            $stmt->bindParam(':imagen', $array["imagen"], PDO::PARAM_STR);
        }
        $stmt->execute($params);
  } else {
    $sql = "INSERT INTO articulos (nombre, descripcion_corta, precio, stock, id_categoria, imagen) VALUES (:nombre, :descripcion_corta, :precio, :stock, :id_categoria, :imagen) ";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':nombre', $array["nombre"], PDO::PARAM_STR);
  $stmt->bindParam(':descripcion_corta', $array["descripcion_corta"], PDO::PARAM_STR);
  $stmt->bindParam(':precio', $array["precio"], PDO::PARAM_INT);
  $stmt->bindParam(':stock', $array["stock"], PDO::PARAM_INT);
  $stmt->bindParam(':id_categoria', $array["id_categoria"], PDO::PARAM_INT);
  $stmt->bindParam(':imagen', $array["imagen"], PDO::PARAM_STR);
  $stmt->execute();
  }
  
  
}

function eliminarArticulo($id) {
    $pdo = getConnection();
    $sql = "DELETE FROM articulos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    //INTENTA ELIMINAR
    try {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        //CONT ROW VERIFICA SI SE MODIFICO 1 REGISTRO
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    } catch (PDOException $e) {
        error_log("Error de BD (DELETE artículo ID $id): " . $e->getMessage());
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

function obtenerCategorias() {
    $pdo = getConnection(); 
    
    $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

function verificarUsuario($email, $password) {
    $pdo = getConnection();
    $sql = "SELECT id, password, id_rol, email 
            FROM usuarios 
            WHERE email = :email ";
    $param = [
      ":email" => $email,
    ];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($param);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        return false;
    }

    
    if ($password === $usuario['password']) {
        return $usuario;
    } else {
        return false;
    }
}

function obtenerProductosPorListaDeIds($product_ids) {
   $pdo = getConnection();

    if (empty($product_ids)) {
        return [];
    }
    
    // Convertimos el array de IDs en una cadena para la consulta SQL: '?, ?, ?'
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

function eliminarCategoria($id) {
    $pdo = getConnection();
    // DUDA.... QUE PASA SI ELIMINO UN CATEGORIA QUE TIENE PRODUCTOS ASOCIADOS?
    $sql = "DELETE FROM categorias WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function obtenerArticulosCarrito($carrito) {
    if (empty($carrito)) {
        return [];
    }

    $pdo = getConnection();
    
    //Extraer solo los IDs de los productos del carrito
    $ids_articulos = array_keys($carrito);
    
    //CREA UNA CADENA DE MARCADORES PARA LA CONSULTA SQL
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


function guardarOrdenDeVenta($id_usuario, $datos_envio, $carrito, $subtotal, $total_final) {
    $pdo = getConnection();
    $pdo->beginTransaction(); 
    $costo_envio = 0.00; 

    try {
        //Recalcular Subtotal
        $articulos_bd = obtenerArticulosCarrito($carrito);
        $subtotal_calculado = 0;
        foreach ($carrito as $id_articulo => $cantidad) {
            if (!isset($articulos_bd[$id_articulo])) {
                throw new Exception("Artículo {$id_articulo} no existe o no tiene datos de BD.");
            }
            $subtotal_calculado += $articulos_bd[$id_articulo]['precio'] * $cantidad;
        }

        //CONTROL DE DIFERENCIAS..
        if (abs($subtotal - $subtotal_calculado) > 0.01 || abs($total_final - $subtotal_calculado) > 0.01) {
             throw new Exception("Discrepancia en el total del pedido."); 
        }

        //GUARDAR DATOS DEL PEDIDO
        $sql_pedido = "INSERT INTO pedidos (
            id_usuario, fecha_pedido, total, subtotal, costo_envio,
            nombre_cliente, direccion_envio, telefono_contacto
        ) VALUES (
            :id_u, NOW(), :total, :subtotal, :envio,
            :nombre_c, :direccion, :telefono
        )";
        $stmt_pedido = $pdo->prepare($sql_pedido);
        $stmt_pedido->execute([
            ':id_u' => $id_usuario,
            ':total' => $total_final,
            ':subtotal' => $subtotal_calculado,
            ':envio' => $costo_envio,
            ':nombre_c' => $datos_envio['nombre'] . ' ' . $datos_envio['apellido'],
            ':direccion' => $datos_envio['direccion'],
            ':telefono' => $datos_envio['telefono']
        ]);
        
        $id_pedido = $pdo->lastInsertId();
        
        //GUARDO EN DETALLES LOS PRODUCTOS Y DESCUENTO EL STOCK DE ARTICULOS
        $sql_detalle = "INSERT INTO detalles_pedido (id_pedido, id_articulo, cantidad, precio_unitario) 
                        VALUES (:idp, :ida, :cant, :precio)";
        $stmt_detalle = $pdo->prepare($sql_detalle);

        $sql_stock = "UPDATE articulos SET stock = stock - :cantidad WHERE id = :ida AND stock >= :cantidad";
        $stmt_stock = $pdo->prepare($sql_stock);

        foreach ($carrito as $id_articulo => $cantidad) {
            $articulo_data = $articulos_bd[$id_articulo];
            
            //DESCUENTO STOCK
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
        error_log("Error de Venta (ID Usuario: {$id_usuario}): " . $e->getMessage());
        return false;
    }
}

function obtenerTodosLosPedidos() {
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
            JOIN usuarios u ON p.id_usuario = u.id
            ORDER BY p.fecha_pedido DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function obtenerDetallePedido($id_pedido) {
    $pdo = getConnection();

    $sql_pedido = "SELECT * FROM pedidos WHERE id = :id";
    $stmt_pedido = $pdo->prepare($sql_pedido);
    $stmt_pedido->bindParam(':id', $id_pedido, PDO::PARAM_INT);
    $stmt_pedido->execute();
    $pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        return false;
    }

    // Consulta de los productos comprados (detalles)
    $sql_detalles = "SELECT 
                        dp.cantidad,
                        dp.precio_unitario,
                        a.nombre AS nombre_articulo,
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

function contarTotalArticulos($filtro_nombre, $filtro_marca) {
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

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        
        return (int)$stmt->fetchColumn(); 

    } catch (PDOException $e) {
        error_log("Error de BD al contar artículos: " . $e->getMessage());
        return 0;
    }
}
?>