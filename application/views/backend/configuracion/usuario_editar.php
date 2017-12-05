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
                <a href="<?= site_url('backend/configuracion/usuarios') ?>">Usuarios</a> <span class="divider">/</span>
            </li>
            <li class="active"><?= isset($usuario) ?$usuario->usuario:'Crear' ?></li>
        </ul>

        <form class="ajaxForm" method="post" action="<?= site_url('backend/configuracion/usuario_editar_form/' . (isset($usuario)?$usuario->id:'')) ?>">
            <fieldset>
                <legend>Editar usuario</legend>
                <div class="validacion"></div>
                <label>Nombre de Usuario</label>
                <input type="text" name="usuario" class="input-xxlarge" value="<?=isset($usuario)?$usuario->usuario:''?>" <?=  isset($usuario)?'disabled':''?>/>
                <label>Contraseña</label>
                <input type="password" name="password" class="input-xxlarge" value=""/>
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirm" class="input-xxlarge" value=""/>
                <label>Nombres</label>
                <input type="text" name="nombres" class="input-xxlarge" value="<?=isset($usuario)?$usuario->nombres:''?>"/>
                <label>Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="input-xxlarge" value="<?=isset($usuario)?$usuario->apellido_paterno:''?>"/>
                <label>Apellido Materno</label>
                <input type="text" name="apellido_materno" class="input-xxlarge" value="<?=isset($usuario)?$usuario->apellido_materno:''?>"/>
                <label>Correo electrónico</label>
                <input type="text" name="email" class="input-xxlarge" value="<?=isset($usuario)?$usuario->email:''?>"/>
                <label class="checkbox"><input type="checkbox" name="vacaciones" value="1" <?=isset($usuario) && $usuario->vacaciones?'checked':''?> /> ¿Fuera de oficina?</label>    
                <label>Grupos de Usuarios</label>
                <select class="chosen" name="grupos_usuarios[]" class="input-xxlarge" data-placeholder="Seleccione los grupos de usuarios" multiple>
                    <?php foreach($grupos_usuarios as $g): ?>
                    <option value="<?=$g->id?>" <?=isset($usuario) && $usuario->hasGrupoUsuarios($g->id)?'selected':''?>><?=$g->nombre?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                    <a class="btn" href="<?=site_url('backend/configuracion/usuarios')?>">Cancelar</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>