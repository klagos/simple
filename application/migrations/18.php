<?php

class Migration_18 extends Doctrine_Migration_Base {

    public function up() {
    
    	$columns = array(
    			'id' => array(
    					'type' => 'int(10) unsigned AUTO_INCREMENT',
    					'notnull' =>1
    			),
    			'fecha' => array(
    					'type' => 'datetime',
    					'notnull' => true
    			),
    			'motivo' => array(
    					'type' => 'varchar(512)',
    					'notnull' => true
    			),
    			'detalles' => array(
    					'type' => 'text',
    					'notnull' => true
    			),
    			'operacion' => array(
    					'type' => "varchar(128)"
    			),
    			'usuario' => array (
    					'type' => 'varchar(390)',
    					'notnull' => true
    			)
    			
    	);
    	
    	$this->createTable('auditoria_operaciones', $columns, array('primary' => array('id')));
    	
    }

    public function down() {
		$this->dropTable('auditoria_operaciones');
    }

}
?>