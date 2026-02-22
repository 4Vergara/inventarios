-- ============================================================
-- MIGRACIÓN: Módulo de Facturación y Cierre de Caja
-- Sistema Saho - Inventarios (Colombia / DIAN)
-- Fecha: 2026-02-21
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- 1. Datos del emisor (empresa) para la facturación
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `empresa_emisor`;
CREATE TABLE `empresa_emisor` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(255) NOT NULL COMMENT 'Razón social registrada ante DIAN',
  `nit` varchar(20) NOT NULL COMMENT 'NIT con dígito de verificación (ej: 900123456-7)',
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `telefono` varchar(30) NULL DEFAULT NULL,
  `correo` varchar(150) NULL DEFAULT NULL,
  `regimen` enum('Responsable de IVA','No responsable de IVA') NOT NULL DEFAULT 'Responsable de IVA',
  `resolucion_dian` varchar(100) NULL DEFAULT NULL COMMENT 'Número de resolución de facturación DIAN',
  `fecha_resolucion` date NULL DEFAULT NULL COMMENT 'Fecha de la resolución',
  `prefijo_factura` varchar(10) NOT NULL DEFAULT 'FAC' COMMENT 'Prefijo autorizado por DIAN',
  `rango_desde` int NOT NULL DEFAULT 1 COMMENT 'Rango inicial autorizado',
  `rango_hasta` int NOT NULL DEFAULT 99999 COMMENT 'Rango final autorizado',
  `consecutivo_actual` int NOT NULL DEFAULT 0 COMMENT 'Último consecutivo usado',
  `logo_url` varchar(500) NULL DEFAULT NULL COMMENT 'Ruta al logo de la empresa',
  `actividad_economica` varchar(10) NULL DEFAULT NULL COMMENT 'Código CIIU actividad económica',
  `creado_por` varchar(100) NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Datos del emisor para facturación electrónica DIAN';

-- Insertar datos iniciales del emisor (modificar según empresa real)
INSERT INTO `empresa_emisor` VALUES (
  1,
  'SAHO COMERCIALIZADORA S.A.S.',
  '900000000-0',
  'Calle 100 # 10-20 Oficina 301',
  'Bogotá D.C.',
  'Cundinamarca',
  '601-1234567',
  'facturacion@saho.com.co',
  'Responsable de IVA',
  'Resolución No. 18764000000000',
  '2026-01-01',
  'FAC',
  1,
  99999,
  0,
  NULL,
  '4791',
  'Sistema',
  NOW(),
  NULL,
  NOW()
);

