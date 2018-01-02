<?php

class TramiteTable extends Doctrine_Table {
    

    //busca los tramites donde el $usuario_id ha participado
    public function findParticipados($usuario_id,$cuenta='localhost',$limite,$inicio,$datos,$result){        
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u ')
                //->from('DatoSeguimiento d, d.Etapa ex, ex.Tramite t, t.Etapas e, t.Proceso.Cuenta c, e.Usuario u')               
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->having('COUNT(t.id) > 0')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy('t.updated_at desc')
                ->limit($limite)
                ->offset($inicio);

        if($result)
            $query->whereIn('t.id',$datos);       

        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);        

        return $query->execute();
    }
   
    public function findParticipadosALL($usuario_id, $cuenta='localhost'){        
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u')
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->orderBy('t.updated_at desc');
        
        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);        
        return $query->execute();
    }
    
    public function findParticipadosMatched($usuario_id, $cuenta='localhost', $datos, $buscar){
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u ')
                //->from('DatoSeguimiento d, d.Etapa ex, ex.Tramite t, t.Etapas e, t.Proceso.Cuenta c, e.Usuario u')               
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->having('COUNT(t.id) > 0')  //Mostramos solo los que se han avanzado o tienen datos
                ->groupBy('t.id')
                ->orderBy('t.updated_at desc');                

        if($buscar)
            $query->whereIn('t.id',$datos);       

        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);        

        return $query->execute();
    }
	//Dentro del proceso de subsidios, busca el dato licencias
	//los criterios de busqueda son el numero de la licencia y el rut del trabajado
	public function findLicencias($licencia_numero, $trabajador_rut, $proceso_id, $inicio, $limite){

                $query= Doctrine_Query::create()
                        ->from('Tramite t,t.Proceso p,  t.Etapas e, e.DatosSeguimiento d')
			->where('p.activo=1 AND p.id = ?', $proceso_id);
                if($licencia_numero && $trabajador_rut){ 
			$query->andWhere("d.nombre = 'numero_licencia' AND d.valor LIKE ?",'%'.$licencia_numero.'%');	
			$query->andWhere("t.id IN (SELECT tr.id FROM Tramite tr INNER JOIN tr.Etapas et INNER JOIN et.DatosSeguimiento ds WHERE ds.nombre = 'rut_trabajador_subsidio' AND ds.valor LIKE ?)", '%'.$trabajador_rut.'%'); 	
                }
		else{
                        if($licencia_numero)
                                $query->andWhere("d.nombre = 'numero_licencia' AND d.valor LIKE ?",'%'.$licencia_numero.'%');
                        else
                                $query->andWhere("d.nombre = 'rut_trabajador_subsidio' AND d.valor LIKE ?",'%'.$trabajador_rut.'%');

                }
                if($inicio) $query->offset($inicio);
                if($limite) $query->limit($limite);
                $query->orderBy('t.updated_at desc');

                return $query->execute();
        }



}
