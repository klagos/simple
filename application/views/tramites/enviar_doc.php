<h2>Solicitud de Documentos</h2>

<?php if (count($procesos) > 0): ?>

<table id="mainTable" class="table" title="Tabla con listado de trámites disponibles">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
	<?php 
		$validProcesos=explode('-',array_procesos_solicitud_doc); 
		$procesos_solic_doc = [13];
		foreach ($procesos as $p){
			if (in_array($p->id,$validProcesos)){
				if(strpos($p->nombre, 'ENERO')) $procesos_solic_doc[0]=$p;
				if(strpos($p->nombre, 'FEBRERO')) $procesos_solic_doc[1]=$p;
				if(strpos($p->nombre, 'MARZO')) $procesos_solic_doc[2]=$p;
				if(strpos($p->nombre, 'ABRIL')) $procesos_solic_doc[3]=$p;
				if(strpos($p->nombre, 'MAYO')) $procesos_solic_doc[4]=$p;
				if(strpos($p->nombre, 'JUNIO')) $procesos_solic_doc[5]=$p;
				if(strpos($p->nombre, 'JULIO')) $procesos_solic_doc[6]=$p;
				if(strpos($p->nombre, 'AGOSTO')) $procesos_solic_doc[7]=$p;
				if(strpos($p->nombre, 'SEPTIEMBRE')) $procesos_solic_doc[8]=$p;
				if(strpos($p->nombre, 'OCTUBRE')) $procesos_solic_doc[9]=$p;
				if(strpos($p->nombre, 'NOVIEMBRE')) $procesos_solic_doc[10]=$p;
				if(strpos($p->nombre, 'DICIEMBRE')) $procesos_solic_doc[11]=$p;
				if(strpos($p->nombre, 'PSICOSOCIALES')) $procesos_solic_doc[12]=$p;
			}
		}

	?>
        <?php for ($i = 0 ; $i<13 ;$i++):
		$p = $procesos_solic_doc[$i];
	 ?>
	  <?php ?> 
            <tr>
                <td class="name">
                    <?php if($p->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)):?>
                    <a class="preventDoubleRequest" href="<?=site_url('tramites/iniciar/'.$p->id)?>"><?= $p->nombre ?></a>
                    <?php else: ?>
                        <?php if($p->getTareaInicial()->acceso_modo=='claveunica'):?>
                        <a href="<?=site_url('autenticacion/login_openid')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>"><?= $p->nombre ?></a>
                        <?php else:?>
                        <a href="<?=site_url('autenticacion/login')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>"><?= $p->nombre ?></a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
                <td class="actions">
                    <?php if($p->canUsuarioIniciarlo(UsuarioSesion::usuario()->id)):?>
                    <a href="<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary preventDoubleRequest"><i class="icon-file icon-white"></i> Iniciar</a>
                    <?php else: ?>
                        <?php if($p->getTareaInicial()->acceso_modo=='claveunica'):?>
                        <a href="<?=site_url('autenticacion/login_openid')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>"><img style="max-width: none;" src="<?=base_url('assets/img/claveunica-medium.png')?>" alt="ClaveUnica" /></a>
                        <?php else:?>
                        <a href="<?=site_url('autenticacion/login')?>?redirect=<?=site_url('tramites/iniciar/'.$p->id)?>" class="btn btn-primary"><i class="icon-white icon-off"></i> Autenticarse</a>
                        <?php endif ?>
                    <?php endif ?>
                </td>
            </tr>
	  <?php  ?>
        <?php endfor; ?>
    </tbody>
</table>

<?php else: ?>
<p>No se puede solicitar documentos. </p>
<?php endif; ?>
