<?php

class Licencia {
	
    public $id;
    public $rut_trabajador_subsidio;
    public $numero_licencia;
    public $fecha_inicio_licencia;
    public $fecha_termino_licencia;  
    public $t_pendiente;  //si tramite esta pendiente o no
    public $tareas_completadas; 
    public $etapa_id; //id etapa pendiente
    public $estado_licencia; 
    public $etapas_tramites;
   
    public function __construct($id){
	$this->id = $id;
    }
	
    public function getEtapasTramites() {
	return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ?', array($this->id))
                        ->andWhere('e.pendiente=0')
                        ->execute();
    }

}

