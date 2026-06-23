-- =====================================================
-- BASE DE DATOS PARA LAVANDERÍA "AQUACLEAN"
-- =====================================================
-- 1. CREACIÓN Y SELECCIÓN DE LA BASE DE DATOS
DROP DATABASE IF EXISTS aquaclean_db;
CREATE DATABASE aquaclean_db;
USE aquaclean_db;

-- =====================================================
-- 2. TABLAS PRINCIPALES

-- 2.1 CIUDADES
CREATE TABLE ciudades (
    id_ciudad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    provincia VARCHAR(100) NOT NULL,
    estado ENUM('ACTIVA', 'INACTIVA') DEFAULT 'ACTIVA'
);

-- 2.2 CARGOS
CREATE TABLE cargos (
    id_cargo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(200)
);

-- 2.3 USUARIOS (CORREGIDO - SIN COMA AL FINAL)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(120) UNIQUE NOT NULL,
    direccion VARCHAR(200),
    id_ciudad INT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('ADMIN', 'EMPLEADO', 'CLIENTE') NOT NULL,
    estado ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO',
    token_remember VARCHAR(255) NULL,
    token_expira DATETIME NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ciudad) REFERENCES ciudades(id_ciudad)
);

-- 2.4 EMPLEADOS
CREATE TABLE empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cargo INT NOT NULL,
    salario DECIMAL(10, 2),
    fecha_ingreso DATE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cargo) REFERENCES cargos(id_cargo)
);

-- 2.5 CLIENTES
CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- 2.6 PRENDAS
CREATE TABLE prendas (
    id_prenda INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    peso_promedio_kg DECIMAL(5, 2) DEFAULT 0.5
);

-- 2.7 SERVICIOS
CREATE TABLE servicios (
    id_servicio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    tipo_cobro ENUM('KG', 'PRENDA') DEFAULT 'KG',
    precio_unitario DECIMAL(10, 2) NOT NULL,
    tiempo_estimado_horas INT DEFAULT 1,
    estado ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO'
);

-- 2.8 ESTADOS DE ORDEN
CREATE TABLE estados_orden (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) UNIQUE NOT NULL,
    descripcion VARCHAR(200)
);

-- 2.9 ÓRDENES
CREATE TABLE ordenes (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    codigo_orden VARCHAR(30) UNIQUE,
    id_cliente INT NOT NULL,
    id_empleado INT NULL,
    fecha_recepcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega_estimada DATETIME,
    fecha_entrega_real DATETIME,
    id_estado INT NOT NULL,
    observaciones TEXT,
    subtotal DECIMAL(10, 2) DEFAULT 0,
    iva DECIMAL(10, 2) DEFAULT 0,
    descuento DECIMAL(10, 2) DEFAULT 0,
    total_pagar DECIMAL(10, 2) DEFAULT 0,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado),
    FOREIGN KEY (id_estado) REFERENCES estados_orden(id_estado)
);

-- 2.10 DETALLE DE ORDEN
CREATE TABLE detalle_orden (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    id_servicio INT NOT NULL,
    id_prenda INT NULL,
    cantidad DECIMAL(10, 2) NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_orden) REFERENCES ordenes(id_orden) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio),
    FOREIGN KEY (id_prenda) REFERENCES prendas(id_prenda)
);

-- 2.11 PAGOS
CREATE TABLE pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    metodo_pago ENUM('EFECTIVO', 'TRANSFERENCIA', 'TARJETA') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    estado ENUM('PENDIENTE', 'PAGADO', 'ANULADO') DEFAULT 'PAGADO',
    FOREIGN KEY (id_orden) REFERENCES ordenes(id_orden)
);

-- 2.12 INVENTARIO
CREATE TABLE inventario (
    id_insumo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock_actual DECIMAL(10, 2) NOT NULL,
    stock_minimo DECIMAL(10, 2) DEFAULT 10,
    unidad_medida VARCHAR(20),
    estado ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO',
    fecha_actualizacion DATE DEFAULT (CURRENT_DATE)
);

-- 2.13 PROVEEDORES
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    correo VARCHAR(100)
);

