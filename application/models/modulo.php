<?php

class Modulo extends Doctrine_Record {

    function setTableDefinition() {
        $this->hasColumn('id');
        $this->hasColumn('nombre');
    }

    function setUp() {
        parent::setUp();
        
	$this->hasMany('GrupoUsuarios as GruposUsuarios', array(
            'local' => 'id',
            'foreign' => 'modulo_id'
        ));        
        

    }
    
}
