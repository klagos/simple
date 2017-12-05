<form method="POST" class="ajaxForm" action="<?=site_url('cuentas/editar_form')?>">
    <fieldset>
        <legend>Completa la información de tu cuenta</legend>
        <div class="validacion"></div>
        <label>Nombres</label>
        <input type="text" name="nombres" value="<?=$usuario->nombres?>" />
        <label>Apellido Paterno</label>
        <input type="text" name="apellido_paterno" value="<?=$usuario->apellido_paterno?>" />
        <label>Apellido Materno</label>
        <input type="text" name="apellido_materno" value="<?=$usuario->apellido_materno?>" />
        <label>Correo electrónico</label>
        <input type="text" name="email" value="<?=$usuario->email?>" />
        <?php if($usuario->cuenta_id): ?>
        <label class="checkbox"><input type="checkbox" name="vacaciones" value="1" <?=$usuario->vacaciones?'checked':''?> /> ¿Fuera de oficina?</label>
        <?php endif ?>
        <input type="hidden" name="redirect" value="<?=$redirect?>" />
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <button class="btn" type="button" onclick="javascript:history.back()">Cancelar</button>
        </div>
    </fieldset>
</form>