CREATE DATABASE IF NOT EXISTS siges
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE siges;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'empleado', 'cliente') NOT NULL
) ENGINE=InnoDB;

-- Tabla de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL DEFAULT '',
    identificacion VARCHAR(30) NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    direccion VARCHAR(200) NOT NULL,
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB;

-- Tabla de empleados
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    especialidad VARCHAR(100) NOT NULL,
    estado VARCHAR(30) NOT NULL,
    CONSTRAINT fk_empleados_usuarios
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- Tabla de servicios
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- Tabla de solicitudes
CREATE TABLE solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servicio_id INT NOT NULL,
    empleado_id INT,
    descripcion TEXT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Pendiente', 'En proceso', 'En espera', 'Finalizada')
        NOT NULL DEFAULT 'Pendiente',
    prioridad ENUM('Baja', 'Media', 'Alta') NOT NULL,
    CONSTRAINT fk_solicitudes_clientes
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    CONSTRAINT fk_solicitudes_servicios
    FOREIGN KEY (servicio_id) REFERENCES servicios(id),
    CONSTRAINT fk_solicitudes_empleados
    FOREIGN KEY (empleado_id) REFERENCES empleados(id)
) ENGINE=InnoDB;

-- Tabla de historial
CREATE TABLE historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_anterior VARCHAR(30) NOT NULL,
    estado_nuevo VARCHAR(30) NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_solicitudes
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id),
    CONSTRAINT fk_historial_usuarios
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;