<h2 style="line-height: 28px;">
    Reportes de licencias
    <!--buscador--> 
<br></br>
<!--
<tr>
        <td><h4> Descarga por criterios </h4></td>
</tr>  
<form  method="GET" action="<?= site_url('licencias/reporte_descargar')?>">
<fieldset>
        <label>Fecha inicial</label>
                <input type="text" class="datepicker" name="fecha_inicial" value=""  autocomplete="off" placeholder="dd-mm-aaaa" />	
        <label>Fecha final</label>
		<input type="text" class="datepicker" name="fecha_termino" value=""  autocomplete="off" placeholder="dd-mm-aaaa" />
	<div class="form-actions">
		<button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>
-->
<tr>
        <td><h4> Toda solicitud no descargada </h4></td>
</tr>
<form  method="GET" action="<?= site_url('licencias/reporte_descargar')?>">
<fieldset>

	<label>Fecha final</label>
                <input type="text" class="datepicker" name="fecha_termino" value=""  autocomplete="off" placeholder="dd-mm-aaaa" />

	<input type="hidden" name="downloaded" id="downloaded" value="2">	
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form> 


<tr>
        <td><h4> Descarga masiva </h4></td>
</tr>
<form  method="GET" action="<?= site_url('licencias/reporte_masivo')?>">
<fieldset>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form> 
