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

function obtenerArticulos($nombre = "", $filtro_marca) {
  $pdo = getConnection();
  //$sql = "SELECT * FROM articulos A WHERE 1=1 ";
  $sql = " SELECT A.*, M.nombre AS nombre_categoria  FROM articulos A ";
  $sql .=" INNER JOIN marcas M ON (A.id_categoria = M.id) WHERE 1=1 ";
  
  if (!empty($nombre)) {
        $sql .= " AND A.nombre LIKE '%" . $nombre . "%'";
    }

    if (!empty($filtro_marca) && is_numeric($filtro_marca)) {
        $sql .= " AND A.id_categoria = " . $filtro_marca;
    }
   
  $stmt = $pdo->query($sql);
  $articulos = [];
  while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $articulos[] = $fila;
  }
  return $articulos;
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
  $sql = "DELETE FROM articulos WHERE id = :id ";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->execute();
}

function buscarPorId($id) {
  $pdo = getConnection();
  $sql = "SELECT A.*, M.nombre AS nombre_categoria  FROM articulos A INNER JOIN marcas M on (A.id_categoria = M.id) WHERE A.id = ? ";
  
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(1, $id, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerTodasLasMarcas() {
    $pdo = getConnection(); 
    
    $sql = "SELECT id, nombre FROM marcas ORDER BY nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function filtrarArticulos (){
$pdo = getConnection();
$query= "SELECT M.nombre, ";
$query.=" SUM(I.precio_unitario * I.cantidad) AS subtotal ";
$query.=" FROM items I INNER JOIN articulos A ON (I.id_articulo = A.id) ";
$query.=" INNER JOIN marcas M ON (A.id_categoria = M.id) GROUP BY M.nombre";

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
            a.id, a.nombre, a.precio, a.id_categoria, a.stock,
            m.nombre AS nombre_categoria
        FROM 
            articulos a
        JOIN 
            marcas m ON a.id_categoria = m.id
        WHERE 
            a.id IN ({$placeholders})
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    
    // Devolvemos los resultados, indexados por el ID del producto para fácil acceso
    $productos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productos[$row['id']] = $row;
    }
    
    return $productos;
}

// Asegúrate de que esta función también existe, la usarás en el header
function contarProductosCarrito() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    return array_sum($_SESSION['carrito']); 
}
?>