<!DOCTYPE html>
<?php require_once(FCPATH."procesos.php");?>
<html lang="es">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?php $this->load->view('head') ?>
</head>

<body>
<ul class="saltar">
<li><a href="#main" tabindex="1">Ir al contenido</a>
</li>
</ul>
<header>
    <div class="container">
	<div class="row">
	    <div class="span2">
		<h1 id="logo"><a href="<?= site_url() ?>"><img src="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.png') ?>" alt="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" /></a></h1>
	    </div>
	    <div class="span4">
		<h1><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : '' ?></h1>
		<p><?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->mensaje : '' ?></p>
	    </div>
	    <div class="offset3 span3">
		<ul id="userMenu" class="nav nav-pills pull-right">
		    <?php if (!UsuarioSesion::usuario()->registrado): ?>
			<li class="dropdown">
			    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Iniciar sesión<span class="caret"></span></a>
			    <ul class="dropdown-menu pull-right">
				<li id="loginView">
				    <?php //if(!$claveunicaOnly=Cuenta::cuentaSegunDominio()->usesClaveUnicaOnly()):?>
				    <div class="simple">
					<div class="wrapper">
				    <form method="post" class="ajaxForm" action="<?= site_url('autenticacion/login_form') ?>">        
					<fieldset>
					    <div class="validacion"></div>
					    <input type="hidden" name="redirect" value="<?= current_url() ?>" />
					    <label for="usuario">Usuario o Correo electrónico</label>
					    <input name="usuario" id="usuario" type="text" class="input-xlarge">
					    <label for="password">Contraseña</label>
					    <input name="password" id="password" type="password" class="input-xlarge">
					    <!--<div id="login_captcha"></div>-->
					    <p class="olvido">
						<a href="<?= site_url('autenticacion/olvido') ?>">¿Olvidaste tu contraseña?</a>  <!--<a href="<?= site_url('autenticacion/registrar') ?>">Registrate aquí</a>-->
					    </p>
					    <button class="btn btn-primary pull-right" type="submit">Ingresar</button>
					</fieldset>
				    </form>
				    </div>
				    </div>
				    <?php //endif ?>
				    <!--
					<div class="claveunica">
					<div class="wrapper">
					<?php //if(!$claveunicaOnly):?><p>O utilice ClaveÚnica</p><?php //endif ?> <a href="<?= site_url('autenticacion/login_openid?redirect=' . current_url()) ?>"><img src="<?= base_url() ?>assets/img/claveunica-medium.png" alt="OpenID"/></a>
					</div>
				    </div>-->
				</li>
			    </ul>
			</li>
		    <?php else: ?>
			<li class="dropdown">
			    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Bienvenido/a <?= UsuarioSesion::usuario()->displayName() ?><span class="caret"></span></a>
			    <ul class="dropdown-menu">
				<?php if (!UsuarioSesion::usuario()->open_id): ?> 
				    <li><a href="<?= site_url('cuentas/editar') ?>"><i class="icon-user"></i> Mi cuenta</a></li>
				<?php endif; ?>
				<?php if (!UsuarioSesion::usuario()->open_id): ?><li><a href="<?= site_url('cuentas/editar_password') ?>"><i class="icon-lock"></i> Cambiar contraseña</a></li><?php endif; ?>
				<li><a href="<?= site_url('autenticacion/logout') ?>"><i class="icon-off"></i> Cerrar sesión</a></li>
			    </ul>
			</li>
		    <?php endif; ?>
		</ul>
	    </div>

	</div>
    </div>
</header>




