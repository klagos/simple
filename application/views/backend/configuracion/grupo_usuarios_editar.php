<div class="row-fluid">

    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?= site_url('backend/configuracion') ?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="<?= site_url('backend/configuracion/grupos_usuarios') ?>">Grupos de Usuarios</a> <span class="divider">/</span>
            </li>
            <li class="active"><?= isset($grupo_usuarios) ?$grupo_usuarios->nombre:'Crear' ?></li>
        </ul>

        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/grupo_usuarios_editar_form/' . (isset($grupo_usuarios)?$grupo_usuarios->id:'')) ?>">
            <fieldset>
                <legend>Editar grupo de usuario</legend>
                <div class="validacion"></div>
                <?php if(isset($grupo_usuarios)):?>
                <label>Id</label>
                <input type='text' class="input-xxlarge" value='<?=$grupo_usuarios->id?>' disabled />
                <?php endif ?>
                <label>Nombre</label>
                <input type="text" class="input-xlarge" name="nombre" value="<?=isset($grupo_usuarios)?$grupo_usuarios->nombre:''?>"/>
                <label>Este grupo lo componen</label>
                <select id="select-usuarios" class="input-xlarge" name="usuarios[]" data-placeholder="Seleccione los usuarios" multiple>
                    <?php foreach($grupo_usuarios->Usuarios as $g): ?>
                        <option value="<?=$g->id?>" selected><?=$g->displayUsername(true)?></option>
                    <?php endforeach; ?>
                </select>
                <script>
                    $(document).ready(function(){
                        $("#select-usuarios").select2({
                            ajax: {
                                url: site_url+"backend/configuracion/ajax_get_usuarios",
                                cache: true,
                                data: function(params) {
                                    return {query: params.term};
                                },
                                processResults: function(data,page){
                                    return {
                                        results: data
                                    }
                                }
                            }

                        });
                    })
                </script>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a class="btn" href="<?=site_url('backend/configuracion/grupos_usuarios')?>">Cancelar</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>