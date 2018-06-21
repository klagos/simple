<h2 style="line-height: 28px;">
    Reporte de beneficios 
    <!--buscador--> 
<br></br>
<table>
<tr>
        <td><h4> Resumen </h4></td>
</tr>
</table>
<form  method="GET" action="<?= site_url('fas/generarconsolidado')?>">
<fieldset>
        <label>Fecha inicial</label>
                <input type="text" class="datepicker" name="fecha_inicial" value="" autocomplete="off"  placeholder="dd-mm-aaaa" required/>	
        <label>Fecha final</label>
		 <input type="text" class="datepicker" name="fecha_final" value="" autocomplete="off" placeholder="dd-mm-aaaa" required/> 
	<div class="form-actions">
		<button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>

<table>
<tr>
        <td><h4> Pagos </h4></td>
</tr>
</table>
<form  method="GET" action="<?= site_url('fas/generarpago')?>">
<fieldset>
	<label>Incluir ayuda escolar</label>
                <select class="select" name="escolar" required>
                        <option value="">Seleccionar</option>
                        <option value="si">Sí</option>
                        <option value="no">No</option>
                </select>
        <div class="form-actions">
                <button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>

 
