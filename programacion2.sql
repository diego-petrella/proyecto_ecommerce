-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-10-2025 a las 00:30:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `programacion2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion_corta` text NOT NULL,
  `precio` decimal(10,0) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id`, `nombre`, `descripcion_corta`, `precio`, `stock`, `id_categoria`, `imagen`) VALUES
(23, 'Cortadora de Cesped Daewoo DAC1200', 'La cortadora de césped eléctrica Daewoo DAC1200 es ideal para jardines pequeños y medianos. Con 1200 watts de potencia y un funcionamiento silencioso, te permite trabajar de manera eficiente y sin esfuerzo.', 226600, 2, 4, 'img_68f6a8423f1457.49291087.png'),
(24, 'Notebook Exo Intel N4020 14\" 4GB SSD 128GB', 'La Notebook Exo Intel N4020 es una alternativa económica y funcional para usuarios que buscan un equipo portátil para tareas básicas. Con una pantalla de 14 pulgadas y un diseño compacto, es ideal para el uso diario en la oficina, el hogar o para estudiar. Su procesador Intel Celeron N4020 permite ejecutar aplicaciones ligeras y navegar por internet con facilidad.', 339999, 2, 2, 'img_68f6aa7da022e0.55688781.webp'),
(25, 'Samsung Galaxy A06 4/128 GB', 'Equipado con 4GB de RAM y 128GB de almacenamiento, este celular te ofrece un rendimiento fluido y suficiente espacio para tus aplicaciones, fotos y videos. Su potente batería asegura que permanezcas conectado durante más tiempo. ¿Te gustaría un dispositivo que no te deje a medias en tus tareas diarias?', 219999, 3, 1, 'img_68f6a463b2a3c9.20928146.jpg'),
(26, 'Joystick Dualsense PS5 Blanco', 'El DualSense es el controlador inalámbrico diseñado específicamente para la consola PlayStation 5 de Sony. El modelo White (CFI-ZCT1W) es una variante de color blanco, que complementa la estética de la consola PS5. El DualSense presenta un diseño ergonómico y futurista, con curvas suaves y una disposición de botones intuitiva. Una de sus características más destacadas es la inclusión de tecnología háptica, que permite una retroalimentación táctil más precisa y envolvente para una experiencia de juego inmersiva.', 169999, 2, 2, 'img_68f6aadf70b869.93868306.webp'),
(37, 'Tablet Galaxy Tab S10 FE', '¿Querés una tablet potente, elegante y lista para todo? La Samsung Galaxy Tab S10 FE Gray tiene una pantalla de 10.9\" a 90 Hz, ideal para ver contenido, trabajar o dibujar con gran fluidez y definición. Su diseño fino y moderno es cómodo para llevar a todas partes.', 1599999, 2, 2, 'img_68f6ab41a05727.54874535.webp'),
(41, 'Samsung Galaxy A54 8/256 GB', '¿Necesitás más espacio y rendimiento? Con 8GB de RAM y 256GB de almacenamiento, tenés lugar de sobra para tus fotos, aplicaciones y archivos sin preocuparte por la memoria. Además, su batería de larga duración te acompaña todo el día sin interrupciones.', 949000, 2, 1, 'img_68f6a4f57122e3.91654537.png'),
(46, 'Samsung Galaxy A16 4/128 GB', '¿Necesitás potencia y rendimiento sin interrupciones? Con un procesador de alto rendimiento, el Galaxy A16 te permite jugar, navegar y trabajar sin esfuerzo. Además, su diseño ultrafino de 7.9 mm lo hace cómodo de llevar a cualquier lugar.', 369999, 3, 1, 'img_68f6a594ba8712.07206568.jpg'),
(47, 'Smart TV TCL 32\" FHD Google TV', 'El Smart TV TCL 32\" QLED Full HD te ofrece una calidad de imagen superior gracias a su panel QLED con HDR y Micro Dimming, ideal para ver tus series y películas con colores vivos y gran contraste, incluso en espacios pequeños. Además, su tamaño lo hace perfecto para dormitorios, cocinas o espacios reducidos.', 299999, 1, 3, 'img_68f6abb01d8909.32881528.webp'),
(50, 'Samsung Galaxy A36 5G 8/256 GB', 'Disfrutá de la velocidad 5G con el Samsung Galaxy A36, un smartphone con 8GB de RAM y 256GB de almacenamiento, ideal para quienes necesitan potencia y espacio de sobra. Su pantalla Super AMOLED de 6.5\" te brinda colores vibrantes y una experiencia visual increíble.', 999999, 2, 1, 'img_68f6a63f2c3d18.47263266.webp'),
(52, 'Samsung Galaxy S25FE 8/256 GB', '¿Por qué no elegir un celular que combina potencia, diseño y cámaras de calidad? El Galaxy S25 FE es la opción perfecta para quienes buscan más en cada detalle. ¡Llevátelo hoy y disfrutá de la experiencia Samsung!', 1799999, 1, 1, 'img_68f6a6b03c48b6.39056209.webp'),
(53, 'Silla Gamer Panacom Rojo', 'La Panacom GMC00815 en rojo tiene respaldo reclinable hasta 135°, cojines ergonómicos y soporte para hasta 150 kg, perfecta para acompañarte por horas sin sacrificar postura.', 259999, 2, 2, 'img_68f6ac05165481.60451213.webp'),
(54, 'Tablet Samsung Galaxy Tab A7 Lite', 'La Galaxy Tab A7 Lite es compacta y cómoda, con solo 366g de peso, ideal para usar con una mano y disfrutar contenido donde estés gracias a su pantalla de 8.7”.\r\n\r\nSu procesador Octa-Core y 3GB de RAM garantizan un rendimiento ágil para apps, juegos y streaming. Además, tenés 32GB de almacenamiento interno expandible hasta 1TB con MicroSD, para que no te falte espacio para lo que más usás.', 349999, 2, 2, 'img_68f161637c6a96.46525474.png'),
(55, 'Heladera Drean HDR300 No-Frost', 'Disfrutá de tus alimentos frescos y almacenalos de manera práctica y cómoda en la heladera Drean, la protagonista de la cocina.\r\n\r\nComodidad para tu casa\r\nSu sistema no frost evita la generación de escarcha y te va a permitir conservar el sabor y las propiedades nutritivas de los productos.', 890499, 3, 5, 'img_68f40ad8769dc1.56819595.png'),
(56, 'BORDEADORA ELECTRICA 450W – DAT450', 'Descubre cómo la DAT450 facilita el mantenimiento de tus áreas verdes con comodidad y precisión.\r\n\r\nPotencia: 450 W\r\nVelocidad sin carga: 10000 rpm\r\nAncho de corte: 30 cm\r\nDiám. de tanza de nylon: 1.4 mm\r\nLong. de tanza de nylon: 6 m\r\nSistema de corte: 2 líneas', 86000, 2, 4, 'img_68f6a8c93f86d7.84208233.png'),
(57, 'DESMALEZADORA A GASOLINA 52cc 4 en 1 – DAMT520', 'Si buscas una herramienta versátil y poderosa para el cuidado de tu jardín, la Desmalezadora Multifunción 4 en 1 DAMT520 es la elección perfecta. Con su cilindrada de 52 CC y una potencia de 2 hp, esta herramienta puede realizar diversas tareas de manera eficiente y con un rendimiento excepcional, sus cabezales intercambiables permiten tener una bordeadora, desmalezadora, corta cerco y podadora de altura todo en una misma herramienta.', 365000, 1, 4, 'img_68f6a92c9a7441.73856450.png'),
(58, 'HIDROLAVADORA DE ALTA PRESIÓN ELECTRICA 1400W – DAX1400', 'Hidrolavadora de Alta Presión 1400W\r\n\r\nMantén tus espacios siempre limpios y libres de suciedad con esta potente hidrolavadora de 1400 W, diseñada para ofrecer un rendimiento confiable en tareas de limpieza del hogar, jardín o vehículo.', 95369, 2, 4, 'img_68f6a99f4b0d46.93904580.png'),
(59, 'BORDEADORA ELECTRICA 300W – DAT300', 'Mantener tu jardín en perfecto estado nunca fue tan sencillo. Si necesitas trabajar en espacios reducidos como pequeños jardines o canteros, nuestra DAT300 es la herramienta ideal gracias a su diseño ergonómico y dimensiones perfectas. Con un motor robusto de 300 watts y un carretel de dos líneas que carga 6 metros de tanza de 1,2 mm de diámetro, ofrece una capacidad de corte de 22 cm. ¡En cuestión de minutos, tendrás a tu disposición el aliado perfecto para que tu jardín luzca espectacular!', 49999, 5, 4, 'img_68f6aa327c1d52.64757159.png'),
(60, 'Heladera Gafa 282L Blanca', 'La Heladera Gafa HGF358AFB es una opción práctica y confiable, con 282 litros de capacidad neta para que puedas guardar todo lo que necesitás, desde comidas caseras hasta bebidas y congelados. Su diseño con freezer superior y manija ergonómica facilita el uso diario, ideal para cualquier cocina.', 692999, 1, 5, 'img_68f6ad808abdc2.94105070.webp'),
(61, 'Lavarropas Automatico Midea 6 Kg 1000 rpm', 'El Lavarropas 6kg MF100W60/T-A1 de 1000 RPM en Titanio A+ es una excelente opción para hogares pequeños o de mediana capacidad. Con un diseño moderno y compacto, optimiza el espacio y ofrece gran eficiencia energética, lo que se traduce en ahorro de dinero a largo plazo.', 549999, 1, 5, 'img_68f6add2c0aac6.99596224.webp'),
(62, 'Lavarropas Automatico Drean 8 Kg 1400 rpm', 'El Lavarropas Drean 8kg LFDR0814SB0 es ideal para quienes buscan eficiencia y tecnología en su hogar. Con su motor Inverter, disfruta de un menor consumo de energía y una mayor durabilidad, al mismo tiempo que proporciona una capacidad de 8kg, perfecta para familias de tamaño medio. Además, con una velocidad de 1400 rpm, asegura un excelente centrifugado y ropa casi lista para secar.', 792999, 1, 5, 'img_68f6ae6ce6d030.52518076.webp'),
(63, 'Cocina Drean 56 cm Acero inoxidable', 'La Cocina Drean CD5603AI0 de 56cm combina funcionalidad y diseño en acero inoxidable. Es multigas, con encendido automático y luz en el horno, ideal para cocinar de forma práctica y segura todos los días.', 715999, 1, 5, 'img_68f6af13569ee9.68956195.webp'),
(64, 'Ventilador de Pie 18\" 3 en 1 W&B', 'Ventilador de Pie con Cabezal Oscilante\r\n\r\n• Pie redondo plástico\r\n• Barral regulable en altura hasta 1,65 m.\r\n• Parrillas plásticas espiraladas.\r\n• Cabezal reclinable.\r\n• 3 velocidades.\r\n• Palas de 3 aspas plásticas.\r\n• Color: negro.\r\n• Potencia: 75 Watts.\r\n\r\nGarantía 1 Año\r\nIndustria Argentina', 49999, 20, 6, 'img_68f6af902e5911.77068361.jpg'),
(65, 'Aire Acondicionado Surrey 3430W F/C On/Off', 'El Surrey Split 3430W Frío/Calor GFQ1201F es ideal para vos: enfría rápido en verano y te abriga en invierno. Además, memoriza tu configuración favorita con la función Mi Clima, para que cada vez que lo prendas, ya esté como te gusta.', 759000, 2, 6, 'img_68f6b0000fa929.24019244.webp'),
(66, 'Ventilador Turbo 20\" 90w Liliana', 'Tus ambientes sin ruidos molestos. Ideal para tener espacios refrigerados.\r\nVentilador reclinable con opción de fijación a pared.', 76999, 3, 6, 'img_68f6b0d37fe699.34696296.webp'),
(67, 'Ventilador de Pie 20\" 90w Liliana', 'El Ventilador de Pie Liliana VP20R de 20\" es la solución ideal para refrescar tus ambientes con potencia y comodidad. Gracias a su diámetro de 20 pulgadas, ofrece un gran caudal de aire, cubriendo espacios amplios como dormitorios, livings u oficinas.', 109999, 5, 6, 'img_68f6b13237bbb8.58370849.webp'),
(68, 'Aire Acondicionado Portatil Surrey 3500w F/C', 'El Aire Acondicionado Portátil Surrey 551PXQ12N81F te ofrece frío y calor con una potencia de 3500W (3010 frigorías), ideal para ambientes medianos. Su diseño compacto y moderno permite que lo ubiques donde lo necesites, sin instalaciones complicadas ni obras.', 699999, 1, 6, 'img_68f6b1857bfce5.76496458.webp'),
(69, 'Smart TV Motorla 50\" 4K Google TV', 'Descubre la Motorola 50 MT5000 UHD 4K, un televisor que transforma tu hogar en un centro de entretenimiento. Con su Control Dinámico de Retroiluminación y Mejora y Compensación de Movimiento (MEMC), disfrutarás de imágenes nítidas y colores vibrantes. ¿Te imaginas viendo tus deportes favoritos como si estuvieras en el estadio? Su Modo Deportes resalta cada detalle, haciéndote sentir parte de la acción.', 699999, 1, 3, 'img_68f6b1ee87daa2.30891657.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Celulares'),
(2, 'Tecnologia'),
(3, 'TV y Video'),
(4, 'Casa y Jardin'),
(5, 'Electrodomesticos'),
(6, 'Climatizacion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pedido`
--

