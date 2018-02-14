<?php

class Historial {
	
    public $id;
    public $etapas; //etapas del tramite
    public $pendiente;
    public $proceso_id;
    public $proceso_nombre;
    public $updated_at; 
    public $datoSeg; //datos seguimiento del tramite 
    public $etapas_participadas; //lista de etapas participadas por el usuario logueado
    public $tarea_nombres_part; //lista de nombre de las tareas de las etapas participadas
    public $tarea_nombres_act; //lista de nombre de las tareas de las etapas  actuales
    public $file;

    public function __construct($id){
	$this->id = $id;
	$this->file = false;
    }
	
    //Obtiene nombres de las tareas asociadas a las etapas  por el usuario logueado 
    //si usuario_id es NULL, se obtienen nombres de tareas de etapas actuales
    //en caso contrario, de etapas participadas
    public function getTareaNombres($usuario_id = NULL) {
	$tareasNombre = array();
	$etapas = array();

	if (!$usuario_id)
		 $etapas = $this->getEtapasActuales();
	else
		$etapas = $this->getEtapasParticipadas($usuario_id);
	
	foreach ($etapas as $e){
		$tareasNombre[] = Doctrine::getTable('Tarea')->find($e->tarea_id)->nombre;
	}
	return $tareasNombre;
    }	

    public function getEtapasActuales() {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ? AND e.pendiente=1', $this->id)
                        ->execute();
    }

    public function getEtapasParticipadas($usuario_id) {
        return Doctrine_Query::create()
                        ->from('Etapa e, e.Tramite t')
                        ->where('t.id = ? AND e.usuario_id=?', array($this->id,$usuario_id))
                        ->andWhere('e.pendiente=0')
        		->execute();
    }

    public function getValorDatoSeguimiento() {
        return Doctrine_Query::create()
                        ->from("DatoSeguimiento d, d.Etapa e, e.Tramite t")
                        ->where("t.id = ?   AND e.pendiente=0  ",$this->id)
                        ->execute();
    }


}