-- 2.14 SOLICITUDES DE SERVICIO
CREATE TABLE solicitudes_servicio (
    id_solicitud INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL,
    tipo_servicio VARCHAR(100) NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    direccion TEXT,
    mensaje TEXT,
    estado ENUM('PENDIENTE', 'EN_PROCESO', 'COMPLETADO', 'CANCELADO') DEFAULT 'PENDIENTE',
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. DATOS INICIALES (INSERTS)
-- =====================================================

INSERT INTO ciudades (nombre, provincia) VALUES
('Guayaquil', 'Guayas'),
('Quito', 'Pichincha'),
('Cuenca', 'Azuay'),
('Manta', 'Manabí');

INSERT INTO cargos (nombre, descripcion) VALUES
('Administrador', 'Gestiona el sistema y usuarios'),
('Recepcionista', 'Atención al cliente y registro de órdenes'),
('Operario de Lavado', 'Ejecuta el lavado de prendas'),
('Operario de Planchado', 'Ejecuta el planchado y secado');

INSERT INTO servicios (nombre, descripcion, tipo_cobro, precio_unitario, tiempo_estimado_horas) VALUES
('Lavado', 'Lavado completo con jabón de alta calidad', 'KG', 2.50, 2),
('Planchado', 'Planchado a vapor para prendas impecables', 'KG', 1.50, 1),
('Secado Rápido', 'Secado industrial en menos de 45 minutos', 'KG', 1.00, 1),
('Tinturado', 'Renovación y cambio de color profesional', 'PRENDA', 5.00, 3);

INSERT INTO estados_orden (nombre, descripcion) VALUES
('Recibido', 'Orden registrada, pendiente de iniciar'),
('En Lavado', 'Prendas en proceso de lavado'),
('En Secado', 'Prendas en proceso de secado'),
('En Planchado', 'Prendas en proceso de planchado'),
('Listo para Entrega', 'Servicio completado, esperando retiro'),
('Entregado', 'Orden finalizada y entregada al cliente'),
('Cancelado', 'Orden cancelada por cliente o sistema');

INSERT INTO prendas (nombre, peso_promedio_kg) VALUES
('Camisa', 0.25),
('Pantalón', 0.45),
('Chaqueta', 0.80),
('Sábana', 0.60),
('Toalla', 0.30);

INSERT INTO usuarios (cedula, nombres, apellidos, telefono, correo, direccion, id_ciudad, username, password_hash, rol) VALUES
('0102030405', 'Admin', 'AquaClean', '0999999999', 'admin@aquaclean.com', 'Matriz Av. Principal 123', 1, 'admin', SHA2('123456', 256), 'ADMIN'),
('0405060708', 'María', 'López', '0966666666', 'maria.lopez@gmail.com', 'Ceibos Norte MZ3', 1, 'maria.lopez', SHA2('123456', 256), 'CLIENTE');

INSERT INTO empleados (id_usuario, id_cargo, salario, fecha_ingreso) VALUES
(1, 1, 800.00, '2024-01-10');

INSERT INTO clientes (id_usuario) VALUES (2);

INSERT INTO inventario (nombre, stock_actual, stock_minimo, unidad_medida) VALUES
('Jabón líquido', 150.00, 20, 'Litros'),
('Suavizante', 80.00, 15, 'Litros'),
('Blanqueador', 40.00, 10, 'Litros'),
('Detergente en polvo', 100.00, 25, 'kg'),
('Perfume para ropa', 30.00, 5, 'Litros');

INSERT INTO proveedores (nombre, contacto, telefono, correo) VALUES
('Química Industrial SA', 'Juan Pérez', '042123456', 'ventas@quimicaind.com'),
('Lavatex Cía. Ltda.', 'María Andrade', '0987654321', 'contacto@lavatex.com');

-- =====================================================
-- 4. VERIFICACIÓN FINAL
-- =====================================================
SELECT '=== BASE DE DATOS CREADA CORRECTAMENTE ===' AS Mensaje;
SELECT COUNT(*) AS Total_Tablas FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'aquaclean_db';