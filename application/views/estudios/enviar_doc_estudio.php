<h2>Documentos de Estudio</h2>

<?php if (count($procesos) > 0): ?>

<table id="mainTable" class="table" title="Tabla con listado de trámites disponibles">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
	<?php $validProcesos=explode('-',array_procesos_doc_estudio); ?>
        <?php foreach ($procesos as $p): ?>
	  <?php if (in_array($p->id,$validProcesos)): ?> 
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
	  <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else: ?>
<p>No se puede solicitar documentos. </p>
<?php endif; ?>