-- -----------------------------------------------------------
-- 2. Tabla de facturas (cabecera)
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_factura` varchar(30) NOT NULL COMMENT 'Numeración consecutiva ej: FAC-20260221-0001',
  `id_venta` bigint UNSIGNED NOT NULL COMMENT 'Venta de origen',
  `id_emisor` int UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Empresa emisora',
  `id_cliente` int UNSIGNED NOT NULL,
  `id_vendedor` int UNSIGNED NOT NULL,

  -- Datos del cliente snapshot (para factura legal)
  `cliente_razon_social` varchar(255) NOT NULL COMMENT 'Nombre/Razón social del cliente al facturar',
  `cliente_nit_cc` varchar(30) NOT NULL COMMENT 'NIT o CC del cliente',
  `cliente_direccion` varchar(255) NULL DEFAULT NULL,
  `cliente_correo` varchar(150) NULL DEFAULT NULL,
  `cliente_telefono` varchar(30) NULL DEFAULT NULL,

  `fecha_factura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date NULL DEFAULT NULL COMMENT 'Fecha de vencimiento del pago',

  -- Totales
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base gravable antes de IVA',
  `total_iva` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total IVA',
  `total_descuentos` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_final` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total a pagar',

  -- Forma de pago según DIAN
  `forma_pago` enum('Contado','Crédito') NOT NULL DEFAULT 'Contado',
  `medio_pago` varchar(50) NOT NULL DEFAULT 'Efectivo' COMMENT 'Efectivo, Tarjeta, Transferencia, etc.',

  -- Estado
  `estado` enum('emitida','anulada') NOT NULL DEFAULT 'emitida',
  `motivo_anulacion` text NULL DEFAULT NULL,
  `fecha_anulacion` datetime NULL DEFAULT NULL,

  -- Observaciones
  `observaciones` text NULL DEFAULT NULL,

  -- Auditoría
  `creado_por` varchar(100) NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_numero_factura` (`numero_factura`),
  INDEX `idx_id_venta` (`id_venta`),
  INDEX `idx_fecha_factura` (`fecha_factura`),
  INDEX `idx_estado` (`estado`),
  CONSTRAINT `fk_factura_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_vendedor` FOREIGN KEY (`id_vendedor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_emisor` FOREIGN KEY (`id_emisor`) REFERENCES `empresa_emisor` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Facturas de venta – Normativa Colombia DIAN';

-- -----------------------------------------------------------
-- 3. Detalle de factura (líneas/ítems)
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `facturas_detalle`;
CREATE TABLE `facturas_detalle` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_factura` bigint UNSIGNED NOT NULL,
  `id_producto` bigint UNSIGNED NOT NULL,
  `nombre_producto` varchar(255) NOT NULL COMMENT 'Nombre al momento de facturar',
  `sku` varchar(64) NULL DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(15,2) NOT NULL,
  `porcentaje_iva` decimal(5,2) NOT NULL DEFAULT 0.00,
  `monto_iva` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monto_descuento` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal_linea` decimal(15,2) NOT NULL COMMENT '(cantidad * precio_unitario) + iva - descuento',
  `creado_por` varchar(100) NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_factura_detalle` (`id_factura`),
  CONSTRAINT `fk_factura_det_factura` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_factura_det_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- 4. Cierres de caja
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `cierres_caja`;
CREATE TABLE `cierres_caja` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_cierre` varchar(30) NOT NULL COMMENT 'Código único ej: CC-20260221-0001',
  `tipo_periodo` enum('dia','semana','mes','anio') NOT NULL COMMENT 'Tipo de período del cierre',
  `fecha_inicio` datetime NOT NULL COMMENT 'Inicio del período',
  `fecha_fin` datetime NOT NULL COMMENT 'Fin del período',

  -- Totales de ventas
  `total_ventas` int NOT NULL DEFAULT 0 COMMENT 'Cantidad de ventas en el período',
  `monto_total_vendido` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monto_subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monto_impuestos` decimal(15,2) NOT NULL DEFAULT 0.00,
  `monto_descuentos` decimal(15,2) NOT NULL DEFAULT 0.00,

  -- Desglose por método de pago
  `total_efectivo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_tarjeta_credito` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_tarjeta_debito` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_transferencia` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_cheque` decimal(15,2) NOT NULL DEFAULT 0.00,

  -- Ventas anuladas/canceladas
  `ventas_anuladas` int NOT NULL DEFAULT 0,
  `monto_anulado` decimal(15,2) NOT NULL DEFAULT 0.00,

  -- Control de caja
  `efectivo_inicial` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Base de caja al inicio',
  `efectivo_esperado` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Efectivo que debería haber',
  `efectivo_contado` decimal(15,2) NULL DEFAULT NULL COMMENT 'Efectivo contado físicamente',
  `diferencia_caja` decimal(15,2) NULL DEFAULT NULL COMMENT 'Diferencia (contado - esperado)',

  -- Facturas emitidas en el periodo
  `total_facturas` int NOT NULL DEFAULT 0,

  -- Observaciones
  `observaciones` text NULL DEFAULT NULL,

  -- Auditoría
  `cerrado_por` varchar(100) NOT NULL COMMENT 'Usuario que realizó el cierre',
  `creado_por` varchar(100) NOT NULL,
  `fec_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_por` varchar(100) NULL DEFAULT NULL,
  `fec_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE INDEX `idx_codigo_cierre` (`codigo_cierre`),
  INDEX `idx_tipo_periodo` (`tipo_periodo`),
  INDEX `idx_fecha_rango` (`fecha_inicio`, `fecha_fin`)
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = 'Histórico de cierres de caja';

-- -----------------------------------------------------------
-- 5. Detalle de ventas incluidas en cada cierre
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `cierres_caja_detalle`;
CREATE TABLE `cierres_caja_detalle` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cierre` bigint UNSIGNED NOT NULL,
  `id_venta` bigint UNSIGNED NOT NULL,
  `folio_venta` varchar(20) NOT NULL,
  `total_venta` decimal(15,2) NOT NULL,
  `total_pagado` decimal(15,2) NOT NULL DEFAULT 0.00,
  `metodo_pago_principal` varchar(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_cierre_detalle` (`id_cierre`),
  CONSTRAINT `fk_cierre_det_cierre` FOREIGN KEY (`id_cierre`) REFERENCES `cierres_caja` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_cierre_det_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
