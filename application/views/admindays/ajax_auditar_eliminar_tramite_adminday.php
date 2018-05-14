<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3 id="myModalLabel">Eliminación desolicitud</h3>
</div>
<div class="modal-body">
    <form id="formAuditarRetrocesoEtapa" method='POST' class='ajaxForm' action="<?= site_url('admindays/borrar_tramite_adminday/' . $tramite->id .'/'. $requerimiento.'/'. $rut ) ?>">
        <div class='validacion'></div>
        <label>Indique la razón por la cual elimina el día administrativo - Adicional:</label>
        <textarea class="span6" name='descripcion' type='text' required/>
    </form>

</div>
<div class="modal-footer">
    <button class="btn" data-dismiss="modal">Cerrar</button>
    <a href="#" onclick="javascript:$('#formAuditarRetrocesoEtapa').submit();
        return false;" class="btn btn-danger">Eliminar</a>
</div>
