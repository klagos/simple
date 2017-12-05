<div class="row-fluid">
    
    <div class="span3">
        <?php $this->load->view('backend/configuracion/sidebar') ?>
    </div>
    <div class="span9">
        <ul class="breadcrumb">
            <li>
                <a href="<?=site_url('backend/configuracion')?>">Configuración</a> <span class="divider">/</span>
            </li>
            <li class="active">Usuarios</li>
        </ul>
        
        <p><a class="btn btn-success" href="<?=site_url('backend/configuracion/backend_usuario_editar')?>"><i class="icon-file icon-white"></i> Nuevo</a></p>
        
        <table class="table">
            <tr>
                <th>E-Mail</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php foreach($usuarios as $u): ?>
            <tr>
                <td><?=$u->email?></td>
                <td><?=$u->nombre?></td>
                <td><?=$u->apellidos?></td>
                <td><?=$u->rol?></td>
                <td>
                    <a class="btn btn-primary" href="<?=site_url('backend/configuracion/backend_usuario_editar/'.$u->id)?>"><i class="icon-edit icon-white"></i> Editar</a>
                    <a class="btn btn-danger" href="<?=site_url('backend/configuracion/backend_usuario_eliminar/'.$u->id)?>" onclick="return confirm('¿Está seguro que desea eliminar?')"><i class="icon-remove icon-white"></i> Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>