<div id="main">
    <div class="container">
	<div class="row">
	    <div class="span3">
		    <?php 
			$npendientes=0;
			$nsinasignar=0;
			$nparticipados=0;
			$cont_menu=0;
			if (UsuarioSesion::usuario()->registrado): ?>
			<?php
			$npendientes=count(Doctrine::getTable('Etapa')->findPendientes(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio()));
			$nsinasignar=Doctrine::getTable('Etapa')->findSinAsignar(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio())->count();
			$nparticipados=count(Doctrine::getTable('Tramite')->findParticipadosALL(UsuarioSesion::usuario()->id, Cuenta::cuentaSegunDominio()));
			$revisarLicencia=Doctrine::getTable('Proceso')->canRevisarLicencia(UsuarioSesion::usuario()->id);
			$descargarDocumentosEstudio=Doctrine::getTable('Proceso')->canDesargarDocEstudio(UsuarioSesion::usuario()->id);
			$descargarAvanceEstudio=Doctrine::getTable('Proceso')->canDescargarAvanceEstudio(UsuarioSesion::usuario()->id);
			$revisarDiasAdmin=Doctrine::getTable('Proceso')->canRevisarDiasAdmin(UsuarioSesion::usuario()->id);
			$enviarDoc=Doctrine::getTable('Proceso')->canSolicitarDoc(UsuarioSesion::usuario()->id);
			$pieFirma=Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_PIE_DE_FIRMA");
			$contratoColectivo=Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_NEG_COLECTIVA");
			
			
			$procesoInduccion=Doctrine::getTable('GrupoUsuarios')->cantGruposUsuaros(UsuarioSesion::usuario()->id,"MODULO_INDUCCION");
			$procesoInduccion_resumen = null;
			if($procesoInduccion>1)
				//Verficamos si tiene lo permisos para descargar el estado de avance
				$procesoInduccion_resumen = true;				

			?>
			<ul id="sideMenu" class="nav nav-list">
				<li class="iniciar"><a  href="#" onclick="Slide(['Inicio1','Inicio2','Inicio3'])">&nbsp;&nbsp;&nbsp;Inicio<span style="font-size:20px;top:3px" class="pull-left hidden-xs showopacity glyphicon glyphicon-home"></a></li>
				<li class="<?= isset($sidebar) && $sidebar == 'inbox' ? 'active' : '' ?>"><a id="Inicio1" href="<?= site_url('etapas/inbox') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bandeja de Entrada (<?= $npendientes ?>)&nbsp;&nbsp;&nbsp;<span id="span-inbox" style="font-size:13px;top:3px;left:5px" class="pull-center hidden-xs showopacity glyphicon glyphicon-inbox"></a></a></li>
				<?php if($nsinasignar): ?><li class="<?= isset($sidebar) && $sidebar == 'sinasignar' ? 'active' : '' ?>"><a id="Inicio2" href="<?= site_url('etapas/sinasignar') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sin asignar(<?=$nsinasignar  ?>)&nbsp;&nbsp;&nbsp;  <span id="span-sin_asignar" style="font-size:13px;top:3px" class="pull-center hidden-xs showopacity glyphicon glyphicon-tag"></a></li><?php endif ?>
				<li class="<?= isset($sidebar) && $sidebar == 'participados' ? 'active' : '' ?>"><a id="Inicio3" href="<?= site_url('tramites/participados') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Historial de Trámites (<?= $nparticipados ?>)&nbsp;&nbsp;&nbsp;<span id="span-historial" style="font-size:13px;top:3px" class="pull-center hidden-xs showopacity glyphicon glyphicon-folder-open"></a></li>
			</ul>
		

			<ul id="sideMenu" class="nav nav-list">
				<?php if($revisarLicencia): $cont_menu++; ?><li class="iniciar"><a href="#" onclick="Slide(['Licencia1','Licencia2','Licencia3','Licencia4','Licencia5'])">&nbsp;&nbsp;&nbsp;Licencias  <span style="font-size:20px;top:3px" class="pull-left hidden-xs showopacity glyphicon glyphicon-file"></span></a><?php endif ?>
				<?php if($revisarLicencia): ?><li class="<?= isset($sidebar) && $sidebar == 'agregar_licencia' ? 'active' : '' ?>"><a id="Licencia1" href="<?= site_url('tramites/iniciar/'.proceso_subsidio_id) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Agregar&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:33px" class="pull-center hidden-xs showopacity glyphicon glyphicon-plus"></a></li><?php endif ?>
			     	<?php if($revisarLicencia): ?><li class="<?= isset($sidebar) && $sidebar == 'carga_masiva' ? 'active' : '' ?>"><a id="Licencia2" href="<?= site_url('tramites/iniciar/'.proceso_carga_masiva_id) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Carga Masiva&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;" class="pull-center hidden-xs showopacity glyphicon glyphicon-list-alt"></a></li><?php endif ?>
			     	<?php if($revisarLicencia): ?><li class="<?= isset($sidebar) && $sidebar == 'editar_masiva' ? 'active' : '' ?>"><a id="Licencia3" href="<?= site_url('tramites/iniciar/'.proceso_editar_masiva_id) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edición Masiva&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;" class="pull-center hidden-xs showopacity glyphicon glyphicon-edit"></a></li><?php endif ?>
				<?php if($revisarLicencia): ?><li class="<?= isset($sidebar) && $sidebar == 'licencia' ? 'active' : '' ?>"><a id="Licencia4" href="<?= site_url('licencias/buscador') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:42px" class="pull-center hidden-xs  showopacity glyphicon glyphicon-search"></a></li><?php endif ?>
			     	<?php if($revisarLicencia): ?><li class="<?= isset($sidebar) && $sidebar == 'licencia_pago' ? 'active' : '' ?>"><a id="Licencia5" href="<?= site_url('licencias/pago') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pago&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:52px" class="pull-center hidden-xs showopacity glyphicon glyphicon-briefcase"></a></li><?php endif ?>
			</ul>
			<!-- MODULO DIA ADMINISTRATIVO -->	
			<ul id="sideMenu" class="nav nav-list">
			     <?php if($revisarDiasAdmin): $cont_menu++; ?><li class="iniciar"><a  href="#" onclick="Slide(['DiasAdmin1','DiasAdmin2'])">&nbsp;&nbsp;&nbsp;Días Administrativos<span style="font-size:20px;top:8px" class="pull-left hidden-xs showopacity glyphicon glyphicon-calendar"></span></a></li><?php endif ?>
			     <?php if($revisarDiasAdmin): ?><li class="<?= isset($sidebar) && $sidebar == 'iniciar_admin_days' ? 'active' : '' ?>"><a id="DiasAdmin1" href="<?= site_url('tramites/iniciar/'. proceso_dias_admin_id) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Agregar&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:33px" class="pull-center hidden-xs showopacity glyphicon glyphicon-plus"></a></li><?php endif ?>
			     <?php if($revisarDiasAdmin): ?><li class="<?= isset($sidebar) && $sidebar == 'consultar_admin_days' ? 'active' : '' ?>"><a id="DiasAdmin2" href="<?= site_url('admindays/consultar') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Consultar&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-search"></a></li><?php endif ?>
			</ul>
			<!--MODULO PIE DE FIRMA  -->
			<ul id="sideMenu" class="nav nav-list">
                             <?php if($pieFirma): $cont_menu++; ?><li class="iniciar"><a  href="#" onclick="Slide(['pieFirma''])">&nbsp;&nbsp;&nbsp;Pie de firma<span style="font-size:20px;top:8px" class="pull-left hidden-xs showopacity glyphicon glyphicon-pencil"></span></a></li><?php endif ?>
                             <?php if($pieFirma): ?><li class="<?= isset($sidebar) && $sidebar == 'pie_firma_editar' ? 'active' : '' ?>"><a id="pieFirma" href="<?= site_url('piefirma/editar') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Editar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-cog"></a></li><?php endif ?>
			     <?php if($pieFirma): ?><li class="<?= isset($sidebar) && $sidebar == 'pie_firma_generar' ? 'active' : '' ?>"><a id="pieFirma" href="<?= site_url('piefirma/generar') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Generar&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-download-alt"></a></li><?php endif ?>	
                        </ul>		
			<!-- MODULO CONTRATO COLECTIVO -->
			<ul id="sideMenu" class="nav nav-list">
                           <?php if($contratoColectivo): $cont_menu++; ?><li class="iniciar"><a  href="#" onclick="Slide(['contrato_colectivo'])">&nbsp;&nbsp;&nbsp;Contratos Colectivos<span style="font-size:20px;top:8px" class="pull-left hidden-xs showopacity glyphicon glyphicon-th-list"></span></a></li><?php endif ?>
                           <?php if($contratoColectivo): ?><li class="<?= isset($sidebar) && $sidebar == 'contrato_colectivo' ? 'active' : '' ?>"><a id="contrato_colectivo" href="<?= site_url('contratoColectivo/mostrar') ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descargar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-download-alt"></a></li><?php endif ?>
                        </ul>
			
			<!-- MODULO PROCESO DE INDUCCION -->
                        <ul id="sideMenu" class="nav nav-list">
                           <?php if($procesoInduccion): $cont_menu++; ?><li class="iniciar"><a  href="#" onclick="Slide(['descarga_induccion','agregar_induccion', 'resumen_induccion'])">&nbsp;&nbsp;&nbsp;Proceso de Inducción<span style="font-size:20px;top:8px" class="pull-left hidden-xs showopacity glyphicon glyphicon-folder-open"></span></a></li><?php endif ?>
                           <?php if($procesoInduccion): ?><li class="<?= isset($sidebar) && $sidebar == 'descarga_inducccion' ? 'active' : '' ?>"><a id="descarga_induccion" href="<?= site_url('procesoInduccion/descargar')  ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Descargar&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-download-alt"></a></li><?php endif ?>
			   <?php if($procesoInduccion): ?><li class="<?= isset($sidebar) && $sidebar == 'agregar_inducccion' ? 'active' : '' ?>"><a id="agregar_induccion" href="<?= site_url('tramites/iniciar/'. proceso_induccion_id)  ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Agregar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-plus"></a></li><?php endif ?>
			<?php if($procesoInduccion_resumen): ?><li class="<?= isset($sidebar) && $sidebar == 'resumen_inducccion' ? 'active' : '' ?>"><a id="resumen_induccion" href="<?= site_url('procesoInduccion/resumen')  ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Resumen&nbsp;&nbsp;&nbsp;&nbsp;  <span style="font-size:13px;top:3px;left:28px" class="pull-ccenter hidden-xs showopacity glyphicon glyphicon-th-list"></a></li><?php endif ?>

                        </ul>
		
			
			<!-- MODULO ESTUDIO PSICOSOCIAL -->
			<ul id="sideMenu" class="nav nav-list">     
			    <?php if($descargarDocumentosEstudio or $descargarAvanceEstudio): $cont_menu++; ?><li class="iniciar"><a href="#" onclick="Slide(['Estudio1','Estudio2'])">&nbsp;&nbsp;&nbsp;Protocolo<span style="font-size:20px;top:3px" class="pull-left hidden-xs showopacity glyphicon glyphicon-book"></a></li><?php endif ?>
			    <?php if($descargarDocumentosEstudio): ?><li class="<?= isset($sidebar) && $sidebar == 'documentos_estudio' ? 'active' : '' ?>"><a id="Estudio1" href="<?= site_url('estudios/descargardoc') ?>">&nbsp;&nbsp;&nbsp;Descargar Documentos&nbsp;  <span style="font-size:13px;top:3px" class="pull-center hidden-xs showopacity glyphicon glyphicon-download-alt"></a></li><?php endif ?>
			    <?php if($descargarAvanceEstudio): ?><li class="<?= isset($sidebar) && $sidebar == 'avance_estudio' ? 'active' : '' ?>"><a id="Estudio2" href="<?= site_url('estudios/avance') ?>">&nbsp;&nbsp;&nbsp;Descargar Avance <span style="font-size:13px;top:3px;left:36px" class="pull-center hidden-xs showopacity glyphicon glyphicon-download-alt"></a></li><?php endif ?>
			    <?php if($descargarDocumentosEstudio): ?><li class="<?= isset($sidebar) && $sidebar == 'enviar_doc_estudio' ? 'active' : '' ?>"><a id="Estudio3" href="<?= site_url('estudios/enviardoc') ?>">&nbsp;&nbsp;&nbsp;Enviar&nbsp;  <span style="font-size:13px;top:3px" class="pull-center hidden-xs showopacity glyphicon glyphicon-send"></a></li><?php endif ?>
                        </ul>

			<ul id="sideMenu" class="nav nav-list">
                            <?php if($enviarDoc): $cont_menu++; ?><li class="iniciar"><a href="#" onclick="Slide(['Doc1'])">&nbsp;&nbsp;&nbsp;Documentación<span style="font-size:20px;top:3px" class="pull-left hidden-xs showopacity glyphicon glyphicon-file"></a></li><?php endif ?>
                            <?php if($enviarDoc): ?><li class="<?= isset($sidebar) && $sidebar == 'enviar_doc' ? 'active' : '' ?>"><a id="Doc1" href="<?= site_url('tramites/enviardoc') ?>">&nbsp;&nbsp;&nbsp;Enviar&nbsp;  <span style="font-size:13px;top:3px;left:15px" class="pull-center hidden-xs showopacity glyphicon glyphicon-send"></a></li><?php endif ?>
                        </ul>
			<ul id="sideMenu" class="nav nav-list">
                            <li class="iniciar"><a href="#" onclick="Slide(['Perfil1','Perfil2','Perfil3'])">&nbsp;&nbsp;&nbsp;Mi Perfil<span style="font-size:20px;top:3px" class="pull-left hidden-xs showopacity glyphicon glyphicon-user"></a></li>
                            <li class="<?= isset($sidebar) && $sidebar == 'mi_cuenta' ? 'active' : '' ?>"><a id="Perfil1" href="<?= site_url('cuentas/editar') ?>">&nbsp;&nbsp;&nbsp;Mi Cuenta&nbsp;  <span style="font-size:13px;top:3px;left:80px" class="pull-center hidden-xs showopacity glyphicon glyphicon-user"></a></li>
			    <li class="<?= isset($sidebar) && $sidebar == 'editar_pass' ? 'active' : '' ?>"><a id="Perfil2" href="<?= site_url('cuentas/editar_password') ?>">&nbsp;&nbsp;&nbsp;Cambiar Contraseña&nbsp;  <span style="font-size:13px;top:3px;left:15px" class="pull-center hidden-xs showopacity glyphicon glyphicon-lock"></a></li>
                            <li class="<?= isset($sidebar) && $sidebar == '' ? '' : '' ?>"><a id="Perfil3" href="<?= site_url('autenticacion/logout') ?>">&nbsp;&nbsp;&nbsp;Cerrar Sesión&nbsp;  <span style="font-size:13px;top:3px;left:56px" class="pull-center hidden-xs showopacity glyphicon glyphicon-off"></a></li>
			    <?php endif; ?>
                        </ul>
                    </div>
                    <div class="offset1 span8">
                        <?php $this->load->view('messages')?>
                        <?php $this->load->view($content) ?>
                    </div>
		</ul>
                </div>
            </div>
        </div>

