<ul class="breadcrumb">
    <li><a href="<?=site_url('backend/seguimiento')?>">Seguimiento de Procesos</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('backend/seguimiento/index_proceso/'.$etapa->Tramite->proceso_id)?>"><?=$etapa->Tramite->Proceso->nombre?></a> <span class="divider">/</span></li>
    <li class="active"><a href="<?=site_url('backend/seguimiento/ver/'.$etapa->tramite_id)?>">Trámite # <?= $etapa->tramite_id ?></a> <span class="divider">/</span></li></li>
    <li><a href="<?=site_url('backend/seguimiento/ver_etapa/'.$etapa->id)?>"><?=$etapa->Tarea->nombre?></a> <span class="divider">/</span></li>
    <li class="active">Paso <?=$secuencia+1?></li>
</ul>

<div class="row-fluid">
    <div class="span3">
        <div class="well">
            <p>Estado: <?= $etapa->pendiente == 0 ? 'Completado' : ($etapa->vencida() ? 'Vencida' :'Pendiente') ?></p>
            <p><?= $etapa->created_at ? 'Inicio: ' . strftime('%c', mysql_to_unix($etapa->created_at)) : '' ?></p>
            <p><?= $etapa->ended_at ? 'Término: ' . strftime('%c', mysql_to_unix($etapa->ended_at)) : '' ?></p>
            <script>
                $(document).ready(function(){
                    $("#reasignarLink").click(function(){
                        $("#reasignarForm").show();
                        return false;
                    });
                });

            	function auditarRetrocesoEtapa(etapaId) {
            	    $("#auditar").load(site_url + "backend/seguimiento/ajax_auditar_retroceder_etapa/" + etapaId);
            	    $("#auditar").modal();
            	    return false;
            	}
            </script>
            <p>Asignado a: <?=!$etapa->usuario_id?'Ninguno':!$etapa->Usuario->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->Usuario->displayInfo().'">'.$etapa->Usuario->displayUsername().'</abbr>'?> <?php if($etapa->pendiente):?>(<a id="reasignarLink" href="<?=site_url('seguimiento/reasignar')?>">Reasignar</a>)<?php endif?></p>
            <form id="reasignarForm" method="POST" action="<?=site_url('backend/seguimiento/reasignar_form/'.$etapa->id)?>" class="ajaxForm hide">
                <div class="validacion"></div>
                <label>¿A quien deseas asignarle esta etapa?</label>
                <select name="usuario_id">
                    <?php foreach($etapa->getUsuariosFromGruposDeUsuarioDeCuenta() as $u):?>
                    <option value="<?=$u->id?>" <?=$u->id==$etapa->usuario_id?'selected':''?>><?=$u->open_id?$u->nombres.' '.$u->apellido_paterno:$u->usuario?></option>
                    <?php endforeach?>
                </select>
                <button class="btn btn-primary" type="submit">Reasignar</button>
            </form>
            <?php if (!in_array( 'seguimiento',explode(',',UsuarioBackendSesion::usuario()->rol)) && 
				((count($etapa->Tramite->Etapas)>1  && $etapa->pendiente) || $etapa->isFinal())):?> 
            <p><a href="#" onclick ="return auditarRetrocesoEtapa(<?php echo $etapa->id; ?>)">Retroceder etapa</a></p>
            <?php endif?>
        </div>
    </div>
    <div class="span9">
        <form class="form-horizontal dynaForm" onsubmit="return false;">    
            <fieldset>
                <div class="validacion"></div>
                <?php if ($paso):?>
                <legend><?= $paso->Formulario->nombre ?></legend>
                <?php foreach ($paso->Formulario->Campos as $c): ?>
                    <div class="control-group campo" data-id="<?= $c->id ?>" <?= $c->dependiente_campo ? 'data-dependiente-campo="' . $c->dependiente_campo.'" data-dependiente-valor="' . $c->dependiente_valor .'" data-dependiente-tipo="' . $c->dependiente_tipo.'" data-dependiente-relacion="'.$c->dependiente_relacion.'"' : '' ?> style="display: <?= $c->isCurrentlyVisible($etapa->id)? 'block' : 'none'?>;" data-readonly="<?=$paso->modo=='visualizacion' || $c->readonly?>" >
                        <?=$c->displayConDatoSeguimiento($etapa->id,$paso->modo)?>
                    </div>
                <?php endforeach ?>
                <div class="form-actions">
                    <?php if ($secuencia > 0): ?><a class="btn" href="<?= site_url('backend/seguimiento/ver_etapa/' . $etapa->id . '/' . ($secuencia - 1)) ?>"><i class="icon-chevron-left"></i> Volver</a><?php endif; ?>
                    <?php if ($secuencia + 1 < count($etapa->getPasosEjecutables())): ?><a class="btn btn-primary" href="<?= site_url('backend/seguimiento/ver_etapa/' . $etapa->id . '/' . ($secuencia + 1)) ?>">Siguiente</a><?php endif; ?>
                </div>
                <?php else: ?><legend>No tiene formulario</legend><?php endif ?>
            </fieldset>
        </form>
    </div>
</div>



<div id="auditar" class="modal hide fade" >

</div>