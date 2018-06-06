<h2 style="line-height: 28px;">
    Reporte de pago de Licencias
    <!--buscador--> 
<br></br>  
<form  method="GET" action="<?= site_url('fas/generarconsolidado')?>">
<fieldset>
        <label>Fecha inicial</label>
                <input type="text" class="datepicker" name="fecha_inicial" value="" placeholder="dd-mm-aaaa" required/>	
        <label>Fecha final</label>
		 <input type="text" class="datepicker" name="fecha_final" value="" placeholder="dd-mm-aaaa" required/> 
	<div class="form-actions">
		<button class="btn btn-primary" type="submit">Generar</button>
        </div>
</fieldset>
</form>
 
