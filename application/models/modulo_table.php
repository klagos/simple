<?php
require_once(FCPATH."procesos.php");
class ModuloTable extends Doctrine_Table {
   
	//Busca si se encuentra en el modulo de pie de firma el usuario
	public function moduloPieFirma($usuario_id){
		$moduloPieFirma = 1;//modulo_pie_firma;
		$usuario=Doctrine::getTable('Usuario')->find($usuario_id);
		
		$u = Doctrine_Query::create()
                        ->from('Usuario u, u.GruposUsuarios g')
                        ->where('u.id = ?', $usuario->id)
                        ->andWhere('g.modulo_id = ?', $moduloPieFirma)
                        ->fetchOne();	
		if($u)
			return true;
		else
			return false;
		
	}
	
	//Busca si se encuentra en el modulo negociacion colectiva de firma el usuario
        public function moduloContratoColectivo($usuario_id){
                $moduloPieFirma = 2;//modulo_pie_firma;
                $usuario=Doctrine::getTable('Usuario')->find($usuario_id);

                $u = Doctrine_Query::create()
                        ->from('Usuario u, u.GruposUsuarios g')
                        ->where('u.id = ?', $usuario->id)
                        ->andWhere('g.modulo_id = ?', $moduloPieFirma)
                        ->fetchOne();
                if($u)
                        return true;
                else
                        return false;

        }

	
}
