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
		<th>Eliminar</th>
            </tr>
        </thead>
        <tbody>		
            <?php
		$rut_param = $this->input->get("trabajador_rut");	
		$revisarLicencia   = Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_LICENCIA");
		foreach ($tramites as $t): //$t es el objeto licencia?> 
                <?php
			   $id_tramite 	 = $t->id;
			   $licencia_nro = $t->numero_licencia;
			   $trabajador_rut = $t->rut_trabajador_subsidio;
			  
  			   $rut_cuerpo	   = explode("-", $trabajador_rut)[0]; 
			   $rut_dv	   = explode("-", $trabajador_rut)[1];  
			   if($rut_dv =='K')
				$rut_dv  = 10;
			   
			   $licencia_fecha_i = $t->fecha_inicio_licencia;
			   $licencia_fecha_t = $t->fecha_termino_licencia;
			   $licencia_dia_no_cubierto = false;//$t->dia_no_cubierto;
				
  			   $licencia_cant_d  =0;
			   if($licencia_fecha_i!='' && $licencia_fecha_t!=''){
				$date_i = new DateTime( $licencia_fecha_i);
				$date_t = new DateTime( $licencia_fecha_t);
				
				$licencia_cant_d = intval($date_t->diff($date_i)->format("%a"))+1;
				
			    }

			    $tareas_completadas = $t->tareas_completadas;		
			    $etapa_id=$t->etapa_id;
			    $etapa_nombre = $t->estado_licencia;
			   
			    $delete_tramite = $t->delete_tramite; 
			    $nombre_accion = "Retornar";
			    if ($etapa_nombre == "Ingresada" or $etapa_nombre == "Mantener en pago" or $etapa_nombre == "Proceso de pago") $nombre_accion = "Pagar";
                ?>

                <tr>                     
                    <td class="name"> <?php echo $licencia_nro != '' ? $licencia_nro : 'N/A';?> <?php if( $licencia_dia_no_cubierto) : ?> 
		<span  style="color:yellow" class="glyphicon glyphicon-warning-sign" ></span>	
		<?php else: ?>
                  
                   <?php endif ?>
			 </td>
                    <td class="name"> <?php echo $trabajador_rut!=''?$trabajador_rut:'N/A'; ?> </td>
                    <td class="name"> <?php echo ($licencia_fecha_i !='')?$licencia_fecha_i:'N/A';?> </td>
                    <td class="name"> <?php echo $licencia_fecha_t!=''?$licencia_fecha_t:'N/A';?> </td>
		    <td class="name"> <?php echo $licencia_cant_d!=0?$licencia_cant_d:'N/A';?> </td>
		    <td class="name"> <?php echo $t->pendiente ? $etapa_nombre : 'Finalizada'  ?></td>				
	            <td class="actions" style="text-align:center;"> 
		    <?php  if($etapa_id != 0 && ($revisarLicencia >1) ) : ?>
			<a  href="<?= site_url(($rut_param?'etapas/asignar_ejecutar_licencia/' . $etapa_id.'/'.$rut_param:'etapas/asignar_ejecutar/'. $etapa_id)) ?>" class="btn btn-primary preventDoubleRequest"><i class="icon-edit icon-white"></i> <?= $nombre_accion;?></a>
		    <?php else: ?>
			- 
	           <?php endif ?>
	            </td>
                    <td class="actions">
                        <?php $etapas = $t->etapas_tramites ?>
                        <?php if ($revisarLicencia == 1) : ?>
                            <a href="<?= site_url('etapas/ver_sinpermiso/' . $etapas[0]->id) ?>" class="btn btn-info">Ver detalle</a>
			
                        <?php else: ?>
                            <div class="btn-group">
                                <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                                    Ver detalles
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
		<?php if ($delete_tramite || $revisarLicencia>1) : ?>	
			<td id="<?php echo 'delete_'.$id_tramite; ?>"> 
				<a class= "btn btn-danger" href="#" onclick = "return eliminarTramite(<?=$id_tramite ?>,<?= $rut_cuerpo ?>,<?= $rut_dv ?>);"  ><i class='icon-white icon-trash'></i>
			</td>
		 <?php else: ?>
			<td></td>
		<?php endif ?>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><?= $links ?></p>
<?php else: ?>
    <p>No hay licencias asociadas a esta busqueda.</p>
<?php endif; ?>

<div id="modal" class="modal hide fade" > </div>

<script>

function eliminarTramite(tramiteId,rut,dv){	
        if(dv==10)
                dv='K'
        rut = rut + '-' + dv;
	console.log(site_url + "licencias/ajax_auditar_eliminar_tramite_licencias/" + tramiteId + "/"+rut);   
        $("#modal").load(site_url + "licencias/ajax_auditar_eliminar_tramite_licencias/" + tramiteId + "/"+rut );
	$("#modal").modal();
        return false;
}

</script>
