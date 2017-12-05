<div class="row-fluid">
    <div class="span3">
        <?php $this->load->view('backend/api/sidebar') ?>
    </div>
    <div class="span9">
        
        
        <form class="ajaxForm" method="post" action="<?=site_url('backend/api/token_form')?>">
            <fieldset>
                <legend>Configurar Código de Acceso</legend>
                <div class="validacion"></div>
                <p>Para poder acceder a la API deberas configrar un código de acceso (token). Si dejas en blanco el token no se podra acceder a la API.</p>
                <label>token</label>
                <input type="text" name="api_token" value="<?=$cuenta->api_token?>" />
                <div class="help-block">Especificar un código aleatorio de máximo 32 caracteres.</div>
                <div class="form-actions">
                    <a class="btn" href="<?=site_url('backend/api')?>">Cancelar</a>
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
                
            </fieldset>
            
        </form>
    </div>
</div>