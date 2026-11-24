<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
$rol = $_SESSION['rol'] ?? '';
$uid = (int)($_SESSION['id_usuario'] ?? 0);
if (!$uid || !in_array($rol, ['administrador','empleado','cliente'], true)) { header('Location: ../auth/login.php'); exit; }
$estados = ['Pendiente','En proceso','En espera','Finalizada'];
$prioridades = ['Baja','Media','Alta'];
$_SESSION['csrf_solicitudes'] ??= bin2hex(random_bytes(32));
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function consulta($sql, $args = []) { global $conexion; $q=$conexion->prepare($sql); $q->execute($args); return $q; }
function permiso($roles) { global $rol; if (!in_array($rol,$roles,true)) { http_response_code(403); exit('Acceso no autorizado.'); } }
function token() { return '<input type="hidden" name="csrf" value="'.h($_SESSION['csrf_solicitudes']).'">'; }
function verificar() { if (!hash_equals($_SESSION['csrf_solicitudes'], (string)($_POST['csrf'] ?? ''))) { http_response_code(403); exit('Formulario inválido.'); } }
function alcance() { global $rol,$uid; if ($rol==='empleado') return ['e.usuario_id = ?',[$uid]]; if ($rol==='cliente') return ['c.correo = ?',[ $_SESSION['correo'] ?? '' ]]; return ['1=1',[]]; }
function baseSQL() { return ' FROM solicitudes s JOIN clientes c ON c.id=s.cliente_id JOIN servicios v ON v.id=s.servicio_id LEFT JOIN empleados e ON e.id=s.empleado_id LEFT JOIN usuarios u ON u.id=e.usuario_id '; }
function solicitud($id, $bloquear=false) { [$w,$a]=alcance(); $a[]=$id; $r=consulta('SELECT s.*, c.nombre AS cliente, v.nombre AS servicio, u.nombre AS empleado'.baseSQL().' WHERE '.$w.' AND s.id=?'.($bloquear?' FOR UPDATE':''),$a)->fetch(PDO::FETCH_ASSOC); if (!$r) { http_response_code(404); exit('Solicitud no encontrada.'); } return $r; }
function inicio($titulo,$pagina='solicitudes') { global $rol; $paginaActual=$pagina; ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=h($titulo)?> | SIGES</title><link rel="stylesheet" href="../assets/css/dashboard.css"><link rel="stylesheet" href="../assets/css/solicitudes.css"></head><body><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="main-content"><header class="header"><div><h1><?=h($titulo)?></h1><p>Sistema de gestión de servicios</p></div><strong><?=h(ucfirst($rol))?></strong></header><?php }
function fin() { echo '</main></body></html>'; }
function opciones($items,$actual='') { foreach($items as $v) echo '<option'.($v===$actual?' selected':'').'>'.h($v).'</option>'; }
function seleccionar($filas,$actual=0) { foreach($filas as $f) echo '<option value="'.h($f['id']).'"'.((int)$f['id']===(int)$actual?' selected':'').'>'.h($f['nombre']).'</option>'; }
function volver() { header('Location: listar.php?ok=1'); exit; }
