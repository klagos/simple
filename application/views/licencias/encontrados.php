<h2 style="line-height: 28px;">
    Licencias encontradas   
</h2>
<?php if (count($tramites) > 0): ?>
    <table id="mainTable" class="table">
        <thead>
            <tr>
                <th>Nro</th>
                <th>Rut</th>
                <th>Fecha Inicio</th>
                <th>Fecha Término</th>
                <th>Días</th>
                <th>Estado</th>
                <th>Realizar</th>
		<th>Revisar</th>
            </tr>
        </thead>
        <tbody>		
            <?php
		$rut_param = $this->input->get("trabajador_rut");	
		foreach ($tramites as $t): //$t es el objeto licencia?> 
                <?php
			   $licencia_nro = $t->numero_licencia;
			   $trabajador_rut = $t->rut_trabajador_subsidio;
			   $licencia_fecha_i = $t->fecha_inicio_licencia;
			   $licencia_fecha_t = $t->fecha_termino_licencia;
  			   $licencia_cant_d  =0;
			   if($licencia_fecha_i!='' && $licencia_fecha_t!=''){
				$date_i = new DateTime( $licencia_fecha_i);
				$date_t = new DateTime( $licencia_fecha_t);
				
				$licencia_cant_d = intval($date_t->diff($date_i)->format("%a"))+1;
				
			    }

			    $tareas_completadas = $t->tareas_completadas;		
			    $etapa_id=$t->etapa_id;
			    $etapa_nombre = $t->estado_licencia;
			    
			    $nombre_accion = "Retornar";
			    if ($etapa_nombre == "Ingresada" or $etapa_nombre == "Mantener en pago" or $etapa_nombre == "Proceso de pago") $nombre_accion = "Pagar";
                ?>

                <tr>                     
                    <td class="name"> <?php echo $licencia_nro != '' ? $licencia_nro : 'N/A';?> </td>
                    <td class="name"> <?php echo $trabajador_rut!=''?$trabajador_rut:'N/A'; ?> </td>
                    <td class="name"> <?php echo ($licencia_fecha_i !='')?$licencia_fecha_i:'N/A';?> </td>
                    <td class="name"> <?php echo $licencia_fecha_t!=''?$licencia_fecha_t:'N/A';?> </td>
		    <td class="name"> <?php echo $licencia_cant_d!=0?$licencia_cant_d:'N/A';?> </td>
		    <td class="name"> <?php echo $t->pendiente ? $etapa_nombre : 'Finalizada'  ?></td>
	            <td class="actions" style="text-align:center;"> <?php  if($etapa_id != 0) : ?>
			<a  href="<?= site_url(($rut_param?'etapas/asignar_ejecutar_licencia/' . $etapa_id.'/'.$rut_param:'etapas/asignar_ejecutar/'. $etapa_id)) ?>" class="btn btn-primary preventDoubleRequest"><i class="icon-edit icon-white"></i> <?= $nombre_accion;?></a>
		    <?php else: ?>
			- 
	           <?php endif ?>
	            </td>
                    <td class="actions">
                        <?php $etapas = $t->etapas_tramites ?>
                        <?php if (count($etapas) == 3e4354) : ?>
                            <a href="<?= site_url('etapas/ver_sinpermiso/' . $etapas[0]->id) ?>" class="btn btn-info">Ver historial</a>
			
                        <?php else: ?>
                            <div class="btn-group">
                                <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                                    Ver historial
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($etapas as $e): ?>
                                        <li><a href="<?= site_url('etapas/ver_sinpermiso/' . $e->id) ?>" target="_blank" ><?= $e->Tarea->nombre ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><?= $links ?></p>
<?php else: ?>
    <p>No hay licencias asociadas a esta busqueda.</p>
<?php endif; ?>
