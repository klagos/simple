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
            <?php $registros=false; ?>
            <?php foreach ($tramites as $t): ?>

                <?php
                      
                     $file =false; 

			    $licencia_nro ='';
			    $trabajdor_rut='';
                            $licencia_fecha_i='';
                            $licencia_fecha_t='';
                            $licencia_cant_d=0;
                        
        
                            foreach ($t->getValorDatoSeguimiento() as $tra_nro){
                               if($tra_nro->nombre == 'numero_licencia')
                                        $licencia_nro = $tra_nro->valor;

                               if($tra_nro->nombre == 'rut_trabajador_subsidio')
                                        $trabajador_rut = $tra_nro->valor;
                                
                               if($tra_nro->nombre == 'fecha_inicio_licencia')
                                        $licencia_fecha_i = $tra_nro->valor;
                                
                               if($tra_nro->nombre == 'fecha_termino_licencia')
                                        $licencia_fecha_t = $tra_nro->valor;            
                                                
                            }
			    if($licencia_fecha_i!='' && $licencia_fecha_t!=''){
				$date_i = new DateTime( $licencia_fecha_i);
				$date_t = new DateTime( $licencia_fecha_t);
				
				$licencia_cant_d = $date_t->diff($date_i)->format("%a");
				
			    }
			    $etapa_id=0;
			    $etapa_nombre='';
			    $etapas_array=array();
			    $etapas_array_n=array();
			    foreach ($t->getEtapasActuales() as $e){
				$etapas_array[] = $e->id;
				$etapas_array_n[] = $e->Tarea->nombre;
			    }
			    $etapa_id=implode(', ', $etapas_array);
			    $etapa_nombre=implode(', ', $etapas_array_n);
                ?>

                <tr>
                    <!-- <td><?= $t->id ?></td>-->                  
                    <td class="name"> <?php echo $licencia_nro != '' ? $licencia_nro : 'N/A';?> </td>
                    <td class="name"> <?php echo $trabajador_rut!=''?$trabajador_rut:'N/A'; ?> </td>
                    <td class="name"> <?php echo ($licencia_fecha_i !='')?$licencia_fecha_i:'N/A';?> </td>
                    <td class="name"> <?php echo $licencia_fecha_t!=''?$licencia_fecha_t:'N/A';?> </td>
		    <td class="name"> <?php echo $licencia_cant_d!=0?$licencia_cant_d:'N/A';?> </td>
		    <td class="name"> <?php echo (count($t->getTareasCompletadas())==3)?'Completada':((count($t->getTareasCompletadas())==2)?'Pagada':'Ingresada'); ?></td>
	            <td class="actions" style="text-align:center;"> <?php  if($etapa_id != 0) : ?>
			<a  href="<?= site_url('etapas/ejecutar/' . $etapa_id) ?>" class="btn btn-primary preventDoubleRequest"><i class="icon-edit icon-white"></i> <?=   (count($t->getTareasCompletadas())==1)?'Pagar':'Retornar';?></a>
		    <?php else: ?>
			- 
	           <?php endif ?>
	            </td>
                    <td class="actions">
                        <?php $etapas = $t->getEtapasParticipadas(UsuarioSesion::usuario()->id) ?>
                        <?php if (count($etapas) == 3e4354) : ?>
                            <a href="<?= site_url('etapas/ver/' . $etapas[0]->id) ?>" class="btn btn-info">Ver historial</a>
                        <?php else: ?>
                            <div class="btn-group">
                                <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                                    Ver historial
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($etapas as $e): ?>
                                        <li><a href="<?= site_url('etapas/ver/' . $e->id) ?>"><?= $e->Tarea->nombre ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay licencias asociadas a esta busqueda.</p>
<?php endif; ?>