CREATE TABLE `detalles_pedido` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `detalles_pedido`
--

INSERT INTO `detalles_pedido` (`id`, `id_pedido`, `id_articulo`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 23, 1, 200.00),
(2, 2, 68, 1, 699999.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `items`
--

INSERT INTO `items` (`id`, `id_articulo`, `cantidad`, `precio_unitario`) VALUES
(1, 14, 4, 200),
(2, 17, 2, 1000),
(5, 18, 1, 1300),
(6, 19, 3, 900);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `direccion_envio` varchar(255) NOT NULL,
  `telefono_contacto` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_usuario`, `fecha_pedido`, `total`, `subtotal`, `costo_envio`, `nombre_cliente`, `direccion_envio`, `telefono_contacto`) VALUES
(1, 2, '2025-10-19 10:32:53', 200.00, 200.00, 0.00, 'usuario prueba', 'Padre Doglia 255', '123456'),
(2, 2, '2025-10-21 20:39:16', 699999.00, 699999.00, 0.00, 'usuario prueba', 'Padre Doglia 255', '123456');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'admin'),
(2, 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `apellido` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `password`, `id_rol`) VALUES
(1, 'admin', '', '', 1234567, 'admin@tienda.com.ar', '123456', 1),
(2, 'usuario', 'prueba', 'Padre Doglia 255', 123456, 'usuario@gmail.com', '123456', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_articulo` (`id_articulo`);

--
-- Indices de la tabla `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD CONSTRAINT `detalles_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalles_pedido_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
