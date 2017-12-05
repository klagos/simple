<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">×</button>
	<h3 id="myModalLabel">Actualizar ID de Trámites</h3>
</div>
<div class="modal-body">
	<form id="formActualizarId" method='POST' class='ajaxForm'
		action="<?= site_url('backend/seguimiento/actualizar_id_tramites_form') ?>">
	<div class='validacion'></div>
	<label>Indique el nuevo Id inicial(Obs: Debe ser mayor a <?=$max?>):</label>
	<input class="span6" name='id' type='text' required />
	</form>

</div>
<div class="modal-footer">
	<button class="btn" data-dismiss="modal">Cancelar</button>
	<a href="#"
		onclick="javascript:$('#formActualizarId').submit();
        return false;"
		class="btn btn-primary">Guardar</a>
</div>