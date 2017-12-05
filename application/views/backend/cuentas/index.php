<div class="row-fluid">


    <div class="span12">

        
        <?php if($this->session->flashdata('message')):?>
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <?=$this->session->flashdata('message')?></div>
        <?php endif?>

        <form class="ajaxForm" method="post" action="<?= site_url('backend/cuentas/cuenta_form/' . (isset($usuario)?$usuario->id:'')) ?>">
            <fieldset>
                <legend>Cambiar contraseña</legend>
                <div class="validacion"></div>
                <label>Contraseña</label>
                <input type="password" name="password" value=""/>
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirm" value=""/>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a class="btn" href="#" onclick="javascript:history.back()">Cancelar</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>