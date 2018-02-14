<?php
require_once(FCPATH."procesos.php");
class ProcesoTable extends Doctrine_Table {

    public function findProcesosDisponiblesParaIniciar($usuario_id,$cuenta='localhost',$orderby='id',$direction='desc'){
        $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
        
        $query=Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c, p.Tareas t')
                ->where('p.activo=1 AND t.inicial = 1')
                //Si el usuario tiene permisos de acceso
                //->andWhere('(t.acceso_modo="grupos_usuarios" AND u.id = ?) OR (t.acceso_modo = "registrados") OR (t.acceso_modo = "claveunica") OR (t.acceso_modo="publico")',$usuario->id)
                //Si la tarea se encuentra activa
                ->andWhere('1!=(t.activacion="no" OR ( t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin) )))')
                ->orderBy($orderby.' '.$direction);
        
        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);
        
        $procesos=$query->execute();
              
        //Chequeamos los permisos de acceso
        foreach($procesos as $key=>$p)
            if(!$p->canUsuarioListarlo($usuario_id))
                unset($procesos[$key]);
            
        return $procesos;
    }
	/*Proceso verifica si el usuario puede revisar el historial de Licencias*/
   	public function canRevisarLicencia($usuario_id){
		$procesoLicencia = proceso_subsidio_id;
		$usuario=Doctrine::getTable('Usuario')->find($usuario_id);
		$query=Doctrine_Query::create()
		->from('Proceso p, p.Cuenta c, p.Tareas t')
                ->where('p.activo=1 AND t.inicial = 1')
		->andWhere('p.id=?',$procesoLicencia)
		->andWhere('1!=(t.activacion="no" OR ( t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin) )))');
		$procesos=$query->execute();
			
		//Chequeamos los permisos de acceso
        	foreach($procesos as $key=>$p)
            		if(!$p->canUsuarioListarlo($usuario_id))
                		unset($procesos[$key]);
		return (count($procesos)!=0)?true:false;
	}

	 /*Proceso verifica si el usuario puede revisar el historial de Licencias*/
        public function canDesargarDocEstudio($usuario_id){
                $proceso_estudio = proceso_estudio_comite_id;
                $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
                $query=Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c, p.Tareas t')
                ->where('p.activo=1 AND t.inicial = 1')
                ->andWhere('p.id=?',$proceso_estudio)
                ->andWhere('1!=(t.activacion="no" OR ( t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin) )))');
                $procesos=$query->execute();

                //Chequeamos los permisos de acceso
                foreach($procesos as $key=>$p)
                        if(!$p->canUsuarioListarlo($usuario_id))
                                unset($procesos[$key]);
                return (count($procesos)!=0)?true:false;
        }
	
	public function canRevisarDiasAdmin($usuario_id){
                $proceso_dias_admin = proceso_dias_admin_id;
                $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
                $query=Doctrine_Query::create()
                ->from('Proceso p, p.Cuenta c, p.Tareas t')
                ->where('p.activo=1 AND t.inicial = 1')
                ->andWhere('p.id=?',$proceso_dias_admin)
                ->andWhere('1!=(t.activacion="no" OR ( t.activacion="entre_fechas" AND ((t.activacion_inicio IS NOT NULL AND t.activacion_inicio>NOW()) OR (t.activacion_fin IS NOT NULL AND NOW()>t.activacion_fin) )))');
                $procesos=$query->execute();

                //Chequeamos los permisos de acceso
                foreach($procesos as $key=>$p)
                        if(!$p->canUsuarioListarlo($usuario_id))
                                unset($procesos[$key]);
                return (count($procesos)!=0)?true:false;
        }


}
