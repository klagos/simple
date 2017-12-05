<?php
class Migration_19 extends Doctrine_Migration_Base {
    public function up(){
        $this->addColumn( 'auditoria_operaciones', 'proceso', 'varchar' ,128, array( 'notnull' => 1));
        
    }
    
    public function down(){
        $this->removeColumn( 'auditoria_operaciones', 'proceso' );
    }
}
?>