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
   
    public function __construct(){
	$this->id = 0;
        $this->rut_trabajador_subsidio = "";
	$this->numero_licencia = 0;
	$this->fechainicio_licencia = "";
	$this->fecha_termino_licencia = "";
	$this->t_pendiente = 0;
	$this->tareas_completadas = 0;
	$this->etapa_id = 0;
	$this->estado_licencia;
    }
	
    public function getEtapasTramites() {
	return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ?', array($this->id))
                        ->andWhere('e.pendiente=0')
                        ->execute();
    }
	
}

