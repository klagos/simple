<?php
class Migration_21 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'auditoria_operaciones', 'cuenta_id', 'int(10) unsigned', null, array('notnull'=>1));
    }

    public function postUp() {
        $q = Doctrine_Manager::getInstance()->getCurrentConnection();
        $q->execute("UPDATE auditoria_operaciones set cuenta_id=3");
    }

    public function down(){
        $this->removeColumn('auditoria_operaciones', 'cuenta_id');
    }
}
