<?php

class FileTable extends Doctrine_Table{
      
        
    //Busca el valor del dato hasta la etapa $etapa_id
   	public function findOneByTipoAndTramite($tipo,$tramite_id){
		$tramite=Doctrine_Core::getTable('Tramite')->find($tramite_id);
	
                $file= Doctrine_Query::create()
		->from('File f, f.Tramite t')
                ->where('t.id = ?',$tramite->id)
		->andWhere('f.tipo = ?',$tipo)
		->execute(); 	
		
		return $file;
	}
    
}
