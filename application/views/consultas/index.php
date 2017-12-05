<!DOCTYPE html>
<html lang="es">
    <head>
    	<title>Consultas de Documentos</title>
  	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="<?= base_url() ?>assets/css/bootstrap.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/css/responsive.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/js/bootstrap-datepicker/css/datepicker.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/css/common.css" rel="stylesheet">
        <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
        <script src="<?= base_url() ?>assets/js/jquery-ui/js/jquery-1.8.3.js"></script>
        <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>              
        <script src="<?= base_url() ?>assets/js/jquery.chosen/chosen.jquery.min.js"></script>
        <!--<script src="<?= base_url() ?>assets/js/common.js"></script> -->
        <script src="<?= base_url() ?>assets/js/helpdoc/jquery_makinput.js"></script> 
        <script src="<?= base_url() ?>assets/js/helpdoc/jquery.numeric.js"></script>		 
        <link href="<?= base_url() ?>assets/js/helpdoc/introjs.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/js/helpdoc/demo.css" rel="stylesheet">
        <link href="<?= base_url() ?>assets/js/helpdoc/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="<?= base_url() ?>assets/img/favicon.png">
        <script src="<?= base_url() ?>assets/js/jquery.jsplumb/jquery.jsPlumb-1.3.16-all-min.js" type="text/javascript"></script>
        <script src="<?= base_url() ?>assets/js/grafico-consulta.js"></script>
        <style>
            #diagramContainer {
                width: 100%;
                margin-bottom: 10px;
            }
            .tarea {
                width: 30px;
                height: 30px;
                -moz-border-radius: 50%;
                -webkit-border-radius: 50%;
                border-radius: 50%;
                display: inline-block;
                text-align: center;
                padding-top: 13px;
                padding-left:6px;
                padding-right: 6px;
                margin: 5px;
                font-size: 15px;
                font-weight: bold;
                color: white;
            }
            .info {
                width: 20px;
                height: 20px;
                -moz-border-radius: 50%;
                -webkit-border-radius: 50%;
                border-radius: 50%;
            }
            #puntoFinal {
                border: 1.5px dashed #333;
                height: 0;
                width: 75px;
                position: relative;
                left: 5px;
                top: 25px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">                
                <header>
                    <div class="container">
                        <div class="row">
                            <div class="span2">
                                <h1 id="logo"><a href="<?= site_url() ?>"><img src="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->logoADesplegar : base_url('assets/img/logo.png') ?>" alt="<?= Cuenta::cuentaSegunDominio()!='localhost' ? Cuenta::cuentaSegunDominio()->nombre_largo : 'Simple' ?>" /></a></h1>
                            </div>
                            <div class="span10">
                                <h1 style="font-size:22px;"><?= $titulo ?></h1>
                                <p style="font-size:14px;">
                                    <i class="icon icon-home"></i> <?= Cuenta::cuentaSegunDominio()->nombre_largo ?>
                                </p>
                                <!--
                                <p style="font-size:14px;">
                                    <i class="icon icon-random"></i> Trámites: Seguimiento de Expedientes oficiales (Notas y documentos ingresados por mesa de entrada)
                                </p>
                                -->
                                <p style="font-size:10px; text-shadown:#CCCCCC 2px 2px 2px 2px;">
                                    <i class="icon icon-info-sign"></i> A través de esta pequeña y simple aplicación puedes dar seguimiento a cualquier trámite que se ha ingresado
                                </p>                                  
                            </div>
                            <div class="offset3 span3"></div>
                        </div>
                    </div>
                </header>
                
                <div id="main">
                    <div class="container">
                        <div class="row">
                            <div class="span12">
                                <p></p>
                                <!--
                                <p style="font-size:14px;">Ingrese los datos de tu documento recepcionado    
                                    <a href="javascript:void(0);" title="Ayuda" class="btn" onclick="javascript:introJs().start();"><i class="icon icon-question-sign"></i></a>                                           
                                </p> 
                                -->
                		<form  method="POST" action="<?= current_url() ?>">
                                    <?=form_error('fecha')?>
                                    <?=form_error('nrotramite')?>
                                    <div class="campo control-group">
                                        <label class="control-label" style="color:#465f6e;"><i class="icon icon-chevron-right"></i>Nro. de Trámite</label>
                                        <div class="controls" > 
                                            <input name="nrotramite" id="nrotramite" type="text" value="<?= $nrotramite ?>"
                                                data-step="1" data-intro="Ingresá el Nro. de la Mesa de Entrada  <img src='<?= base_url() ?>assets/js/helpdoc/ayu1.png'/>" data-position='center'>
                                        </div>
                			
                                        <label class="control-label" style="color:#465f6e;"><i class="icon icon-chevron-right"></i>Ingrese el año que fue ingresado el trámite</label>
                                        
                                        <div class="controls input-append date form_date" data-date="" data-date-format="yyyy" data-link-field="fecha1" data-link-format="yyyy">
                                            <input type="text" placeholder="aaaa" value="<?=$fecha?>" name="fecha" id="fecha" onkeypress="validarDatos(event,'#fecha','#buscar');"
                                            data-step="2" data-intro="Ingresá la Fecha de Entrada <img src='<?= base_url() ?>assets/js/helpdoc/ayu2.png'/>" data-position='center' readonly=""/>                                            
                                        <span class="add-on"><i class="icon-th"></i></span>
                                        </div>
                                            <input size="16" type="hidden" value="<?=$fecha?>" name="fecha1" id="fecha1">
                                        <div>
                                           <button class="btn btn-primary" type="submit" id="buscar" name="buscar" 
                                                   data-step="3" data-intro="Presioná el botón, para dar seguimiento a su documento" data-position='right'>Buscar</button>
                                        </div>
                                    </div> 
                                </form>
                                
                                <?php $indice=1; if (count($tareas) > 0 && $tareas > 0):  ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            tareas=<?= json_encode($tareas); ?>;
                                            graficar(tareas);
                                       });
                                    </script>
                                    <div id="diagramContainer"><div id="dibujo"></div></div>
                                    <div class="responsive">
                                        <div class="panel panel-default">
                                            <div class="panel-body" style="margin-left: 100px;">
                                                <div class="row-fluid">
                                                    <div class="span2">
                                                        <div class="info" style="background: green; float: left;"></div>
                                                        <div style="margin-left: 30px;"><b>Completados</b></div>
                                                    </div>
                                                    <div class="span2">
                                                        <div class="info" style="background: goldenrod; float: left;"></div>
                                                        <div style="margin-left: 30px;"><b>Pendientes</b></div>
                                                    </div>
                                                </div>
                                                <div class="row-fluid">
                                                    <b>Observación:</b> Para ver más detalles; realizar click en cada gráfico.
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                        <table class="table table-striped table-condensed table-bordered table-hover" >
                                            <thead>
                                                <th>Nro.</th>	                            		
                                                <th>Pasos del Trámite</th>	                            		
                                                <th>Finalizado en Fecha</th>
                                                <th>Responsable</th>
                                                <th>Estado</th>	
                                            </thead>
                                            <tbody>	           												
                                                <?php foreach ($tareas as $d): ?>
                                                    <tr>
                                                        <td style="text-align:center;"><?= $indice++ ?></td>                            		 
                                                        <td><?= $d['tarea_nombre'] ?></td>                    					
                                                        <td><?= $d['termino'] ?></td>
                                                        <td><?= $d['usuario'] ?></td>
                                                        <td style="text-align:center;"><?= $d['estado'] ?></td>	            						    
                                                    </tr> 
                                                <?php endforeach; ?> 				                            
                                            </tbody>
                                    	</table>
                                    </div>	
                            	<?php else: ?> <?=$vacio?> <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>              
            </div>
        </div> 

        <footer>
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
                                      <!-- <p class="modernizacion"><a href="http://www.modernizacion.gob.cl" target="_blank">Iniciativa de la Unidad de Modernización y Gobierno Digital</a><br/>
                                            <a class="ministerio" href="http://www.minsegpres.gob.cl" target="_blank">Ministerio Secretaría General de la Presidencia</a></p>
                                        <br/>
                                        <p><a href="http://instituciones.chilesinpapeleo.cl/page/view/simple" target="_blank">Powered by SIMPLE</a></p>
                                    -->
					<p class="modernizacion">Iniciativa de la unidad de Automatizaci�n de procesos</p>
					</br>
					<p class="ministerio">Gerencia de personas</p>
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
                    <a href="http://www.gob.cl" target="_blank"><img class="footerGob" src="<?= base_url() ?>assets/img/gobierno_chile.png" alt="Gobierno de Chile" /></a>
                </div>
            </div>
        </footer>
        <script type="text/javascript" src="<?= base_url() ?>assets/js/helpdoc/datepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script type="text/javascript" src="<?= base_url() ?>assets/js/helpdoc/datepicker/bootstrap-datetimepicker.es.js" charset="UTF-8"></script> 
        <script src="<?= base_url() ?>assets/js/helpdoc/scripts.js"></script>
        <script src="<?= base_url() ?>assets/js/helpdoc/intro.js"></script>       
    </body>
</html>















