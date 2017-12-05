<?php

class Migration_31 extends Doctrine_Migration_Base {

    public function up() {
    
        $columns = array(
                'componente' => array(
                        'type' => 'varchar(45)',
                        'notnull' =>true
                ),
                'cuenta' => array(
                        'type' => 'int(2)',
                        'notnull' => true
                ),
                'llave' => array(
                        'type' => 'varchar(80)',
                        'notnull' => true
                ),
                'valor' => array(
                        'type' => 'varchar(256)',
                        'notnull' => false
                )
        );
        $this->createTable('config_general', $columns, array('primary' => array('componente','cuenta','llave')));
    }
    public function down() {
        $this->dropTable('config_general');
    }
}
?>
