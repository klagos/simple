<div class="front">
    <div class="cabecera">
        <h3><?=$widget->nombre?></h3>
        <a class="config" href="#" onclick="return widgetConfig(this)"><i class="icon-wrench icon-white"></i></a>
    </div>
    <div class="contenido">
        <?=$widget->display()?>
    </div>
</div>
<div class="back">
    <form class="ajaxForm" method="POST" action="<?= site_url('backend/gestion/widget_config_form/'.$widget->id) ?>" data-onsuccess="widgetConfigOk">
        <div class="cabecera">
            <h3>Configuración</h3>
            <button type="submit" class="volver btn btn-mini">ok</button>
        </div>
        <div class="contenido">
            <div class="validacion"></div>
            <label>Nombre</label>
            <input type="text" name="nombre" value="<?=$widget->nombre?>" />
            <?= $widget->displayForm() ?>
            
            <a class="btn btn-danger btn-block" href="<?=site_url('backend/gestion/widget_remove/'.$widget->id)?>" style="margin-top: 100px;" onclick="return confirm('¿Esta seguro que desea eliminar este widget?')"><i class="icon-white icon-trash"></i> Eliminar</a>
        </div>
    </form>   
</div>

