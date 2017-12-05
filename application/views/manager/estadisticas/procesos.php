<ul class="breadcrumb">
    <li><a href="<?=site_url('manager')?>">Inicio</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas')?>">Estadisticas</a> <span class="divider">/</span></li>
    <li><a href="<?=site_url('manager/estadisticas/cuentas')?>">Cuentas</a> <span class="divider">/</span></li>
    <li class="active"><?=$title?></li>
</ul>

<p style="text-align: right; color: red;">*Estadisticas con respecto a los últimos 30 días.</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Proceso</th>
            <th>Nº de Trámites</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($procesos as $p): ?>
        <tr>
            <td><a href="<?=site_url('manager/estadisticas/cuentas/'.$p->cuenta_id.'/'.$p->id)?>"><?=$p->nombre?></a></td>
            <td><?=$p->ntramites?></td>
        </tr>
        <?php endforeach; ?>
        
        <tr class="success">
            <td>Total</td>
            <td><?=$ntramites?></td>
        </tr>
    </tbody>
</table>