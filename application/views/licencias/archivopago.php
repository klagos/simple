<h2 style="line-height: 28px;">
    Archivo de pago
</h2>
<?php if ($contador > 0): ?>
<form  method="GET" action="<?= site_url('licencias/generarexcel/'.$fecha.'/'.$tipo)?>">
<fieldset>
        <div class="form-actions">
                <button class="btn btn-success" type="submit">Descargar excel</button>
        </div>
</fieldset>
</form>
<?php else: ?>
    <p>No hay licencias asociadas a esta busqueda.</p>
<?php endif; ?>
