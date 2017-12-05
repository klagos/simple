<?php

class Migration_17 extends Doctrine_Migration_Base {

    public function up() {
    
    	$this->changeColumn('tarea', 'vencimiento_unidad', "enum('D','W','M','Y')",null,array('notnull'=>true));    	
    	
    }


    public function down() {
		$this->changeColumn('tarea', 'vencimiento_unidad', "enum('D','W','M')",null,array('notnull'=>true));
    }

}
?>