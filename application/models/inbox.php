<?php

class Inbox {
	
    public $id;
    public $tarea_id;
    public $tarea_nombre; 
    public $tramite_id;
    public $proceso_nombre;
    public $updated_at;
    public $vencimiento_at;
    public $netapas;
    public $trDatoSeg; //datos segimiento del tramite asociado a la etapa
    public $file;
    public $tareaPreVis; //pre Visualizacion del tramite asociado a la etapa	

    public function __construct($id){
	$this->id = $id;
	$this->file = false;
    }
	
    //Obtiene nombres de las tareas asociadas a las etapas  por el usuario logueado 
    //si usuario_id es NULL, se obtienen nombres de tareas de etapas actuales
    //en caso contrario, de etapas participadas
    public function getTareaNombre($tarea_id) {
	return Doctrine::getTable('Tarea')->find($tarea_id)->nombre;
    }	

    public function getValorDatoSeguimiento($tramite_id) {
        return Doctrine_Query::create()
                        ->from("DatoSeguimiento d, d.Etapa e, e.Tramite t")
                        ->where("t.id = ?   AND e.pendiente=0  ",$tramite_id)
                        ->execute();
    }

    public function getPrevisualizacion(){
	$tarea=Doctrine::getTable('Tarea')->find($this->tarea_id);
        if(!$tarea->previsualizacion)
            return '';

        $r = new Regla($tarea->previsualizacion);

        return $r->getExpresionParaOutput($this->id);
    }


}

