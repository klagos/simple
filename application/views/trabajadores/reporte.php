<h2 style="line-height: 28px;">
    Reportes de Licencias
    <!--buscador--> 
<br></br>
<tr>
        <td><h4> Descarga de licencias por criterios </h4></td>
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

<tr>
<!--        <td><h4> Toda solicitud no descargada </h4></td>
</tr>
<form  method="GET" action="<?= site_url('trabajadores/reporte_descargar')?>">
<fieldset>
	<input type="hidden" name="downloaded" id="downloaded" value="2">	
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form> 


<tr>
        <td><h4> Descarga masiva </h4></td>
</tr>
<form  method="GET" action="<?= site_url('trabajadores/reporte_masivo')?>">
<fieldset>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form> 

-->
<h2 style="line-height: 28px;">
    Reporte  de Vacaciones
</h2>
<br></br>


		
<tr>
        <td><h4> Provisión del mes </h4></td>
</tr>
<form  method="GET" action="<?= site_url('vacation/provision_descargar')?>">
<fieldset>
        <label>Seleccione el mes</label>
                <select class="select" name="mes" required>
                        <option value="">Seleccionar</option>
                        <option value=3>Abril</option>
                        <option value=4>Mayo</option>
                        <option value=5>Junio</option>
                        <option value=6>Julio</option>
                </select>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>


<tr>
        <td><h4> Reporte por periodos </h4></td>
</tr>
<form  method="GET" action="<?= site_url('vacation/reporte')?>">
<fieldset>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>

	
<tr>
        <td><h4> Reporte de Solicitudes *</h4></td>
	<td><h6>Este informe tarda aproximadamente 20 minutos en generarse</h6></td>
</tr>

<form  method="GET" action="<?= site_url('vacation/reporte_solicitudes')?>">
<fieldset>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>

