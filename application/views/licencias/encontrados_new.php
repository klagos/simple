<h2 style="line-height: 28px;">
    Licencias encontradas   
</h2>
<?php if (count($tramites) > 0): ?>

    <table id="mainTable" class="table"   >
        <thead>
            <tr>
                <th style="width:16%">Nro</th>
                <th style="width:21%">Rut</th>
		<th style="width:30%">Nombre</th>
                <th style="width:20%">Fecha Inicio</th>
                <th style="width:20%">Fecha Término</th>
                <th style="width:10%" >Días</th>
                <th  style="width:18%">Estado</th>
                <th style="width:22%">Realizar</th>
		<th style="width:22%">Revisar</th>
		<th style="width:15%">Eliminar</th>
            </tr>
        </thead>
        <tbody>		
            <?php
		$rut_param = $this->input->get("trabajador_rut");
		foreach ($tramites as $t):?> 
                <?php
			   $id_tramite 	 = $t->id;
			   $licencia_nro = $t->numero_licencia;
			   $trabajador_rut = $t->rut_trabajador_subsidio;
			  
  			   $rut_cuerpo	   = explode("-", $trabajador_rut)[0]; 
			   $rut_dv	   = explode("-", $trabajador_rut)[1];  
			   if($rut_dv =='K')
				$rut_dv  = 10;
			   
			   $trabajador_nombre = $t->nombre_completo_trabajador_subsidio;
			   $licencia_fecha_i = $t->fecha_inicio_licencia;
			   $licencia_fecha_t = $t->fecha_termino_licencia;
			   $licencia_dia_no_cubierto = false;//$t->dia_no_cubierto;
				
  			   $licencia_cant_d  = $t->dias;
			   $estado = $t->estado_licencia;

			   $tareas_completadas = $t->tareas_completadas;		
			   
			   $pendiente=$t->pendiente;
		
			   $etapa_nombre = $t->estado_licencia;
			   
			   $delete_tramite = $t->delete; 
			   $nombre_accion  = $t->accion;
                ?>

                <tr>                     
                    <td class="name"> <?php echo $licencia_nro != '' ? $licencia_nro : 'N/A';?> <?php if( $licencia_dia_no_cubierto) : ?> 
		<span  style="color:yellow" class="glyphicon glyphicon-warning-sign" ></span>	
		<?php else: ?>
                  
                   <?php endif ?>
			 </td>
                    <td class="name" > <?php echo $trabajador_rut!=''?$trabajador_rut:'N/A'; ?> </td>
		    <td class="name" > <?php echo $trabajador_nombre ?></td>
                    <td class="name" > <?php echo ($licencia_fecha_i !='')?$licencia_fecha_i:'N/A';?> </td>
                    <td class="name" > <?php echo $licencia_fecha_t!=''?$licencia_fecha_t:'N/A';?> </td>
		    <td class="name"> <?php echo $licencia_cant_d!=0?$licencia_cant_d:'N/A';?> </td>
		    <td class="name"> <?php echo $estado  ?></td>				
	            <td class="actions" style="text-align:center;"> 
		    <?php  if($pendiente != 0 ) : ?>
			<a  href="<?= site_url(($rut_param?'etapas/asignar_ejecutar_licencia/' . $pendiente.'/'.$rut_param:'etapas/asignar_ejecutar/'. $pendiente)) ?>" class="btn btn-primary preventDoubleRequest"><i class="icon-edit icon-white"></i> <?= $nombre_accion;?></a>
		    <?php else: ?>
			- 
	           <?php endif ?>
	            </td>
                    <td class="actions">
                        <?php $etapas = $t->etapas_tramites ?>
                        	
                            <div class="btn-group">
                                <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">Detalles
                                    
                                    <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($etapas as $e): ?>
                                        <li><a href="<?= site_url('etapas/ver_sinpermiso/' . $e->id) ?>" target="_blank" ><?= $e->Tarea->nombre ?></a></li>
                                    <?php endforeach ?>
                                </ul>
                            </div> 
                    </td>
		<?php if ($delete_tramite) : ?>	
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
