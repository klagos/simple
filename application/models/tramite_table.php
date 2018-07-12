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

        return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }
   
    public function findParticipadosALL($usuario_id, $cuenta='localhost'){        
        $query=Doctrine_Query::create()
                ->from('Tramite t, t.Proceso.Cuenta c, t.Etapas e, e.Usuario u')
                ->where('u.id = ?',$usuario_id)
                ->andWhere('e.pendiente=0')
                ->orderBy('t.updated_at desc');
        
        if($cuenta!='localhost')
            $query->andWhere('c.nombre = ?',$cuenta->nombre);        
        return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
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
	public function findLicencias($licencia_numero,$licencia_tipo,$licencia_estado,$trabajador_rut, $proceso_id, $inicio, $limite){
                $query= Doctrine_Query::create()
                        ->from('Tramite t,t.Proceso p,  t.Etapas e, e.DatosSeguimiento d')
			->where('p.activo=1 AND p.id = ?', $proceso_id);

		$query->andWhere("t.id IN (SELECT trNU.id FROM Tramite trNU INNER JOIN trNU.Etapas etNU INNER JOIN etNU.DatosSeguimiento dsNU WHERE dsNU.nombre = 'numero_licencia' AND dsNU.valor IS NOT NULL)");

		//LICENCIA NUMERO
		if($licencia_numero)
			$query->andWhere("t.id IN (SELECT tr.id FROM Tramite tr INNER JOIN tr.Etapas et INNER JOIN et.DatosSeguimiento ds WHERE ds.nombre = 'numero_licencia' AND ds.valor LIKE ?)",'%'.$licencia_numero.'%');

		//TRABAJADOR RUT
		if($trabajador_rut)
                        $query->andWhere("t.id IN (SELECT trA.id FROM Tramite trA INNER JOIN trA.Etapas etA INNER JOIN etA.DatosSeguimiento dsA WHERE dsA.nombre = 'rut_trabajador_subsidio' AND dsA.valor LIKE ?)", '%'.$trabajador_rut.'%');

		//TIPO LICENCIA		
		if($licencia_tipo)
			 $query->andWhere("t.id IN (SELECT trTi.id FROM Tramite trTi INNER JOIN trTi.Etapas etTi INNER JOIN etTi.DatosSeguimiento dsTi WHERE dsTi.nombre = 'tipo_licencia' AND dsTi.valor LIKE ?)", '%'.$licencia_tipo.'%');

		//ESTADO LICENCIA	
		if($licencia_estado){
                        if($licencia_estado=="ingresada"){
					$query->andWhere("t.id NOT IN (SELECT trES.id FROM Tramite trES INNER JOIN trES.Etapas etES INNER JOIN etES.DatosSeguimiento dsES WHERE dsES.nombre = 'fecha_pago_subsidio' AND dsES.valor IS NOT NULL)");
					
			}
                        if($licencia_estado=="pagada"){
                                        $query->andWhere("t.id IN     (SELECT trES.id FROM Tramite trES INNER JOIN trES.Etapas etES INNER JOIN etES.DatosSeguimiento dsES WHERE dsES.nombre = 'fecha_pago_subsidio' AND dsES.valor IS NOT NULL)");

                                        $query->andWhere("t.id NOT IN (SELECT tra.id FROM Tramite tra INNER JOIN tra.Etapas eta INNER JOIN eta.DatosSeguimiento dse WHERE dse.nombre = 'fecha_retorno_subsidio' AND dse.valor IS NOT NULL)");
                        }
                        if($licencia_estado=="retornada")
                                        $query->andWhere("t.id IN     (SELECT trES.id FROM Tramite trES INNER JOIN trES.Etapas etES INNER JOIN etES.DatosSeguimiento dsES WHERE dsES.nombre = 'fecha_retorno_subsidio' AND dsES.valor IS NOT NULL)");
                } 	

                if($inicio) $query->offset($inicio);
                if($limite) $query->limit($limite);
                $query->orderBy('t.updated_at desc');
                return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        }
	
	//Se busca las licencias que coincidan con la fecha de busqueda
	public function findLicenciasPago($fecha_pago,$proceso_id){
		$query= Doctrine_Query::create()
                        ->from('Tramite t, t.Proceso p, t.Etapas e, e.DatosSeguimiento d')
                        ->where('p.activo=1 AND p.id = ?', $proceso_id);
		$query->andWhere("d.nombre = 'fecha_pago_subsidio' AND d.valor LIKE ?",'%'.$fecha_pago.'%');
		$query->orderBy('t.updated_at desc');
                return $query->execute();
		
	}
	     
	//retorna los tramites que completaron la segunda etapa (pago), pero aun no la tercera (retorno) 
	//y que pasaron asi una cierta cantidad de dias
	public function findLicenciasNoRetornadas($proceso_id, $days){
		
		$days_invalid = array();
		
		for ($i=-90; $i < $days; $i++){
			$days_invalid[] = date("d-m-Y",mktime(0,0,0,date("m"),date("d")-$i,date("Y"))); 
		}
	
                $query= Doctrine_Query::create()
                        ->from('Tramite t,t.Proceso p,  t.Etapas e, e.DatosSeguimiento d')
                        ->where('p.activo=1 AND p.id = ?', $proceso_id)
			
			->andWhere('d.nombre = "fecha_pago_subsidio" AND d.valor IS NOT NULL');
		foreach ($days_invalid as $day){
			$query->andWhere("d.nombre = 'fecha_pago_subsidio' AND d.valor NOT LIKE ?",'%'.$day.'%');
		}
			
			$query->andWhere("t.id NOT IN (SELECT tr.id FROM Tramite tr INNER JOIN tr.Etapas et INNER JOIN et.DatosSeguimiento ds WHERE ds.nombre = 'fecha_retorno_subsidio' AND ds.valor IS NOT NULL)");
		return $query->execute();
        }


	public function getDocumentosProcesoEstudios($proceso_id){
		$query= Doctrine_Query::create()
                        ->from('Tramite t,t.Proceso p,  t.Etapas e, e.DatosSeguimiento d')
                        ->where('p.activo=1 AND p.id = ?', $proceso_id);
		return $query->execute();
	}

	public function findLicenciasMasiva($proceso_id,$inicio,$limite){
 		$query= Doctrine_Query::create()
           	 ->from('Tramite t,t.Proceso p,  t.Etapas e, e.DatosSeguimiento d')
           	 ->where('p.activo=1 AND p.id = ?', $proceso_id);

 	   	$query->andWhere("t.id IN (SELECT trNU.id FROM Tramite trNU INNER JOIN trNU.Etapas etNU INNER JOIN etNU.DatosSeguimiento dsNU WHERE dsNU.nombre = 'numero_licencia' AND dsNU.valor IS NOT NULL)");
		if($inicio) $query->offset($inicio);
                if($limite) $query->limit($limite);
    		$query->orderBy('t.updated_at desc');
    		return $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}


}
