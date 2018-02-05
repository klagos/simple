<h2 style="line-height: 28px;">
    Documentación para estudio de factores psicosociales.   
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
                <h4>Instructivo aplicación cuestionario</h4>
        </div>
        <div class="control-group campo"  data-readonly="1">
                <p><a class="btn btn-success" target="_blank" href="<?=site_url($url_instructivo) ?>"><i class="icon-download-alt icon-white"></i> Descagar</a></p>          
        </div>
	<br>
  	<div class="control-group campo"  data-readonly="1">
                <h4>Instructivo para trabajadores</h4>
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

</fieldset>
<?php else: ?>
    <p>No hay documentación asociada.</p>
<?php endif; ?>
