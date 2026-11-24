<?php
require 'comun.php';
[$w,$a]=alcance();
$estado=$_GET['estado'] ?? ''; $prioridad=$_GET['prioridad'] ?? ''; $buscar=trim($_GET['buscar'] ?? '');
if(in_array($estado,$estados,true)){ $w.=' AND s.estado=?'; $a[]=$estado; }
if(in_array($prioridad,$prioridades,true)){ $w.=' AND s.prioridad=?'; $a[]=$prioridad; }
if($buscar!==''){ $w.=' AND (c.nombre LIKE ? OR c.apellido LIKE ? OR v.nombre LIKE ? OR s.descripcion LIKE ?)'; for($i=0;$i<4;$i++) $a[]='%'.$buscar.'%'; }
$filas=consulta('SELECT s.*, CONCAT(c.nombre," ",c.apellido) cliente,v.nombre servicio,u.nombre empleado'.baseSQL().' WHERE '.$w.' ORDER BY s.fecha DESC,s.id DESC',$a)->fetchAll(PDO::FETCH_ASSOC);
inicio('Solicitudes de servicios'); ?>
<section class="section">
<?php if(isset($_GET['ok'])): ?><p class="aviso">Cambios guardados correctamente.</p><?php endif; ?>
<?php if($rol!=='empleado'): ?><a class="btn" href="crear.php">Nueva solicitud</a><?php endif; ?>
<form class="filtros" method="get"><label>Buscar<input name="buscar" value="<?=h($buscar)?>" placeholder="Cliente, servicio o descripción"></label><label>Estado<select name="estado"><option value="">Todos</option><?php opciones($estados,$estado); ?></select></label><label>Prioridad<select name="prioridad"><option value="">Todas</option><?php opciones($prioridades,$prioridad); ?></select></label><button class="btn">Filtrar</button><a href="listar.php">Limpiar</a></form>
<div class="tabla-scroll"><table><thead><tr><th>ID / Fecha</th><th>Cliente y servicio</th><th>Descripción</th><th>Empleado</th><th>Prioridad</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
<?php foreach($filas as $f): ?><tr><td>#<?=h($f['id'])?><br><?=h($f['fecha'])?></td><td><?=h($f['cliente'])?><br><?=h($f['servicio'])?></td><td><?=h($f['descripcion'])?></td><td><?=h($f['empleado'] ?? 'Sin asignar')?></td><td><span class="etiqueta <?= $f['prioridad']==='Alta'?'alta':'' ?>"><?=h($f['prioridad'])?></span></td><td><?=h($f['estado'])?></td><td class="acciones"><a href="historial.php?id=<?=$f['id']?>">Historial</a><?php if($rol==='administrador'): ?><a href="editar.php?id=<?=$f['id']?>">Editar</a><a href="asignar.php?id=<?=$f['id']?>">Asignar</a><?php endif; ?><?php if($rol!=='cliente'): ?><a href="cambiar_estado.php?id=<?=$f['id']?>">Estado</a><?php endif; ?></td></tr><?php endforeach; ?>
<?php if(!$filas): ?><tr><td colspan="7">No hay solicitudes para mostrar.</td></tr><?php endif; ?></tbody></table></div></section><?php fin(); ?>
