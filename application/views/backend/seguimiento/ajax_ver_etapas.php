<?php foreach($etapas as $etapa):?>
<ol>
    <li>
        <p>Estado: <?= $etapa->pendiente == 0 ? 'Completado' : ($etapa->vencida() ? 'Vencida' :'Pendiente') ?></p>
        <p><?=$etapa->created_at?'Inicio: '.strftime('%c',mysql_to_unix($etapa->created_at)):''?></p>
        <p><?=$etapa->ended_at?'Término: '.strftime('%c',mysql_to_unix($etapa->ended_at)):''?></p>
        <p>Asignado a: <?=!$etapa->usuario_id?'Ninguno':!$etapa->Usuario->registrado?'No registrado':'<abbr class="tt" title="'.$etapa->Usuario->displayInfo().'">'.$etapa->Usuario->displayUsername().'</abbr>'?></p>
        <p><a href="<?=site_url('backend/seguimiento/ver_etapa/'.$etapa->id)?>">Revisar detalle</a></p>
		<?php if (!in_array( 'seguimiento',explode(',',UsuarioBackendSesion::usuario()->rol)) && 
		((count($etapa->Tramite->Etapas)>1  && $etapa->pendiente) || $etapa->isFinal())):?> 
        <p><a href="#" onclick ="return auditarRetrocesoEtapa(<?php echo $etapa->id; ?>)">Retroceder etapa</a></p>
        <?php endif?>
    </li>
</ol>
<?php endforeach; ?>
