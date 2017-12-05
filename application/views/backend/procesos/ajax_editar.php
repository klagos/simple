<div class="modal-header">
    <button class="close" data-dismiss="modal">×</button>
    <h3>Editar Proceso</h3>
</div>
<div class="modal-body">
    <form id="formEditarProceso" class="ajaxForm" method="POST" action="<?=site_url('backend/procesos/editar_form/'.$proceso->id)?>">
        <div class="validacion"></div>
        <label>Nombre</label>
        <input type="text" name="nombre" value="<?=$proceso->nombre?>" />
        <label>Tamaño de la Grilla</label>
        <input type="text" name="width" value="<?=$proceso->width?>" class="input-small" /> X <input type="text" name="height" value="<?=$proceso->height?>" class="input-small" />
    </form>
</div>
<div class="modal-footer">
    <a href="#" data-dismiss="modal" class="btn">Cerrar</a>
    <a href="#" onclick="javascript:$('#formEditarProceso').submit();return false;" class="btn btn-primary">Guardar</a>
</div>