<script>
//funcion para desplegar menus
function Slide(ids, speed = "fast") {
	for (var i=0; i < ids.length; i++)
        	$("#"+ids[i]).slideToggle(speed);
        return false;
}

//funcion que se usara para alinear los iconos. Calcula ordenes de magnitud de un numero positivo o 0
function magnitude_order(n){
	if (n == 0) //logaritmo indefinido
		return 1;
	else
		return Math.trunc(Math.log10(n)) + 1;
}

var npendientes  = "<?php echo $npendientes?>";
var nparticipados = "<?php echo $nparticipados?>";
var nsinasignar = "<?php echo $nsinasignar?>";
var diff = magnitude_order(npendientes)-magnitude_order(nparticipados);

var cont_menu = "<?php echo $cont_menu?>";

//inicialmente, mi perfil no estará desplegado
Slide(["Perfil1","Perfil2","Perfil3"],0);

//alinear íconos de bandeja de entrada con historial (sin que la cantidad de dígitos de los números corran la alineación)
if (diff < 0){
	if (document.getElementById("span-inbox") != null)
		document.getElementById("span-inbox").style = "font-size:13px;top:3px;left:" + (5 + -7.5*diff) + "px";
	if (document.getElementById("span-sin_asignar") != null) 
		document.getElementById("span-sin_asignar").style = "font-size:13px;top:3px;left:" + (60 + -7.5*diff - 7.5*(magnitude_order(nsinasignar)-1)) + "px";
} else {
	if (document.getElementById("span-historial") != null)
		document.getElementById("span-historial").style = "font-size:13px;top:3px;left:" + (1 + 7.5*diff) + "px";
	if (document.getElementById("span-sin_asignar") != null)
		document.getElementById("span-sin_asignar").style = "font-size:13px;top:3px;left:" + (60 + 7.5*diff - 7.5*(magnitude_order(nsinasignar)-1)) + "px";
}

