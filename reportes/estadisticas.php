<?php
require_once __DIR__.'/../solicitudes/comun.php'; permiso(['administrador']);
$totalClientes=consulta('SELECT COUNT(*) FROM clientes')->fetchColumn();
$conteos=array_fill_keys($estados,0);
foreach(consulta('SELECT estado,COUNT(*) total FROM solicitudes GROUP BY estado') as $f) $conteos[$f['estado']]=(int)$f['total'];
$servicios=consulta('SELECT v.nombre,COUNT(s.id) total FROM servicios v LEFT JOIN solicitudes s ON s.servicio_id=v.id GROUP BY v.id,v.nombre ORDER BY total DESC,v.nombre')->fetchAll(PDO::FETCH_ASSOC);
$empleados=consulta("SELECT u.nombre,COUNT(s.id) total,SUM(CASE WHEN s.estado='Finalizada' THEN 1 ELSE 0 END) finalizadas FROM empleados e JOIN usuarios u ON u.id=e.usuario_id LEFT JOIN solicitudes s ON s.empleado_id=e.id GROUP BY e.id,u.nombre ORDER BY finalizadas DESC,u.nombre")->fetchAll(PDO::FETCH_ASSOC);
