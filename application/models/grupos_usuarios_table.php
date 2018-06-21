<?php
require_once(FCPATH."procesos.php");
class GrupoUsuariosTable extends Doctrine_Table {
	
	const MODULO_PIE_DE_FIRMA = 1;
	const MODULO_NEG_COLECTIVA= 2;
	const MODULO_INDUCCION	= 3;
	const MODULO_DIAS_ADMIN	= 4;
	const MODULO_GUIA_TELEFONO = 5;
	const MODULO_GUIA_TRABAJADORES = 6;
	const MODULO_FAS = 7;	
	const MODULO_VACATION = 8;

	public function cantGruposUsuaros($usuario_id,$mod){
                $modulo = $this->modulo($mod);
                $usuario=Doctrine::getTable('Usuario')->find($usuario_id);
		
                $u = Doctrine_Query::create()
                        ->from('GrupoUsuarios g, g.Modulo m, g.Usuarios u')
                        ->where('u.id = ?', $usuario->id)
                        ->andWhere('g.modulo_id = ?', $modulo);
		
                return $u->count();
        }
	
	public function modulo($mod){
		switch ($mod) {
			case "MODULO_PIE_DE_FIRMA":
                                return self::MODULO_PIE_DE_FIRMA;
    			case "MODULO_NEG_COLECTIVA":
                                return self::MODULO_NEG_COLECTIVA;
			case "MODULO_INDUCCION":
				return self::MODULO_INDUCCION;
    			case "MODULO_DIAS_ADMIN":
				return self::MODULO_DIAS_ADMIN;
			case "MODULO_GUIA_TELEFONO":
                                return self::MODULO_GUIA_TELEFONO;
			case "MODULO_GUIA_TRABAJADORES":
                                return self::MODULO_GUIA_TRABAJADORES;
			case "MODULO_FAS":
                                return self::MODULO_FAS;
			case "MODULO_VACATION":
                                return self::MODULO_VACATION;	
    			
		}
	}	
}
