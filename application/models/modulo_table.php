<?php
require_once(FCPATH."procesos.php");
class ModuloTable extends Doctrine_Table {
   
	//Busca si se encuentra en el modulo de pie de firma el usuario
	public function moduloPieFirma($usuario_id){
	}
	
	//Busca si se encuentra en el modulo negociacion colectiva de firma el usuario
        public function moduloContratoColectivo($usuario_id){
        }
        
}
