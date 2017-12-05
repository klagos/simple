<ul class="breadcrumb">
    <li><a href="<?=site_url('manager')?>">Inicio</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas')?>">Estadisticas</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas')?>">Cuentas</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas/'.$proceso->Cuenta->id)?>"><?=$proceso->Cuenta->nombre?></a> <span class="divider">/</span></li>
    <li class="active"><?=$title?></li>
</ul>

<p style="text-align: right; color: red;">*Estadisticas con respecto a los últimos 30 días.</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Etapa Actual</th>
            <th>Estado</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tramites as $t): ?>
            <tr>
                <td><?= $t->id ?></td>
                <td>
                    <?php
                    $etapas=$t->getEtapasActuales();
                    $etapas_arr=array();
                    foreach($etapas as $e)
                        $etapas_arr[]=$e->Tarea->nombre;
                    echo implode(', ', $etapas_arr);
                    ?>
                </td>
                <td><?= $t->pendiente ? 'Pendiente' : 'Completado' ?></td>
                <td><?= strftime('%c', strtotime($t->updated_at)) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>