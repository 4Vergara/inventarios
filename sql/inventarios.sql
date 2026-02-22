/*
 Navicat Premium Data Transfer

 Source Server         : Localhost
 Source Server Type    : MySQL
 Source Server Version : 100138
 Source Host           : localhost:3306
 Source Schema         : inventarios

 Target Server Type    : MySQL
 Target Server Version : 100138
 File Encoding         : 65001

 Date: 21/02/2026 21:41:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cierres_caja
-- ----------------------------
DROP TABLE IF EXISTS `cierres_caja`;
CREATE TABLE `cierres_caja`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_cierre` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'C??digo ??nico ej: CC-20260221-0001',
  `tipo_periodo` enum('dia','semana','mes','anio') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Tipo de per??odo del cierre',
  `fecha_inicio` datetime NOT NULL COMMENT 'Inicio del per??odo',
  `fecha_fin` datetime NOT NULL COMMENT 'Fin del per??odo',
  `total_ventas` int NOT NULL DEFAULT 0 COMMENT 'Cantidad de ventas en el per??odo',
  `monto_total_vendido` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `monto_subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `monto_impuestos` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `monto_descuentos` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_efectivo` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_tarjeta_credito` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_tarjeta_debito` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_transferencia` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_cheque` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `ventas_anuladas` int NOT NULL DEFAULT 0,
  `monto_anulado` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `efectivo_inicial` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Base de caja al inicio',
  `efectivo_esperado` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Efectivo que deber??a haber',
  `efectivo_contado` decimal(15, 2) NULL DEFAULT NULL COMMENT 'Efectivo contado f??sicamente',
  `diferencia_caja` decimal(15, 2) NULL DEFAULT NULL COMMENT 'Diferencia (contado - esperado)',
  `total_facturas` int NOT NULL DEFAULT 0,
  `observaciones` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `cerrado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Usuario que realiz?? el cierre',
  `creado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_codigo_cierre`(`codigo_cierre` ASC) USING BTREE,
  INDEX `idx_tipo_periodo`(`tipo_periodo` ASC) USING BTREE,
  INDEX `idx_fecha_rango`(`fecha_inicio` ASC, `fecha_fin` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'Hist??rico de cierres de caja' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cierres_caja
-- ----------------------------
INSERT INTO `cierres_caja` VALUES (1, 'CC-20260221-0001', 'dia', '2026-02-21 00:00:00', '2026-02-21 23:59:59', 1, 10710000.00, 9000000.00, 1710000.00, 0.00, 10710000.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 10000000.00, 20710000.00, 20710000.00, 0.00, 2, '', 'Carlos Vergara', 'Carlos Vergara', '2026-02-21 20:46:47', NULL, '2026-02-21 20:46:47');

-- ----------------------------
-- Table structure for cierres_caja_detalle
-- ----------------------------
DROP TABLE IF EXISTS `cierres_caja_detalle`;
CREATE TABLE `cierres_caja_detalle`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cierre` bigint UNSIGNED NOT NULL,
  `id_venta` bigint UNSIGNED NOT NULL,
  `folio_venta` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `total_venta` decimal(15, 2) NOT NULL,
  `total_pagado` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `metodo_pago_principal` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_cierre_detalle`(`id_cierre` ASC) USING BTREE,
  INDEX `fk_cierre_det_venta`(`id_venta` ASC) USING BTREE,
  CONSTRAINT `fk_cierre_det_cierre` FOREIGN KEY (`id_cierre`) REFERENCES `cierres_caja` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_cierre_det_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of cierres_caja_detalle
-- ----------------------------
INSERT INTO `cierres_caja_detalle` VALUES (1, 1, 4, 'VTA-20260221-0001', 10710000.00, 10710000.00, 'Efectivo');

-- ----------------------------
-- Table structure for clientes
-- ----------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la tabla',
  `nombre_completo` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre del cliente',
  `numero_documento` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Numero de documento del cliente',
  `correo_electronico` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Correo electronico del cliente',
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'Tabla para registrar los datos básicos de clientes para facturación' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of clientes
-- ----------------------------
INSERT INTO `clientes` VALUES (1, 'CONSUMIDOR FINAL', '999999999999', NULL, 'Sistema', '2026-02-04 14:19:59', NULL, '2026-02-04 14:20:09');
INSERT INTO `clientes` VALUES (2, 'Sara Beatriz', '123456789', 'Sara@correo.com', 'Carlos Vergara', '2026-02-09 20:13:35', NULL, NULL);
INSERT INTO `clientes` VALUES (3, 'Emmanuel Pallares', '1120555888', 'emmanuelp@correo.com', 'Carlos Vergara', '2026-02-09 20:43:45', 'Carlos Vergara', '2026-02-09 20:43:54');
INSERT INTO `clientes` VALUES (4, 'Valentina Vergara', '1099222222', NULL, 'Carlos Vergara', '2026-02-21 20:28:42', NULL, NULL);

-- ----------------------------
-- Table structure for configuraciones
-- ----------------------------
DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la tabla',
  `id_padre` int UNSIGNED NULL DEFAULT NULL COMMENT 'Id de la tabla configuraciones al que hace referencia',
  `nombre` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de la configuracion',
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Descripcion de la configuracion',
  `valor` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `valor_final` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 69 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of configuraciones
-- ----------------------------
INSERT INTO `configuraciones` VALUES (1, NULL, 'Categorías', 'Nodo raíz para la clasificación de productos', 'CAT_ROOT', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (2, 1, 'Tecnología y Electrónica', 'Dispositivos, hardware y accesorios', 'TECH', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (3, 1, 'Ropa y Moda', 'Prendas de vestir, calzado y accesorios de moda', 'FASHION', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (4, 1, 'Alimentos y Bebidas', 'Productos perecederos y no perecederos', 'FOOD', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (5, 1, 'Hogar y Muebles', 'Artículos para casa, decoración y mobiliario', 'HOME', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (6, 1, 'Salud y Belleza', 'Cuidado personal, cosméticos y bienestar', 'BEAUTY', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (7, 1, 'Deportes y Fitness', 'Equipamiento deportivo y suplementos', 'SPORTS', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (8, 1, 'Automotriz', 'Repuestos y accesorios para vehículos', 'AUTO', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (9, 1, 'Juguetería y Hobbies', 'Juguetes y artículos de colección', 'TOYS', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (10, 1, 'Herramientas y Construcción', 'Ferretería y materiales', 'TOOLS', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (11, 1, 'Oficina y Papelería', 'Insumos para oficina y estudio', 'OFFICE', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (12, 2, 'Smartphones', 'Teléfonos inteligentes', 'TECH_MOBILE', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (13, 2, 'Laptops', 'Computadoras portátiles', 'TECH_LAPTOP', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (14, 4, 'Lácteos', 'Leche, quesos y derivados', 'FOOD_DAIRY', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (15, 4, 'Cárnicos', 'Carnes rojas y blancas', 'FOOD_MEAT', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (16, 3, 'Calzado', 'Zapatos, tenis y botas', 'FASH_FOOTWEAR', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (17, 3, 'Ropa Exterior', 'Chaquetas y abrigos', 'FASH_OUTERWEAR', NULL, 'admin', '2026-02-01 12:43:44', NULL, NULL);
INSERT INTO `configuraciones` VALUES (18, NULL, 'Tallas', 'Listado maestro independiente para select de tallas', 'SIZE_MASTER', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (19, 18, 'XS', NULL, 'SIZE_XS', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (20, 18, 'S', NULL, 'SIZE_S', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (21, 18, 'M', NULL, 'SIZE_M', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (22, 18, 'L', NULL, 'SIZE_L', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (23, 18, 'XL', NULL, 'SIZE_XL', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (24, 18, 'XXL', NULL, 'SIZE_XXL', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (25, 18, 'XXXL', NULL, 'SIZE_XXXL', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (26, 18, '36', NULL, 'SIZE_36', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (27, 18, '38', NULL, 'SIZE_38', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (28, 18, '40', NULL, 'SIZE_40', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (29, 18, '42', NULL, 'SIZE_42', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (30, 18, '44', NULL, 'SIZE_44', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (31, 18, '46', NULL, 'SIZE_46', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (32, 18, '48', NULL, 'SIZE_48', NULL, 'admin', '2026-02-01 14:26:53', NULL, NULL);
INSERT INTO `configuraciones` VALUES (34, NULL, 'Unidades de Medida', 'Listado maestro para unidades de venta y stock', 'UNIT_MASTER', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (35, 34, 'Unidad', NULL, 'UNIT_UN', NULL, 'admin', '2026-02-01 14:33:32', NULL, '2026-02-01 20:19:13');
INSERT INTO `configuraciones` VALUES (36, 34, 'Kilogramo (kg)', NULL, 'UNIT_KG', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (37, 34, 'Gramo (g)', NULL, 'UNIT_G', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (38, 34, 'Litro (l)', NULL, 'UNIT_L', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (39, 34, 'Mililitro (ml)', NULL, 'UNIT_ML', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (40, 34, 'Metro (m)', NULL, 'UNIT_M', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (41, 34, 'Centímetro (cm)', NULL, 'UNIT_CM', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (42, 34, 'Paquete', NULL, 'UNIT_PKG', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (43, 34, 'Caja', NULL, 'UNIT_BOX', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (44, 34, 'Docena', NULL, 'UNIT_DZ', NULL, 'admin', '2026-02-01 14:33:32', NULL, NULL);
INSERT INTO `configuraciones` VALUES (50, NULL, 'Temperaturas de Conservación', 'Parámetros de almacenamiento para productos perecederos', 'TEMP_MASTER', NULL, 'admin', '2026-02-01 14:42:26', NULL, NULL);
INSERT INTO `configuraciones` VALUES (51, 50, 'Ambiente', NULL, 'TEMP_AMB', NULL, 'admin', '2026-02-01 14:42:26', NULL, NULL);
INSERT INTO `configuraciones` VALUES (52, 50, 'Refrigerado (0-4°C)', NULL, 'TEMP_REFR', NULL, 'admin', '2026-02-01 14:42:26', NULL, NULL);
INSERT INTO `configuraciones` VALUES (53, 50, 'Congelado (-18°C)', NULL, 'TEMP_CONG', NULL, 'admin', '2026-02-01 14:42:26', NULL, NULL);
INSERT INTO `configuraciones` VALUES (54, 50, 'Fresco (10-15°C)', NULL, 'TEMP_FRSH', NULL, 'admin', '2026-02-01 14:42:26', NULL, NULL);
INSERT INTO `configuraciones` VALUES (58, NULL, 'Géneros', 'Clasificación de género para productos y usuarios', 'GENDER_MASTER', NULL, 'admin', '2026-02-01 14:46:15', NULL, NULL);
INSERT INTO `configuraciones` VALUES (59, 58, 'Hombre', NULL, 'GENDER_MALE', NULL, 'admin', '2026-02-01 14:46:15', NULL, NULL);
INSERT INTO `configuraciones` VALUES (60, 58, 'Mujer', NULL, 'GENDER_FEMALE', NULL, 'admin', '2026-02-01 14:46:15', NULL, NULL);
INSERT INTO `configuraciones` VALUES (61, 58, 'Sin especificar', NULL, 'GENDER_NONE', NULL, 'admin', '2026-02-01 14:46:15', NULL, NULL);
INSERT INTO `configuraciones` VALUES (62, NULL, 'Voltajes y Alimentación', 'Especificaciones de energía para productos electrónicos', 'VOLT_MASTER', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (63, 62, '110V', NULL, 'VOLT_110', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (64, 62, '220V', NULL, 'VOLT_220', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (65, 62, '110V-220V', NULL, 'VOLT_DUAL', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (66, 62, 'USB', NULL, 'VOLT_USB', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (67, 62, 'USB-C', NULL, 'VOLT_USBC', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);
INSERT INTO `configuraciones` VALUES (68, 62, 'Batería', NULL, 'VOLT_BATT', NULL, 'admin', '2026-02-01 14:49:11', NULL, NULL);

-- ----------------------------
-- Table structure for empresa_emisor
-- ----------------------------
DROP TABLE IF EXISTS `empresa_emisor`;
CREATE TABLE `empresa_emisor`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Raz??n social registrada ante DIAN',
  `nit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'NIT con d??gito de verificaci??n (ej: 900123456-7)',
  `direccion` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ciudad` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `departamento` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `telefono` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `correo` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `regimen` enum('Responsable de IVA','No responsable de IVA') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Responsable de IVA',
  `resolucion_dian` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'N??mero de resoluci??n de facturaci??n DIAN',
  `fecha_resolucion` date NULL DEFAULT NULL COMMENT 'Fecha de la resoluci??n',
  `prefijo_factura` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'FAC' COMMENT 'Prefijo autorizado por DIAN',
  `rango_desde` int NOT NULL DEFAULT 1 COMMENT 'Rango inicial autorizado',
  `rango_hasta` int NOT NULL DEFAULT 99999 COMMENT 'Rango final autorizado',
  `consecutivo_actual` int NOT NULL DEFAULT 0 COMMENT '??ltimo consecutivo usado',
  `logo_url` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Ruta al logo de la empresa',
  `actividad_economica` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'C??digo CIIU actividad econ??mica',
  `creado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'Datos del emisor para facturaci??n electr??nica DIAN' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of empresa_emisor
-- ----------------------------
INSERT INTO `empresa_emisor` VALUES (1, 'SAHO COMERCIALIZADORA S.A.S.', '900000000-0', 'Calle 100 # 10-20 Oficina 301', 'Bogot?? D.C.', 'Cundinamarca', '601-1234567', 'facturacion@saho.com.co', 'Responsable de IVA', 'Resoluci??n No. 18764000000000', '2026-01-01', 'FAC', 1, 99999, 2, NULL, '4791', 'Sistema', '2026-02-21 19:18:39', NULL, '2026-02-21 20:29:10');

-- ----------------------------
-- Table structure for facturas
-- ----------------------------
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Numeraci??n consecutiva ej: FAC-20260221-0001',
  `id_venta` bigint UNSIGNED NOT NULL COMMENT 'Venta de origen',
  `id_emisor` int UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Empresa emisora',
  `id_cliente` int UNSIGNED NOT NULL,
  `id_vendedor` int UNSIGNED NOT NULL,
  `cliente_razon_social` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre/Raz??n social del cliente al facturar',
  `cliente_nit_cc` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'NIT o CC del cliente',
  `cliente_direccion` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cliente_correo` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cliente_telefono` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fecha_factura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento del pago',
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Base gravable antes de IVA',
  `total_iva` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total IVA',
  `total_descuentos` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_final` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total a pagar',
  `forma_pago` enum('Contado','Cr??dito') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Contado',
  `medio_pago` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Efectivo' COMMENT 'Efectivo, Tarjeta, Transferencia, etc.',
  `estado` enum('emitida','anulada') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'emitida',
  `motivo_anulacion` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `fecha_anulacion` datetime NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `creado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_numero_factura`(`numero_factura` ASC) USING BTREE,
  INDEX `idx_id_venta`(`id_venta` ASC) USING BTREE,
  INDEX `idx_fecha_factura`(`fecha_factura` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE,
  INDEX `fk_factura_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `fk_factura_vendedor`(`id_vendedor` ASC) USING BTREE,
  INDEX `fk_factura_emisor`(`id_emisor` ASC) USING BTREE,
  CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_emisor` FOREIGN KEY (`id_emisor`) REFERENCES `empresa_emisor` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_vendedor` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'Facturas de venta ??? Normativa Colombia DIAN' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of facturas
-- ----------------------------
INSERT INTO `facturas` VALUES (1, 'FAC-20260221-0001', 3, 1, 3, 1, 'Emmanuel Pallares', '1120555888', '', 'emmanuelp@correo.com', '', '2026-02-21 19:41:18', '0000-00-00', 18000000.00, 3420000.00, 0.00, 21420000.00, 'Contado', 'Tarjeta de Crédito', 'emitida', NULL, NULL, '', 'Carlos Vergara', '2026-02-21 19:41:18', NULL, '2026-02-21 19:41:18');
INSERT INTO `facturas` VALUES (2, 'FAC-20260221-0002', 4, 1, 4, 1, 'Valentina Vergara', '1099222222', '', '', '', '2026-02-21 20:29:10', '0000-00-00', 9000000.00, 1710000.00, 0.00, 10710000.00, 'Contado', 'Efectivo', 'emitida', NULL, NULL, '', 'Carlos Vergara', '2026-02-21 20:29:10', NULL, '2026-02-21 20:29:10');

-- ----------------------------
-- Table structure for facturas_detalle
-- ----------------------------
DROP TABLE IF EXISTS `facturas_detalle`;
CREATE TABLE `facturas_detalle`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_factura` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `nombre_producto` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre al momento de facturar',
  `sku` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(15, 2) NOT NULL,
  `porcentaje_iva` decimal(5, 2) NOT NULL DEFAULT 0.00,
  `monto_iva` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `monto_descuento` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `subtotal_linea` decimal(15, 2) NOT NULL COMMENT '(cantidad * precio_unitario) + iva - descuento',
  `creado_por` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_factura_detalle`(`id_factura` ASC) USING BTREE,
  INDEX `fk_factura_det_producto`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `fk_factura_det_factura` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_det_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of facturas_detalle
-- ----------------------------
INSERT INTO `facturas_detalle` VALUES (1, 1, 8, 'Asus ROG Zephyrus', 'LAP-003', 2, 9000000.00, 19.00, 3420000.00, 0.00, 21420000.00, 'Carlos Vergara', '2026-02-21 19:41:18');
INSERT INTO `facturas_detalle` VALUES (2, 2, 8, 'Asus ROG Zephyrus', 'LAP-003', 1, 9000000.00, 19.00, 1710000.00, 0.00, 10710000.00, 'Carlos Vergara', '2026-02-21 20:29:10');

-- ----------------------------
-- Table structure for pagos
-- ----------------------------
DROP TABLE IF EXISTS `pagos`;
CREATE TABLE `pagos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_venta` bigint UNSIGNED NOT NULL COMMENT 'Relación con el ID de la tabla ventas',
  `monto` decimal(15, 2) NOT NULL COMMENT 'Monto abonado',
  `metodo_pago` enum('Efectivo','Tarjeta de Crédito','Tarjeta de Débito','Transferencia','Cheque') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `referencia` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Número de comprobante o transacción bancaria',
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_pago_venta`(`id_venta` ASC) USING BTREE,
  CONSTRAINT `fk_pago_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of pagos
-- ----------------------------
INSERT INTO `pagos` VALUES (1, 1, 1743200.00, 'Efectivo', '', '2026-02-04 19:22:00', 'Carlos Vergara', '2026-02-04 14:22:19', NULL, '2026-02-04 14:22:19');
INSERT INTO `pagos` VALUES (2, 3, 21420000.00, 'Tarjeta de Crédito', '', '2026-02-10 02:06:00', 'Carlos Vergara', '2026-02-09 21:07:05', NULL, '2026-02-09 21:07:05');
INSERT INTO `pagos` VALUES (3, 4, 10710000.00, 'Efectivo', '', '2026-02-22 01:28:00', 'Carlos Vergara', '2026-02-21 20:28:55', NULL, '2026-02-21 20:28:55');

-- ----------------------------
-- Table structure for permisos
-- ----------------------------
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la tabla',
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del permiso ',
  `descripcion` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Descripcion de lo que hace el permiso y que seccion afecta',
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of permisos
-- ----------------------------

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_categoria` int UNSIGNED NOT NULL,
  `sku` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `codigo_barras` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion_corta` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `descripcion_detallada` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `imagen_principal_url` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `precio_costo` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `precio_venta` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `porcentaje_impuesto` decimal(5, 2) NULL DEFAULT 0.00,
  `stock_actual` int NOT NULL DEFAULT 0,
  `stock_minimo` int NULL DEFAULT 5,
  `id_unidad_medida` int UNSIGNED NULL DEFAULT NULL COMMENT 'Ref a UNIT_MASTER',
  `estado` enum('activo','inactivo','descatalogado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'activo',
  `peso_kg` decimal(10, 3) NULL DEFAULT NULL,
  `ancho_cm` decimal(10, 2) NULL DEFAULT NULL,
  `alto_cm` decimal(10, 2) NULL DEFAULT NULL,
  `profundidad_cm` decimal(10, 2) NULL DEFAULT NULL,
  `es_envio_gratis` tinyint(1) NULL DEFAULT 0,
  `es_perecedero` tinyint(1) NULL DEFAULT 0,
  `fecha_vencimiento` date NULL DEFAULT NULL,
  `fecha_elaboracion` date NULL DEFAULT NULL,
  `ingredientes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `info_nutricional` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `id_temperatura_conservacion` int UNSIGNED NULL DEFAULT NULL COMMENT 'Ref a TEMP_MASTER',
  `id_talla` int UNSIGNED NULL DEFAULT NULL COMMENT 'Ref a SIZE_MASTER',
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `material_principal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `id_genero` int UNSIGNED NULL DEFAULT NULL COMMENT 'Ref a GENDER_MASTER',
  `estilo_corte` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `modelo_tecnico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `garantia_meses` int NULL DEFAULT 0,
  `consumo_watts` int NULL DEFAULT NULL,
  `id_voltaje` int UNSIGNED NULL DEFAULT NULL COMMENT 'Ref a VOLT_MASTER',
  `es_inteligente` tinyint(1) NULL DEFAULT 0,
  `especificaciones_extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `creado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sku`(`sku` ASC) USING BTREE,
  INDEX `fk_producto_categoria`(`id_categoria` ASC) USING BTREE,
  INDEX `fk_producto_unidad`(`id_unidad_medida` ASC) USING BTREE,
  INDEX `fk_producto_temp`(`id_temperatura_conservacion` ASC) USING BTREE,
  INDEX `fk_producto_talla`(`id_talla` ASC) USING BTREE,
  INDEX `fk_producto_genero`(`id_genero` ASC) USING BTREE,
  INDEX `fk_producto_voltaje`(`id_voltaje` ASC) USING BTREE,
  CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto_genero` FOREIGN KEY (`id_genero`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto_talla` FOREIGN KEY (`id_talla`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto_temp` FOREIGN KEY (`id_temperatura_conservacion`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto_unidad` FOREIGN KEY (`id_unidad_medida`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_producto_voltaje` FOREIGN KEY (`id_voltaje`) REFERENCES `configuraciones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES (1, 13, 'LAP-001', NULL, 'MacBook Air M2', NULL, NULL, 'Apple', NULL, 3200000.00, 4299000.00, 0.00, 10, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (2, 13, 'LAP-002', NULL, 'Dell XPS 13', NULL, NULL, 'Dell', NULL, 4500000.00, 6800000.00, 0.00, 5, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (3, 12, 'TEL-001', NULL, 'iPhone 15 Pro', NULL, NULL, 'Apple', NULL, 4800000.00, 5899000.00, 0.00, 25, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (4, 12, 'TEL-002', NULL, 'Samsung S24 Ultra', NULL, NULL, 'Samsung', NULL, 5500000.00, 6899920.00, 0.00, 15, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (5, 12, 'TEL-003', NULL, 'Google Pixel 8', NULL, NULL, 'Google', NULL, 2100000.00, 2950000.00, 0.00, 8, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (6, 12, 'TEL-004', NULL, 'Xiaomi 14', NULL, NULL, 'Xiaomi', NULL, 1800000.00, 2400000.00, 0.00, 30, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (7, 12, 'TEL-005', NULL, 'Motorola Edge 40', NULL, NULL, 'Motorola', NULL, 1200000.00, 1699000.00, 0.00, 12, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 63, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (8, 13, 'LAP-003', NULL, 'Asus ROG Zephyrus', NULL, NULL, 'Asus', NULL, 7000000.00, 9000000.00, 19.00, 20, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 24, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-21 21:25:21');
INSERT INTO `productos` VALUES (9, 13, 'LAP-004', NULL, 'Lenovo ThinkPad X1', NULL, NULL, 'Lenovo', NULL, 0.00, 1400.00, 0.00, 7, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 36, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (10, 13, 'LAP-005', NULL, 'HP Spectre x360', NULL, NULL, 'HP', NULL, 0.00, 1250.00, 0.00, 6, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (11, 12, 'TEL-006', NULL, 'Nothing Phone 2', NULL, NULL, 'Nothing', NULL, 0.00, 599.00, 0.00, 10, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (12, 12, 'TEL-007', NULL, 'Sony Xperia 1 V', NULL, NULL, 'Sony', NULL, 0.00, 1399.00, 0.00, 3, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (13, 13, 'LAP-006', NULL, 'Acer Swift Go', NULL, NULL, 'Acer', 'https://cdn.uc.assets.prezly.com/c56dcbaa-49c6-4eaa-8eb3-343ddd4d0405/Swift-Go-14-SFG14-73-02.png', 0.00, 699.00, 0.00, 20, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-04 13:37:03');
INSERT INTO `productos` VALUES (14, 13, 'LAP-007', NULL, 'MSI Prestige 14', NULL, NULL, 'MSI', NULL, 0.00, 950.00, 0.00, 5, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 67, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (15, 12, 'TEL-008', NULL, 'Oppo Find X6', NULL, NULL, 'Oppo', NULL, 0.00, 850.00, 0.00, 14, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, 64, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (16, 16, 'ZAP-001', NULL, 'Air Max 270', NULL, NULL, 'Nike', NULL, 650000.00, 914950.00, 0.00, 50, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 29, 'Negro', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (17, 16, 'ZAP-002', NULL, 'UltraBoost 22', NULL, NULL, 'Adidas', NULL, 580000.00, 850000.00, 0.00, 40, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 28, 'Blanco', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (18, 17, 'ROP-001', NULL, 'Chaqueta de Cuero', NULL, NULL, 'Zara', 'https://www.freelineclothes.com/cdn/shop/files/Diseno_sin_titulo_1_d4f7fff8-c157-47ff-9f03-0b62cfd45379.png?v=1763936925&width=800', 280000.00, 435000.00, 0.00, 34, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Café', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-04 14:21:57');
INSERT INTO `productos` VALUES (19, 17, 'ROP-002', NULL, 'Abrigo de Lana', NULL, NULL, 'H&M', 'https://fieito.com/wp-content/uploads/2024/09/abrigo-lana-merino-mujer-gris-oscuro-7.jpg.webp', 190000.00, 325000.00, 0.00, 21, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Gris', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-07 18:00:58');
INSERT INTO `productos` VALUES (20, 16, 'ZAP-003', NULL, 'Classic Leather', NULL, NULL, 'Reebok', NULL, 0.00, 75.00, 0.00, 100, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 27, 'Blanco', NULL, 60, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (21, 16, 'ZAP-004', NULL, 'Old Skool', NULL, NULL, 'Vans', NULL, 0.00, 65.00, 0.00, 60, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 29, 'Negro', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (22, 17, 'ROP-003', NULL, 'Sudadera Basic', NULL, NULL, 'Nike', NULL, 0.00, 45.00, 0.00, 30, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 20, 'Verde', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (23, 16, 'ZAP-005', NULL, 'Stan Smith', NULL, NULL, 'Adidas', NULL, 0.00, 90.00, 0.00, 20, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 30, 'Blanco', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (24, 17, 'ROP-004', NULL, 'Pantalón Chino', NULL, NULL, 'Dockers', NULL, 0.00, 60.00, 0.00, 40, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 26, 'Beige', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (25, 17, 'ROP-005', NULL, 'Vestido Verano', NULL, NULL, 'Mango', NULL, 0.00, 35.00, 0.00, 15, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 20, 'Azul', NULL, 60, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (26, 16, 'ZAP-006', NULL, 'Botas Chelsea', NULL, NULL, 'Dr Martens', NULL, 0.00, 190.00, 0.00, 12, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 28, 'Negro', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (27, 17, 'ROP-006', NULL, 'Suéter Cachemira', NULL, NULL, 'Uniqlo', NULL, 0.00, 80.00, 0.00, 22, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 23, 'Rojo', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (28, 17, 'ROP-007', NULL, 'Jeans 501', NULL, NULL, 'Levis', NULL, 0.00, 95.00, 0.00, 50, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 27, 'Azul', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (29, 16, 'ZAP-007', NULL, 'Sandalias Playa', NULL, NULL, 'Quiksilver', NULL, 0.00, 25.00, 0.00, 100, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 29, 'Negro', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (30, 17, 'ROP-008', NULL, 'Blusa Seda', NULL, NULL, 'Zara', NULL, 120000.00, 180000.00, 19.00, 17, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Blanco', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-04 14:21:57');
INSERT INTO `productos` VALUES (31, 16, 'ZAP-008', NULL, 'Zapatos Oxford', NULL, NULL, 'Clarks', NULL, 0.00, 110.00, 0.00, 15, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 29, 'Marrón', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (32, 17, 'ROP-009', NULL, 'Chaqueta Denim', NULL, NULL, 'Pull&Bear', NULL, 0.00, 55.00, 0.00, 25, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 22, 'Gris', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (33, 16, 'ZAP-009', NULL, 'Tenis Running', NULL, NULL, 'Asics', NULL, 0.00, 130.00, 0.00, 30, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 30, 'Azul/Naranja', NULL, 59, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (34, 17, 'ROP-010', NULL, 'Bufanda Lana', NULL, NULL, 'Benetton', NULL, 35000.00, 50000.00, 19.00, 54, 5, 43, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Multicolor', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '{}', 'admin', '2026-02-01 19:55:08', 'Carlos Vergara', '2026-02-07 18:00:58');
INSERT INTO `productos` VALUES (35, 16, 'ZAP-010', NULL, 'Mocasines', NULL, NULL, 'Gucci', NULL, 0.00, 650.00, 0.00, 5, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Negro', NULL, 61, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (36, 14, 'COM-001', NULL, 'Leche Entera 1L', NULL, NULL, NULL, NULL, 0.00, 1.50, 0.00, 200, 5, 38, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-05-20', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (37, 14, 'COM-002', NULL, 'Queso Parmesano', NULL, NULL, NULL, NULL, 0.00, 8.50, 0.00, 45, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-08-15', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (38, 15, 'COM-003', NULL, 'Filete de Salmón', NULL, NULL, NULL, NULL, 0.00, 22.00, 0.00, 20, 5, 36, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-02-15', NULL, NULL, NULL, 53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (39, 15, 'COM-004', NULL, 'Pechuga de Pollo', NULL, NULL, NULL, NULL, 0.00, 6.50, 0.00, 60, 5, 36, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-02-10', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (40, 14, 'COM-005', NULL, 'Yogur Griego', NULL, NULL, NULL, NULL, 0.00, 0.90, 0.00, 150, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-03-01', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (41, 14, 'COM-006', NULL, 'Mantequilla Salada', NULL, NULL, NULL, NULL, 0.00, 3.20, 0.00, 80, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-06-30', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (42, 15, 'COM-007', NULL, 'Carne Molida Premium', NULL, NULL, NULL, NULL, 0.00, 10.00, 0.00, 40, 5, 36, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-02-08', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (43, 15, 'COM-008', NULL, 'Jamón Serrano', NULL, NULL, NULL, NULL, 0.00, 15.50, 0.00, 25, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-12-31', NULL, NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (44, 14, 'COM-009', NULL, 'Queso Crema', NULL, NULL, NULL, NULL, 0.00, 2.80, 0.00, 55, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-04-15', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (45, 14, 'COM-010', NULL, 'Crema de Leche', NULL, NULL, NULL, NULL, 0.00, 2.10, 0.00, 70, 5, 38, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-05-10', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (46, 15, 'COM-011', NULL, 'Tira de Asado', NULL, NULL, NULL, NULL, 0.00, 12.00, 0.00, 30, 5, 36, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-02-12', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-01 19:55:08');
INSERT INTO `productos` VALUES (47, 15, 'COM-012', NULL, 'Tocino Ahumado', NULL, NULL, NULL, NULL, 3800.00, 5001.00, 0.00, 45, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-07-20', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (48, 14, 'COM-013', NULL, 'Leche de Almendras', NULL, NULL, NULL, NULL, 12000.00, 15500.00, 0.00, 100, 5, 38, 'activo', NULL, NULL, NULL, NULL, 0, 0, '2026-12-01', NULL, NULL, NULL, 51, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (49, 15, 'COM-014', NULL, 'Salchicha Alemana', NULL, NULL, NULL, NULL, 15000.00, 21000.00, 0.00, 50, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-04-05', NULL, NULL, NULL, 52, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (50, 15, 'COM-015', NULL, 'Pavo Ahumado Entero', NULL, NULL, NULL, NULL, 55000.00, 75000.00, 0.00, 10, 5, 35, 'activo', NULL, NULL, NULL, NULL, 0, 1, '2026-03-20', NULL, NULL, NULL, 53, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, 'admin', '2026-02-01 19:55:08', NULL, '2026-02-04 12:45:22');
INSERT INTO `productos` VALUES (51, 7, 'CPO-000', NULL, 'Zapatos deportivos', NULL, NULL, 'Nike', NULL, 22000.00, 32000.00, 19.00, 20, 3, 42, 'activo', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '{}', 'Carlos Vergara', '2026-02-04 12:33:46', 'Carlos Vergara', '2026-02-04 12:45:22');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la tabla',
  `nombre` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del rol',
  `estado` smallint NULL DEFAULT 1 COMMENT '1 = Activo y 0 = Inactivo',
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'Administrador', 1, 'Sistema', '2026-01-31 14:54:32', NULL, NULL);
INSERT INTO `roles` VALUES (2, 'Vendedor', 1, 'Sistema', '2026-01-31 14:54:47', NULL, NULL);

-- ----------------------------
-- Table structure for roles_permisos
-- ----------------------------
DROP TABLE IF EXISTS `roles_permisos`;
CREATE TABLE `roles_permisos`  (
  `id_rol` int UNSIGNED NOT NULL COMMENT 'Llave foranea con la tabla roles',
  `id_permiso` int UNSIGNED NOT NULL COMMENT 'Llave foranea con la tabla permisos',
  `estado` smallint NOT NULL DEFAULT 1 COMMENT 'Estado del rol permiso, 1 = activo y 0 = inactivo',
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  INDEX `id_rol`(`id_rol` ASC) USING BTREE,
  INDEX `id_permiso`(`id_permiso` ASC) USING BTREE,
  CONSTRAINT `roles_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `roles_permisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of roles_permisos
-- ----------------------------

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`  (
  `id_usuario` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador unico de la tabla',
  `nombre_completo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario',
  `correo` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Correo de ingreso',
  `contrasena` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Contraseña de ingreso al sistema',
  `id_rol` int UNSIGNED NOT NULL COMMENT 'Llave foranea con la tabla roles',
  `creado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del usuario que lo genera',
  `fec_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
  `actualizado_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de usuario que actualiza',
  `fec_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha en la que se actualiza el registro',
  PRIMARY KEY (`id_usuario`) USING BTREE,
  INDEX `id_rol`(`id_rol` ASC) USING BTREE,
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'Tabla para la gestion de los usuarios del sistema.' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES (1, 'Carlos Vergara', 'carlos.correo@gmail.com', '6a09e5ea87cd62f6bdeab61de7d6886f2e4dcc1da828c662e197535d36770730', 1, 'Desde la db', '2026-01-25 15:14:05', NULL, '2026-02-04 13:27:40');

-- ----------------------------
-- Table structure for ventas
-- ----------------------------
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `folio_factura` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Número legal de factura o ticket',
  `id_cliente` int UNSIGNED NOT NULL COMMENT 'Usuario que compra (Ref: clientes)',
  `id_vendedor` int UNSIGNED NOT NULL COMMENT 'Usuario que vende (Ref: usuarios)',
  `fecha_venta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Suma de detalles antes de impuestos y descuentos',
  `total_impuestos` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_descuentos` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_final` decimal(15, 2) NOT NULL DEFAULT 0.00 COMMENT 'Monto neto a pagar',
  `creado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `idx_folio`(`folio_factura` ASC) USING BTREE,
  INDEX `fk_venta_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `fk_venta_vendedor`(`id_vendedor` ASC) USING BTREE,
  CONSTRAINT `fk_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_venta_vendedor` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of ventas
-- ----------------------------
INSERT INTO `ventas` VALUES (1, 'VTA-20260204-0001', 1, 1, '2026-02-04 14:21:57', 1690000.00, 53200.00, 0.00, 1743200.00, 'Carlos Vergara', '2026-02-04 14:21:57', NULL, '2026-02-04 14:21:57');
INSERT INTO `ventas` VALUES (2, 'VTA-20260207-0001', 1, 1, '2026-02-07 18:00:58', 525000.00, 38000.00, 0.00, 563000.00, 'Carlos Vergara', '2026-02-07 18:00:58', NULL, '2026-02-07 18:00:58');
INSERT INTO `ventas` VALUES (3, 'VTA-20260209-0001', 3, 1, '2026-02-09 21:06:50', 18000000.00, 3420000.00, 0.00, 21420000.00, 'Carlos Vergara', '2026-02-09 21:06:50', NULL, '2026-02-09 21:06:50');
INSERT INTO `ventas` VALUES (4, 'VTA-20260221-0001', 4, 1, '2026-02-21 20:28:47', 9000000.00, 1710000.00, 0.00, 10710000.00, 'Carlos Vergara', '2026-02-21 20:28:47', NULL, '2026-02-21 20:28:47');

-- ----------------------------
-- Table structure for ventas_detalle
-- ----------------------------
DROP TABLE IF EXISTS `ventas_detalle`;
CREATE TABLE `ventas_detalle`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_venta` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL COMMENT 'ID original para trazabilidad',
  `nombre_historico` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Nombre del producto al momento de venta',
  `sku_historico` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `categoria_historica` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Nombre de la categoría al momento de venta',
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(15, 2) NOT NULL COMMENT 'Precio de venta aplicado',
  `costo_unitario_historico` decimal(15, 2) NOT NULL COMMENT 'Para cálculo de utilidad real',
  `porcentaje_impuesto` decimal(5, 2) NOT NULL DEFAULT 0.00,
  `monto_impuesto` decimal(15, 2) NOT NULL,
  `monto_descuento` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `subtotal_linea` decimal(15, 2) NOT NULL COMMENT '(cantidad * precio) - descuento',
  `creado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_detalle_venta`(`id_venta` ASC) USING BTREE,
  INDEX `fk_detalle_producto`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_detalle_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of ventas_detalle
-- ----------------------------
INSERT INTO `ventas_detalle` VALUES (1, 1, 19, 'Abrigo de Lana', 'ROP-002', 'Ropa Exterior', 3, 325000.00, 190000.00, 0.00, 0.00, 0.00, 975000.00, 'Carlos Vergara', '2026-02-04 14:21:57', NULL, '2026-02-04 14:21:57');
INSERT INTO `ventas_detalle` VALUES (2, 1, 34, 'Bufanda Lana', 'ROP-010', 'Ropa Exterior', 2, 50000.00, 35000.00, 19.00, 19000.00, 0.00, 119000.00, 'Carlos Vergara', '2026-02-04 14:21:57', NULL, '2026-02-04 14:21:57');
INSERT INTO `ventas_detalle` VALUES (3, 1, 30, 'Blusa Seda', 'ROP-008', 'Ropa Exterior', 1, 180000.00, 120000.00, 19.00, 34200.00, 0.00, 214200.00, 'Carlos Vergara', '2026-02-04 14:21:57', NULL, '2026-02-04 14:21:57');
INSERT INTO `ventas_detalle` VALUES (4, 1, 18, 'Chaqueta de Cuero', 'ROP-001', 'Ropa Exterior', 1, 435000.00, 280000.00, 0.00, 0.00, 0.00, 435000.00, 'Carlos Vergara', '2026-02-04 14:21:57', NULL, '2026-02-04 14:21:57');
INSERT INTO `ventas_detalle` VALUES (5, 2, 19, 'Abrigo de Lana', 'ROP-002', 'Ropa Exterior', 1, 325000.00, 190000.00, 0.00, 0.00, 0.00, 325000.00, 'Carlos Vergara', '2026-02-07 18:00:58', NULL, '2026-02-07 18:00:58');
INSERT INTO `ventas_detalle` VALUES (6, 2, 34, 'Bufanda Lana', 'ROP-010', 'Ropa Exterior', 4, 50000.00, 35000.00, 19.00, 38000.00, 0.00, 238000.00, 'Carlos Vergara', '2026-02-07 18:00:58', NULL, '2026-02-07 18:00:58');
INSERT INTO `ventas_detalle` VALUES (7, 3, 8, 'Asus ROG Zephyrus', 'LAP-003', 'Laptops', 2, 9000000.00, 7000000.00, 19.00, 3420000.00, 0.00, 21420000.00, 'Carlos Vergara', '2026-02-09 21:06:50', NULL, '2026-02-09 21:06:50');
INSERT INTO `ventas_detalle` VALUES (8, 4, 8, 'Asus ROG Zephyrus', 'LAP-003', 'Laptops', 1, 9000000.00, 7000000.00, 19.00, 1710000.00, 0.00, 10710000.00, 'Carlos Vergara', '2026-02-21 20:28:47', NULL, '2026-02-21 20:28:47');

SET FOREIGN_KEY_CHECKS = 1;
