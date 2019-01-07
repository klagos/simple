<div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>Seleccione el tipo de acción
        <a href="/assets/ayuda/simple/backend/modelamiento-del-proceso/acciones.html#acciones_tipo" target="_blank">
            <span class="glyphicon glyphicon-info-sign" style="font-size: 15px;"></span>
        </a>
    </h3>
</div>
<div class="modal-body">
    <form id="formAgregarAccion" class="ajaxForm" method="POST" action="<?= site_url('backend/acciones/seleccionar_form/'.$proceso_id) ?>">
        <div class="validacion"></div>
        <label>Tipo de acción</label>
        <select name="tipo">
            <option value="enviar_correo">Enviar correo</option>
            <option value="enviar_admin_days">Enviar Admin Days</option>
	    <option value="webservice">Consultar Webservice</option>
           <option value="webservice_put">PUT Webservice</option>
	    <option value="variable">Generar Variable</option>
	    <option value="excel_licencia">Excel Licencia</option>
	    <option value="validar_excel_licencia">Validar Excel Licencia</option>
	    <option value="editar_licencia">Editar Licencia</option>
	    <option value="guardar_finiquito">Guardar Finiquito</option>
        <option value="guardar_licencia">Guardar Licencia</option>
	    <option value="guardar_fas_medico">Guardar FAS Medico</option>
	    <option value="guardar_medico">Guardar Medico</option>
	    <option value="accion_usuario_personal">Guardar Usuarios D.Pers.</option>
	    <option value="accion_enviar_vacation">Guardar Vacation</option>	
	    <option value="accion_guardar_convenio">Guardar Convenio</option>		
	    <option value="accion_guardar_convenio_medico">Guardar Convenio Medico</option>
	    <option value="guardar_trabajador">Guardar Trabajador</option>
        <option value="guardar_rut_annos">Guardar Rut Años</option>
        <option value="accion_correo_masivo">Correo Masivo</option>
        </select>
    </form>
</div>
<div class="modal-footer">
    <a href="#" data-dismiss="modal" class="btn">Cerrar</a>
    <a href="#" onclick="javascript:$('#formAgregarAccion').submit();return false;" class="btn btn-primary">Continuar</a>
</div>