//si hay más de 3 menús, ninguno estará desplegado
if (cont_menu > 3)
	Slide(["Licencia1","Licencia2","Licencia3","Licencia4","DiasAdmin1","DiasAdmin2","Estudio1","Estudio2","Doc1"],0);

</script>

        <footer>
            <div class="area1">
                <div class="container">
                    
                </div>
            </div>
            <div class="area2">
                <div class="container">
                    <div class="row">
                        <div class="span5">
                            <div class="col">
                                <div class="media">
                                    <div class="pull-left">
                                        <img class="media-object" src="<?= base_url() ?>assets/img/ico_cc.png" alt="CC" />
                                    </div>
                                    <div class="media-body">
                                     <!-- <p class="modernizacion"><a href="http://www.modernizacion.gob.cl" target="_blank">Iniciativa de la Unidad de Modernización y Gobierno Digital</a><br/>>
                                            <a class="ministerio" href="http://www.minsegpres.gob.cl" target="_blank">Ministerio Secretaría General de la Presidencia</a></p>
                                        <br/>
                                        <p><a href="http://instituciones.chilesinpapeleo.cl/page/view/simple" target="_blank">Powered by SIMPLE</a></p>
                                    -->
                                        <p class="modernizacion">Iniciativa de la unidad de Automatización de procesos</p>
                                       
                                        <p class="ministerio">Gerencia de personas IST</p>
				    </div>
                                </div>

                            </div>
                        </div>
                        <div class="span3">
                            <div class="col"></div>
                        </div>
                        <div class="span4">
                            &nbsp;
                        </div>
                    </div>
                    <!--  <a href="http://www.gob.cl" target="_blank"><img class="footerGob" src="<?= base_url() ?>assets/img/gobierno_chile.png" alt="Gobierno de Chile" /></a> -->
                </div>
            </div>
        </footer>
       <!-- <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=es"></script>-->
    </body>
</html>
