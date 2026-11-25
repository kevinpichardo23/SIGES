USE siges;

-- 1. Usuarios
INSERT INTO usuarios (id, nombre, correo, contraseña, rol) VALUES
(1, 'Administrador', 'admin@siges.com', '$2y$12$Z37k7jR0.ZwOKy2f5oO.yeEEQe5EfeOAygSiw25bOIh73e4Ei6iNq', 'administrador'),
(2, 'Carlos Méndez', 'carlos@siges.com', '$2y$12$rFZCNw3e56AOCcMfgnSoO.uoZI2MNeGgoRivoKHsjkeCR7ymNszo6', 'empleado'),
(3, 'Laura Pérez', 'laura@siges.com', '$2y$12$oXPVlog6eT1jS9fcSPxbXu4S/9ORyGwXMEal5ckvi3zV8WhPl/AM.', 'empleado'),
(4, 'Pedro Gómez', 'pedro@siges.com', '$2y$12$YskqtTO2PHQ6Sdx2Oog.P.jkrKsWuuZ2Oy8XnAdvMU3tAAVoRq43W', 'cliente');

-- 2. Clientes
INSERT INTO clientes (id, usuario_id, nombre, apellido, identificacion, telefono, correo, direccion, estado) VALUES
(1, 4, 'Pedro', 'Gómez', '001-1234567-8', '809-555-1001', 'pedro@siges.com', 'Bonao', 'Activo'),
(2, NULL, 'María', 'Rodríguez', '001-9876543-2', '809-555-1002', 'maria@gmail.com', 'Santiago', 'Activo'),
(3, NULL, 'José', 'Martínez', '001-4567890-1', '809-555-1003', 'jose@gmail.com', 'La Vega', 'Activo');

-- 3. Empleados
INSERT INTO empleados (id, usuario_id, especialidad, estado) VALUES
(1, 2, 'Soporte técnico', 'Disponible'),
(2, 3, 'Desarrollo web', 'Ocupado');

-- 4. Servicios
INSERT INTO servicios (id, nombre, descripcion, precio) VALUES
(1, 'Mantenimiento de equipos', 'Revisión y mantenimiento de computadoras', 1500.00),
(2, 'Desarrollo web', 'Creación y mantenimiento de páginas web', 8000.00),
(3, 'Instalación de redes', 'Instalación y configuración de redes informáticas', 3500.00);

-- 5. Solicitudes
INSERT INTO solicitudes
(id, cliente_id, servicio_id, empleado_id, descripcion, fecha, estado, prioridad)
VALUES
(1, 1, 1, 1, 'La computadora presenta lentitud', '2026-11-20 09:00:00', 'Pendiente', 'Media'),
(2, 2, 2, 2, 'Crear una página web para un negocio', '2026-11-20 10:30:00', 'En proceso', 'Alta'),
(3, 3, 3, 1, 'Instalar red en una oficina', '2026-11-21 08:15:00', 'Finalizada', 'Baja'),
(4, 1, 1, NULL, 'Revisar una computadora que no enciende', '2026-11-21 11:00:00', 'En espera', 'Alta');

-- 6. Historial
INSERT INTO historial
(id, solicitud_id, usuario_id, estado_anterior, estado_nuevo, fecha)
VALUES
(1, 1, 2, 'Pendiente', 'Pendiente', '2026-11-20 09:00:00'),
(2, 2, 3, 'Pendiente', 'En proceso', '2026-11-20 11:00:00'),
(3, 3, 2, 'En proceso', 'Finalizada', '2026-11-21 09:30:00'),
(4, 4, 1, 'Pendiente', 'En espera', '2026-11-21 11:30:00');