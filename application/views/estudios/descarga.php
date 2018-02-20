<h2 style="line-height: 28px;">
     Documentación Protocolo de Riesgos Psicosociales. 
</h2>
<?php if ($some_doc): ?>

<fieldset>
	<br>
	<div class="control-group campo"  data-readonly="1">
        	<h4>Formatos acta constitución y reunión</h4>                    
	</div>
	<div class="control-group campo"  data-readonly="1"> 
       		<p><a class="btn btn-success" target="_blank" href="<?=site_url($url_formato_acta) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>                   
	</div>
	<br>
	<div class="control-group campo"  data-readonly="1">
                <h4>Instructivo para contestar el cuestionario plataforma online IST</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_instructivo) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>          
        </div>
	<br>
  	<div class="control-group campo"  data-readonly="1">
                <h4>Díptico de difusión para trabajadores</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_instructivo_trabajadores) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p> 
        </div>
	<br>
	 <div class="control-group campo"  data-readonly="1">
                <h4>Registro entrega de códigos</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_registro_entrega_codigos) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p> 
        </div>
        <br>
	<div class="control-group campo"  data-readonly="1">
                <h4>Taller informativo trabajadores email</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_taller_trabajadores_mail) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>
        </div>
        <br>
	<div class="control-group campo"  data-readonly="1">
                <h4>Taller informativo trabajadores presencial</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_taller_trabajadores_presencial) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>
        </div>
        <br>
	
	<div class="control-group campo"  data-readonly="1">
                <h4>Registro de sensibilización y difusión (Formato de registro de capacitación IST)</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_registro_sensibilizacion) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>
        </div>
        <br>
	
	 <div class="control-group campo"  data-readonly="1">
                <h4>Registro de difusión de resultados (Formato de registro de capacitación IST)</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_registro_difusion) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>
        </div>
        <br>

	  <div class="control-group campo"  data-readonly="1">
                <h4>Carta Gantt</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_carta_gantt) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>
        </div>
        <br>


</fieldset>
<?php else: ?>
    <p>No hay documentación asociada.</p>
<?php endif; ?>
